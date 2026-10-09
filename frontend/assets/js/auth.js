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

    var lastFocused = null;

    // The .legal-root wrapper is looked up lazily (NOT cached at load time).
    // Static auth pages (login.html / otp_verification.html / test.html) inject
    // legal_modals.html via an async fetch() that resolves AFTER this script
    // runs, so a load-time querySelector would capture null and openModal()
    // would bail on its first guard. Dashboard pages inject the wrapper via a
    // server-side PHP include, so it is present immediately. Re-querying on
    // each call satisfies both load paths.
    function getRoot() {
        return document.querySelector('.legal-root');
    }

    function openModal(id) {
        var modal = document.getElementById(id);
        var root = getRoot();
        if (!modal || !root) return;
        lastFocused = document.activeElement;
        root.removeAttribute('inert');
        modal.hidden = false;
        // Inspiration theme fades via .is-open; keep hidden-attr logic as source of truth.
        requestAnimationFrame(function () { modal.classList.add('is-open'); });
        document.body.style.overflow = 'hidden';
        var closeBtn = modal.querySelector('.legal-close');
        if (closeBtn) closeBtn.focus();
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        if (!modal || modal.hidden) return;
        modal.classList.remove('is-open');
        modal.hidden = true;
        // Re-inert the wrapper only when no other legal modal is still open.
        var root = getRoot();
        if (root && !root.querySelector('.legal-overlay:not([hidden])')) {
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