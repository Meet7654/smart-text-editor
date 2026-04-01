/**
 * Smart Text Editor — Frontend Animation Controller
 * Uses IntersectionObserver to trigger scroll animations.
 * Created by Meet Patel
 */
( function() {
    'use strict';

    function init() {
        var elements = document.querySelectorAll( '[data-ste-anim]' );
        if ( ! elements.length ) return;

        if ( 'IntersectionObserver' in window ) {
            var observer = new IntersectionObserver( function( entries ) {
                entries.forEach( function( entry ) {
                    if ( entry.isIntersecting ) {
                        trigger( entry.target );
                        observer.unobserve( entry.target );
                    }
                } );
            }, { threshold: 0.15 } );

            elements.forEach( function( el ) { observer.observe( el ); } );
        } else {
            /* No IntersectionObserver — show everything immediately */
            elements.forEach( function( el ) {
                el.classList.add( 'ste-anim-no-observer' );
            } );
        }
    }

    function trigger( el ) {
        var type = el.getAttribute( 'data-ste-anim' );
        var dur  = parseFloat( el.getAttribute( 'data-ste-anim-dur' ) ) || 0.6;
        if ( ! type ) return;

        /* The animation keyframes handle opacity (0 → 1) and transforms.
           fill-mode 'both' keeps the final state after animation ends.
           We also explicitly set opacity:1 on animationend to prevent
           the CSS rule [data-ste-anim]{opacity:0} from hiding it again. */
        el.style.animation = 'ste-' + type + ' ' + dur + 's ease both';
        el.addEventListener( 'animationend', function() { el.style.opacity = '1'; }, { once: true } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
} )();
