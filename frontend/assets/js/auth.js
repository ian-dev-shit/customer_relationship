/**
 * Auth legal modals controller.
 * Powers src/components/legal_modals.php (Privacy Policy + Terms of Service).
 *
 * - Defines the legacy globals (openPrivacyModal/closePrivacyModal/
 *   openTermsModal/closeTermsModal) already referenced by the footer links.
 * - Also supports declarative [data-legal-open] / [data-legal-close] triggers.
 * - Handles Escape, backdrop click, inert toggling, scroll lock, focus restore.
 */
(function () {
    'use strict';

    var root = document.querySelector('.legal-root');
    var lastFocused = null;

    function openModal(id) {
        var modal = document.getElementById(id);
        if (!modal || !root) return;
        lastFocused = document.activeElement;
        root.removeAttribute('inert');
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        var closeBtn = modal.querySelector('.legal-close');
        if (closeBtn) closeBtn.focus();
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (!modal || modal.hidden) return;
        modal.hidden = true;
        // Re-inert the wrapper only when no other legal modal is still open.
        if (!root.querySelector('.legal-overlay:not([hidden])')) {
            root.setAttribute('inert', '');
            document.body.style.overflow = '';
            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
            lastFocused = null;
        }
    }

    // Legacy handlers used by src/components/footer.php links.
    window.openPrivacyModal = function () { openModal('legal-privacy'); };
    window.closePrivacyModal = function () { closeModal('legal-privacy'); };
    window.openTermsModal = function () { openModal('legal-terms'); };
    window.closeTermsModal = function () { closeModal('legal-terms'); };

    document.addEventListener('click', function (e) {
        var target = e.target;
        if (!target || typeof target.closest !== 'function') return;

        var opener = target.closest('[data-legal-open]');
        if (opener) {
            e.preventDefault();
            openModal(opener.getAttribute('data-legal-open'));
            return;
        }

        var closer = target.closest('[data-legal-close]');
        if (closer) {
            e.preventDefault();
            closeModal(closer.getAttribute('data-legal-close'));
            return;
        }

        // Backdrop click: only when the overlay itself (not its content) is hit.
        var overlay = target.closest('.legal-overlay');
        if (overlay && target === overlay) {
            closeModal(overlay.id);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        var open = document.querySelector('.legal-overlay:not([hidden])');
        if (open) closeModal(open.id);
    });
})();