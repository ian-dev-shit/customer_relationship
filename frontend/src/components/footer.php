<footer class="bg-slate-950 text-slate-500 py-8 px-6 text-xs border-t border-white/10">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
        <p>&copy; 2026 Priority Handling Logistics Inc. All rights reserved.</p>
        <div class="flex gap-6">
            <a href="javascript:void(0)" onclick="openPrivacyModal()" class="hover:text-white transition-colors">Privacy Policy (RA 10173)</a>
            <a href="javascript:void(0)" onclick="openTermsModal()" class="hover:text-white transition-colors">Terms of Service (PH Law)</a>
        </div>
    </div>
</footer>

<!-- LEGAL MODALS: Privacy Policy (RA 10173) + Terms of Service -->
<?php include_once __DIR__ . '/legal_modals.php'; ?>

<!-- Legal modal controller: open/close, backdrop + Escape key -->
<script src="../../assets/js/auth.js"></script>