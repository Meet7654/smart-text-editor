=== Smart Text Editor ===
Contributors: meetpatel
Tags: editor, text editor, rich text, custom editor, page builder
Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.3.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A custom rich text editor — no Gutenberg, no TinyMCE. Gradient text, 3D effects, glow, animations, 48 style presets, and HTML/CSS export.

== Description ==

Smart Text Editor replaces the default WordPress editor with a custom-built contenteditable editor on all posts and pages. It provides a complete set of formatting tools plus advanced text styling effects that are not available in the standard WordPress editor.

= Rich Text Editing =
* Bold, italic, underline, strikethrough, subscript, superscript
* Headings (H1–H6), paragraphs, preformatted text
* Bullet lists, numbered lists, blockquotes
* Text alignment — left, center, right, justify
* Font family picker (15 fonts) and font size selector (8px–96px)
* Text color and highlight/background color
* Indent, outdent, undo, redo

= Insert & Media =
* Insert links and images from the WordPress Media Library
* Drag & drop images directly from desktop
* Insert tables with customizable grid, border style, padding, and header row
* Table context toolbar — add/delete rows & columns, merge/split cells, cell background and border colors
* Horizontal rule, Read More tag, special characters (65+ symbols)

= Style Effects =
* **Text Shadow** — adjustable X, Y, blur, and color
* **Gradient Text** — 3-color gradient with angle control
* **3D Text** — depth and color for layered shadow effect
* **Glow** — color and intensity for neon-style glow
* **Copy / Paste Style** — copy styles from one selection and apply to another
* **Clear Formatting** — strip all formatting in one click

= Scroll Animations =
* 11 animation types — Fade, Slide Up, Slide Down, Slide Left, Slide Right, Reveal, Bounce, Zoom In, Zoom Out, Flip, Typewriter
* Adjustable duration (0.1s – 3s)
* Preview button to test animations in the editor
* Powered by IntersectionObserver for performance

= 48 Built-in Style Presets =
* Glow & Neon — Neon Glow, Neon Pink, Neon Orange, Cyber Green, Frosted, Fire, Electric Blue
* Gradients — Gold Luxury, Ocean Wave, Sunset, Candy, Rainbow, Nature, Rose Gold, Aurora, Midnight, Peach, Berry, Chrome
* 3D & Shadow — Retro Pop, Deep Shadow, Long Shadow, Emboss, Letterpress, Comic 3D
* Outline & Stroke — Outline, Outline Red, Outline Gold, Thick Outline
* Typography — Elegant Serif, Typewriter, Modern Clean, Bold Impact, Handwritten, Small Caps
* Colors — Blood Red, Royal Purple, Forest Green, Coral, Steel Blue
* Special — Highlight Yellow/Green/Blue, Tag Dark/Blue, Underline Accent, Strikethrough Red, Wavy Underline
* Plus save your own custom presets

= Tools & Extras =
* Floating toolbar on text selection for quick formatting
* HTML source code view
* HTML/CSS export
* Paste as plain text toggle
* Fullscreen distraction-free mode (F11)
* Word and character count
* `[ste_doc id="123"]` shortcode to embed styled content from another post

= Keyboard Shortcuts =
* Ctrl+B — Bold
* Ctrl+I — Italic
* Ctrl+U — Underline
* Ctrl+K — Insert Link
* Ctrl+S — Save / Publish
* Ctrl+Z / Ctrl+Y — Undo / Redo
* Ctrl+Shift+D — Strikethrough
* Ctrl+Shift+X — Clear Formatting
* Ctrl+Shift+7 / 8 — Ordered / Unordered List
* Tab / Shift+Tab — Navigate table cells
* F11 — Fullscreen toggle
* Escape — Close modal

== Installation ==

1. Upload the `smart-text-editor` folder to the `/wp-content/plugins/` directory, or install through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Pages > Add New** or **Posts > Add New** — you will see the Smart Text Editor.
4. Type your content, select text, and use the style controls below the editor.
5. Click **Publish** or **Update** as usual.

== Frequently Asked Questions ==

= Does this replace Gutenberg? =
Yes. When activated, Smart Text Editor disables both Gutenberg and the Classic Editor on posts and pages, replacing them with its own custom editor.

= Will my existing content be preserved? =
Yes. Your existing post and page content will appear in the Smart Text Editor. The content is stored as standard HTML in the WordPress database.

= Does it work with custom post types? =
Currently it works on posts and pages. Support for custom post types can be added by extending the `$post_types` array in the code.

= Are the style effects visible on the frontend? =
Yes. All styles (gradients, shadows, glow, etc.) are saved as inline CSS and render on the frontend. Scroll animations are powered by a lightweight frontend script.

= Can I use this alongside other page builders? =
Smart Text Editor replaces the default editor. It is not designed to run alongside Gutenberg or other page builders on the same post type.

== Screenshots ==

1. The editor toolbar with formatting controls
2. Style effects bar — Shadow, Gradient, 3D, Glow tabs
3. Animation panel with preview
4. Style presets panel
5. Table insertion with grid picker
6. Floating toolbar on text selection
7. HTML source code view
8. Fullscreen editing mode

== Changelog ==

= 1.3.0 =
* Added 48 built-in style presets across 7 categories
* Added 6 new animation types: Slide Down, Slide Right, Zoom In, Zoom Out, Flip, Typewriter
* Added animation preview in editor
* Added drag & drop image support
* Added word and character count
* Added Escape key to close modals
* Added Ctrl+S save shortcut
* Added [ste_doc] shortcode
* Fixed animation display (transition conflict, inline-block for transforms)
* Fixed frontend CSS enqueue timing
* Fixed table border rendering on frontend
* Fixed floating toolbar positioning
* Security: added nonce verification, improved HTML escaping
* Removed AI Style feature

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.3.0 =
Major update with 48 presets, new animations, security fixes, and frontend rendering improvements.
