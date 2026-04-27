/**
 * Smart Text Editor — Frontend Live Editor
 * Created by Meet Patel
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ── Elements ── */
        var canvas = document.getElementById('ste-live-canvas');
        var toolbar = document.getElementById('ste-live-toolbar');
        var fontSelect = document.getElementById('ste-live-font');
        var sizeSelect = document.getElementById('ste-live-size');
        var headingSelect = document.getElementById('ste-live-heading');
        var fgColor = document.getElementById('ste-live-fg');
        var bgColor = document.getElementById('ste-live-bg');
        var fxPanel = document.getElementById('ste-fx-panel');
        var fxContent = document.getElementById('ste-fx-panel-content');
        var wordCount = document.getElementById('ste-live-wordcount');
        var charCount = document.getElementById('ste-live-charcount');

        /* ── Pricing billing toggle (homepage) ── */
        var pricingToggle = document.getElementById('ste-pricing-cycle-toggle');
        if (pricingToggle && window.steTheme) {
            pricingToggle.addEventListener('change', function () {
                var yearly = this.checked;
                document.querySelectorAll('.ste-hp-price-monthly, .ste-hp-period-monthly').forEach(function (el) { el.style.display = yearly ? 'none' : ''; });
                document.querySelectorAll('.ste-hp-price-yearly, .ste-hp-period-yearly').forEach(function (el) { el.style.display = yearly ? '' : 'none'; });
                document.querySelectorAll('.ste-pricing-yearly-info').forEach(function (el) { el.style.display = yearly ? 'block' : 'none'; });
                document.querySelectorAll('.ste-hp-checkout-link').forEach(function (el) {
                    var plan = el.getAttribute('data-plan');
                    el.href = window.steTheme.checkoutUrl + '?plan=' + plan + '&billing=' + (yearly ? 'yearly' : 'monthly');
                });
                document.querySelectorAll('.ste-pricing-toggle-label').forEach(function (el) {
                    el.classList.toggle('ste-toggle-active', (yearly && el.dataset.cycle === 'yearly') || (!yearly && el.dataset.cycle === 'monthly'));
                });
            });
        }

        /* ── Legal page TOC scroll-spy ── */
        var tocLinks = document.querySelectorAll('.ste-legal-toc-inner nav a');
        if (tocLinks.length) {
            var sections = [];
            tocLinks.forEach(function (a) {
                var id = a.getAttribute('href').replace('#', '');
                var el = document.getElementById(id);
                if (el) sections.push({ el: el, a: a });
            });
            if (sections.length) {
                var headerH = 96;
                function onTocScroll() {
                    var scrollY = window.scrollY || window.pageYOffset;
                    var active = sections[0];
                    for (var i = 0; i < sections.length; i++) {
                        if (sections[i].el.getBoundingClientRect().top + scrollY - headerH - 20 <= scrollY) {
                            active = sections[i];
                        }
                    }
                    tocLinks.forEach(function (a) { a.classList.remove('ste-toc-active'); });
                    active.a.classList.add('ste-toc-active');
                    var toc = document.querySelector('.ste-legal-toc-inner nav');
                    if (toc) {
                        var aTop = active.a.offsetTop;
                        var tocH = toc.clientHeight;
                        if (aTop < toc.scrollTop || aTop > toc.scrollTop + tocH - 40) {
                            toc.scrollTop = aTop - tocH / 2;
                        }
                    }
                }
                window.addEventListener('scroll', onTocScroll, { passive: true });
                onTocScroll();
            }
        }

        if (!canvas) return;

        /* ── Toolbar commands ── */
        toolbar.addEventListener('click', function (e) {
            var btn = e.target.closest('.ste-live-btn[data-cmd]');
            if (!btn) return;
            e.preventDefault();
            canvas.focus();
            document.execCommand(btn.dataset.cmd, false, null);
            updateCounts();
        });

        /* ── Font family ── */
        fontSelect.addEventListener('change', function () {
            if (!this.value) return;
            canvas.focus();
            wrapSelection('font-family', this.value);
            this.value = '';
        });

        /* ── Font size ── */
        sizeSelect.addEventListener('change', function () {
            if (!this.value) return;
            canvas.focus();
            wrapSelection('font-size', this.value);
            this.value = '';
        });

        /* ── Block type (heading) ── */
        headingSelect.addEventListener('change', function () {
            canvas.focus();
            document.execCommand('formatBlock', false, '<' + this.value + '>');
        });

        /* ── Text color ── */
        fgColor.addEventListener('input', function () {
            canvas.focus();
            document.execCommand('foreColor', false, this.value);
        });

        /* ── Highlight color ── */
        bgColor.addEventListener('input', function () {
            canvas.focus();
            document.execCommand('hiliteColor', false, this.value);
        });

        /* ── Effects buttons ── */
        var activeFx = null;

        document.getElementById('ste-fx-gradient').addEventListener('click', function () {
            toggleFxPanel('gradient');
        });
        document.getElementById('ste-fx-shadow').addEventListener('click', function () {
            toggleFxPanel('shadow');
        });
        document.getElementById('ste-fx-3d').addEventListener('click', function () {
            toggleFxPanel('3d');
        });
        document.getElementById('ste-fx-glow').addEventListener('click', function () {
            toggleFxPanel('glow');
        });

        function toggleFxPanel(type) {
            if (activeFx === type) {
                fxPanel.style.display = 'none';
                activeFx = null;
                return;
            }
            activeFx = type;
            fxPanel.style.display = 'block';
            renderFxPanel(type);
        }

        function renderFxPanel(type) {
            var html = '';
            if (type === 'gradient') {
                html = '<label>Color 1<input type="color" id="fx-c1" value="#ff6b6b"></label>' +
                    '<label>Color 2<input type="color" id="fx-c2" value="#48dbfb"></label>' +
                    '<label>Color 3<input type="color" id="fx-c3" value="#feca57"></label>' +
                    '<label>Angle <span class="ste-fx-val" id="fx-angle-val">90</span>&deg;<input type="range" id="fx-angle" min="0" max="360" value="90"></label>' +
                    '<button class="ste-fx-apply" id="fx-apply-grad">Apply Gradient</button>' +
                    '<button class="ste-fx-clear" id="fx-clear-grad">Clear</button>';
            } else if (type === 'shadow') {
                html = '<label>X <span class="ste-fx-val" id="fx-sx-val">2</span>px<input type="range" id="fx-sx" min="-20" max="20" value="2"></label>' +
                    '<label>Y <span class="ste-fx-val" id="fx-sy-val">2</span>px<input type="range" id="fx-sy" min="-20" max="20" value="2"></label>' +
                    '<label>Blur <span class="ste-fx-val" id="fx-sb-val">4</span>px<input type="range" id="fx-sb" min="0" max="40" value="4"></label>' +
                    '<label>Color<input type="color" id="fx-sc" value="#000000"></label>' +
                    '<button class="ste-fx-apply" id="fx-apply-shadow">Apply Shadow</button>' +
                    '<button class="ste-fx-clear" id="fx-clear-shadow">Clear</button>';
            } else if (type === '3d') {
                html = '<label>Depth <span class="ste-fx-val" id="fx-3d-val">5</span><input type="range" id="fx-3d-depth" min="1" max="15" value="5"></label>' +
                    '<label>Color<input type="color" id="fx-3d-color" value="#aaaaaa"></label>' +
                    '<button class="ste-fx-apply" id="fx-apply-3d">Apply 3D</button>' +
                    '<button class="ste-fx-clear" id="fx-clear-3d">Clear</button>';
            } else if (type === 'glow') {
                html = '<label>Color<input type="color" id="fx-glow-color" value="#00ffff"></label>' +
                    '<label>Intensity <span class="ste-fx-val" id="fx-glow-val">15</span><input type="range" id="fx-glow-int" min="1" max="50" value="15"></label>' +
                    '<button class="ste-fx-apply" id="fx-apply-glow">Apply Glow</button>' +
                    '<button class="ste-fx-clear" id="fx-clear-glow">Clear</button>';
            }
            fxContent.innerHTML = html;
            bindFxEvents(type);
        }

        function bindFxEvents(type) {
            // Range value displays — find the <span class="ste-fx-val"> by id pattern
            fxContent.querySelectorAll('input[type="range"]').forEach(function (r) {
                r.addEventListener('input', function () {
                    var valSpan = fxContent.querySelector('#' + this.id.replace('fx-', 'fx-') + '-val');
                    // Fallback: find span with matching id convention (e.g. fx-angle → fx-angle-val)
                    if (!valSpan) valSpan = fxContent.querySelector('[id$="-val"][id^="' + this.id + '"]');
                    if (valSpan) valSpan.textContent = this.value;
                });
            });

            if (type === 'gradient') {
                on('fx-apply-grad', function () {
                    var c1 = val('fx-c1'), c2 = val('fx-c2'), c3 = val('fx-c3'), a = val('fx-angle');
                    var bg = 'linear-gradient(' + a + 'deg,' + c1 + ',' + c2 + ',' + c3 + ')';
                    wrapSelectionMulti({
                        'background': bg,
                        '-webkit-background-clip': 'text',
                        '-webkit-text-fill-color': 'transparent',
                        'background-clip': 'text'
                    });
                });
                on('fx-clear-grad', function () { clearSelectionStyles(['background', '-webkit-background-clip', '-webkit-text-fill-color', 'background-clip']); });
            } else if (type === 'shadow') {
                on('fx-apply-shadow', function () {
                    var shadow = val('fx-sx') + 'px ' + val('fx-sy') + 'px ' + val('fx-sb') + 'px ' + val('fx-sc');
                    wrapSelection('text-shadow', shadow);
                });
                on('fx-clear-shadow', function () { clearSelectionStyles(['text-shadow']); });
            } else if (type === '3d') {
                on('fx-apply-3d', function () {
                    var depth = parseInt(val('fx-3d-depth'));
                    var color = val('fx-3d-color');
                    var r = parseInt(color.slice(1, 3), 16), g = parseInt(color.slice(3, 5), 16), b = parseInt(color.slice(5, 7), 16);
                    var shadows = [];
                    for (var i = 1; i <= depth; i++) {
                        var f = 1 - (i / depth) * 0.3;
                        shadows.push(i + 'px ' + i + 'px 0 rgb(' + Math.round(r * f) + ',' + Math.round(g * f) + ',' + Math.round(b * f) + ')');
                    }
                    wrapSelection('text-shadow', shadows.join(','));
                });
                on('fx-clear-3d', function () { clearSelectionStyles(['text-shadow']); });
            } else if (type === 'glow') {
                on('fx-apply-glow', function () {
                    var c = val('fx-glow-color'), inten = parseInt(val('fx-glow-int'));
                    var shadow = '0 0 ' + inten + 'px ' + c + ', 0 0 ' + (inten * 2) + 'px ' + c;
                    wrapSelectionMulti({ 'text-shadow': shadow, 'color': c });
                });
                on('fx-clear-glow', function () { clearSelectionStyles(['text-shadow', 'color']); });
            }
        }

        /* ── Utility: wrap selection in styled span ── */
        function wrapSelection(prop, value) {
            var sel = window.getSelection();
            if (!sel.rangeCount || sel.isCollapsed) { toast('Select some text first'); return; }
            var range = sel.getRangeAt(0);
            var span = document.createElement('span');
            span.style[toCamel(prop)] = value;
            range.surroundContents(span);
            sel.removeAllRanges();
            toast('Style applied!');
        }

        function wrapSelectionMulti(styles) {
            var sel = window.getSelection();
            if (!sel.rangeCount || sel.isCollapsed) { toast('Select some text first'); return; }
            var range = sel.getRangeAt(0);
            var span = document.createElement('span');
            for (var k in styles) span.style[toCamel(k)] = styles[k];
            range.surroundContents(span);
            sel.removeAllRanges();
            toast('Style applied!');
        }

        function clearSelectionStyles(props) {
            var sel = window.getSelection();
            if (!sel.rangeCount) return;
            var node = sel.anchorNode;
            if (node && node.nodeType === 3) node = node.parentElement;
            if (node && node.tagName === 'SPAN') {
                props.forEach(function (p) { node.style[toCamel(p)] = ''; });
                if (!node.getAttribute('style') || !node.getAttribute('style').trim()) {
                    node.replaceWith.apply(node, Array.from(node.childNodes));
                }
                toast('Style cleared');
            }
        }

        function toCamel(s) { return s.replace(/-([a-z])/g, function (m, c) { return c.toUpperCase(); }); }
        function val(id) { var el = document.getElementById(id); return el ? el.value : ''; }
        function on(id, fn) { var el = document.getElementById(id); if (el) el.addEventListener('click', fn); }

        /* ── Word/char count ── */
        function updateCounts() {
            var text = canvas.innerText || '';
            var words = text.trim().split(/\s+/).filter(function (w) { return w.length > 0; });
            wordCount.textContent = words.length + ' words';
            charCount.textContent = text.length + ' characters';
        }
        canvas.addEventListener('input', updateCounts);
        updateCounts();

        /* ── Font filter ── */
        var filterBtns = document.querySelectorAll('.ste-filter-btn');
        var fontCards = document.querySelectorAll('.ste-font-card');

        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterBtns.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                var filter = btn.dataset.filter;
                fontCards.forEach(function (card) {
                    if (filter === 'all' || card.dataset.cat === filter) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });
        });

        /* ── Font card click → apply to editor ── */
        fontCards.forEach(function (card) {
            card.addEventListener('click', function () {
                var font = card.dataset.font;
                canvas.focus();
                var sel = window.getSelection();
                if (sel.rangeCount && !sel.isCollapsed) {
                    wrapSelection('font-family', font);
                } else {
                    toast('Font: ' + card.querySelector('.ste-font-name').textContent + ' — select text in editor to apply');
                }
            });
        });

        /* ── Mobile nav toggle ── */
        var mobileToggle = document.querySelector('.ste-mobile-toggle');
        var nav = document.querySelector('.ste-nav');
        if (mobileToggle && nav) {
            mobileToggle.addEventListener('click', function () {
                nav.classList.toggle('open');
            });
            nav.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () { nav.classList.remove('open'); });
            });
        }

        /* ── Smooth scroll for anchor links ── */
        document.querySelectorAll('a[href^="#"]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                var href = this.getAttribute('href');
                if (href && href.charAt(0) === '#' && href.length > 1) {
                    var target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        /* ── Header scroll effect ── */
        var header = document.querySelector('.ste-site-header');
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                header.style.boxShadow = '0 2px 20px rgba(0,0,0,.06)';
            } else {
                header.style.boxShadow = 'none';
            }
        });

        /* ── Toast ── */
        var toastEl = null;
        function toast(msg) {
            if (!toastEl) {
                toastEl = document.createElement('div');
                toastEl.className = 'ste-toast-notify';
                document.body.appendChild(toastEl);
            }
            toastEl.textContent = msg;
            toastEl.classList.add('show');
            setTimeout(function () { toastEl.classList.remove('show'); }, 2000);
        }

        /* ── Keyboard shortcuts ── */
        canvas.addEventListener('keydown', function (e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key.toLowerCase()) {
                    case 'b': e.preventDefault(); document.execCommand('bold'); break;
                    case 'i': e.preventDefault(); document.execCommand('italic'); break;
                    case 'u': e.preventDefault(); document.execCommand('underline'); break;
                }
            }
        });

    });
})();
