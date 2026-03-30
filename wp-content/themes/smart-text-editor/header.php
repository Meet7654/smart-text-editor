<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="ste-site-header">
    <div class="ste-container">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ste-logo">
            <span class="ste-logo-icon">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="url(#logo-grad)"/>
                    <path d="M8 10h16M8 16h12M8 22h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                    <defs><linearGradient id="logo-grad" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#a855f7"/></linearGradient></defs>
                </svg>
            </span>
            <span class="ste-logo-text">Smart<strong>TextEditor</strong></span>
        </a>
        <nav class="ste-nav">
            <a href="#features" class="ste-nav-link">Features</a>
            <a href="#editor" class="ste-nav-link">Try Editor</a>
            <a href="#fonts" class="ste-nav-link">Fonts</a>
            <a href="#styles" class="ste-nav-link">Styles</a>
            <a href="#pricing" class="ste-nav-link">Pricing</a>
            <a href="#editor" class="ste-nav-btn">Start Writing</a>
        </nav>
        <button class="ste-mobile-toggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
