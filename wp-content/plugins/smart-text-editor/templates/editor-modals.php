<?php
/**
 * Partial: Editor modals and overlay UI (floating toolbar, table picker,
 * table context bar, charmap, shortcuts, export, upgrade modals).
 * Included by STE_Editor::render_editor(). Variables available: $plan, $plan_data.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
        <!-- Floating toolbar -->
        <div id="ste-float-toolbar" class="ste-hidden">
            <button type="button" data-cmd="bold" class="ste-ft"><b>B</b></button>
            <button type="button" data-cmd="italic" class="ste-ft"><i>I</i></button>
            <button type="button" data-cmd="underline" class="ste-ft"><u>U</u></button>
            <span class="ste-ft-sep"></span>
            <label class="ste-ft-clr"><span>A</span><input type="color" id="ste-ft-color" value="#333333"></label>
            <button type="button" id="ste-ft-shadow" class="ste-ft" title="Shadow">S</button>
            <button type="button" id="ste-ft-gradient" class="ste-ft" title="Gradient">G</button>
            <button type="button" id="ste-ft-link" class="ste-ft" title="Link">🔗</button>
        </div>

        <!-- Table picker popover -->
        <div id="ste-table-picker" class="ste-hidden">
            <div class="ste-tp-section">
                <div class="ste-tp-title">Insert Table</div>
                <div id="ste-table-grid"></div>
                <div id="ste-table-label">1 × 1</div>
            </div>
            <div class="ste-tp-section">
                <div class="ste-tp-title">Border Style</div>
                <div class="ste-tp-row">
                    <select id="ste-tbl-border-style">
                        <option value="solid">Solid</option>
                        <option value="dashed">Dashed</option>
                        <option value="dotted">Dotted</option>
                        <option value="double">Double</option>
                        <option value="none">None</option>
                    </select>
                    <input type="number" id="ste-tbl-border-width" value="1" min="0" max="10" title="Border width (px)">
                    <input type="color" id="ste-tbl-border-color" value="#dddddd" title="Border color">
                </div>
            </div>
            <div class="ste-tp-section">
                <div class="ste-tp-title">Cell Padding</div>
                <div class="ste-tp-row">
                    <input type="number" id="ste-tbl-padding" value="8" min="0" max="40" title="Padding (px)"> px
                </div>
            </div>
            <div class="ste-tp-section">
                <div class="ste-tp-title">Header Row</div>
                <div class="ste-tp-row">
                    <label class="ste-tp-check"><input type="checkbox" id="ste-tbl-header" checked> Include header row</label>
                </div>
            </div>
        </div>

        <!-- Table context toolbar (appears when cursor is in a table) -->
        <div id="ste-table-ctx" class="ste-hidden">
            <button type="button" class="ste-tc-btn" data-taction="row-above" title="Insert row above">↑ Row</button>
            <button type="button" class="ste-tc-btn" data-taction="row-below" title="Insert row below">↓ Row</button>
            <button type="button" class="ste-tc-btn" data-taction="col-left" title="Insert column left">← Col</button>
            <button type="button" class="ste-tc-btn" data-taction="col-right" title="Insert column right">→ Col</button>
            <span class="ste-tc-sep"></span>
            <button type="button" class="ste-tc-btn" data-taction="del-row" title="Delete row">✕ Row</button>
            <button type="button" class="ste-tc-btn" data-taction="del-col" title="Delete column">✕ Col</button>
            <span class="ste-tc-sep"></span>
            <button type="button" class="ste-tc-btn" data-taction="merge" title="Merge selected cells">Merge</button>
            <button type="button" class="ste-tc-btn" data-taction="split" title="Split cell">Split</button>
            <span class="ste-tc-sep"></span>
            <label class="ste-tc-clr" title="Cell background">
                <span>BG</span>
                <input type="color" id="ste-tc-bg" value="#ffffff">
            </label>
            <label class="ste-tc-clr" title="Border color">
                <span>Bd</span>
                <input type="color" id="ste-tc-border" value="#dddddd">
            </label>
            <span class="ste-tc-sep"></span>
            <button type="button" class="ste-tc-btn ste-tc-del" data-taction="del-table" title="Delete table">🗑 Table</button>
        </div>

        <!-- Charmap modal -->
        <div id="ste-charmap-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box" style="width:440px;">
                <div class="ste-modal-head"><h3>Special Characters</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div id="ste-charmap-grid"></div>
            </div>
        </div>

        <!-- Shortcuts modal -->
        <div id="ste-shortcuts-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box" style="width:480px;">
                <div class="ste-modal-head"><h3>Keyboard Shortcuts</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div style="padding:16px;max-height:50vh;overflow-y:auto;">
                    <table class="ste-shortcuts-table">
                        <tr><td><kbd>Ctrl+B</kbd></td><td>Bold</td></tr>
                        <tr><td><kbd>Ctrl+I</kbd></td><td>Italic</td></tr>
                        <tr><td><kbd>Ctrl+U</kbd></td><td>Underline</td></tr>
                        <tr><td><kbd>Ctrl+K</kbd></td><td>Insert Link</td></tr>
                        <tr><td><kbd>Ctrl+Z</kbd></td><td>Undo</td></tr>
                        <tr><td><kbd>Ctrl+Y</kbd></td><td>Redo</td></tr>
                        <tr><td><kbd>Ctrl+Shift+7</kbd></td><td>Ordered List</td></tr>
                        <tr><td><kbd>Ctrl+Shift+8</kbd></td><td>Unordered List</td></tr>
                        <tr><td><kbd>Ctrl+Shift+D</kbd></td><td>Strikethrough</td></tr>
                        <tr><td><kbd>Ctrl+Shift+X</kbd></td><td>Clear Formatting</td></tr>
                        <tr><td><kbd>Ctrl+S</kbd></td><td>Save</td></tr>
                        <tr><td><kbd>Tab</kbd></td><td>Next table cell</td></tr>
                        <tr><td><kbd>Shift+Tab</kbd></td><td>Previous table cell</td></tr>
                        <tr><td><kbd>Escape</kbd></td><td>Close modal / dialog</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Export modal -->
        <div id="ste-export-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box">
                <div class="ste-modal-head"><h3>Export HTML / CSS</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div class="ste-modal-tabs"><button type="button" class="ste-mt active" data-target="html">HTML</button><button type="button" class="ste-mt" data-target="css">CSS</button></div>
                <pre id="ste-export-html" class="ste-code active"></pre>
                <pre id="ste-export-css" class="ste-code"></pre>
                <div class="ste-modal-foot"><button type="button" id="ste-export-copy" class="ste-apply">Copy to Clipboard</button></div>
            </div>
        </div>

        <!-- Upgrade modal -->
        <div id="ste-upgrade-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box" style="width:440px;">
                <div class="ste-modal-head"><h3 id="ste-upgrade-title">Upgrade Required</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div style="padding:24px;text-align:center;">
                    <div style="font-size:48px;margin-bottom:12px;">&#128274;</div>
                    <p id="ste-upgrade-msg" style="color:#666;font-size:14px;line-height:1.6;margin:0 0 20px;">This feature is available on the <strong>Pro</strong> plan and above.</p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=ste-license' ) ); ?>" class="ste-apply" style="display:inline-block;padding:10px 28px;text-decoration:none;font-size:14px;">Upgrade Now</a>
                    <p style="color:#999;font-size:12px;margin-top:12px;">Current plan: <strong><?php echo esc_html( $plan_data['label'] ); ?></strong></p>
                </div>
            </div>
        </div>
        <?php
