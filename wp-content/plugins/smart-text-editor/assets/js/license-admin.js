/**
 * Smart Text Editor — License admin page JS.
 * Handles the billing cycle toggle and destructive-action confirmation dialogs.
 * Dynamic values are passed via wp_localize_script as window.steAdminLicense.
 */
(function () {
    'use strict';

    var cfg = window.steAdminLicense || {};

    /* ── Billing cycle toggle ── */
    var toggle = document.getElementById('ste-billing-toggle');
    if (toggle) {
        function update(yearly) {
            document.querySelectorAll('.ste-price-monthly, .ste-period-monthly, .ste-purchase-price-monthly').forEach(function (el) {
                el.style.display = yearly ? 'none' : '';
            });
            document.querySelectorAll('.ste-price-yearly, .ste-period-yearly, .ste-purchase-price-yearly').forEach(function (el) {
                el.style.display = yearly ? '' : 'none';
            });
            document.querySelectorAll('.ste-plan-yearly-equiv').forEach(function (el) {
                el.style.display = yearly ? 'block' : 'none';
            });
            document.querySelectorAll('.ste-purchase-link').forEach(function (el) {
                var plan = el.getAttribute('data-plan');
                el.setAttribute('href', cfg.checkoutUrl + '?plan=' + plan + '&billing=' + (yearly ? 'yearly' : 'monthly'));
            });
            document.querySelectorAll('.ste-plan-btn-upgrade.ste-purchase-link').forEach(function (el) {
                var span = el.querySelector('.ste-btn-price-text');
                if (span) {
                    span.textContent = yearly ? el.getAttribute('data-price-yearly') : el.getAttribute('data-price-monthly');
                }
            });
            document.querySelectorAll('.ste-billing-label').forEach(function (el) {
                el.classList.toggle('active', (yearly && el.dataset.cycle === 'yearly') || (!yearly && el.dataset.cycle === 'monthly'));
            });
        }

        toggle.addEventListener('change', function () { update(this.checked); });
        update(false);
    }

    /* ── Confirmation dialogs for destructive actions ── */
    ['ste-btn-downgrade', 'ste-btn-deactivate'].forEach(function (id) {
        var btn = document.getElementById(id);
        if (!btn) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var msg = id === 'ste-btn-downgrade'
                ? cfg.msgDowngrade
                : cfg.msgDeactivate;

            var modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99999;display:flex;align-items:center;justify-content:center;';

            var inner = document.createElement('div');
            inner.style.cssText = 'background:#fff;border-radius:12px;padding:28px 32px;max-width:400px;width:90%;box-shadow:0 8px 40px rgba(0,0,0,.2);font-family:inherit;';

            var p = document.createElement('p');
            p.style.cssText = 'font-size:15px;color:#333;margin:0 0 20px;line-height:1.6;';
            p.textContent = msg;

            var actions = document.createElement('div');
            actions.style.cssText = 'display:flex;gap:10px;justify-content:flex-end;';

            var cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.textContent = cfg.labelCancel;
            cancelBtn.style.cssText = 'padding:8px 18px;border:1px solid #ddd;border-radius:6px;background:#fff;cursor:pointer;font-size:13px;';

            var confirmBtn = document.createElement('button');
            confirmBtn.type = 'button';
            confirmBtn.textContent = cfg.labelConfirm;
            confirmBtn.style.cssText = 'padding:8px 18px;border:none;border-radius:6px;background:#dc2626;color:#fff;cursor:pointer;font-size:13px;font-weight:600;';

            cancelBtn.addEventListener('click', function () { modal.remove(); });
            confirmBtn.addEventListener('click', function () { modal.remove(); btn.closest('form').submit(); });

            actions.appendChild(cancelBtn);
            actions.appendChild(confirmBtn);
            inner.appendChild(p);
            inner.appendChild(actions);
            modal.appendChild(inner);
            document.body.appendChild(modal);
        });
    });
})();
