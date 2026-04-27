<?php
/**
 * REST API endpoints for the Smart Keyboard mobile app.
 *
 * Register in smart-text-editor.php:
 *   require_once STE_PLUGIN_DIR . 'includes/class-ste-keyboard-api.php';
 *   STE_Keyboard_API::init();
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class STE_Keyboard_API {

    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
    }

    public static function register_routes() {
        $ns = 'ste/v1';

        // Text suggestions (prefix-based)
        register_rest_route( $ns, '/suggest', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_suggest' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'prefix'  => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
                'context' => array( 'default'  => '', 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        // AI rewrite (requires valid token)
        register_rest_route( $ns, '/rewrite', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_rewrite' ),
            'permission_callback' => array( __CLASS__, 'check_token' ),
            'args'                => array(
                'text' => array( 'required' => true, 'sanitize_callback' => 'sanitize_textarea_field' ),
                'tone' => array( 'default'  => 'professional', 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        // Style presets (plan-gated)
        register_rest_route( $ns, '/presets', array(
            'methods'             => 'GET',
            'callback'            => array( __CLASS__, 'handle_presets' ),
            'permission_callback' => '__return_true',
        ) );

        // Keyboard auth — exchange license key for JWT
        register_rest_route( $ns, '/auth/keyboard', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_auth' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'licenseKey' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );

        // Token refresh — re-issue JWT for an existing valid license key
        register_rest_route( $ns, '/auth/refresh', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_auth' ), // same logic
            'permission_callback' => '__return_true',
            'args'                => array(
                'licenseKey' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
            ),
        ) );
    }

    // ── Handlers ──────────────────────────────────────────────────────────────

    public static function handle_suggest( WP_REST_Request $request ) {
        // Rate limit: max 60 requests per minute per IP
        if ( ! self::check_rate_limit( 'suggest', 60, 60 ) ) {
            return new WP_Error( 'rate_limited', 'Too many requests.', array( 'status' => 429 ) );
        }

        $prefix = strtolower( $request->get_param( 'prefix' ) );
        if ( strlen( $prefix ) < 2 ) {
            return rest_ensure_response( array( 'suggestions' => array() ) );
        }

        // Simple prefix match against a stored word list.
        // In production: replace with a proper n-gram model or OpenAI API call.
        $words = self::get_word_list();
        $matches = array_values( array_filter( $words, function( $w ) use ( $prefix ) {
            return strpos( $w, $prefix ) === 0;
        } ) );
        usort( $matches, function( $a, $b ) { return strlen( $a ) - strlen( $b ); } );

        return rest_ensure_response( array( 'suggestions' => array_slice( $matches, 0, 3 ) ) );
    }

    public static function handle_rewrite( WP_REST_Request $request ) {
        $text = $request->get_param( 'text' );
        $tone = $request->get_param( 'tone' );

        // Placeholder — integrate OpenAI / Claude here.
        // Example: call OpenAI chat completions with a system prompt.
        $rewritten = self::ai_rewrite( $text, $tone );

        return rest_ensure_response( array( 'rewritten' => $rewritten ) );
    }

    public static function handle_presets( WP_REST_Request $request ) {
        $plan = self::get_plan_from_token( $request );

        // Mirror the DEFAULT_PRESETS from editor.js — only return plan-allowed ones.
        $all_presets = self::get_all_presets();
        $allowed = array_filter( $all_presets, function( $p ) use ( $plan ) {
            if ( 'business' === $plan ) return true;
            if ( 'pro' === $plan ) return in_array( $p['plan'], array( 'free', 'pro' ), true );
            return 'free' === $p['plan'];
        } );

        return rest_ensure_response( array_values( $allowed ) );
    }

    public static function handle_auth( WP_REST_Request $request ) {
        // Rate limit: max 10 auth attempts per minute per IP
        if ( ! self::check_rate_limit( 'auth', 10, 60 ) ) {
            return new WP_Error( 'rate_limited', 'Too many requests.', array( 'status' => 429 ) );
        }

        $key  = strtoupper( trim( $request->get_param( 'licenseKey' ) ) );
        $plan = STE_License::plan_from_key( $key );

        if ( ! $plan || ! STE_License::validate_key( $key, $plan ) ) {
            return new WP_Error( 'invalid_key', 'Invalid license key.', array( 'status' => 401 ) );
        }

        $expires_at = gmdate( 'Y-m-d H:i:s', strtotime( '+30 days' ) );
        $token      = self::generate_jwt( $key, $plan, $expires_at );

        return rest_ensure_response( array(
            'token'     => $token,
            'plan'      => $plan,
            'expiresAt' => $expires_at,
        ) );
    }

    // ── Auth middleware ───────────────────────────────────────────────────────

    public static function check_token( WP_REST_Request $request ) {
        $auth = $request->get_header( 'authorization' );
        if ( ! $auth || strpos( $auth, 'Bearer ' ) !== 0 ) return false;
        $token = substr( $auth, 7 );
        return self::verify_jwt( $token );
    }

    // ── JWT helpers (HMAC-SHA256, no external library needed) ────────────────

    private static function generate_jwt( $license_key, $plan, $expires_at ) {
        $header  = self::base64url_encode( wp_json_encode( array( 'alg' => 'HS256', 'typ' => 'JWT' ) ) );
        $payload = self::base64url_encode( wp_json_encode( array(
            'sub' => $license_key,
            'pln' => $plan,
            'exp' => strtotime( $expires_at ),
            'iat' => time(),
        ) ) );
        $sig = self::base64url_encode( hash_hmac( 'sha256', "$header.$payload", self::get_jwt_secret(), true ) );
        return "$header.$payload.$sig";
    }

    private static function verify_jwt( $token ) {
        $parts = explode( '.', $token );
        if ( count( $parts ) !== 3 ) return false;
        list( $header, $payload, $sig ) = $parts;
        $expected = self::base64url_encode( hash_hmac( 'sha256', "$header.$payload", self::get_jwt_secret(), true ) );
        if ( ! hash_equals( $expected, $sig ) ) return false;
        $data = json_decode( self::base64url_decode( $payload ), true );
        return isset( $data['exp'] ) && time() < $data['exp'];
    }

    private static function get_jwt_secret() {
        $secret = get_option( 'ste_keyboard_jwt_secret', '' );
        if ( empty( $secret ) ) {
            $secret = bin2hex( random_bytes( 32 ) );
            update_option( 'ste_keyboard_jwt_secret', $secret, false );
        }
        return $secret;
    }

    private static function base64url_encode( $data ) {
        return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
    }

    private static function base64url_decode( $data ) {
        return base64_decode( strtr( $data, '-_', '+/' ) . str_repeat( '=', 3 - ( 3 + strlen( $data ) ) % 4 ) );
    }

    // ── Rate limiting (transient-based, per IP) ───────────────────────────────

    private static function check_rate_limit( $action, $max_requests, $window_seconds ) {
        $ip  = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
        $key = 'ste_rl_' . $action . '_' . md5( $ip );
        $count = (int) get_transient( $key );
        if ( $count >= $max_requests ) return false;
        set_transient( $key, $count + 1, $window_seconds );
        return true;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private static function get_plan_from_token( WP_REST_Request $request ) {
        $auth = $request->get_header( 'authorization' );
        if ( ! $auth || strpos( $auth, 'Bearer ' ) !== 0 ) return 'free';
        $token  = substr( $auth, 7 );
        $parts  = explode( '.', $token );
        if ( count( $parts ) !== 3 ) return 'free';
        $data = json_decode( self::base64url_decode( $parts[1] ), true );
        return isset( $data['pln'] ) ? sanitize_text_field( $data['pln'] ) : 'free';
    }

    private static function ai_rewrite( $text, $tone ) {
        // Stub — replace with real AI API call.
        // Example OpenAI integration:
        // $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [...]);
        return $text; // passthrough until AI is wired up
    }

    private static function get_word_list() {
        // Mirrors the seed list from Android/iOS SuggestionEngine
        return array(
            'the','be','to','of','and','a','in','that','have','it',
            'for','not','on','with','he','as','you','do','at','this',
            'but','his','by','from','they','we','say','her','she','or',
            'will','my','one','all','would','there','their','what','so',
            'up','out','if','about','who','get','which','go','me','when',
            'make','can','like','time','no','just','him','know','take',
            'people','into','year','your','good','some','could','them',
            'see','other','than','then','now','look','only','come','its',
            'over','think','also','back','after','use','two','how','our',
            'work','first','well','way','even','new','want','because','any',
            'hello','help','here','home','hope','hour','house','however',
            'smart','keyboard','text','editor','style','gradient','shadow',
            'glow','preset','font','color','design','write','word',
            'please','thank','thanks','sorry','yes','yeah','okay','sure',
            'maybe','really','actually','definitely','probably','beautiful',
            'wonderful','amazing','awesome','perfect','great','important',
            'interesting','different','possible','available','information',
            'question','answer','problem','solution','message','email',
            'phone','address','meeting','schedule','project','deadline',
            'update','review','feedback','report','document','send','receive',
            'reply','forward','create','delete','edit','save','open','close',
            'today','tomorrow','yesterday','morning','afternoon','evening',
            'monday','tuesday','wednesday','thursday','friday','saturday','sunday',
        );
    }

    private static function get_all_presets() {
        // Full 48-preset list mirroring DEFAULT_PRESETS in editor.js
        return array(
            // ── Glow & Neon ──
            array( 'id' => 'neon-glow',    'name' => 'Neon Glow',    'plan' => 'pro',
                   'css' => 'color:#0ff;text-shadow:0 0 7px #0ff,0 0 14px #0ff,0 0 28px #0ff;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'cyber-green',  'name' => 'Cyber Green',  'plan' => 'pro',
                   'css' => 'color:#00ff41;text-shadow:0 0 5px #00ff41,0 0 15px #00ff41;font-family:monospace;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'frosted',      'name' => 'Frosted',      'plan' => 'pro',
                   'css' => 'color:#a8d8ea;text-shadow:0 0 10px rgba(168,216,234,0.8),0 0 20px rgba(168,216,234,0.4);font-weight:600;', 'cls' => 'ste-styled' ),
            array( 'id' => 'fire',         'name' => 'Fire',         'plan' => 'pro',
                   'css' => 'color:#ff6600;text-shadow:0 0 5px #ff6600,0 0 12px #ff3300,0 0 24px #ff0000;font-weight:800;', 'cls' => 'ste-styled' ),
            array( 'id' => 'neon-pink',    'name' => 'Neon Pink',    'plan' => 'pro',
                   'css' => 'color:#ff00ff;text-shadow:0 0 7px #ff00ff,0 0 14px #ff00ff;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'neon-orange',  'name' => 'Neon Orange',  'plan' => 'pro',
                   'css' => 'color:#ff8c00;text-shadow:0 0 7px #ff8c00,0 0 14px #ff8c00;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'electric-blue','name' => 'Electric Blue','plan' => 'pro',
                   'css' => 'color:#00bfff;text-shadow:0 0 7px #00bfff,0 0 14px #00bfff,0 0 28px #00bfff;font-weight:700;', 'cls' => 'ste-styled' ),
            // ── Gradients ──
            array( 'id' => 'gold-luxury',  'name' => 'Gold Luxury',  'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(135deg,#bf953f,#fcf6ba,#b38728,#fbf5b7,#aa771c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;font-family:Georgia,serif;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'ocean-wave',   'name' => 'Ocean Wave',   'plan' => 'free',
                   'css' => 'background-image:linear-gradient(90deg,#0077b6,#00b4d8,#90e0ef);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'sunset',       'name' => 'Sunset',       'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(90deg,#ff6b6b,#feca57,#ff9ff3);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'candy',        'name' => 'Candy',        'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(90deg,#f093fb,#f5576c,#4facfe);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:800;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'rainbow',      'name' => 'Rainbow',      'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(90deg,#ff0000,#ff8800,#ffff00,#00cc00,#0066ff,#8800ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:800;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'nature',       'name' => 'Nature',       'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(90deg,#2d6a4f,#52b788,#d8f3dc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:600;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'rose-gold',    'name' => 'Rose Gold',    'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(135deg,#b76e79,#e8c4c8,#b76e79);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'aurora',       'name' => 'Aurora',       'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(90deg,#00c9ff,#92fe9d,#f0ff00,#ff6ec7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'midnight',     'name' => 'Midnight',     'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(135deg,#0f0c29,#302b63,#24243e);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'peach',        'name' => 'Peach',        'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(90deg,#ffecd2,#fcb69f);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:700;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'berry',        'name' => 'Berry',        'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(90deg,#8e2de2,#4a00e0,#e100ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:800;', 'cls' => 'ste-styled ste-gradient-text' ),
            array( 'id' => 'chrome',       'name' => 'Chrome',       'plan' => 'pro',
                   'css' => 'background-image:linear-gradient(180deg,#e0e0e0,#ffffff,#a0a0a0,#e0e0e0);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;font-weight:800;', 'cls' => 'ste-styled ste-gradient-text' ),
            // ── 3D & Shadow ──
            array( 'id' => 'retro-pop',    'name' => 'Retro Pop',    'plan' => 'pro',
                   'css' => 'color:#ff6f61;text-shadow:3px 3px 0 #ffc857,6px 6px 0 #2ec4b6;font-weight:800;', 'cls' => 'ste-styled' ),
            array( 'id' => 'deep-shadow',  'name' => 'Deep Shadow',  'plan' => 'pro',
                   'css' => 'color:#1a1a2e;text-shadow:2px 2px 0 #16213e,4px 4px 0 #0f3460,6px 6px 0 #533483;font-weight:800;', 'cls' => 'ste-styled' ),
            array( 'id' => 'long-shadow',  'name' => 'Long Shadow',  'plan' => 'pro',
                   'css' => 'color:#e74c3c;text-shadow:1px 1px 0 #c0392b,2px 2px 0 #c0392b,3px 3px 0 #c0392b,4px 4px 0 #c0392b,5px 5px 0 #c0392b,6px 6px 0 #c0392b,7px 7px 0 #c0392b,8px 8px 0 #c0392b;font-weight:800;', 'cls' => 'ste-styled' ),
            array( 'id' => 'emboss',       'name' => 'Emboss',       'plan' => 'pro',
                   'css' => 'color:#ccc;text-shadow:-1px -1px 0 #fff,1px 1px 0 #333;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'letterpress',  'name' => 'Letterpress',  'plan' => 'pro',
                   'css' => 'color:#222;text-shadow:0 1px 0 rgba(255,255,255,0.6);font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'comic-3d',     'name' => 'Comic 3D',     'plan' => 'pro',
                   'css' => 'color:#f1c40f;text-shadow:2px 2px 0 #e67e22,4px 4px 0 #d35400,-1px -1px 0 #000,1px -1px 0 #000,-1px 1px 0 #000;font-weight:900;', 'cls' => 'ste-styled' ),
            // ── Outline & Stroke ──
            array( 'id' => 'outline',      'name' => 'Outline',      'plan' => 'pro',
                   'css' => 'color:transparent;-webkit-text-stroke:2px #4f46e5;font-weight:800;', 'cls' => 'ste-styled' ),
            array( 'id' => 'outline-red',  'name' => 'Outline Red',  'plan' => 'pro',
                   'css' => 'color:transparent;-webkit-text-stroke:2px #e74c3c;font-weight:800;', 'cls' => 'ste-styled' ),
            array( 'id' => 'outline-gold', 'name' => 'Outline Gold', 'plan' => 'pro',
                   'css' => 'color:transparent;-webkit-text-stroke:2px #f59e0b;font-weight:800;', 'cls' => 'ste-styled' ),
            array( 'id' => 'thick-outline','name' => 'Thick Outline','plan' => 'pro',
                   'css' => 'color:transparent;-webkit-text-stroke:3px #1a1a2e;font-weight:900;', 'cls' => 'ste-styled' ),
            // ── Typography ──
            array( 'id' => 'elegant-serif','name' => 'Elegant Serif','plan' => 'free',
                   'css' => 'color:#2c3e50;font-family:Georgia,serif;font-style:italic;letter-spacing:2px;', 'cls' => 'ste-styled' ),
            array( 'id' => 'typewriter',   'name' => 'Typewriter',   'plan' => 'free',
                   'css' => 'color:#333;font-family:"Courier New",monospace;letter-spacing:1px;font-weight:400;', 'cls' => 'ste-styled' ),
            array( 'id' => 'modern-clean', 'name' => 'Modern Clean', 'plan' => 'pro',
                   'css' => 'color:#222;font-family:"Segoe UI",sans-serif;font-weight:300;letter-spacing:3px;text-transform:uppercase;', 'cls' => 'ste-styled' ),
            array( 'id' => 'bold-impact',  'name' => 'Bold Impact',  'plan' => 'pro',
                   'css' => 'color:#111;font-family:Impact,sans-serif;text-transform:uppercase;letter-spacing:2px;', 'cls' => 'ste-styled' ),
            array( 'id' => 'handwritten',  'name' => 'Handwritten',  'plan' => 'pro',
                   'css' => 'color:#5c4033;font-family:cursive;font-size:1.1em;font-weight:400;', 'cls' => 'ste-styled' ),
            array( 'id' => 'small-caps',   'name' => 'Small Caps',   'plan' => 'pro',
                   'css' => 'color:#34495e;font-family:Georgia,serif;font-variant:small-caps;letter-spacing:1px;font-weight:600;', 'cls' => 'ste-styled' ),
            // ── Colors ──
            array( 'id' => 'blood-red',    'name' => 'Blood Red',    'plan' => 'pro',
                   'css' => 'color:#c0392b;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'royal-purple', 'name' => 'Royal Purple', 'plan' => 'pro',
                   'css' => 'color:#7d3c98;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'forest-green', 'name' => 'Forest Green', 'plan' => 'pro',
                   'css' => 'color:#1e8449;font-weight:700;', 'cls' => 'ste-styled' ),
            array( 'id' => 'coral',        'name' => 'Coral',        'plan' => 'pro',
                   'css' => 'color:#ff6b6b;font-weight:600;', 'cls' => 'ste-styled' ),
            array( 'id' => 'steel-blue',   'name' => 'Steel Blue',   'plan' => 'pro',
                   'css' => 'color:#2e86c1;font-weight:600;', 'cls' => 'ste-styled' ),
            // ── Special ──
            array( 'id' => 'highlight-yellow','name' => 'Highlight Yellow','plan' => 'free',
                   'css' => 'background-color:#fff176;color:#333;padding:2px 6px;font-weight:600;', 'cls' => 'ste-styled' ),
            array( 'id' => 'highlight-green', 'name' => 'Highlight Green', 'plan' => 'pro',
                   'css' => 'background-color:#b9f6ca;color:#1b5e20;padding:2px 6px;font-weight:600;', 'cls' => 'ste-styled' ),
            array( 'id' => 'highlight-blue',  'name' => 'Highlight Blue',  'plan' => 'pro',
                   'css' => 'background-color:#bbdefb;color:#0d47a1;padding:2px 6px;font-weight:600;', 'cls' => 'ste-styled' ),
            array( 'id' => 'tag-dark',     'name' => 'Tag Dark',     'plan' => 'free',
                   'css' => 'background-color:#1a1a2e;color:#eee;padding:2px 8px;border-radius:3px;font-family:monospace;font-size:0.9em;', 'cls' => 'ste-styled' ),
            array( 'id' => 'tag-blue',     'name' => 'Tag Blue',     'plan' => 'free',
                   'css' => 'background-color:#e3f2fd;color:#1565c0;padding:2px 8px;border-radius:3px;font-weight:600;font-size:0.9em;', 'cls' => 'ste-styled' ),
            array( 'id' => 'underline-accent','name' => 'Underline Accent','plan' => 'pro',
                   'css' => 'color:#333;border-bottom:3px solid #4f46e5;padding-bottom:2px;font-weight:600;', 'cls' => 'ste-styled' ),
            array( 'id' => 'strikethrough-red','name' => 'Strikethrough Red','plan' => 'pro',
                   'css' => 'color:#999;text-decoration:line-through;text-decoration-color:#e74c3c;', 'cls' => 'ste-styled' ),
            array( 'id' => 'wavy-underline','name' => 'Wavy Underline','plan' => 'pro',
                   'css' => 'color:#333;text-decoration:underline wavy #e74c3c;font-weight:500;', 'cls' => 'ste-styled' ),
        );
    }
}
