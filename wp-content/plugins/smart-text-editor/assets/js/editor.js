/**
 * Smart Text Editor — Full Editor Engine
 * All TinyMCE features + custom style effects, built from scratch.
 * Created by Meet Patel
 */
(function () {
    'use strict';

    var editor, store, floatToolbar;
    var copiedStyles = null;
    var savedRange = null;
    var pasteAsText = false;
    var sourceMode = false;

    function restoreSelection() {
        if (!savedRange) return false;
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedRange);
        return true;
    }

    /* ─── Boot ─── */
    document.addEventListener('DOMContentLoaded', function () {
        editor = document.getElementById('ste-editor');
        store = document.getElementById('content');
        floatToolbar = document.getElementById('ste-float-toolbar');
        if (!editor || !store) return;

        /* Track selection */
        document.addEventListener('selectionchange', function () {
            var sel = window.getSelection();
            if (sel.rangeCount > 0 && editor.contains(sel.anchorNode)) {
                savedRange = sel.getRangeAt(0).cloneRange();
            }
        });

        /* Prevent toolbar buttons from stealing focus */
        ['#ste-toolbar', '#ste-style-bar', '#ste-float-toolbar'].forEach(function (s) {
            var el = document.querySelector(s);
            if (el) el.addEventListener('mousedown', function (e) {
                var tag = e.target.tagName.toLowerCase();
                if (tag === 'input' || tag === 'select' || tag === 'textarea') return;
                e.preventDefault();
            });
        });

        initStyleTabs();
        initFormatBar();
        initBlockType();
        initColors();
        initLink();
        initFloat();
        initRangeDisplays();
        initApply();
        initClear();
        initCopyPaste();
        initPresets();
        initExport();
        initSync();
        initWordCount();
        initReadBack();
        // New TinyMCE features
        initHR();
        initImage();
        initTable();
        initCharmap();
        initPasteAsText();
        initRemoveFormat();
        initMoreTag();
        initSourceView();
        initToolbarToggle();
        initShortcuts();
        initKeyboardShortcuts();
        initTableTabNav();
        initFontFamily();
        initFontSize();
        initAnimPreview();
        initQuickEffects();
        initDayNight();

        /* Ensure Enter creates <p> instead of <div> in Chrome */
        try { document.execCommand('defaultParagraphSeparator', false, 'p'); } catch (e) {}

        /* ━━━ Plan gating ━━━ */
        initPlanGating();

        if (!editor.innerHTML.trim()) editor.innerHTML = '<p><br></p>';

        /* Prevent editor from becoming completely empty */
        editor.addEventListener('input', function () {
            if (!editor.innerHTML.trim() || editor.innerHTML === '<br>') {
                editor.innerHTML = '<p><br></p>';
                setCursorIn(editor.querySelector('p'));
            }
        });

        /* Handle drag & drop images from desktop */
        editor.addEventListener('drop', function (e) {
            var files = e.dataTransfer && e.dataTransfer.files;
            if (!files || !files.length) return;
            var file = files[0];
            if (!file.type.match(/^image\//)) return;
            e.preventDefault();
            var reader = new FileReader();
            reader.onload = function (ev) {
                editor.focus();
                exec('insertHTML', '<img src="' + escAttr(ev.target.result) + '" alt="" style="max-width:100%;height:auto;">');
                sync();
                toast('Image dropped');
            };
            reader.readAsDataURL(file);
        });
        editor.addEventListener('dragover', function (e) {
            if (e.dataTransfer && e.dataTransfer.types && e.dataTransfer.types.indexOf('Files') !== -1) {
                e.preventDefault();
            }
        });
    });

    /* ━━━ Helpers ━━━ */
    function exec(cmd, v) { document.execCommand(cmd, false, v || null); editor.focus(); }
    function val(id) { var e = document.getElementById(id); return e ? e.value : ''; }
    function num(id) { return parseFloat(val(id)) || 0; }
    function setControl(id, value) {
        var el = document.getElementById(id);
        if (!el) return;
        el.value = value;
        var rv = document.querySelector('.ste-rv[data-for="' + id.replace('ste-', '') + '"]');
        if (rv) rv.textContent = value;
    }
    var activeToast = null;
    function toast(msg) {
        if (activeToast) { activeToast.remove(); activeToast = null; }
        var t = document.createElement('div');
        t.className = 'ste-toast'; t.textContent = msg;
        document.body.appendChild(t);
        activeToast = t;
        requestAnimationFrame(function () { t.classList.add('show'); });
        setTimeout(function () { t.classList.remove('show'); setTimeout(function () { if (activeToast === t) activeToast = null; t.remove(); }, 300); }, 2000);
    }
    function escHtml(s) { var d = document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }
    function escAttr(s) { return (s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
    var openModals = [];
    function openModal(id) {
        document.getElementById(id).classList.remove('ste-hidden');
        if (openModals.indexOf(id) === -1) openModals.push(id);
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('ste-hidden');
        var idx = openModals.indexOf(id);
        if (idx !== -1) openModals.splice(idx, 1);
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && openModals.length) {
            closeModal(openModals[openModals.length - 1]);
        }
    });

    function getStyledAncestor() {
        var sel = window.getSelection();
        if (!sel.rangeCount) return null;
        var n = sel.anchorNode;
        while (n && n !== editor) {
            if (n.nodeType === 1 && n.classList && n.classList.contains('ste-styled')) return n;
            n = n.parentNode;
        }
        return null;
    }

    function getAncestorTag(tag) {
        var sel = window.getSelection();
        if (!sel.rangeCount) return null;
        var n = sel.anchorNode;
        tag = tag.toUpperCase();
        while (n && n !== editor) {
            if (n.nodeType === 1 && n.tagName === tag) return n;
            n = n.parentNode;
        }
        return null;
    }

    function wrapSelection(css, attrs) {
        restoreSelection();
        var sel = window.getSelection();
        if (!sel.rangeCount || sel.isCollapsed) { toast('Select some text first.'); return null; }
        var range = sel.getRangeAt(0);
        var existing = getStyledAncestor();
        if (existing) {
            if (css) existing.setAttribute('style', css);
            if (attrs) for (var k in attrs) if (attrs.hasOwnProperty(k)) existing.setAttribute(k, attrs[k]);
            sync(); return existing;
        }
        var span = document.createElement('span');
        span.className = 'ste-styled';
        if (css) span.setAttribute('style', css);
        if (attrs) for (var k2 in attrs) if (attrs.hasOwnProperty(k2)) span.setAttribute(k2, attrs[k2]);
        try { range.surroundContents(span); } catch (e) {
            var frag = range.extractContents(); span.appendChild(frag); range.insertNode(span);
        }
        sel.removeAllRanges();
        var r = document.createRange(); r.selectNodeContents(span); sel.addRange(r);
        savedRange = r.cloneRange();
        sync(); return span;
    }

    /* ━━━ Content sync ━━━ */
    function initSync() {
        editor.addEventListener('input', sync);
        editor.addEventListener('keyup', sync);
        var form = document.getElementById('post');
        if (form) form.addEventListener('submit', function () { sync(); }, true);
        document.addEventListener('click', function (e) {
            var t = e.target;
            if (t && (t.id === 'publish' || t.id === 'save-post' || (t.closest && (t.closest('#publish') || t.closest('#save-post'))))) sync();
        }, true);
        setInterval(sync, 2000);
        sync();
    }
    function sync() {
        if (!store || !editor) return;
        if (sourceMode) return; // don't overwrite while in source mode
        var html = editor.innerHTML;
        // Convert visual more-tag back to WP comment
        html = html.replace(/<hr class="ste-more-tag"[^>]*>/gi, '<!--more-->');
        store.value = html;
    }

    /* ━━━ Word count ━━━ */
    function initWordCount() {
        var el = document.getElementById('ste-word-count');
        if (!el) return;
        function update() {
            var text = (editor.innerText || '').trim();
            var words = text ? text.split(/\s+/).length : 0;
            var chars = text.length;
            el.textContent = words + ' word' + (words !== 1 ? 's' : '') + ' | ' + chars + ' char' + (chars !== 1 ? 's' : '');
        }
        editor.addEventListener('input', update);
        update();
    }

    /* ━━━ Style Tabs ━━━ */
    function initStyleTabs() {
        var c = document.getElementById('ste-style-tabs');
        if (!c) return;
        c.addEventListener('click', function (e) {
            var btn = e.target.closest('.ste-st');
            if (!btn) return;
            switchTab(btn.getAttribute('data-tab'));
        });
    }
    function switchTab(name) {
        var c = document.getElementById('ste-style-tabs');
        if (!c) return;
        c.querySelectorAll('.ste-st').forEach(function (t) { t.classList.remove('active'); });
        var b = c.querySelector('.ste-st[data-tab="' + name + '"]');
        if (b) b.classList.add('active');
        document.querySelectorAll('#ste-style-bar .ste-sp').forEach(function (p) { p.classList.remove('active'); });
        var panel = document.querySelector('#ste-style-bar .ste-sp[data-panel="' + name + '"]');
        if (panel) panel.classList.add('active');
    }

    /* ━━━ Format bar (handles all data-cmd buttons) ━━━ */
    function initFormatBar() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-cmd]');
            if (!btn) return;
            if (!btn.closest('#ste-toolbar') && !btn.closest('#ste-float-toolbar')) return;
            restoreSelection();
            var cmd = btn.getAttribute('data-cmd');
            var cmdVal = btn.getAttribute('data-val') || null;
            if (cmd === 'formatBlock' && cmdVal) {
                exec(cmd, '<' + cmdVal + '>');
            } else {
                exec(cmd, cmdVal);
            }
            updateActive();
            sync();
        });
        document.addEventListener('selectionchange', updateActive);
    }
    function updateActive() {
        ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript',
         'insertUnorderedList', 'insertOrderedList', 'justifyLeft', 'justifyCenter',
         'justifyRight', 'justifyFull'].forEach(function (cmd) {
            var on = false;
            try { on = document.queryCommandState(cmd); } catch (e) {}
            document.querySelectorAll('#ste-toolbar [data-cmd="' + cmd + '"], #ste-float-toolbar [data-cmd="' + cmd + '"]').forEach(function (b) {
                b.classList.toggle('active', on);
            });
        });
    }

    /* ━━━ Block type ━━━ */
    function initBlockType() {
        var sel = document.getElementById('ste-block-type');
        if (!sel) return;
        sel.addEventListener('change', function () {
            restoreSelection();
            exec('formatBlock', '<' + this.value + '>');
            sync();
        });
        document.addEventListener('selectionchange', function () {
            var b = getBlock();
            if (b) {
                var t = b.tagName.toLowerCase();
                if (['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre'].indexOf(t) !== -1) sel.value = t;
            }
        });
    }
    function getBlock() {
        var s = window.getSelection();
        if (!s.rangeCount) return null;
        var n = s.anchorNode;
        while (n && n !== editor) {
            if (n.nodeType === 1) {
                var t = n.tagName.toLowerCase();
                if (['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'li', 'blockquote', 'div'].indexOf(t) !== -1) return n;
            }
            n = n.parentNode;
        }
        return null;
    }

    /* ━━━ Font Family ━━━ */
    function initFontFamily() {
        var sel = document.getElementById('ste-font-family');
        if (!sel) return;
        sel.addEventListener('change', function () {
            var family = this.value;
            if (!family) return;
            restoreSelection();
            var s = window.getSelection();
            if (!s.rangeCount || s.isCollapsed) { toast('Select text first'); this.value = ''; return; }
            /* Wrap in span with font-family */
            var range = s.getRangeAt(0);
            var span = document.createElement('span');
            span.style.fontFamily = family;
            try { range.surroundContents(span); } catch (e) {
                var frag = range.extractContents(); span.appendChild(frag); range.insertNode(span);
            }
            sync();
            this.value = '';
        });

        /* Read current font on selection change */
        document.addEventListener('selectionchange', function () {
            var s = window.getSelection();
            if (!s.rangeCount || !editor.contains(s.anchorNode)) return;
            var node = s.anchorNode;
            if (node.nodeType === 3) node = node.parentNode;
            if (!node || !node.style) return;
            var cs = window.getComputedStyle(node);
            var current = cs.fontFamily || '';
            /* Try to match to a dropdown option */
            var matched = false;
            Array.from(sel.options).forEach(function (opt) {
                if (!opt.value) return;
                /* Check if any part of the option value matches the computed font */
                var optFonts = opt.value.split(',').map(function (f) { return f.trim().replace(/['"]/g, '').toLowerCase(); });
                var curFonts = current.split(',').map(function (f) { return f.trim().replace(/['"]/g, '').toLowerCase(); });
                if (optFonts[0] && curFonts.indexOf(optFonts[0]) !== -1) {
                    sel.value = opt.value;
                    matched = true;
                }
            });
            if (!matched) sel.value = '';
        });
    }

    /* ━━━ Font Size ━━━ */
    function initFontSize() {
        var sel = document.getElementById('ste-font-size');
        if (!sel) return;
        sel.addEventListener('change', function () {
            var size = this.value;
            if (!size) return;
            restoreSelection();
            var s = window.getSelection();
            if (!s.rangeCount || s.isCollapsed) { toast('Select text first'); this.value = ''; return; }
            /* Wrap in span with font-size */
            var range = s.getRangeAt(0);
            var span = document.createElement('span');
            span.style.fontSize = size;
            try { range.surroundContents(span); } catch (e) {
                var frag = range.extractContents(); span.appendChild(frag); range.insertNode(span);
            }
            sync();
            this.value = '';
        });

        /* Read current size on selection change */
        document.addEventListener('selectionchange', function () {
            var s = window.getSelection();
            if (!s.rangeCount || !editor.contains(s.anchorNode)) return;
            var node = s.anchorNode;
            if (node.nodeType === 3) node = node.parentNode;
            if (!node) return;
            var cs = window.getComputedStyle(node);
            var px = Math.round(parseFloat(cs.fontSize));
            /* Match to closest option */
            var matched = false;
            Array.from(sel.options).forEach(function (opt) {
                if (opt.value && parseInt(opt.value) === px) {
                    sel.value = opt.value;
                    matched = true;
                }
            });
            if (!matched) sel.value = '';
        });
    }

    /* ━━━ Colors ━━━ */
    function initColors() {
        bindColor('ste-fg-color', 'foreColor');
        bindColor('ste-bg-color', 'hiliteColor');
        bindColor('ste-ft-color', 'foreColor');
    }
    function bindColor(id, cmd) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', function () { restoreSelection(); exec(cmd, this.value); sync(); });
    }

    /* ━━━ Link ━━━ */
    function initLink() {
        var handler = function () {
            restoreSelection();
            var url = prompt('Enter URL:', 'https://');
            if (url) { restoreSelection(); exec('createLink', url); sync(); }
        };
        ['ste-btn-link', 'ste-ft-link'].forEach(function (id) {
            var b = document.getElementById(id);
            if (b) b.addEventListener('click', handler);
        });
    }

    /* ━━━ Horizontal Rule ━━━ */
    function initHR() {
        var b = document.getElementById('ste-btn-hr');
        if (b) b.addEventListener('click', function () { restoreSelection(); exec('insertHorizontalRule'); sync(); });
    }

    /* ━━━ Insert Image (WP Media Library) ━━━ */
    function initImage() {
        var b = document.getElementById('ste-btn-image');
        if (!b) return;
        b.addEventListener('click', function () {
            if (typeof wp === 'undefined' || !wp.media) { toast('Media library not available'); return; }
            var frame = wp.media({ title: 'Insert Image', button: { text: 'Insert' }, multiple: false });
            frame.on('select', function () {
                var att = frame.state().get('selection').first().toJSON();
                restoreSelection();
                var html = '<img src="' + escAttr(att.url) + '" alt="' + escAttr(att.alt || '') + '"';
                if (att.width) html += ' width="' + escAttr(String(att.width)) + '"';
                html += ' style="max-width:100%;height:auto;">';
                exec('insertHTML', html);
                sync();
            });
            frame.open();
        });
    }

    /* ━━━ Insert Table ━━━ */
    /* ━━━ Table: Insert with options ━━━ */
    function initTable() {
        var btn = document.getElementById('ste-btn-table');
        var picker = document.getElementById('ste-table-picker');
        var grid = document.getElementById('ste-table-grid');
        var label = document.getElementById('ste-table-label');
        if (!btn || !picker || !grid) return;

        /* Build 8x8 grid */
        for (var r = 0; r < 8; r++) {
            for (var c = 0; c < 8; c++) {
                var cell = document.createElement('div');
                cell.className = 'ste-tg-cell';
                cell.setAttribute('data-r', r + 1);
                cell.setAttribute('data-c', c + 1);
                grid.appendChild(cell);
            }
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var rect = btn.getBoundingClientRect();
            picker.style.left = Math.min(rect.left, window.innerWidth - 260) + 'px';
            picker.style.top = (rect.bottom + 4) + 'px';
            picker.classList.toggle('ste-hidden');
        });

        grid.addEventListener('mouseover', function (e) {
            var cell = e.target.closest('.ste-tg-cell');
            if (!cell) return;
            var row = +cell.getAttribute('data-r'), col = +cell.getAttribute('data-c');
            label.textContent = col + ' \u00D7 ' + row;
            grid.querySelectorAll('.ste-tg-cell').forEach(function (c) {
                var cr = +c.getAttribute('data-r'), cc = +c.getAttribute('data-c');
                c.classList.toggle('ste-tg-active', cr <= row && cc <= col);
            });
        });

        grid.addEventListener('click', function (e) {
            var cell = e.target.closest('.ste-tg-cell');
            if (!cell) return;
            var rows = +cell.getAttribute('data-r'), cols = +cell.getAttribute('data-c');
            /* Read options */
            var bStyle = val('ste-tbl-border-style') || 'solid';
            var bWidth = (parseInt(val('ste-tbl-border-width'), 10) || 1);
            var bColor = val('ste-tbl-border-color') || '#dddddd';
            var padding = (parseInt(val('ste-tbl-padding'), 10) || 8);
            var hasHeader = document.getElementById('ste-tbl-header') && document.getElementById('ste-tbl-header').checked;
            var borderCSS = bStyle === 'none' ? 'border:none;' : 'border:' + bWidth + 'px ' + bStyle + ' ' + bColor + ';';
            var cellStyle = borderCSS + 'padding:' + padding + 'px;';
            var tblStyle = 'border-collapse:collapse;width:100%;';

            restoreSelection();
            var html = '<table style="' + tblStyle + '">';
            if (hasHeader) {
                html += '<thead><tr>';
                for (var hc = 0; hc < cols; hc++) html += '<th style="' + cellStyle + 'background:#f5f5f5;font-weight:600;">&nbsp;</th>';
                html += '</tr></thead>';
                rows--; /* body rows minus the header */
            }
            html += '<tbody>';
            for (var r = 0; r < rows; r++) {
                html += '<tr>';
                for (var c = 0; c < cols; c++) html += '<td style="' + cellStyle + '">&nbsp;</td>';
                html += '</tr>';
            }
            html += '</tbody></table><p><br></p>';
            exec('insertHTML', html);
            picker.classList.add('ste-hidden');
            sync();
        });

        /* Close picker on outside click */
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#ste-table-picker') && !e.target.closest('#ste-btn-table')) {
                picker.classList.add('ste-hidden');
            }
        });

        /* Prevent picker mousedown from stealing editor focus */
        picker.addEventListener('mousedown', function (e) {
            var tag = e.target.tagName.toLowerCase();
            if (tag === 'input' || tag === 'select') return;
            e.preventDefault();
        });
    }

    /* ━━━ Table: Context toolbar (row/col/merge/delete/colors) ━━━ */
    function initTableTabNav() {
        var ctx = document.getElementById('ste-table-ctx');
        if (!ctx) return;

        /* Show/hide context bar when cursor enters/leaves a table */
        document.addEventListener('selectionchange', function () {
            var td = getAncestorTag('td') || getAncestorTag('th');
            if (td && editor.contains(td)) {
                var table = td.closest('table');
                var rect = table.getBoundingClientRect();
                ctx.style.left = rect.left + 'px';
                /* Show above table; if no room, show below */
                var ctxTop = rect.top - 38;
                if (ctxTop < 4) ctxTop = rect.bottom + 4;
                ctx.style.top = ctxTop + 'px';
                ctx.classList.remove('ste-hidden');
            } else {
                ctx.classList.add('ste-hidden');
            }
        });

        /* Tab navigation between cells */
        editor.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var td = getAncestorTag('td') || getAncestorTag('th');
            if (!td) return;
            e.preventDefault();
            var cells = Array.from(td.closest('table').querySelectorAll('td, th'));
            var idx = cells.indexOf(td);
            var next = e.shiftKey ? cells[idx - 1] : cells[idx + 1];
            if (next) setCursorIn(next);
        });

        /* Context toolbar actions */
        ctx.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-taction]');
            if (!btn) return;
            var action = btn.getAttribute('data-taction');
            var td = getAncestorTag('td') || getAncestorTag('th');
            if (!td) return;
            var tr = td.closest('tr');
            var table = td.closest('table');
            var tbody = table.querySelector('tbody') || table;
            var colIdx = Array.from(tr.children).indexOf(td);

            switch (action) {
                case 'row-above':
                    tr.parentNode.insertBefore(createRow(tr.children.length, td), tr);
                    break;
                case 'row-below':
                    tr.parentNode.insertBefore(createRow(tr.children.length, td), tr.nextSibling);
                    break;
                case 'col-left':
                    insertCol(table, colIdx, 'before', td);
                    break;
                case 'col-right':
                    insertCol(table, colIdx, 'after', td);
                    break;
                case 'del-row':
                    if (table.querySelectorAll('tr').length > 1) {
                        tr.remove();
                    } else {
                        table.remove();
                    }
                    break;
                case 'del-col':
                    var allRows = table.querySelectorAll('tr');
                    if (allRows[0].children.length > 1) {
                        allRows.forEach(function (row) {
                            if (row.children[colIdx]) row.children[colIdx].remove();
                        });
                    } else {
                        table.remove();
                    }
                    break;
                case 'merge':
                    mergeSelectedCells(table);
                    break;
                case 'split':
                    splitCell(td);
                    break;
                case 'del-table':
                    table.remove();
                    ctx.classList.add('ste-hidden');
                    break;
            }
            sync();
        });

        /* Cell background color */
        var bgInput = document.getElementById('ste-tc-bg');
        if (bgInput) bgInput.addEventListener('input', function () {
            var td = getAncestorTag('td') || getAncestorTag('th');
            if (td) { td.style.backgroundColor = this.value; sync(); }
        });

        /* Cell border color */
        var bdInput = document.getElementById('ste-tc-border');
        if (bdInput) bdInput.addEventListener('input', function () {
            var td = getAncestorTag('td') || getAncestorTag('th');
            if (!td) return;
            var table = td.closest('table');
            table.querySelectorAll('td, th').forEach(function (cell) {
                cell.style.borderColor = bdInput.value;
            });
            sync();
        });

        /* Prevent ctx bar from stealing focus */
        ctx.addEventListener('mousedown', function (e) {
            var tag = e.target.tagName.toLowerCase();
            if (tag !== 'input' && tag !== 'select') e.preventDefault();
        });
    }

    /* ── Table helpers ── */
    function createRow(numCols, refCell) {
        var tr = document.createElement('tr');
        var style = refCell ? refCell.getAttribute('style') || '' : 'border:1px solid #ddd;padding:8px;';
        for (var i = 0; i < numCols; i++) {
            var td = document.createElement('td');
            td.setAttribute('style', style);
            td.innerHTML = '&nbsp;';
            tr.appendChild(td);
        }
        return tr;
    }

    function insertCol(table, colIdx, position, refCell) {
        var style = refCell ? refCell.getAttribute('style') || '' : 'border:1px solid #ddd;padding:8px;';
        table.querySelectorAll('tr').forEach(function (row) {
            var newCell = document.createElement(row.parentNode.tagName === 'THEAD' ? 'th' : 'td');
            newCell.setAttribute('style', style);
            if (row.parentNode.tagName === 'THEAD') newCell.style.fontWeight = '600';
            newCell.innerHTML = '&nbsp;';
            var ref = row.children[colIdx];
            if (position === 'before') {
                row.insertBefore(newCell, ref);
            } else {
                row.insertBefore(newCell, ref ? ref.nextSibling : null);
            }
        });
    }

    function mergeSelectedCells(table) {
        var sel = window.getSelection();
        if (!sel.rangeCount) return;
        /* Simple merge: find all selected tds by checking if they're in the range */
        var range = sel.getRangeAt(0);
        var cells = Array.from(table.querySelectorAll('td, th'));
        var inRange = cells.filter(function (c) { return range.intersectsNode(c); });
        if (inRange.length < 2) { toast('Select multiple cells to merge'); return; }
        /* Merge text into first cell */
        var first = inRange[0];
        var text = '';
        inRange.forEach(function (c, i) {
            if (i > 0) { text += ' ' + c.textContent; c.remove(); }
        });
        first.textContent = (first.textContent + text).trim() || '\u00A0';
        first.setAttribute('colspan', inRange.length);
        toast('Cells merged');
    }

    function splitCell(td) {
        var colspan = parseInt(td.getAttribute('colspan'), 10) || 1;
        if (colspan <= 1) { toast('Cell is not merged'); return; }
        td.removeAttribute('colspan');
        var style = td.getAttribute('style') || '';
        for (var i = 1; i < colspan; i++) {
            var newTd = document.createElement(td.tagName.toLowerCase());
            newTd.setAttribute('style', style);
            newTd.innerHTML = '&nbsp;';
            td.parentNode.insertBefore(newTd, td.nextSibling);
        }
        toast('Cell split');
    }

    function setCursorIn(el) {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    }

    /* ━━━ Special Characters ━━━ */
    function initCharmap() {
        var btn = document.getElementById('ste-btn-charmap');
        var modal = document.getElementById('ste-charmap-modal');
        var grid = document.getElementById('ste-charmap-grid');
        if (!btn || !modal || !grid) return;

        var chars = ["\u00A9","\u00AE","\u2122","\u20AC","\u00A3","\u00A5","\u00A2","\u00A7","\u00B6","\u2020","\u2021","\u00B0","\u00B1","\u00F7","\u00D7","\u221E","\u2260","\u2248","\u2264","\u2265","\u2190","\u2192","\u2191","\u2193","\u2194","\u21D0","\u21D2","\u2605","\u2606","\u2660","\u2663","\u2665","\u2666","\u266A","\u266B","\u2713","\u2717","\u2022","\u2026","\u2014","\u2013","\u00AB","\u00BB","\u2039","\u203A","\u201C","\u201D","\u2018","\u2019","\u00A1","\u00BF","\u00B5","\u03A9","\u03C0","\u2211","\u0394","\u03B1","\u03B2","\u03B3","\u222B","\u221A","\u00BD","\u2153","\u00BC","\u215B"];
        chars.forEach(function (ch) {
            var b = document.createElement('button');
            b.type = 'button';
            b.textContent = ch;
            b.title = ch;
            b.addEventListener('click', function () {
                closeModal('ste-charmap-modal');
                restoreSelection();
                exec('insertHTML', ch);
                sync();
            });
            grid.appendChild(b);
        });

        btn.addEventListener('click', function () { openModal('ste-charmap-modal'); });
        modal.querySelector('.ste-modal-x').addEventListener('click', function () { closeModal('ste-charmap-modal'); });
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal('ste-charmap-modal'); });
    }

    /* ━━━ Paste as Text ━━━ */
    function initPasteAsText() {
        var btn = document.getElementById('ste-btn-paste-text');
        if (!btn) return;
        btn.addEventListener('click', function () {
            pasteAsText = !pasteAsText;
            this.classList.toggle('active', pasteAsText);
            toast(pasteAsText ? 'Paste as text: ON' : 'Paste as text: OFF');
        });
        editor.addEventListener('paste', function (e) {
            if (!pasteAsText) return;
            e.preventDefault();
            var cbd = e.clipboardData || window.clipboardData;
            if (!cbd) return;
            var text = cbd.getData('text/plain');
            exec('insertText', text);
        });
    }

    /* ━━━ Clear Formatting ━━━ */
    function initRemoveFormat() {
        var btn = document.getElementById('ste-btn-remove-format');
        if (!btn) return;
        btn.addEventListener('click', function () {
            restoreSelection();
            exec('removeFormat');
            var node = getStyledAncestor();
            if (node) {
                while (node.firstChild) node.parentNode.insertBefore(node.firstChild, node);
                node.parentNode.removeChild(node);
            }
            sync();
            toast('Formatting cleared');
        });
    }

    /* ━━━ Insert Read More ━━━ */
    function initMoreTag() {
        var btn = document.getElementById('ste-btn-more');
        if (!btn) return;
        btn.addEventListener('click', function () {
            restoreSelection();
            exec('insertHTML', '<hr class="ste-more-tag">');
            sync();
        });
    }

    /* ━━━ Source Code View ━━━ */
    function initSourceView() {
        var btn = document.getElementById('ste-btn-source');
        var srcEl = document.getElementById('ste-source-editor');
        if (!btn || !srcEl) return;
        btn.addEventListener('click', function () {
            sourceMode = !sourceMode;
            this.classList.toggle('active', sourceMode);
            if (sourceMode) {
                srcEl.value = editor.innerHTML;
                editor.style.display = 'none';
                srcEl.style.display = 'block';
                srcEl.focus();
            } else {
                /* Sanitize: strip <script>, <iframe>, event handlers before inserting */
                var clean = srcEl.value
                    .replace(/<script[\s\S]*?<\/script>/gi, '')
                    .replace(/<iframe[\s\S]*?<\/iframe>/gi, '')
                    .replace(/<object[\s\S]*?<\/object>/gi, '')
                    .replace(/<embed[\s\S]*?>/gi, '')
                    .replace(/\s+on\w+\s*=\s*["'][^"']*["']/gi, '');
                editor.innerHTML = clean;
                editor.style.display = '';
                srcEl.style.display = 'none';
                sync();
                editor.focus();
            }
        });
        // Keep store in sync while editing source
        srcEl.addEventListener('input', function () {
            store.value = this.value.replace(/<hr class="ste-more-tag"[^>]*>/gi, '<!--more-->');
        });
    }

    /* ━━━ Toolbar Toggle ━━━ */
    function initToolbarToggle() {
        var btn = document.getElementById('ste-btn-toggle-toolbar');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var bar = document.getElementById('ste-style-bar');
            bar.classList.toggle('ste-collapsed');
            this.classList.toggle('active');
        });
    }

    /* ━━━ Shortcuts Modal ━━━ */
    function initShortcuts() {
        var btn = document.getElementById('ste-btn-shortcuts');
        var modal = document.getElementById('ste-shortcuts-modal');
        if (!btn || !modal) return;
        btn.addEventListener('click', function () { openModal('ste-shortcuts-modal'); });
        modal.querySelector('.ste-modal-x').addEventListener('click', function () { closeModal('ste-shortcuts-modal'); });
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal('ste-shortcuts-modal'); });
    }

    /* ━━━ Keyboard Shortcuts ━━━ */
    function initKeyboardShortcuts() {
        editor.addEventListener('keydown', function (e) {
            var ctrl = e.ctrlKey || e.metaKey;
            if (ctrl && e.key === 'k') { e.preventDefault(); document.getElementById('ste-btn-link').click(); }
            if (ctrl && e.shiftKey && e.key === 'D') { e.preventDefault(); exec('strikeThrough'); }
            if (ctrl && e.shiftKey && e.key === 'X') { e.preventDefault(); document.getElementById('ste-btn-remove-format').click(); }
            if (ctrl && e.shiftKey && e.key === '7') { e.preventDefault(); exec('insertOrderedList'); }
            if (ctrl && e.shiftKey && e.key === '8') { e.preventDefault(); exec('insertUnorderedList'); }
            if (ctrl && e.key === 's') { e.preventDefault(); var pb = document.getElementById('publish'); if (pb) pb.click(); }
        });
    }

    /* ━━━ Floating toolbar ━━━ */
    function initFloat() {
        if (!floatToolbar) return;
        editor.addEventListener('mouseup', function () { setTimeout(posFloat, 20); });
        editor.addEventListener('keyup', function (e) { if (e.shiftKey) posFloat(); });
        var qs = document.getElementById('ste-ft-shadow');
        if (qs) qs.addEventListener('click', function () { wrapSelection('text-shadow: 2px 2px 6px #555555;'); });
        var qg = document.getElementById('ste-ft-gradient');
        if (qg) qg.addEventListener('click', function () {
            wrapSelection('background-image: linear-gradient(90deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;',
                { 'class': 'ste-styled ste-gradient-text' });
        });
    }
    function posFloat() {
        if (!floatToolbar) return;
        var sel = window.getSelection();
        if (!sel.rangeCount || sel.isCollapsed || !editor.contains(sel.anchorNode)) { floatToolbar.classList.add('ste-hidden'); return; }
        var r = sel.getRangeAt(0).getBoundingClientRect();
        if (r.width < 3) { floatToolbar.classList.add('ste-hidden'); return; }
        floatToolbar.classList.remove('ste-hidden');
        var w = floatToolbar.offsetWidth, left = r.left + r.width / 2 - w / 2;
        left = Math.max(8, Math.min(left, window.innerWidth - w - 8));
        floatToolbar.style.left = left + 'px';
        /* Show above selection; if no room, show below */
        var top = r.top - 46;
        if (top < 4) top = r.bottom + 8;
        floatToolbar.style.top = top + 'px';
    }

    /* ━━━ Range displays ━━━ */
    function initRangeDisplays() {
        document.querySelectorAll('#ste-style-bar input[type="range"]').forEach(function (inp) {
            inp.addEventListener('input', function () {
                var rv = document.querySelector('.ste-rv[data-for="' + this.id.replace('ste-', '') + '"]');
                if (rv) rv.textContent = this.value;
            });
        });
    }

    /* ━━━ Apply effects ━━━ */
    function initApply() {
        document.querySelectorAll('.ste-apply[data-apply]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                ({ shadow: doShadow, gradient: doGradient, threed: do3D, glow: doGlow, animation: doAnim })[this.getAttribute('data-apply')]();
            });
        });
    }
    function doShadow() {
        var css = 'text-shadow: ' + num('ste-shadow-x') + 'px ' + num('ste-shadow-y') + 'px ' + num('ste-shadow-blur') + 'px ' + val('ste-shadow-color') + ';';
        if (wrapSelection(css)) toast('Shadow applied');
    }
    function doGradient() {
        var c1 = val('ste-grad-c1'), c2 = val('ste-grad-c2'), c3 = val('ste-grad-c3');
        var cols = c1 + ', ' + c2; if (c3 && c3 !== c2) cols += ', ' + c3;
        if (wrapSelection('background-image: linear-gradient(' + num('ste-grad-angle') + 'deg, ' + cols + '); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;', { 'class': 'ste-styled ste-gradient-text' })) toast('Gradient applied');
    }
    function do3D() {
        var d = num('ste-3d-depth'), c = val('ste-3d-color'), l = [];
        for (var i = 1; i <= d; i++) l.push(i + 'px ' + i + 'px 0 ' + c);
        if (wrapSelection('text-shadow: ' + l.join(', ') + ';')) toast('3D applied');
    }
    function doGlow() {
        var c = val('ste-glow-color'), n = num('ste-glow-int');
        if (wrapSelection('text-shadow: 0 0 ' + n + 'px ' + c + ', 0 0 ' + (n * 2) + 'px ' + c + ', 0 0 ' + (n * 3) + 'px ' + c + ';')) toast('Glow applied');
    }
    function doAnim() {
        var type = val('ste-anim-type'), dur = val('ste-anim-dur');
        if (!type) { toast('Pick an animation'); return; }
        restoreSelection();
        var sel = window.getSelection();
        if (!sel.rangeCount || sel.isCollapsed) { toast('Select text first'); return; }
        var target = getStyledAncestor() || wrapSelection('');
        if (target) { target.setAttribute('data-ste-anim', type); target.setAttribute('data-ste-anim-dur', dur); sync(); toast('Animation: ' + type); }
    }
    /* ━━━ Animation Preview in editor ━━━ */
    function initAnimPreview() {
        var btn = document.getElementById('ste-anim-preview');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var type = val('ste-anim-type'), dur = parseFloat(val('ste-anim-dur')) || 0.6;
            if (!type) { toast('Pick an animation type first'); return; }
            restoreSelection();
            /* Find the element to preview — styled ancestor or animated element */
            var target = getStyledAncestor();
            if (!target) {
                var sel = window.getSelection();
                if (sel.rangeCount) {
                    var n = sel.anchorNode;
                    while (n && n !== editor) {
                        if (n.nodeType === 1 && n.hasAttribute && n.hasAttribute('data-ste-anim')) { target = n; break; }
                        n = n.parentNode;
                    }
                }
            }
            if (!target) { toast('Place cursor in styled/animated text'); return; }
            /* Run the animation as a preview */
            target.style.animation = 'none';
            void target.offsetWidth; /* force reflow */
            target.style.animation = 'ste-' + type + ' ' + dur + 's ease both';
            toast('Preview: ' + type);
            /* Clean up after animation finishes */
            setTimeout(function () { target.style.animation = ''; }, (dur * 1000) + 100);
        });
    }
    /* ━━━ Quick effect buttons (toolbar shortcuts) ━━━ */
    function initQuickEffects() {
        var tabs = { gradient: 'gradient', shadow: 'shadow', '3d': 'threed', glow: 'glow' };
        Object.keys(tabs).forEach(function (key) {
            var btn = document.getElementById('ste-quick-' + key);
            if (!btn) return;
            btn.addEventListener('click', function () {
                /* Open the corresponding style bar tab */
                var bar = document.getElementById('ste-style-bar');
                if (bar && bar.classList.contains('ste-collapsed')) bar.classList.remove('ste-collapsed');
                var tabBtn = document.querySelector('.ste-st[data-tab="' + tabs[key] + '"]');
                if (tabBtn) tabBtn.click();
            });
        });
        /* Presets button */
        var presetsBtn = document.getElementById('ste-btn-presets');
        if (presetsBtn) {
            presetsBtn.addEventListener('click', function () {
                var bar = document.getElementById('ste-style-bar');
                if (bar && bar.classList.contains('ste-collapsed')) bar.classList.remove('ste-collapsed');
                switchTab('presets');
            });
        }
    }
    /* ━━━ Day / Night mode ━━━ */
    function initDayNight() {
        var btn = document.getElementById('ste-btn-daynight');
        if (!btn) return;
        var wrap = document.getElementById('ste-wrap');
        var icon = btn.querySelector('.ste-dn-icon');
        var key = 'ste_day_mode';

        /* Restore saved preference */
        if (localStorage.getItem(key) === '1') {
            wrap.classList.add('ste-light');
            icon.textContent = '\uD83C\uDF19'; /* moon */
            btn.classList.add('active');
        }

        btn.addEventListener('click', function () {
            var isLight = wrap.classList.toggle('ste-light');
            icon.textContent = isLight ? '\uD83C\uDF19' : '\u2600\uFE0F';
            btn.classList.toggle('active', isLight);
            localStorage.setItem(key, isLight ? '1' : '0');
            toast(isLight ? 'Day mode' : 'Night mode');
        });
    }

    /* ━━━ Clear effects ━━━ */
    function initClear() {
        document.querySelectorAll('.ste-clear-fx[data-clear]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                restoreSelection();
                var node = getStyledAncestor();
                if (!node) { toast('Place cursor in styled text'); return; }
                if (this.getAttribute('data-clear') === 'animation') {
                    node.removeAttribute('data-ste-anim');
                    node.removeAttribute('data-ste-anim-dur');
                } else {
                    while (node.firstChild) node.parentNode.insertBefore(node.firstChild, node);
                    node.parentNode.removeChild(node);
                }
                sync(); toast('Effect removed');
            });
        });
    }

    /* ━━━ Copy / Paste style ━━━ */
    function initCopyPaste() {
        var bc = document.getElementById('ste-btn-copy-style');
        var bp = document.getElementById('ste-btn-paste-style');
        if (bc) bc.addEventListener('click', function () {
            restoreSelection();
            var n = getStyledAncestor();
            if (n) { copiedStyles = { css: n.getAttribute('style') || '', cls: n.className || 'ste-styled' }; toast('Style copied'); }
            else toast('Place cursor in styled text');
        });
        if (bp) bp.addEventListener('click', function () {
            if (!copiedStyles) { toast('No style copied'); return; }
            if (wrapSelection(copiedStyles.css, { 'class': copiedStyles.cls })) toast('Style pasted');
        });
    }

    /* ━━━ Read Back (click styled text → populate controls) ━━━ */
    function initReadBack() {
        editor.addEventListener('click', function (e) {
            var node = e.target.closest('.ste-styled');
            if (!node || !editor.contains(node)) return;
            editor.querySelectorAll('.ste-styled').forEach(function (s) { s.classList.remove('ste-active'); });
            node.classList.add('ste-active');
            readStylesFromNode(node);
        });
    }
    function readStylesFromNode(node) {
        var style = node.getAttribute('style') || '';
        var css = parseCSS(style);
        var tab = 'shadow';
        if (css['background-image'] || css['-webkit-background-clip']) { tab = 'gradient'; readGradient(css); }
        if (css['text-shadow']) {
            var layers = splitShadow(css['text-shadow']);
            if (is3D(layers)) { tab = 'threed'; read3D(layers); }
            else if (isGlow(layers)) { tab = 'glow'; readGlowVals(layers); }
            else { tab = 'shadow'; readShadowVals(layers); }
        }
        if (node.hasAttribute('data-ste-anim')) { tab = 'anim'; setControl('ste-anim-type', node.getAttribute('data-ste-anim')); setControl('ste-anim-dur', node.getAttribute('data-ste-anim-dur') || '0.6'); }
        if (css['color']) { var hex = toHex(css['color']); if (hex) setControl('ste-fg-color', hex); }
        switchTab(tab);
    }
    function parseCSS(s) { var r = {}; if (!s) return r; s.split(';').forEach(function (p) { var i = p.indexOf(':'); if (i > -1) { var k = p.substring(0, i).trim().toLowerCase(), v = p.substring(i + 1).trim(); if (k && v) r[k] = v; } }); return r; }
    function splitShadow(v) { if (!v || v === 'none') return []; var r = [], d = 0, c = ''; for (var i = 0; i < v.length; i++) { var ch = v[i]; if (ch === '(') d++; else if (ch === ')') d--; else if (ch === ',' && d === 0) { r.push(c.trim()); c = ''; continue; } c += ch; } if (c.trim()) r.push(c.trim()); return r; }
    function is3D(l) { if (l.length < 2) return false; var a = l[0].match(/^(\d+)px\s+(\d+)px\s+0/), b = l[1].match(/^(\d+)px\s+(\d+)px\s+0/); return a && b && +a[1] === 1 && +b[1] === 2; }
    function isGlow(l) { return l.length >= 2 && /^0\s+0\s+\d+px/.test(l[0]) && /^0\s+0\s+\d+px/.test(l[1]); }
    function readShadowVals(l) { if (!l.length) return; var m = l[0].match(/^(-?\d+)px\s+(-?\d+)px\s+(\d+)px\s+(.*)/); if (!m) return; setControl('ste-shadow-x', +m[1]); setControl('ste-shadow-y', +m[2]); setControl('ste-shadow-blur', +m[3]); var h = toHex(m[4].trim()); if (h) setControl('ste-shadow-color', h); }
    function read3D(l) { setControl('ste-3d-depth', l.length); var m = l[0].match(/\d+px\s+\d+px\s+\d+\s+(.*)/); if (m) { var h = toHex(m[1].trim()); if (h) setControl('ste-3d-color', h); } }
    function readGlowVals(l) { var m = l[0].match(/^0\s+0\s+(\d+)px\s+(.*)/); if (!m) return; setControl('ste-glow-int', +m[1]); var h = toHex(m[2].trim()); if (h) setControl('ste-glow-color', h); }
    function readGradient(css) { var bg = css['background-image'] || ''; var m = bg.match(/linear-gradient\(\s*(\d+)deg\s*,\s*(.*)\)/); if (!m) return; setControl('ste-grad-angle', +m[1]); var c = m[2].split(',').map(function (s) { return s.trim(); }); if (c[0]) setControl('ste-grad-c1', toHex(c[0]) || c[0]); if (c[1]) setControl('ste-grad-c2', toHex(c[1]) || c[1]); if (c[2]) setControl('ste-grad-c3', toHex(c[2]) || c[2]); }
    function toHex(c) { if (!c) return null; c = c.trim(); if (/^#[0-9a-fA-F]{6}$/.test(c)) return c; if (/^#[0-9a-fA-F]{3}$/.test(c)) return '#' + c[1] + c[1] + c[2] + c[2] + c[3] + c[3]; var t = document.createElement('div'); t.style.color = c; document.body.appendChild(t); var v = window.getComputedStyle(t).color; document.body.removeChild(t); var m = v.match(/(\d+)/g); if (!m || m.length < 3) return null; return '#' + ('0' + (+m[0]).toString(16)).slice(-2) + ('0' + (+m[1]).toString(16)).slice(-2) + ('0' + (+m[2]).toString(16)).slice(-2); }

    /* ━━━ Presets ━━━ */
    var DEFAULT_PRESETS = [
        /* ── Glow & Neon ── */
        { name: 'Neon Glow', css: 'color: #0ff; text-shadow: 0 0 7px #0ff, 0 0 14px #0ff, 0 0 28px #0ff; font-weight: 700;', cls: 'ste-styled' },
        { name: 'Cyber Green', css: 'color: #00ff41; text-shadow: 0 0 5px #00ff41, 0 0 15px #00ff41; font-family: monospace; font-weight: 700;', cls: 'ste-styled' },
        { name: 'Frosted', css: 'color: #a8d8ea; text-shadow: 0 0 10px rgba(168,216,234,0.8), 0 0 20px rgba(168,216,234,0.4); font-weight: 600;', cls: 'ste-styled' },
        { name: 'Fire', css: 'color: #ff6600; text-shadow: 0 0 5px #ff6600, 0 0 12px #ff3300, 0 0 24px #ff0000; font-weight: 800;', cls: 'ste-styled' },
        /* ── Gradients ── */
        { name: 'Gold Luxury', css: 'background-image: linear-gradient(135deg, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700; font-family: Georgia, serif;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Ocean Wave', css: 'background-image: linear-gradient(90deg, #0077b6, #00b4d8, #90e0ef); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Sunset', css: 'background-image: linear-gradient(90deg, #ff6b6b, #feca57, #ff9ff3); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Candy', css: 'background-image: linear-gradient(90deg, #f093fb, #f5576c, #4facfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 800;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Rainbow', css: 'background-image: linear-gradient(90deg, #ff0000, #ff8800, #ffff00, #00cc00, #0066ff, #8800ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 800;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Nature', css: 'background-image: linear-gradient(90deg, #2d6a4f, #52b788, #d8f3dc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 600;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Rose Gold', css: 'background-image: linear-gradient(135deg, #b76e79, #e8c4c8, #b76e79); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Aurora', css: 'background-image: linear-gradient(90deg, #00c9ff, #92fe9d, #f0ff00, #ff6ec7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Midnight', css: 'background-image: linear-gradient(135deg, #0f0c29, #302b63, #24243e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Peach', css: 'background-image: linear-gradient(90deg, #ffecd2, #fcb69f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Berry', css: 'background-image: linear-gradient(90deg, #8e2de2, #4a00e0, #e100ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 800;', cls: 'ste-styled ste-gradient-text' },
        { name: 'Chrome', css: 'background-image: linear-gradient(180deg, #e0e0e0, #ffffff, #a0a0a0, #e0e0e0); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 800;', cls: 'ste-styled ste-gradient-text' },
        /* ── 3D & Shadow ── */
        { name: 'Retro Pop', css: 'color: #ff6f61; text-shadow: 3px 3px 0 #ffc857, 6px 6px 0 #2ec4b6; font-weight: 800;', cls: 'ste-styled' },
        { name: 'Deep Shadow', css: 'color: #1a1a2e; text-shadow: 2px 2px 0 #16213e, 4px 4px 0 #0f3460, 6px 6px 0 #533483; font-weight: 800;', cls: 'ste-styled' },
        { name: 'Long Shadow', css: 'color: #e74c3c; text-shadow: 1px 1px 0 #c0392b, 2px 2px 0 #c0392b, 3px 3px 0 #c0392b, 4px 4px 0 #c0392b, 5px 5px 0 #c0392b, 6px 6px 0 #c0392b, 7px 7px 0 #c0392b, 8px 8px 0 #c0392b; font-weight: 800;', cls: 'ste-styled' },
        { name: 'Emboss', css: 'color: #ccc; text-shadow: -1px -1px 0 #fff, 1px 1px 0 #333; font-weight: 700;', cls: 'ste-styled' },
        { name: 'Letterpress', css: 'color: #222; text-shadow: 0 1px 0 rgba(255,255,255,0.6); font-weight: 700;', cls: 'ste-styled' },
        { name: 'Comic 3D', css: 'color: #f1c40f; text-shadow: 2px 2px 0 #e67e22, 4px 4px 0 #d35400, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000; font-weight: 900;', cls: 'ste-styled' },
        /* ── Outline & Stroke ── */
        { name: 'Outline', css: 'color: transparent; -webkit-text-stroke: 2px #4f46e5; font-weight: 800;', cls: 'ste-styled' },
        { name: 'Thick Outline', css: 'color: transparent; -webkit-text-stroke: 3px #1a1a2e; font-weight: 900;', cls: 'ste-styled' },
        /* ── Typography ── */
        { name: 'Elegant Serif', css: 'color: #2c3e50; font-family: Georgia, serif; font-style: italic; letter-spacing: 2px;', cls: 'ste-styled' },
        { name: 'Typewriter', css: 'color: #333; font-family: "Courier New", monospace; letter-spacing: 1px; font-weight: 400;', cls: 'ste-styled' },
        { name: 'Modern Clean', css: 'color: #222; font-family: "Segoe UI", sans-serif; font-weight: 300; letter-spacing: 3px; text-transform: uppercase;', cls: 'ste-styled' },
        { name: 'Bold Impact', css: 'color: #111; font-family: Impact, sans-serif; text-transform: uppercase; letter-spacing: 2px;', cls: 'ste-styled' },
        { name: 'Handwritten', css: 'color: #5c4033; font-family: cursive; font-size: 1.1em; font-weight: 400;', cls: 'ste-styled' },
        { name: 'Small Caps', css: 'color: #34495e; font-family: Georgia, serif; font-variant: small-caps; letter-spacing: 1px; font-weight: 600;', cls: 'ste-styled' },
        /* ── Special ── */
        { name: 'Highlight Yellow', css: 'background-color: #fff176; color: #333; padding: 2px 6px; font-weight: 600;', cls: 'ste-styled' },
        { name: 'Tag Dark', css: 'background-color: #1a1a2e; color: #eee; padding: 2px 8px; border-radius: 3px; font-family: monospace; font-size: 0.9em;', cls: 'ste-styled' },
        { name: 'Tag Blue', css: 'background-color: #e3f2fd; color: #1565c0; padding: 2px 8px; border-radius: 3px; font-weight: 600; font-size: 0.9em;', cls: 'ste-styled' },
        { name: 'Underline Accent', css: 'color: #333; border-bottom: 3px solid #4f46e5; padding-bottom: 2px; font-weight: 600;', cls: 'ste-styled' },
        { name: 'Strikethrough Red', css: 'color: #999; text-decoration: line-through; text-decoration-color: #e74c3c;', cls: 'ste-styled' },
        { name: 'Wavy Underline', css: 'color: #333; text-decoration: underline wavy #e74c3c; font-weight: 500;', cls: 'ste-styled' }
    ];
    function initPresets() {
        renderPresets();
        var sb = document.getElementById('ste-preset-save');
        if (!sb) return;
        sb.addEventListener('click', function () {
            var ni = document.getElementById('ste-preset-name'), name = (ni.value || '').trim();
            if (!name) { toast('Enter a name'); return; }
            restoreSelection(); var n = getStyledAncestor();
            if (!n) { toast('Select styled text'); return; }
            var p = getUserPresets(); p.push({ name: name, css: n.getAttribute('style') || '', cls: n.className || 'ste-styled' });
            localStorage.setItem('ste_presets', JSON.stringify(p)); ni.value = ''; renderPresets(); toast('Preset saved');
        });
    }
    function getUserPresets() { try { return JSON.parse(localStorage.getItem('ste_presets')) || []; } catch (e) { return []; } }
    function renderPresets() {
        var list = document.getElementById('ste-presets-list'); if (!list) return;
        var allPresets = DEFAULT_PRESETS.concat(getUserPresets());
        list.innerHTML = '';
        allPresets.forEach(function (pr, i) {
            var isDefault = i < DEFAULT_PRESETS.length;
            var el = document.createElement('div'); el.className = 'ste-preset-item' + (isDefault ? ' ste-preset-default' : '');
            var html = '<span class="ste-preset-item-preview" style="' + escAttr(pr.css) + '">Aa</span><span class="ste-preset-item-name">' + escHtml(pr.name) + '</span>';
            if (!isDefault) html += '<button type="button" class="ste-preset-del" data-idx="' + (i - DEFAULT_PRESETS.length) + '">&times;</button>';
            el.innerHTML = html;
            el.addEventListener('click', function (e) { if (e.target.classList.contains('ste-preset-del')) return; var a = {}; if (pr.cls) a['class'] = pr.cls; if (wrapSelection(pr.css, a)) toast('Preset: ' + pr.name); });
            list.appendChild(el);
        });
        list.querySelectorAll('.ste-preset-del').forEach(function (b) {
            b.addEventListener('click', function (e) { e.stopPropagation(); var p = getUserPresets(); p.splice(+this.getAttribute('data-idx'), 1); localStorage.setItem('ste_presets', JSON.stringify(p)); renderPresets(); toast('Deleted'); });
        });
    }

    /* ━━━ Export ━━━ */
    function initExport() {
        var btn = document.getElementById('ste-btn-export'), modal = document.getElementById('ste-export-modal');
        if (!btn || !modal) return;
        btn.addEventListener('click', function () {
            document.getElementById('ste-export-html').textContent = fmtHtml(editor.innerHTML);
            document.getElementById('ste-export-css').textContent = buildCss(editor.innerHTML);
            openModal('ste-export-modal');
        });
        modal.querySelector('.ste-modal-x').addEventListener('click', function () { closeModal('ste-export-modal'); });
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal('ste-export-modal'); });
        modal.querySelectorAll('.ste-mt').forEach(function (tab) {
            tab.addEventListener('click', function () {
                modal.querySelectorAll('.ste-mt').forEach(function (t) { t.classList.remove('active'); });
                modal.querySelectorAll('.ste-code').forEach(function (c) { c.classList.remove('active'); });
                this.classList.add('active');
                document.getElementById('ste-export-' + this.getAttribute('data-target')).classList.add('active');
            });
        });
        document.getElementById('ste-export-copy').addEventListener('click', function () {
            var c = modal.querySelector('.ste-code.active');
            if (c && navigator.clipboard) navigator.clipboard.writeText(c.textContent).then(function () { toast('Copied'); });
        });
    }
    function buildCss(html) {
        var t = document.createElement('div'); t.innerHTML = html;
        var spans = t.querySelectorAll('.ste-styled');
        if (!spans.length) return '/* No custom styles */';
        var lines = ['/* Smart Text Editor — Exported */\n'], idx = 1;
        spans.forEach(function (s) { var st = s.getAttribute('style'); if (st) { var c = 'ste-s-' + idx; s.className = c; s.removeAttribute('style'); lines.push('.' + c + ' {'); st.split(';').forEach(function (r) { r = r.trim(); if (r) lines.push('    ' + r + ';'); }); lines.push('}\n'); idx++; } });
        document.getElementById('ste-export-html').textContent = fmtHtml(t.innerHTML);
        return lines.join('\n');
    }
    function fmtHtml(h) { var lv = 0; return h.replace(/></g, '>\n<').split('\n').map(function (l) { l = l.trim(); if (l.match(/^<\/(p|h[1-6]|ul|ol|li|div|blockquote|table|thead|tbody|tr)/)) lv = Math.max(0, lv - 1); var pad = '  '.repeat(lv); if (l.match(/^<(p|h[1-6]|ul|ol|li|div|blockquote|table|thead|tbody|tr)/) && !l.match(/\/>$/)) lv++; return pad + l; }).join('\n'); }

    /* ━━━ Plan Gating ━━━ */
    function initPlanGating() {
        var cfg = window.stePlan;
        if (!cfg || cfg.plan === 'business') return; // Business has everything

        var featureLabels = {
            effects:      'Style Effects (Gradient, 3D, Glow, Shadow)',
            animations:   'Scroll Animations',
            tableEditor:  'Table Editor',
            exportCss:    'HTML/CSS Export',
            customPresets: 'Custom Presets'
        };

        /* Show upgrade modal */
        function showUpgrade(featureKey) {
            var modal = document.getElementById('ste-upgrade-modal');
            var msg   = document.getElementById('ste-upgrade-msg');
            if (!modal) return;
            var label = featureLabels[featureKey] || featureKey;
            var minPlan = (featureKey === 'customPresets') ? 'Business' : 'Pro';
            msg.innerHTML = '<strong>' + escHtml(label) + '</strong> is available on the <strong>' + minPlan + '</strong> plan and above.';
            modal.classList.remove('ste-hidden');
        }

        /* Close upgrade modal */
        var upgradeModal = document.getElementById('ste-upgrade-modal');
        if (upgradeModal) {
            upgradeModal.querySelector('.ste-modal-x').addEventListener('click', function () {
                upgradeModal.classList.add('ste-hidden');
            });
            upgradeModal.addEventListener('click', function (e) {
                if (e.target === upgradeModal) upgradeModal.classList.add('ste-hidden');
            });
        }

        /* Gate buttons/tabs with data-ste-feature */
        document.querySelectorAll('[data-ste-feature]').forEach(function (el) {
            var feature = el.getAttribute('data-ste-feature');
            if (cfg[feature]) return; // feature is available on current plan

            el.classList.add('ste-locked');
            el.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showUpgrade(feature);
            }, true);
        });

        /* Gate fonts: lock options beyond the free limit */
        if (cfg.plan === 'free') {
            var fontSelect = document.getElementById('ste-font-family');
            if (fontSelect) {
                var freeFonts = cfg.freeFonts || [];
                var opts = fontSelect.querySelectorAll('option[value]');
                opts.forEach(function (opt) {
                    var val = opt.value;
                    if (!val) return; // placeholder
                    var allowed = false;
                    for (var i = 0; i < freeFonts.length; i++) {
                        if (val === freeFonts[i]) { allowed = true; break; }
                    }
                    if (!allowed) {
                        opt.disabled = true;
                        opt.textContent = opt.textContent + ' (Pro)';
                    }
                });
            }

            /* Gate presets: only first N presets available */
            var origRenderPresets = window._steOrigRenderPresets;
            // We'll handle this in the presets render via cfg.maxPresets
        }

        /* Patch preset rendering to lock excess presets */
        patchPresetRendering();

        /* Gate preset saving for non-business plans */
        if (!cfg.customPresets) {
            var saveBtn = document.getElementById('ste-preset-save');
            if (saveBtn) {
                saveBtn.classList.add('ste-locked');
                var nameInput = document.getElementById('ste-preset-name');
                saveBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    showUpgrade('customPresets');
                }, true);
            }
        }
    }

    /* Patch renderPresets to respect plan limits */
    var _origRenderPresets = null;
    var _patchedRenderPresets = false;
    function patchPresetRendering() {
        if (_patchedRenderPresets) return;
        _patchedRenderPresets = true;

        var cfg = window.stePlan;
        if (!cfg || cfg.maxPresets >= 999) return;

        var list = document.getElementById('ste-presets-list');
        if (!list) return;

        // Use MutationObserver to lock excess presets after each render
        var observer = new MutationObserver(function () {
            var items = list.querySelectorAll('.ste-preset-item');
            items.forEach(function (item, idx) {
                if (idx >= cfg.maxPresets && item.classList.contains('ste-preset-default')) {
                    item.classList.add('ste-locked');
                    item.setAttribute('title', 'Upgrade to Pro to unlock this preset');
                }
            });
        });
        observer.observe(list, { childList: true });
    }

})();
