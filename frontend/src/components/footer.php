<footer class="bg-slate-950 text-slate-500 py-8 px-6 text-xs border-t border-white/10">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
        <p>&copy; 2026 Priority Handling Logistics Inc. All rights reserved.</p>
        <div class="flex gap-6">
            <a href="javascript:void(0)" onclick="openPrivacyModal()" class="hover:text-white transition-colors">Privacy Policy (RA 10173)</a>
            <a href="javascript:void(0)" onclick="openTermsModal()" class="hover:text-white transition-colors">Terms of Service (PH Law)</a>
        </div>
    </div>
</footer>

<!-- MODALS -->
<div id="privacyModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
    <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-2xl w-full p-6 text-slate-200 text-xs space-y-3">
        <h3 class="text-base font-bold text-white">Privacy Policy (RA 10173 Compliance)</h3>
        <p>Priority Handling Logistics Inc. processes agent console accounts strictly under the Philippine Data Privacy Act of 2012.</p>
        <button onclick="closePrivacyModal()" class="mt-4 bg-brand-blue text-white px-4 py-2 rounded-lg font-semibold">Close</button>
    </div>
</div>

<div id="termsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
    <div class="bg-slate-900 border border-white/10 rounded-2xl max-w-2xl w-full p-6 text-slate-200 text-xs space-y-3">
        <h3 class="text-base font-bold text-white">Terms of Service (Philippine Law)</h3>
        <p>Governed by Philippine Commercial Law, Customs Modernization and Tariff Act (RA 10863), and Civil Code on Carriers.</p>
        <button onclick="closeTermsModal()" class="mt-4 bg-brand-blue text-white px-4 py-2 rounded-lg font-semibold">Close</button>
    </div>
</div>