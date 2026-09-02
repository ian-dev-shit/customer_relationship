import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from datetime import datetime, timedelta, timezone
from dateutil.relativedelta import relativedelta

def get_growth_percentage(current_val, previous_val):
    if previous_val == 0 or pd.isna(previous_val):
        return 0.0 if current_val == 0 else 100.0
    return float(np.round(((current_val - previous_val) / previous_val) * 100, 1))

def process_sales_analytics(inquiries_data: list, tickets_data: list, bookings_data: list):
    df_inquiries = pd.DataFrame(inquiries_data) if inquiries_data else pd.DataFrame()
    df_tickets = pd.DataFrame(tickets_data) if tickets_data else pd.DataFrame()
    df_bookings = pd.DataFrame(bookings_data) if bookings_data else pd.DataFrame()

    now = datetime.now()
    # Current Month
    curr_m, curr_y = now.month, now.year
    # Previous Month
    prev_date = now - relativedelta(months=1)
    prev_m, prev_y = prev_date.month, prev_date.year

    # 1. INQUIRIES & CONVERSION COMPARISON
    active_leads_count = 0
    conversion_rate = 0.0
    conversion_growth = 0.0

    if not df_inquiries.empty and 'created_at' in df_inquiries.columns:
        df_inquiries['created_at'] = pd.to_datetime(df_inquiries['created_at'])
        df_inquiries['status'] = df_inquiries['status'].astype(str).str.lower().str.strip()

        # Active leads
        active_statuses = ['new_inquiry', 'qualifying', 'quote_sent', 'negotiation']
        active_leads_count = int(df_inquiries[df_inquiries['status'].isin(active_statuses)].shape[0])

        # Current Month Conversion
        curr_inq = df_inquiries[(df_inquiries['created_at'].dt.month == curr_m) & (df_inquiries['created_at'].dt.year == curr_y)]
        curr_total = curr_inq.shape[0]
        curr_won = curr_inq[curr_inq['status'] == 'closed_won'].shape[0]
        conversion_rate = float(np.round((curr_won / curr_total * 100), 1)) if curr_total > 0 else 0.0

        # Previous Month Conversion
        prev_inq = df_inquiries[(df_inquiries['created_at'].dt.month == prev_m) & (df_inquiries['created_at'].dt.year == prev_y)]
        prev_total = prev_inq.shape[0]
        prev_won = prev_inq[prev_inq['status'] == 'closed_won'].shape[0]
        prev_conversion = float(np.round((prev_won / prev_total * 100), 1)) if prev_total > 0 else 0.0

        conversion_growth = get_growth_percentage(conversion_rate, prev_conversion)


    # 2. REVENUE & CLOSED CUSTOMERS COMPARISON (MTD vs Previous Month)
    def get_monthly_stats(df, amount_col):
        if df.empty or 'created_at' not in df.columns:
            return 0.0, 0, 0.0, 0
        
        df['created_at'] = pd.to_datetime(df['created_at'])
        col = amount_col if amount_col in df.columns else 'estimated_amount'

        # Current MTD
        c_mask = (df['created_at'].dt.month == curr_m) & (df['created_at'].dt.year == curr_y)
        c_rev = float(df[c_mask][col].fillna(0).sum()) if col in df.columns else 0.0
        c_closed = int(df[c_mask].shape[0])

        # Previous Month
        p_mask = (df['created_at'].dt.month == prev_m) & (df['created_at'].dt.year == prev_y)
        p_rev = float(df[p_mask][col].fillna(0).sum()) if col in df.columns else 0.0
        p_closed = int(df[p_mask].shape[0])

        return c_rev, c_closed, p_rev, p_closed

    # Fetch from tickets & bookings
    t_c_rev, t_c_closed, t_p_rev, t_p_closed = get_monthly_stats(df_tickets, 'agreed_amount')
    b_c_rev, b_c_closed, b_p_rev, b_p_closed = get_monthly_stats(df_bookings, 'agreed_amount')

    # Totals
    curr_revenue = t_c_rev + b_c_rev
    prev_revenue = t_p_rev + b_p_rev

    curr_closed = t_c_closed + b_c_closed
    prev_closed = t_p_closed + b_p_closed

    revenue_growth = get_growth_percentage(curr_revenue, prev_revenue)
    closed_growth = get_growth_percentage(curr_closed, prev_closed)


    
    # 3. FORECASTING 
    forecast_data = {"labels": [], "actual": [], "predicted": []}
    rev_series_list = []

    if not df_tickets.empty and 'created_at' in df_tickets.columns:
        col = 'agreed_amount' if 'agreed_amount' in df_tickets.columns else 'estimated_amount'
        if col in df_tickets.columns:
            rev_series_list.append(df_tickets[['created_at', col]].rename(columns={col: 'amount'}))

    if not df_bookings.empty and 'created_at' in df_bookings.columns and 'agreed_amount' in df_bookings.columns:
        rev_series_list.append(df_bookings[['created_at', 'agreed_amount']].rename(columns={'agreed_amount': 'amount'}))

    # 1. Bumuo ng 12-month sequence 11 past months + current month
    now = datetime.now()
    month_dates = [now - relativedelta(months=i) for i in range(11, -1, -1)]
    
    # Abbreviated month names ['Sep', 'Oct', 'Nov', ..., 'Aug']
    labels = [d.strftime('%b') for d in month_dates]
    ym_keys = [d.strftime('%Y-%m') for d in month_dates]

    actual_map = {key: 0.0 for key in ym_keys}

    # 2. I-map ang totoong kita sa tamang buwan
    if rev_series_list:
        combined_df = pd.concat(rev_series_list, ignore_index=True)
        combined_df['amount'] = pd.to_numeric(combined_df['amount'], errors='coerce').fillna(0)
        combined_df['created_at'] = pd.to_datetime(combined_df['created_at'])
        
        if combined_df['created_at'].dt.tz is not None:
            combined_df['created_at'] = combined_df['created_at'].dt.tz_localize(None)

        combined_df['year_month'] = combined_df['created_at'].dt.to_period('M').astype(str)
        grouped = combined_df.groupby('year_month')['amount'].sum().to_dict()

        for ym in ym_keys:
            if ym in grouped:
                actual_map[ym] = float(grouped[ym])

    actual_list = [actual_map[ym] for ym in ym_keys]

    # 3. Predict gamit ang Scikit-Learn
    X = np.arange(12).reshape(-1, 1)
    y = np.array(actual_list)
    
    model = LinearRegression().fit(X, y)
    predicted_list = [float(np.round(val, 2)) for val in model.predict(X)]
    predicted_list = [max(0.0, v) for v in predicted_list] 

    forecast_data = {
        "labels": labels,
        "actual": actual_list,
        "predicted": predicted_list
    }

    return {
        "kpis": {
            "active_leads": active_leads_count,
            "booking_conversion_rate": conversion_rate,
            "total_revenue_mtd": curr_revenue,
            "customers_closed_mtd": curr_closed,
            "growth": {
                "conversion_growth": conversion_growth,
                "revenue_growth": revenue_growth,
                "closed_growth": closed_growth
            }
        },
        "revenue_forecast": forecast_data
    }

def get_pipeline_dashboard(supabase_client):
    try:
        # 1. Fetch inquiries & customers tables
        inquiries_res = supabase_client.table('inquiries').select('*').execute()
        customers_res = supabase_client.table('customers').select('*').execute()

        df_inquiries = pd.DataFrame(inquiries_res.data or [])
        df_customers = pd.DataFrame(customers_res.data or [])

        # 2. Pipeline Area Chart Activity 
        now_utc = datetime.now(timezone.utc)
        start_date = now_utc - timedelta(days=30)
        
        # Gumawa ng date keys 
        date_list = [(start_date + timedelta(days=i)).strftime('%m-%d') for i in range(31)]
        daily_counts = {d: 0 for d in date_list}

        if not df_inquiries.empty and 'created_at' in df_inquiries.columns:
            # Convert to UTC datetime safely
            df_inquiries['created_at_dt'] = pd.to_datetime(df_inquiries['created_at'], utc=True, errors='coerce')
            
            # Filter last 30 days
            recent_inquiries = df_inquiries[df_inquiries['created_at_dt'] >= start_date]
            
            for dt in recent_inquiries['created_at_dt']:
                if pd.notnull(dt):
                    day_str = dt.strftime('%m-%d')
                    if day_str in daily_counts:
                        daily_counts[day_str] += 1

        all_dates = list(daily_counts.keys())
        all_counts = list(daily_counts.values())

        # 3. Top Customers Leaderboard
        top_customers = []

        if not df_customers.empty:
            # Hanapin kung anong column ang merong total bookings
            booking_col = None
            for col in ['total_bookings', 'bookings_count', 'booking_count']:
                if col in df_customers.columns:
                    booking_col = col
                    break

            if booking_col:
                df_customers[booking_col] = pd.to_numeric(df_customers[booking_col], errors='coerce').fillna(0).astype(int)
                sorted_customers = df_customers.sort_values(by=booking_col, ascending=False).head(5)
            else:
                sorted_customers = df_customers.head(5)

            for _, row in sorted_customers.iterrows():
                b_count = int(row.get(booking_col, 0)) if booking_col else 0
                
                # Dynamic Tier Calculation (Match sa PHP helper mo)
                if b_count >= 15:
                    tier = "PLATINUM"
                elif b_count >= 10:
                    tier = "GOLD"
                elif b_count >= 5:
                    tier = "SILVER"
                else:
                    tier = "BRONZE"

                name = row.get('company_name') or row.get('contact_person') or row.get('name') or "Unknown Client"
                contact = row.get('contact_person') if row.get('company_name') else (row.get('email') or row.get('phone') or "N/A")

                top_customers.append({
                    "name": str(name),
                    "contact_person": str(contact or "N/A"),
                    "bookings_count": b_count,
                    "tier": tier
                })

        # 4. Stages Counts 
        total_leads = len(df_inquiries)
        new_count = qualifying_count = quote_count = negotiation_count = won_count = 0

        if not df_inquiries.empty and 'status' in df_inquiries.columns:
            df_inquiries['status_clean'] = df_inquiries['status'].astype(str).str.lower().str.strip()
            
            new_count = len(df_inquiries[df_inquiries['status_clean'].str.contains('new|inquiry|pending', na=False)])
            qualifying_count = len(df_inquiries[df_inquiries['status_clean'].str.contains('qualif|review', na=False)])
            quote_count = len(df_inquiries[df_inquiries['status_clean'].str.contains('quote|proposal|sent', na=False)])
            negotiation_count = len(df_inquiries[df_inquiries['status_clean'].str.contains('negotiat|discussion', na=False)])
            won_count = len(df_inquiries[df_inquiries['status_clean'].str.contains('won|closed|booked|approved', na=False)])

        return {
            "status": "success",
            "data": {
                "kpis": {
                    "active_leads": total_leads,
                    "customers_closed_mtd": won_count,
                    "stages": {
                        "new": new_count,
                        "qualifying": qualifying_count,
                        "quote_sent": quote_count,
                        "negotiation": negotiation_count,
                        "won": won_count
                    }
                },
                "pipeline_activity": {
                    "dates": all_dates,   
                    "counts": all_counts
                },
                "top_customers": top_customers
            }
        }

    except Exception as e:
        return {
            "status": "error",
            "message": f"Service Error: {str(e)}",
            "data": {
                "kpis": {"active_leads": 0, "customers_closed_mtd": 0, "stages": {"new": 0, "qualifying": 0, "quote_sent": 0, "negotiation": 0, "won": 0}},
                "pipeline_activity": {"dates": [], "counts": []},
                "top_customers": []
            }
        }