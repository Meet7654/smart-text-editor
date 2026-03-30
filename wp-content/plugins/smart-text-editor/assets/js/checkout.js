/**
 * Smart Text Editor — Checkout (Cashfree Payment Gateway)
 *
 * Flow:
 * 1. User fills name, email, phone → clicks Pay
 * 2. AJAX to server → creates Cashfree order → returns payment_session_id
 * 3. Cashfree JS SDK opens payment form (UPI, cards, netbanking, wallets)
 * 4. On success → user redirected to return_url with order_id
 * 5. Server verifies payment, generates license key, shows success
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var form      = document.getElementById('ste-checkout-form');
        var submitBtn = document.getElementById('ste-checkout-submit');
        var errorBox  = document.getElementById('ste-checkout-error');

        if (!form) {
            // We might be on the success/failure page
            initCopyKey();
            return;
        }

        /* ── Initialize Cashfree SDK ── */
        var cashfree = null;
        if (typeof Cashfree !== 'undefined') {
            cashfree = Cashfree({
                mode: steCheckout.mode === 'production' ? 'production' : 'sandbox'
            });
        }

        /* ── Phone formatting: digits only ── */
        var phoneInput = document.getElementById('ste-phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').substring(0, 15);
            });
        }

        /* ── Helpers ── */
        function showError(msg) {
            errorBox.textContent = msg;
            errorBox.style.display = 'block';
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function hideError() {
            errorBox.style.display = 'none';
        }

        function setLoading(on) {
            submitBtn.disabled = on;
            submitBtn.querySelector('.ste-btn-text').style.display = on ? 'none' : '';
            submitBtn.querySelector('.ste-btn-loading').style.display = on ? 'flex' : 'none';
        }

        /* ── Validation ── */
        function validate() {
            var name  = form.querySelector('[name="name"]').value.trim();
            var email = form.querySelector('[name="email"]').value.trim();
            var phone = form.querySelector('[name="phone"]').value.trim();

            form.querySelectorAll('.ste-input-error').forEach(function (el) {
                el.classList.remove('ste-input-error');
            });

            if (name.length < 2) {
                form.querySelector('[name="name"]').classList.add('ste-input-error');
                return 'Please enter your full name.';
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                form.querySelector('[name="email"]').classList.add('ste-input-error');
                return 'Please enter a valid email address.';
            }
            if (phone.replace(/\D/g, '').length < 10) {
                form.querySelector('[name="phone"]').classList.add('ste-input-error');
                return 'Please enter a valid phone number (10+ digits).';
            }
            return null;
        }

        /* ── Form submit ── */
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            hideError();

            var err = validate();
            if (err) { showError(err); return; }

            if (!cashfree) {
                showError('Payment SDK failed to load. Please refresh the page and try again.');
                return;
            }

            setLoading(true);

            // Step 1: Create Cashfree order via AJAX
            var formData = new FormData(form);
            formData.append('action', 'ste_create_cf_order');
            formData.append('nonce', steCheckout.nonce);

            fetch(steCheckout.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function (res) { return res.json(); })
            .then(function (res) {
                if (!res.success) {
                    setLoading(false);
                    showError(res.data && res.data.message ? res.data.message : 'Failed to create order.');
                    return;
                }

                // Step 2: Open Cashfree payment form
                var paymentSessionId = res.data.payment_session_id;

                cashfree.checkout({
                    paymentSessionId: paymentSessionId,
                    redirectTarget: '_self'
                }).then(function (result) {
                    // This fires if payment form was closed without redirect
                    setLoading(false);
                    if (result.error) {
                        showError(result.error.message || 'Payment was cancelled.');
                    }
                }).catch(function (err) {
                    setLoading(false);
                    showError('Payment failed. Please try again.');
                });
            })
            .catch(function () {
                setLoading(false);
                showError('An unexpected error occurred. Please try again.');
            });
        });

        /* ── Copy key button (for success page) ── */
        initCopyKey();
    });

    function initCopyKey() {
        var copyBtn = document.getElementById('ste-copy-key');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                var key = document.getElementById('ste-success-key').textContent;
                navigator.clipboard.writeText(key).then(function () {
                    copyBtn.classList.add('copied');
                    setTimeout(function () { copyBtn.classList.remove('copied'); }, 2000);
                });
            });
        }
    }
})();
