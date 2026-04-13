<footer class="ste-site-footer">
    <div class="ste-container">
        <div class="ste-footer-grid">
            <div class="ste-footer-brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ste-logo">
                    <span class="ste-logo-icon">
                        <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                            <rect width="32" height="32" rx="8" fill="url(#flogo-grad)"/>
                            <path d="M8 10h16M8 16h12M8 22h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                            <defs><linearGradient id="flogo-grad" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#a855f7"/></linearGradient></defs>
                        </svg>
                    </span>
                    <span class="ste-logo-text">Smart<strong>TextEditor</strong></span>
                </a>
                <p>A powerful rich text editor with 50+ fonts, gradient text, 3D effects, glow, animations, and style presets. No dependencies. Pure craftsmanship.</p>
            </div>
            <div class="ste-footer-col">
                <h4>Features</h4>
                <a href="#features">Rich Text Editing</a>
                <a href="#fonts">50+ Google Fonts</a>
                <a href="#styles">Style Effects</a>
                <a href="#editor">Live Editor</a>
                <a href="#pricing">Pricing</a>
            </div>
            <div class="ste-footer-col">
                <h4>Effects</h4>
                <a href="#styles">Gradient Text</a>
                <a href="#styles">3D Text</a>
                <a href="#styles">Glow Effects</a>
                <a href="#styles">Animations</a>
            </div>
            <div class="ste-footer-col">
                <h4>Resources</h4>
                <a href="<?php echo esc_url( admin_url() ); ?>">WordPress Admin</a>
                <a href="#editor">Try It Now</a>
            </div>
            <div class="ste-footer-col">
                <h4>Legal</h4>
                <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a>
                <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">Terms &amp; Conditions</a>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact Us</a>
            </div>
        </div>
        <div class="ste-footer-bottom">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Smart Text Editor. Created by Meet Patel. &nbsp;&middot;&nbsp; <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:#777;">Privacy Policy</a> &nbsp;&middot;&nbsp; <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" style="color:#777;">Terms &amp; Conditions</a></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
