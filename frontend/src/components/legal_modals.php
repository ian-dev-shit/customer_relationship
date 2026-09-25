<?php
/**
 * Legal modals - Privacy Policy (RA 10173) and Terms of Service.
 *
 * Shared by every public auth screen. Previously the full markup was
 * duplicated inline in login.php while src/components/footer.php shipped a
 * trimmed-down copy under the SAME element ids, so any page including both
 * emitted duplicate ids and getElementById() resolved the wrong modal.
 * This component is now the single source of truth.
 *
 * Requires assets/css/auth.css + assets/js/auth.js.
 */
?>
<div class="legal-root" inert>

    <!-- PRIVACY POLICY -->
    <div id="legal-privacy" class="legal-overlay" hidden role="dialog" aria-modal="true" aria-labelledby="legal-privacy-title">
        <div class="legal-sheet">
            <header class="legal-head">
                <span class="legal-head-ico" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
                <div>
                    <h2 id="legal-privacy-title" class="legal-title">Privacy Policy</h2>
                    <p class="legal-caption">Compliant with Republic Act No. 10173 (Philippine Data Privacy Act of 2012)</p>
                </div>
                <button type="button" class="legal-close" data-legal-close="legal-privacy" aria-label="Close privacy policy"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </header>
            <div class="legal-body">
                    <p class="legal-p">Priority Handling Logistics Inc. (&quot;Carrier,&quot; &quot;we,&quot; &quot;us,&quot; or &quot;our&quot;) is committed to protecting your personal data in compliance with Republic Act No. 10173, also known as the Data Privacy Act of 2012 (DPA). This Privacy Policy explains how we collect, use, disclose, and safeguard your personal information when you use our website, online forms, and logistics or freight forwarding services.</p>
                    <p data-legal="head">1. Information We Collect</p>
                    <p class="legal-p">We collect personal data necessary to provide freight forwarding, courier, and customs services. This includes:</p>
                    <p class="legal-sub"><strong>Account &amp; Contact Data:</strong> Name, business address, email address, phone number, company name, and job title.</p>
                    <p class="legal-sub"><strong>Shipment &amp; Consignment Data:</strong> Consignor and Consignee (recipient) details, including physical addresses, contact phone numbers, customs declarations, descriptions of goods, and order histories.</p>
                    <p class="legal-sub"><strong>Payment &amp; Financial Data:</strong> Billing addresses, transaction logs, and payment details required to settle freight charges.</p>
                    <p class="legal-sub"><strong>Automatically Collected Data:</strong> IP addresses, browser types, and usage logs collected through cookies when you visit our website.</p>
                    <p class="legal-p"><strong>Note on Consignee Data:</strong> When you provide personal information regarding third parties (such as recipients or alternate contact persons), you warrant that you have obtained their explicit consent to share their information with us for delivery purposes.</p>
                    <p data-legal="head">2. How We Use Your Personal Data</p>
                    <p class="legal-p">We process personal data for the following lawful purposes:</p>
                    <p class="legal-sub"><strong>Service Execution:</strong> Arranging, tracking, routing, and delivering shipments via our own network or through successive carriers, agents, and sub-contractors.</p>
                    <p class="legal-sub"><strong>Regulatory Compliance:</strong> Completing customs declarations, tax reporting, and complying with local and international logistics laws (including trade regulations and conventions such as the Warsaw Convention).</p>
                    <p class="legal-sub"><strong>Customer Support:</strong> Addressing inquiries, investigating non-deliveries, and processing damage or loss claims submitted under our Terms and Conditions.</p>
                    <p class="legal-sub"><strong>Communications:</strong> Sending automated shipment status alerts, account updates, and promotional materials (only where express opt-in consent has been provided).</p>
                    <p data-legal="head">3. Data Sharing and Third-Party Disclosures</p>
                    <p class="legal-p">To fulfill our contractual duties, we may share personal data with trusted third parties under strict confidentiality agreements:</p>
                    <p class="legal-sub"><strong>Sub-Contractors and Successive Carriers:</strong> Third-party logistics partners, air/sea freight carriers, driver networks, and local delivery agents necessary to complete transit.</p>
                    <p class="legal-sub"><strong>Customs and Regulatory Authorities:</strong> Government agencies, border control, and customs offices in origin and destination countries to clear consignments.</p>
                    <p class="legal-sub"><strong>Service Providers:</strong> IT support, web hosting, payment processors, and audit providers assisting our business operations.</p>
                    <p class="legal-p">We do not sell, rent, or trade your personal data to third parties for marketing purposes.</p>
                    <p data-legal="head">4. International Data Transfers</p>
                    <p class="legal-p">Because freight logistics frequently involves international transit, your personal data and shipment information may be transferred across international borders to destination countries, overseas customs authorities, or foreign carrier networks in order to complete delivery.</p>
                    <p data-legal="head">5. Data Retention and Security</p>
                    <p class="legal-p">We implement appropriate organizational, physical, and technical security measures to protect your personal information from unauthorized access, alteration, or disclosure. Personal data is retained only for as long as necessary to fulfill the purposes outlined in this policy, settle accounts, resolve disputes, or comply with statutory retention requirements under Philippine law.</p>
                    <p data-legal="head">6. Your Rights Under RA 10173</p>
                    <p class="legal-p">Under the Data Privacy Act of 2012, you have the right to:</p>
                    <p class="legal-sub"><strong>Access</strong> the personal information we hold about you.</p>
                    <p class="legal-sub"><strong>Rectify or correct</strong> inaccurate or outdated data.</p>
                    <p class="legal-sub"><strong>Erasure or Blocking:</strong> Request the suspension, withdrawal, or removal of your personal data from our systems (subject to legal or contractual limitations, such as active freight contracts or customs retention mandates).</p>
                    <p class="legal-sub"><strong>Withdraw Consent:</strong> Opt out of promotional communications at any time.</p>
                    <p data-legal="head">7. Contact Our Data Protection Officer (DPO)</p>
                    <p class="legal-p">For inquiries, requests to exercise your data privacy rights, or feedback regarding our privacy practices, please contact our Data Protection Officer:</p>
                    <p class="legal-sub"><strong>Company Name:</strong> Priority Handling Logistics Inc.</p>
                    <p class="legal-sub"><strong>Email:</strong> cs@priority-ph.com</p>
                    <p class="legal-sub"><strong>Subject Line:</strong> Attn: Data Protection Officer / Privacy Request</p>
            </div>
            <footer class="legal-foot">
                <button type="button" class="auth-btn auth-btn--primary legal-btn" data-legal-close="legal-privacy">I Understand</button>
            </footer>
        </div>
    </div>

    <!-- TERMS OF SERVICE -->
    <div id="legal-terms" class="legal-overlay" hidden role="dialog" aria-modal="true" aria-labelledby="legal-terms-title">
        <div class="legal-sheet">
            <header class="legal-head">
                <span class="legal-head-ico" aria-hidden="true"><i class="fa-solid fa-file-contract"></i></span>
                <div>
                    <h2 id="legal-terms-title" class="legal-title">Terms of Service</h2>
                    <p class="legal-caption">Governed by Philippine law, the Civil Code on Carriers, and RA 10863</p>
                </div>
                <button type="button" class="legal-close" data-legal-close="legal-terms" aria-label="Close terms of service"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
            </header>
            <div class="legal-body">
                    <p data-legal="head">Effective Date: January 1, 2026</p>
                    <p data-legal="lead">TERMS AND CONDITIONS OF CARRIAGE</p>
                    <p class="legal-p"><strong>1. In these conditions.</strong> The &quot;Carrier&quot; means Priority Handling Logistics, Inc. carrying on business in its own name and under any business name and unless the context otherwise requires its officers, servants, agents and sub-contractors THE CARRIES IS NOT A COMMON CARRIER and will accept no liability as such.</p>
                    <p class="legal-p"><strong>2. The Carrier reserves the right to:</strong></p>
                    <p class="legal-sub">a. refuse the carriage or transport of goods to its destination,</p>
                    <p class="legal-sub">b. carry consignor's documents or goods by any route and procedure, and by successive carriers and according to its handling storage and transportation methods, and</p>
                    <p class="legal-sub">c. to inspect any goods consigned to ensure that they are capable of carriage to the countries of destination within the standard operating procedures, customs declarations, and handling methods of the Carrier.</p>
                    <p class="legal-p"><strong>4. The Consignor is responsible</strong> for the packing of the goods including the packing in any container that may be supplied to the Consignor by the Carrier. The Carrier accepts no responsibility for loss or damage or to the goods caused by inadequate of inappropriate packaging. It is the responsibility of the Consignor to address adequately each consignment to enable effective delivery to be made. The Carrier shall not be liable for delay in forwarding or delivery resulting from the Consignor's failure to comply with its obligation in this respect.</p>
                    <p class="legal-p"><strong>5. The Consignor warrants</strong> that it is authorized to accept and is accepting these Conditions on behalf of itself but also as an agent for and on behalf of all other person who are or may hereafter become interested in the goods. The Consignor indemnifies the Carrier against any damages, costs and expenses resulting from any breach of this warranty.</p>
                    <p class="legal-p"><strong>6. The Carrier shall have a lien</strong> on all goods shipped, for freight charges and/or advance for other charges of any kind arising out of transportation hereunder and may refuse to surrender possession of the goods until such charges are paid.</p>
                    <p class="legal-p"><strong>7. The Carrier is liable</strong> for damage sustained in the event of the destruction or loss of, or of damage to any cargo, the occurrence which caused the damage so sustained takes place during carriage and the Carrier is also liable for damage occasioned by delay in the carriage of cargo. The Carrier is not liable if it proves that it and its servants and agents have taken all necessary measures to avoid the damage or that it was impossible for it or them to take such measures. PROVIDED ALWAYS THAT the liability of the Carrier under these conditions shall be limited to the payment by the Carrier by way of damages of a sum of not exceeding US$100.00 or its local currency equivalent and Pesos 100.00 for international and domestic carriage respectively per consignment or in the case where transit insurance is affected the amount payable thereunder in the event of loss or damage to the goods.</p>
                    <p class="legal-p"><strong>8. Any claim brought</strong> by a customer against the Carrier hereunder in respect of duties and liabilities must be notified by the customer to an office of the Carrier with in 48 hours and in writing within 14 days of the day when the goods should have reached their destination. No claim for loss or damage may be entertained if o in is filed beyond the allowable period or until the transportation charges have been paid.</p>
                    <p class="legal-p"><strong>9. The Carrier will not carry</strong> dangerous, hazardous, combustible or explosive materials, gold and silver bullion, coin, dust, cyanides, precipitates or any form of uncoined gold and silver bullion, platinum and other precious metals, precious and semi-precious stones, currency (paper or coin) of any nationality, negotiable securities, stocks, bonds, certificates, stamps, blank or endorsed bank cheques, money orders or travelers cheques, antiques, works of art, livestock, plants, drugs, pharmaceuticals, liquor, firearms, tobacco, foodstuff, IATA restricted articles or perishables and in the event that the Consignor should consign such items. with the Carrier the Consignor indemnities the Carrier for all claims, damages costs and expenses which may arise as a result of such carriage and the Carrier shall have the right to abandon carriage of the same immediately upon Carrier having knowledge that the goods infringe this condition.</p>
                    <p class="legal-p"><strong>10. If the carriage involves</strong> an ultimate destination or stop in a country other than the country of departure, the Warsaw Convention may be applicable and the Convention governs and in most cases limits the liability of carriers in respect of lost of or damage to cargo.</p>
            </div>
            <footer class="legal-foot">
                <button type="button" class="auth-btn auth-btn--primary legal-btn" data-legal-close="legal-terms">Accept Terms</button>
            </footer>
        </div>
    </div>
</div>