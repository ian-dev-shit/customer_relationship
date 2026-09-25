<?php
/**
 * Booking Shipment Modal.
 * Faithfully ported from "Booking Shipment.html" (Priority Handling Logistics
 * consignment note) and wrapped in a SwiftFreight dashboard modal.
 * Opened from the customer dashboard "Book Shipment" / "New Booking" / "Book" actions.
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Sales-agent view_customer.php may preset this to the viewed customer so an
// agent can book on the customer's behalf; otherwise fall back to the session.
$customer_id = $booking_customer_id_preset ?? $_SESSION['customer_id'] ?? $_SESSION['user_id'] ?? '';
?>
<style>
    :root {
      --primary-navy: #0e1e38;
      --navy-header: #0d1b36;
      --border-color: #1a1a1a;
      --border-light: #bbb;
      --btn-blue: #1c4290;
      --btn-blue-hover: #153475;
      --logo-teal: #1aa0d8;
      --bg-page: #f4f6f9;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    

    .form-container {
      background: #fff;
      width: 100%;
      max-width: 950px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border-radius: 4px;
    }

    /* Top Header */
    .top-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;
    }

    .company-info {
      flex: 1;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 6px;
    }

    .logo-icon {
      width: 34px;
      height: 24px;
    }

    .logo-text {
      font-size: 13px;
      font-weight: 900;
      color: var(--logo-teal);
      letter-spacing: 0.5px;
      line-height: 1.1;
    }

    .logo-tagline {
      font-size: 8.5px;
      color: #666;
      font-style: italic;
      font-weight: 500;
    }

    .company-details {
      font-size: 9.5px;
      line-height: 1.45;
      color: #333;
    }

    .company-details p {
      margin-bottom: 2px;
    }

    .consignment-box {
      background-color: var(--navy-header);
      color: #fff;
      padding: 8px 18px;
      border-radius: 6px;
      text-align: center;
      min-width: 190px;
    }

    .consignment-title {
      font-size: 8.5px;
      font-weight: 700;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }

    .consignment-number {
      font-size: 13px;
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .divider-line {
      height: 1.5px;
      background-color: var(--border-color);
      margin-bottom: 12px;
    }

    /* Common Section Styles */
    .section-header {
      background-color: var(--navy-header);
      color: #fff;
      font-size: 11px;
      font-weight: 800;
      text-align: center;
      padding: 4px 6px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .sub-header {
      background-color: var(--navy-header);
      color: #fff;
      font-size: 10px;
      font-weight: 700;
      text-align: center;
      padding: 3px 6px;
      letter-spacing: 0.3px;
    }

    .field-label {
      font-size: 9px;
      font-weight: 700;
      color: #333;
      text-transform: uppercase;
      margin-bottom: 2px;
      display: block;
    }

    .line-input {
      width: 100%;
      border: none;
      border-bottom: 1px solid #777;
      outline: none;
      font-size: 11px;
      font-family: inherit;
      padding: 2px 0 3px 0;
      background: transparent;
    }

    .line-input:focus {
      border-bottom: 1.5px solid var(--btn-blue);
    }

    .select-input {
      width: 100%;
      border: none;
      border-bottom: 1px solid #777;
      outline: none;
      font-size: 11px;
      font-family: inherit;
      padding: 3px 0;
      background: transparent;
      cursor: pointer;
      font-weight: 500;
    }

    /* Top Grid: Consignor & Consignee */
    .consignor-consignee-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      border: 1px solid var(--border-color);
      margin-bottom: -1px;
    }

    .grid-col {
      display: flex;
      flex-direction: column;
    }

    .grid-col:first-child {
      border-right: 1px solid var(--border-color);
    }

    .col-content {
      padding: 8px 12px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      flex: 1;
    }

    .input-row {
      display: flex;
      gap: 12px;
    }

    .flex-1 {
      flex: 1;
    }

    /* Consignor Bottom Clause & Signatures */
    .terms-disclaimer {
      font-size: 7.5px;
      line-height: 1.25;
      color: #555;
      text-transform: uppercase;
      font-weight: 600;
      border-top: 1px solid #ccc;
      padding-top: 6px;
      margin-top: auto;
    }

    .signature-row {
      display: grid;
      grid-template-columns: 1.2fr 1.2fr 1fr;
      gap: 8px;
      align-items: flex-end;
      padding-top: 4px;
    }

    .sig-field {
      display: flex;
      flex-direction: column;
    }

    .sig-label {
      font-size: 8px;
      font-weight: 700;
      color: #444;
      text-transform: uppercase;
      margin-top: 3px;
    }

    .date-box-input {
      border: 1px solid #999;
      padding: 4px 6px;
      font-size: 10px;
      width: 100%;
      text-align: center;
      font-weight: 500;
    }

    /* Proof of Delivery (POD) Box */
    .pod-container {
      border-top: 1px solid var(--border-color);
      margin-top: auto;
    }

    .pod-instruction {
      font-size: 8.5px;
      text-align: center;
      padding: 4px 6px;
      color: #333;
      border-bottom: 1px solid var(--border-color);
      background-color: #fafafa;
    }

    .pod-grid {
      display: grid;
      grid-template-columns: 1.2fr 1.2fr 0.9fr 0.9fr;
    }

    .pod-cell {
      padding: 4px 6px;
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 48px;
    }

    .pod-cell:last-child {
      border-right: none;
    }

    .pod-cell .line-input {
      margin-bottom: 2px;
    }

    /* Bottom 3-Column Section */
    .bottom-grid {
      display: grid;
      grid-template-columns: 1.4fr 1fr 0.8fr;
      border: 1px solid var(--border-color);
      margin-bottom: 14px;
    }

    .bottom-col {
      display: flex;
      flex-direction: column;
      border-right: 1px solid var(--border-color);
    }

    .bottom-col:last-child {
      border-right: none;
    }

    .bottom-content {
      padding: 8px 10px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      flex: 1;
    }

    /* Radio & Checkbox Styles */
    .radio-group, .checkbox-group {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .option-label {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 10px;
      font-weight: 600;
      cursor: pointer;
    }

    .option-label input[type="radio"],
    .option-label input[type="checkbox"] {
      cursor: pointer;
      accent-color: var(--navy-header);
    }

    .custom-textarea {
      width: 100%;
      border: 1px solid #777;
      padding: 6px;
      font-size: 10px;
      font-family: inherit;
      resize: vertical;
      min-height: 52px;
      outline: none;
    }

    .custom-textarea:focus {
      border-color: var(--btn-blue);
    }

    /* Service Checkbox Grid */
    .service-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px 12px;
      padding: 4px 2px;
    }

    .courier-received-row {
      display: grid;
      grid-template-columns: 1.5fr 0.8fr 1.1fr;
      gap: 8px;
      align-items: flex-end;
      margin-top: 4px;
    }

    /* Declared Value & Weight */
    .qty-weight-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 8px;
      text-align: center;
      padding-top: 2px;
    }

    .qty-weight-grid .field-label {
      text-align: center;
    }

    .other-charges-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px 16px;
      padding: 2px;
    }

    /* Dimension Fields */
    .dim-fields {
      display: flex;
      flex-direction: column;
      gap: 16px;
      padding: 6px 2px;
    }

    /* Footer */
    .form-footer {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
    }

    .tax-disclaimer {
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      text-align: center;
      color: #111;
    }

    .submit-container {
      width: 100%;
      display: flex;
      justify-content: flex-end;
    }

    .submit-btn {
      background-color: var(--btn-blue);
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      padding: 9px 24px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      letter-spacing: 0.5px;
      transition: background-color 0.2s ease, transform 0.1s ease;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    }

    .submit-btn:hover {
      background-color: var(--btn-blue-hover);
    }

    .submit-btn:active {
      transform: translateY(1px);
    }

    /* Print styling */
    @media print {
      
      .form-container {
        box-shadow: none;
        padding: 0;
        max-width: 100%;
      }
      .submit-container {
        display: none;
      }
    }

    @media (max-width: 768px) {
      .consignor-consignee-grid,
      .bottom-grid {
        grid-template-columns: 1fr;
      }
      .grid-col:first-child,
      .bottom-col {
        border-right: none;
        border-bottom: 1px solid var(--border-color);
      }
      .submit-container {
        justify-content: center;
      }
    }
  
.form-container { font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; font-size: 11px; }</style>

<!-- ============ BOOKING SHIPMENT MODAL ============ -->
<div id="bookingModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeBookingModal()"></div>

    <!-- Modal Panel -->
    <div class="relative w-full max-w-[1000px] max-h-[92vh] overflow-y-auto bg-white rounded-2xl shadow-2xl border border-slate-200">
        <!-- Modal Header -->
        <div class="sticky top-0 z-10 flex items-center justify-between px-5 py-3 bg-white border-b border-slate-200 rounded-t-2xl">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-truck-fast text-brand-blue"></i>
                <h3 class="text-sm font-bold text-slate-800">Book a Shipment</h3>
            </div>
            <button type="button" onclick="closeAgentBookingModal()">
                <i class="fa-solid fa-xmark"></i>
                </button>
        </div>

        <!-- Scrollable Form Area -->
        <div class="p-4 sm:p-6">
    <!-- Top Header -->
    <header class="top-header">
      <div class="company-info">
        <div class="logo-container">
          <svg class="logo-icon" viewBox="0 0 48 36" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 18C4 10.268 10.268 4 18 4H30C37.732 4 44 10.268 44 18C44 25.732 37.732 32 30 32H18C10.268 32 4 25.732 4 18Z" stroke="#1aa0d8" stroke-width="3.5" stroke-linecap="round"/>
            <path d="M2 18H46" stroke="#1aa0d8" stroke-width="3" stroke-linecap="round"/>
            <path d="M14 10L34 26" stroke="#0e1e38" stroke-width="3" stroke-linecap="round"/>
          </svg>
          <div>
            <div class="logo-text">PRIORITY HANDLING LOGISTICS, INC.</div>
            <div class="logo-tagline">For your Courier and Freight Forwarding Needs</div>
          </div>
        </div>
        <div class="company-details">
          <p>1618-B Copernico St., Brgy. San Isidro, Makati City, Philippines</p>
          <p>VAT Reg. TIN: 239-105-443-00000 | website: www.priority-ph.com | email: cs@priority-ph.com</p>
          <p>Tel. No.: (632) 8843-7484 &bull; 8843-7639 &bull; 8844-2851 | Mobile no.: 0999-228-4667 / 0999-228-4668</p>
        </div>
      </div>

      <div class="consignment-box">
        <div class="consignment-title">CONSIGNMENT NOTE NUMBER</div>
        <div class="consignment-number">AUTO-GENERATE</div>
      </div>
    </header>

    <div class="divider-line"></div>

    <form id="bookingForm" onsubmit="event.preventDefault(); submitBookingForm();">
      <input type="hidden" id="bookingCustomerId" value="<?= htmlspecialchars($customer_id) ?>">
      <!-- CONSIGNOR & CONSIGNEE -->
      <div class="consignor-consignee-grid">
        <!-- CONSIGNOR COLUMN -->
        <div class="grid-col">
          <div class="section-header">CONSIGNOR</div>
          <div class="col-content">
            <div>
              <label class="field-label" for="senderName">SENDER / COMPANY NAME</label>
              <select id="senderName" name="senderName" class="select-input">
                <option value="" selected>Select Company / Sender</option>
                <option value="ABC Corp">ABC Corporation</option>
                <option value="XYZ Trading">XYZ Trading & Logistics</option>
                <option value="Global Exports">Global Exports Inc.</option>
              </select>
            </div>

            <div>
              <label class="field-label" for="senderAddress">ADDRESS</label>
              <input type="text" id="senderAddress" name="senderAddress" class="line-input">
            </div>

            <div>
              <label class="field-label" for="senderCity">CITY</label>
              <input type="text" id="senderCity" name="senderCity" class="line-input">
            </div>

            <div class="input-row">
              <div class="flex-1">
                <label class="field-label" for="senderCountry">COUNTRY</label>
                <input type="text" id="senderCountry" name="senderCountry" class="line-input" value="Philippines">
              </div>
              <div class="flex-1">
                <label class="field-label" for="senderZip">ZIP CODE</label>
                <input type="text" id="senderZip" name="senderZip" class="line-input">
              </div>
            </div>

            <div>
              <label class="field-label" for="senderTel">TELEPHONE</label>
              <input type="text" id="senderTel" name="senderTel" class="line-input">
            </div>

            <div class="terms-disclaimer">
              I/WE AGREE THAT PRIORITY HANDLING LOGISTICS, INC. STANDARD TERMS AND CONDITIONS OF CARRIAGE (SEE REVERSE) APPLY STRICTLY AND THAT ANY DUTIES/TAXES DUE ON THE SHIPMENT AT DESTINATION SHALL BE FOR THE ACCOUNT OF THE CONSIGNEE.
            </div>

            <div class="signature-row">
              <div class="sig-field">
                <input type="text" class="line-input">
                <span class="sig-label">SHIPPER'S SIGNATURE</span>
              </div>
              <div class="sig-field">
                <input type="text" class="line-input">
                <span class="sig-label">PRINT NAME</span>
              </div>
              <div class="sig-field">
                <span class="sig-label" style="margin-bottom: 2px; margin-top: 0;">DATE</span>
                <input type="text" class="date-box-input" value="08/12/2026">
              </div>
            </div>
          </div>
        </div>

        <!-- CONSIGNEE COLUMN -->
        <div class="grid-col">
          <div class="section-header">CONSIGNEE</div>
          <div class="col-content">
            <div>
              <label class="field-label" for="consigneeAttention">ATTENTION</label>
              <input type="text" id="consigneeAttention" name="consigneeAttention" class="line-input">
            </div>

            <div>
              <label class="field-label" for="consigneeCompany">COMPANY NAME</label>
              <input type="text" id="consigneeCompany" name="consigneeCompany" class="line-input">
            </div>

            <div>
              <label class="field-label" for="consigneeAddress">ADDRESS</label>
              <input type="text" id="consigneeAddress" name="consigneeAddress" class="line-input">
            </div>

            <div class="input-row">
              <div class="flex-1">
                <label class="field-label" for="consigneeCity">CITY</label>
                <input type="text" id="consigneeCity" name="consigneeCity" class="line-input">
              </div>
              <div class="flex-1">
                <label class="field-label" for="consigneeState">STATE</label>
                <input type="text" id="consigneeState" name="consigneeState" class="line-input">
              </div>
            </div>

            <div class="input-row">
              <div class="flex-1">
                <label class="field-label" for="consigneeCountry">COUNTRY</label>
                <input type="text" id="consigneeCountry" name="consigneeCountry" class="line-input">
              </div>
              <div class="flex-1">
                <label class="field-label" for="consigneeZip">ZIP CODE</label>
                <input type="text" id="consigneeZip" name="consigneeZip" class="line-input">
              </div>
            </div>

            <div>
              <label class="field-label" for="consigneeTel">TELEPHONE</label>
              <input type="text" id="consigneeTel" name="consigneeTel" class="line-input">
            </div>
          </div>

          <!-- PROOF OF DELIVERY (POD) -->
          <div class="pod-container">
            <div class="sub-header">PROOF OF DELIVERY (POD)</div>
            <div class="pod-instruction">Received the herein described article in good order and condition.</div>
            <div class="pod-grid">
              <div class="pod-cell">
                <span class="field-label">SIGNATURE</span>
                <input type="text" class="line-input">
              </div>
              <div class="pod-cell">
                <span class="field-label">PRINT FULL NAME</span>
                <input type="text" class="line-input">
              </div>
              <div class="pod-cell">
                <span class="field-label">DATE</span>
                <input type="text" class="line-input" value="08/12/2026" style="text-align: center;">
              </div>
              <div class="pod-cell">
                <span class="field-label">TIME</span>
                <input type="text" class="line-input" value="1:46 AM" style="text-align: center;">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- BOTTOM 3-COLUMN GRID -->
      <div class="bottom-grid">
        <!-- SHIPMENT & SERVICE -->
        <div class="bottom-col">
          <div class="sub-header">SHIPMENT (INVOICE / PACKING LIST REQUIRED IF PARCEL)</div>
          <div class="bottom-content">
            <div class="radio-group">
              <label class="option-label"><input type="radio" name="shipment_type" value="DOX"> DOX</label>
              <label class="option-label"><input type="radio" name="shipment_type" value="PARCEL"> PARCEL</label>
              <label class="option-label"><input type="radio" name="pkg_size" value="Small"> Small</label>
              <label class="option-label"><input type="radio" name="pkg_size" value="Medium"> Medium</label>
              <label class="option-label"><input type="radio" name="pkg_size" value="Large"> Large</label>
            </div>

            <div>
              <label class="field-label" for="goodsDesc">FULL DESCRIPTION OF GOODS</label>
              <textarea id="goodsDesc" name="goodsDesc" class="custom-textarea" placeholder="Specify items, contents, or commodity..."></textarea>
            </div>
          </div>

          <div class="sub-header">TYPE OF SERVICE (For Metro Manila Delivery Only)</div>
          <div class="bottom-content">
            <div class="service-grid">
              <label class="option-label"><input type="checkbox" name="service" value="AIR"> AIR</label>
              <label class="option-label"><input type="checkbox" name="service" value="Same Day"> Same Day</label>
              <label class="option-label"><input type="checkbox" name="service" value="Land"> Land</label>
              <label class="option-label"><input type="checkbox" name="service" value="Next Day"> Next Day</label>
              <label class="option-label"><input type="checkbox" name="service" value="Sea"> Sea</label>
              <label class="option-label"><input type="checkbox" name="service" value="3-5 Days"> 3 - 5 Days</label>
            </div>

            <div class="courier-received-row">
              <div>
                <label class="field-label">RECEIVED BY COURIER</label>
                <input type="text" class="line-input">
              </div>
              <div>
                <label class="field-label">TIME</label>
                <input type="text" class="line-input" id="courier_time" name="courier_time" placeholder="--:-- --">
              </div>
              <div>
                <label class="field-label">DATE</label>
                <input type="date" class="line-input" id="courier_date" name="courier_date" style="font-size: 10px;">
              </div>
            </div>
          </div>
        </div>

        <!-- DECLARED VALUE & WEIGHT -->
        <div class="bottom-col">
          <div class="sub-header">DECLARED VALUE</div>
          <div class="bottom-content">
            <div class="checkbox-group" style="justify-content: space-between;">
              <div style="display: flex; gap: 10px;">
                <label class="option-label"><input type="checkbox" name="currency_usd"> USD</label>
                <label class="option-label"><input type="checkbox" name="currency_php" checked> PHP</label>
              </div>
              <label class="option-label"><input type="checkbox" name="insurance"> Insurance</label>
            </div>

            <div>
              <label class="field-label">AMOUNT (SUBJECT TO VALUATION CHARGE)</label><input type="text" class="line-input" id="declared_amount" name="declared_amount">
            </div>

            <div>
              <label class="field-label">NOTES</label>
              <textarea class="custom-textarea" style="min-height: 42px;"></textarea>
            </div>
          </div>

          <div class="sub-header">QUANTITY & CHARGEABLE WEIGHT</div>
          <div class="bottom-content">
            <div class="qty-weight-grid">
              <div>
                <label class="field-label">QTY (PCS)</label>
                <input type="text" id="qty_pcs" name="qty_pcs" class="line-input" style="text-align: center;">
              </div>
              <div>
                <label class="field-label">KILOS</label>
                <input type="text" id="wt_kilos" name="wt_kilos" class="line-input" style="text-align: center;">
              </div>
              <div>
                <label class="field-label">GRAMS</label>
                <input type="text" id="wt_grams" name="wt_grams" class="line-input" style="text-align: center;">
              </div>
            </div>
          </div>

          <div class="sub-header">OTHER CHARGES</div>
          <div class="bottom-content">
            <div class="other-charges-grid">
              <label class="option-label"><input type="checkbox" name="charge" value="DDP"> DDP</label>
              <label class="option-label"><input type="checkbox" name="charge" value="Non DDP"> Non DDP</label>
              <label class="option-label"><input type="checkbox" name="charge" value="ADD"> ADD</label>
              <label class="option-label"><input type="checkbox" name="charge" value="ED"> ED</label>
              <label class="option-label"><input type="checkbox" name="charge" value="CI"> CI</label>
            </div>
          </div>
        </div>

        <!-- DIMENSION -->
        <div class="bottom-col">
          <div class="sub-header">DIMENSION (IN CMS)</div>
          <div class="bottom-content dim-fields">
            <div>
              <label class="field-label">LENGTH</label>
              <input type="text" id="dim_length" name="dim_length" class="line-input">
            </div>
            <div>
              <label class="field-label">WIDTH</label>
              <input type="text" id="dim_width" name="dim_width" class="line-input">
            </div>
            <div>
              <label class="field-label">HEIGHT</label>
              <input type="text" id="dim_height" name="dim_height" class="line-input">
            </div>
          </div>
        </div>
      </div>

      <!-- FOOTER -->
      <footer class="form-footer">
        <div class="tax-disclaimer">"THIS DOCUMENT IS NOT VALID FOR CLAIMING INPUT TAXES"</div>
        <div class="submit-container">
          <button type="submit" class="submit-btn" id="bookingSubmitBtn">SUBMIT BOOKING</button>
        </div>
      </footer>
    </form>
          </div>
    </div>
</div>

