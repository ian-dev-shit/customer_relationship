import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from scipy.interpolate import make_interp_spline
import io
import re
import base64
from typing import Dict, Any
from app.supabase_config.supabase import supabase_secondary, gemini_client
from datetime import datetime

class BIAnalyticsService:

    @staticmethod
    def get_closed_won_revenue_analytics() -> Dict[str, Any]:
        try:
            # 1. Fetch data mula sa Supabase 'inquiries' table (estimated_amount lang)
            response = (
                supabase_secondary.table("inquiries")
                .select("id, estimated_amount, status, created_at")
                .ilike("status", "closed_won")
                .execute()
            )

            raw_data = response.data or []

            # Setup 12 Months Skeleton 
            months_letters = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D']
            monthly_revenue = np.zeros(12)

            total_revenue = 0.0
            if raw_data:
                df = pd.DataFrame(raw_data)
                
                # Directly convert estimated_amount
                df['amount'] = pd.to_numeric(df['estimated_amount'], errors='coerce').fillna(0.0)
                df['created_at'] = pd.to_datetime(df['created_at'], errors='coerce')
                
                total_revenue = float(df['amount'].sum())

                # I-group by month index (0 = Jan, 11 = Dec)
                df['month_num'] = df['created_at'].dt.month - 1
                grouped = df.groupby('month_num')['amount'].sum()

                for m_idx, rev in grouped.items():
                    if 0 <= m_idx < 12:
                        monthly_revenue[int(m_idx)] = float(rev)

            # 2. Matplotlib Styling 
            plt.close('all')
            fig, ax = plt.subplots(figsize=(4.2, 1.8), dpi=150)

            fig.patch.set_facecolor('#EBF3FA')
            ax.set_facecolor('#EBF3FA')

            x = np.arange(12)
            y = monthly_revenue

            # Smooth Curve Interp (Spline)
            if np.count_nonzero(y) > 1:
                x_smooth = np.linspace(x.min(), x.max(), 300)
                spl = make_interp_spline(x, y, k=3)
                y_smooth = spl(x_smooth)
                ax.plot(x_smooth, y_smooth, color='#4A85F6', linewidth=3.5, zorder=2)
            else:
                ax.plot(x, y, color='#4A85F6', linewidth=3.5, zorder=2)

            # Data Dots / Points
            ax.scatter(x, y, color='#4A85F6', s=40, zorder=3, edgecolors='white', linewidth=1.5)

            # Horizontal Dotted Gridlines
            ax.grid(axis='y', linestyle=':', color='#A0B2C6', alpha=0.6, linewidth=1.2)
            ax.set_axisbelow(True)

            # Remove Spines/Borders
            for spine in ax.spines.values():
                spine.set_visible(False)

            # X-Axis Labels (J F M A M J J A S O N D)
            ax.set_xticks(x)
            ax.set_xticklabels(months_letters, color='#334155', fontsize=9, fontweight='bold')
            ax.tick_params(axis='x', which='both', length=0, pad=8)

            # Y-Axis Formatting (₱1.00M / ₱500K / ₱0)
            def currency_fmt(val, pos):
                if val >= 1_000_000:
                    return f'₱{val*1e-6:.2f}M'
                elif val >= 1_000:
                    return f'₱{val*1e-3:.0f}K'
                return f'₱{val:.0f}'

            ax.yaxis.set_major_formatter(plt.FuncFormatter(currency_fmt))
            ax.tick_params(axis='y', which='both', length=0, labelsize=8, labelcolor='#64748B')

            plt.tight_layout()

            # Convert to Base64 String
            buf = io.BytesIO()
            plt.savefig(buf, format='png', bbox_inches='tight', facecolor=fig.get_facecolor(), edgecolor='none')
            buf.seek(0)
            chart_base64 = base64.b64encode(buf.getvalue()).decode('utf-8')
            plt.close(fig)

            return {
                "status": "success",
                "summary": {
                    "total_revenue": round(total_revenue, 2),
                    "formatted_revenue": f"₱{total_revenue:,.2f}"
                },
                "chart_image": f"data:image/png;base64,{chart_base64}"
            }

        except Exception as e:
            raise Exception(f"BI Analytics Error: {str(e)}")


    # --- CARD 2: SERVICE TYPES ANALYTICS  ---
    @staticmethod
    def get_service_types_analytics() -> Dict[str, Any]:
        try:
            # Kinukuha ang total revenue & count per service_type
            response = (
                supabase_secondary.table("inquiries")
                .select("service_type, estimated_amount")
                .ilike("status", "closed_won")
                .execute()
            )

            raw_data = response.data or []
            services_summary = []

            if raw_data:
                df = pd.DataFrame(raw_data)
                df['amount'] = pd.to_numeric(df['estimated_amount'], errors='coerce').fillna(0.0)
                df['service_type'] = df['service_type'].fillna('Standard Freight').str.title()

                grouped = df.groupby('service_type')['amount'].agg(['sum', 'count']).reset_index()
                grouped.sort_values(by='sum', ascending=False, inplace=True)

                for _, row in grouped.iterrows():
                    services_summary.append({
                        "service_name": row['service_type'],
                        "revenue": float(row['sum']),
                        "formatted_revenue": f"₱{row['sum']:,.2f}",
                        "deals_count": int(row['count'])
                    })

            return {
                "status": "success",
                "data": services_summary
            }

        except Exception as e:
            raise Exception(f"Service Types Analytics Error: {str(e)}")


    # --- CARD 3: TOP ROUTES ANALYTICS ---
    @staticmethod
    def get_top_routes_analytics() -> Dict[str, Any]:
        try:
            response = (
                supabase_secondary.table("inquiries")
                .select("origin, destination, estimated_amount, status")
                .execute()
            )

            raw_data = response.data or []
            top_routes_data = []

            if raw_data:
                df = pd.DataFrame(raw_data)
                
                df['origin'] = df['origin'].fillna('Unknown').astype(str).str.strip().str.title()
                df['destination'] = df['destination'].fillna('Unknown').astype(str).str.strip().str.title()
                
                # Full Route Label (e.g., "Manila → China")
                df['route'] = df['origin'] + " → " + df['destination']
                df['amount'] = pd.to_numeric(df['estimated_amount'], errors='coerce').fillna(0.0)

                # Group strictly by unique full route
                grouped = df.groupby('route').agg(
                    total_inquiries=('amount', 'count'),
                    total_revenue=('amount', 'sum'),
                    origin=('origin', 'first')
                ).reset_index()

                # Sort by count descending
                grouped = grouped.sort_values(by='total_inquiries', ascending=False)

                for _, row in grouped.head(5).iterrows():
                    top_routes_data.append({
                        "route_name": str(row['route']),       
                        "short_name": str(row['origin']),        
                        "inquiries_count": int(row['total_inquiries']),
                        "total_revenue": float(row['total_revenue'])
                    })

            return {
                "status": "success",
                "data": top_routes_data
            }

        except Exception as e:
            raise Exception(f"Top Routes Analytics Error: {str(e)}")

    # --- CARD 4: SHIPMENTS CLOSED OVER TIME ---
    @staticmethod
    def get_shipments_closed_analytics() -> Dict[str, Any]:
        try:
            current_year = datetime.now().year
            
            # Kuhanin ang closed/won/completed inquiries
            response = (
                supabase_secondary.table("inquiries")
                .select("created_at, status")
                .in_("status", ["closed_won", "completed"])
                .execute()
            )

            raw_data = response.data or []
            monthly_counts = {i: 0 for i in range(1, 13)}

            if raw_data:
                df = pd.DataFrame(raw_data)
                df['created_at'] = pd.to_datetime(df['created_at'], errors='coerce')
                
                # Filter sa kasalukuyang taon lang
                df_year = df[df['created_at'].dt.year == current_year]

                if not df_year.empty:
                    counts = df_year['created_at'].dt.month.value_counts().to_dict()
                    for month_num, count in counts.items():
                        monthly_counts[int(month_num)] = int(count)

            total_shipments = sum(monthly_counts.values())
            chart_series = [monthly_counts[i] for i in range(1, 13)]

            return {
                "status": "success",
                "total_closed": total_shipments,
                "monthly_series": chart_series
            }

        except Exception as e:
            raise Exception(f"Shipments Closed Analytics Error: {str(e)}")

        
    @staticmethod
    def get_win_loss_by_service_analytics() -> Dict[str, Any]:
        try:
            response = (
                supabase_secondary.table("inquiries")
                .select("service_type, status, estimated_amount, notes")
                .execute()
            )

            raw_data = response.data or []
            
            categories = []
            won_series = []
            lost_series = []
            ai_suggestion = "No inquiry data available yet to generate AI recommendations."
            
            if raw_data:
                df = pd.DataFrame(raw_data)
                
                df['service_type'] = df['service_type'].fillna('Standard Freight').str.title()
                
                won_statuses = ['closed_won', 'completed']
                lost_statuses = ['closed_lost', 'cancelled', 'rejected']
                
                df['is_won'] = df['status'].str.lower().isin(won_statuses)
                df['is_lost'] = df['status'].str.lower().isin(lost_statuses)

                df_finished = df[df['is_won'] | df['is_lost']].copy()

                if not df_finished.empty:
                    grouped = df_finished.groupby('service_type').agg(
                        won=('is_won', 'sum'),
                        lost=('is_lost', 'sum')
                    ).reset_index()

                    grouped['total'] = grouped['won'] + grouped['lost']
                    grouped['win_rate'] = np.where(
                        grouped['total'] > 0, 
                        (grouped['won'] / grouped['total']) * 100, 
                        0.0
                    )

                    categories = grouped['service_type'].tolist()
                    won_series = grouped['won'].astype(int).tolist()
                    lost_series = grouped['lost'].astype(int).tolist()

                    valid_services = grouped[grouped['total'] > 0].sort_values(by='win_rate', ascending=True)

                    if not valid_services.empty:
                        lowest_row = valid_services.iloc[0]
                        lowest_service = str(lowest_row['service_type'])
                        lowest_win_rate = round(float(lowest_row['win_rate']), 1)
                        total_lost = int(lowest_row['lost'])
                        total_won = int(lowest_row['won'])

                        lost_records = df_finished[
                            (df_finished['service_type'] == lowest_service) & 
                            (df_finished['is_lost'] == True)
                        ]
                        sample_notes = lost_records['notes'].dropna().tolist()[:5] if 'notes' in lost_records.columns else []

                        # --- HYBRID AI ENGINE (GEMINI API) ---
                        ai_success = False
                        
                        if gemini_client:
                            try:
                                prompt = f"""
                                You are a Senior Logistics Business Analyst.
                                Analyze the performance for the service type: '{lowest_service}'.
                                Metrics:
                                - Total Won Deals: {total_won}
                                - Total Lost Deals: {total_lost}
                                - Win Rate: {lowest_win_rate}%
                                - Sample Lost Deal Notes/Feedback: {sample_notes if sample_notes else 'No specific feedback provided.'}

                                Task:
                                Provide a concise (2-3 sentences max) actionable business suggestion on why this service type is underperforming and how sales agents can increase conversion rates.
                                """

                               
                                chat_session = gemini_client.chats.create(model='gemini-3.6-flash')
                                ai_response = chat_session.send_message(prompt)
                                
                                dynamic_suggestion = ai_response.text.strip()
                                ai_suggestion = f"[Gemini AI Analysis] {dynamic_suggestion}"
                                ai_success = True

                            except Exception as ai_err:
                                print(f"Gemini API call failed, falling back to Knowledge Base: {str(ai_err)}")

                        # FALLBACK TO KNOWLEDGE BASE (Rule-Based Engine)
                        if not ai_success:
                            preset_strategies = {
                                "Sea Freight": (
                                    f"Sea Freight has the lowest win rate ({lowest_win_rate}%) with {total_lost} lost deals. "
                                    "AI Insight: Ocean freight buyers are highly price-sensitive and vulnerable to long quote delays. "
                                    "Recommendation: Streamline freight forwarding quote generation and benchmark ocean container rates against market benchmarks."
                                ),
                                "Air Freight": (
                                    f"Air Freight conversion is low at {lowest_win_rate}%. "
                                    "AI Insight: Urgency is the main driver for air shipments. Slow response times lead to high drop-offs. "
                                    "Recommendation: Implement immediate auto-replies with estimated pricing tiers for urgent air freight inquiries."
                                ),
                                "Land Transport": (
                                    f"Land Transport / Trucking win rate stands at {lowest_win_rate}%. "
                                    "AI Insight: Customers often look for bundled deals. "
                                    "Recommendation: Package land haulage together with customs clearance or warehousing services to increase deal value."
                                )
                            }

                            ai_suggestion = preset_strategies.get(
                                lowest_service,
                                f"{lowest_service} has a low win rate of {lowest_win_rate}%. "
                                f"AI Recommendation: Review pricing strategy, decrease follow-up response times, and analyze competitor quotes for {lowest_service}."
                            )

            return {
                "status": "success",
                "categories": categories,
                "series": [
                    {"name": "Won Deals", "data": won_series},
                    {"name": "Lost Deals", "data": lost_series}
                ],
                "ai_suggestion": ai_suggestion
            }

        except Exception as e:
            raise Exception(f"Win/Loss Analytics Error: {str(e)}")

    # --- CARD: DONUT CHART - SHARE OF CLOSED WON DEALS PER SERVICE TYPE ---
    @staticmethod
    def get_service_won_distribution_analytics() -> Dict[str, Any]:
        try:
            # 1. Fetch inquiries data from Supabase
            response = (
                supabase_secondary.table("inquiries")
                .select("service_type, status, estimated_amount, notes")
                .execute()
            )

            raw_data = response.data or []
            
            labels = []
            series = []
            percentages = []
            ai_suggestion = "No won deal data available yet to analyze service distribution."

            if raw_data:
                df = pd.DataFrame(raw_data)
                
                # Clean & Normalize Service Type
                df['service_type'] = df['service_type'].fillna('Standard Freight').str.title()
                
                # Filter ONLY Closed Won / Completed deals
                won_statuses = ['closed_won', 'completed']
                df_won = df[df['status'].str.lower().isin(won_statuses)].copy()

                if not df_won.empty:
                    # Group by service_type to count won deals
                    grouped = df_won.groupby('service_type').size().reset_index(name='won_count')
                    
                    total_won = grouped['won_count'].sum()

                    if total_won > 0:
                        grouped['percentage'] = (grouped['won_count'] / total_won) * 100
                        grouped['percentage'] = grouped['percentage'].round(1)

                        labels = grouped['service_type'].tolist()
                        series = grouped['won_count'].astype(int).tolist()
                        percentages = grouped['percentage'].tolist()

                        # Identify top performing service
                        top_row = grouped.sort_values(by='won_count', ascending=False).iloc[0]
                        top_service = top_row['service_type']
                        top_share = top_row['percentage']
                        top_count = int(top_row['won_count'])

                        # --- DYNAMIC GEMINI AI ANALYSIS ---
                        ai_success = False

                        if gemini_client:
                            try:
                                prompt = f"""
                                You are a Senior Logistics Sales Strategist.
                                Analyze the Share of Closed Won Deals across services:
                                - Total Won Deals across all services: {total_won}
                                - Leading Service: '{top_service}' with {top_count} deals ({top_share}% market share of closed deals).
                                - Complete Distribution breakdown: {dict(zip(labels, percentages))}%

                                Task:
                                Provide a concise (2 sentences max) insight explaining why this dominant service type is succeeding and how sales reps can cross-sell lagging service types.
                                """

                                chat_session = gemini_client.chats.create(model='gemini-3.6-flash')
                                ai_response = chat_session.send_message(prompt)

                                dynamic_suggestion = ai_response.text.strip()
                                ai_suggestion = f"[Gemini AI Analysis] {dynamic_suggestion}"
                                ai_success = True

                            except Exception as ai_err:
                                print(f"Gemini API call failed for Donut Analytics: {str(ai_err)}")

                        # FALLBACK TO RULE-BASED KNOWLEDGE BASE
                        if not ai_success:
                            ai_suggestion = (
                                f"{top_service} dominates your closed deals with a {top_share}% share ({top_count} deals). "
                                f"Recommendation: Leverage sales momentum in {top_service} to bundle lower-performing services."
                            )

            return {
                "status": "success",
                "labels": labels,          # e.g., ["Air Freight", "Sea Freight", "Land Transport"]
                "series": series,          # e.g., [12, 8, 5] (Raw Counts)
                "percentages": percentages, # e.g., [48.0, 32.0, 20.0] (%)
                "ai_suggestion": ai_suggestion
            }

        except Exception as e:
            raise Exception(f"Service Won Distribution Error: {str(e)}")

    @staticmethod
    def get_weight_class_win_loss_analytics() -> Dict[str, Any]:
        try:
            response = (
                supabase_secondary.table("inquiries")
                .select("cargo_details, status, service_type")
                .execute()
            )

            raw_data = response.data or []

            def parse_weight_to_tons(cargo_text: str) -> float:
                if not cargo_text:
                    return 0.0
                match_kg = re.search(r'(\d+(?:\.\d+)?)\s*(?:kg|kilograms|kilos)', cargo_text, re.IGNORECASE)
                match_ton = re.search(r'(\d+(?:\.\d+)?)\s*(?:ton|tons|t)', cargo_text, re.IGNORECASE)

                if match_kg:
                    return round(float(match_kg.group(1)) / 1000.0, 2)
                elif match_ton:
                    return round(float(match_ton.group(1)), 2)
                
                match_num = re.search(r'(\d+(?:\.\d+)?)', cargo_text)
                if match_num:
                    return round(float(match_num.group(1)), 2)
                return 0.0

            # Service mappings: Air, Sea, Land
            services = ["Air Freight", "Sea Freight", "Land Transport"]
            
            # Data structure para sa Won at Lost
            won_data = {s: {"count": 0, "weights": []} for s in services}
            lost_data = {s: {"count": 0, "weights": []} for s in services}

            if raw_data:
                for row in raw_data:
                    status = str(row.get("status", "")).lower().strip()
                    cargo_info = str(row.get("cargo_details", ""))
                    srv = str(row.get("service_type", "")).title().strip()

                    # Standardize service key
                    if "Air" in srv:
                        srv_key = "Air Freight"
                    elif "Sea" in srv:
                        srv_key = "Sea Freight"
                    else:
                        srv_key = "Land Transport"

                    weight_tons = parse_weight_to_tons(cargo_info)

                    if status in ["closed_won", "completed"]:
                        won_data[srv_key]["count"] += 1
                        if weight_tons > 0:
                            won_data[srv_key]["weights"].append(f"{weight_tons} tons")
                    elif status in ["closed_lost", "lost", "cancelled"]:
                        lost_data[srv_key]["count"] += 1
                        if weight_tons > 0:
                            lost_data[srv_key]["weights"].append(f"{weight_tons} tons")

            # Series setup para sa ApexCharts Stacked Bar
            series = [
                {
                    "name": "Air Freight",
                    "data": [won_data["Air Freight"]["count"], lost_data["Air Freight"]["count"]],
                    "details": [won_data["Air Freight"]["weights"], lost_data["Air Freight"]["weights"]]
                },
                {
                    "name": "Sea Freight",
                    "data": [won_data["Sea Freight"]["count"], lost_data["Sea Freight"]["count"]],
                    "details": [won_data["Sea Freight"]["weights"], lost_data["Sea Freight"]["weights"]]
                },
                {
                    "name": "Land Transport",
                    "data": [won_data["Land Transport"]["count"], lost_data["Land Transport"]["count"]],
                    "details": [won_data["Land Transport"]["weights"], lost_data["Land Transport"]["weights"]]
                }
            ]

            return {
                "status": "success",
                "categories": ["Closed Won", "Closed Lost"],
                "series": series
            }

        except Exception as e:
            raise Exception(f"Weight Class Analytics Error: {str(e)}")