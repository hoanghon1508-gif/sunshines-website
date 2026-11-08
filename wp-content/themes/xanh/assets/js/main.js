/**
 * XANH theme – JS thuần, không phụ thuộc jQuery.
 */
( function () {
	'use strict';

	/* ---------- Menu mobile ---------- */
	var nav = document.getElementById( 'site-nav' );
	var toggle = document.querySelector( '.navbar__toggle' );
	var overlay = document.querySelector( '.nav-overlay' );
	var closeBtn = document.querySelector( '.site-nav__close' );

	function setNav( open ) {
		if ( ! nav ) {
			return;
		}
		nav.classList.toggle( 'is-open', open );
		if ( toggle ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
		if ( overlay ) {
			overlay.hidden = ! open;
		}
		document.body.style.overflow = open ? 'hidden' : '';
		if ( open && closeBtn ) {
			closeBtn.focus();
		} else if ( ! open && toggle ) {
			toggle.focus();
		}
	}
	if ( toggle ) {
		toggle.addEventListener( 'click', function () { setNav( true ); } );
	}
	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', function () { setNav( false ); } );
	}
	if ( overlay ) {
		overlay.addEventListener( 'click', function () { setNav( false ); } );
	}
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && nav && nav.classList.contains( 'is-open' ) ) {
			setNav( false );
		}
	} );

	/* ---------- Ô tìm kiếm ---------- */
	var searchBtn = document.querySelector( '[data-search-toggle]' );
	var searchBox = document.getElementById( 'header-search' );
	if ( searchBtn && searchBox ) {
		searchBtn.addEventListener( 'click', function () {
			var open = searchBox.hidden;
			searchBox.hidden = ! open;
			searchBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open ) {
				var input = searchBox.querySelector( 'input[type=search]' );
				if ( input ) {
					input.focus();
				}
			}
		} );
	}

	/* ---------- Slider banner trang chủ ---------- */
	document.querySelectorAll( '[data-slider]' ).forEach( function ( slider ) {
		var slides = slider.querySelectorAll( '.hero__slide' );
		var dots = slider.querySelectorAll( '.hero__dot' );
		if ( slides.length < 2 ) {
			return;
		}
		var current = 0;
		var timer;
		var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		function go( i ) {
			slides[ current ].hidden = true;
			slides[ current ].classList.remove( 'is-active' );
			dots[ current ] && dots[ current ].classList.remove( 'is-active' );
			current = ( i + slides.length ) % slides.length;
			slides[ current ].hidden = false;
			slides[ current ].classList.add( 'is-active' );
			dots[ current ] && dots[ current ].classList.add( 'is-active' );
		}
		function start() {
			if ( ! reduce ) {
				stop();
				timer = setInterval( function () { go( current + 1 ); }, 6000 );
			}
		}
		function stop() {
			clearInterval( timer );
		}

		dots.forEach( function ( dot ) {
			dot.addEventListener( 'click', function () {
				go( parseInt( dot.getAttribute( 'data-slide' ), 10 ) );
				start();
			} );
		} );
		slider.addEventListener( 'mouseenter', stop );
		slider.addEventListener( 'mouseleave', start );
		slider.addEventListener( 'focusin', stop );

		// Vuốt trên điện thoại.
		var x0 = null;
		slider.addEventListener( 'touchstart', function ( e ) { x0 = e.touches[ 0 ].clientX; }, { passive: true } );
		slider.addEventListener( 'touchend', function ( e ) {
			if ( null === x0 ) {
				return;
			}
			var dx = e.changedTouches[ 0 ].clientX - x0;
			if ( Math.abs( dx ) > 40 ) {
				go( current + ( dx < 0 ? 1 : -1 ) );
				start();
			}
			x0 = null;
		}, { passive: true } );

		start();
	} );

	/* ---------- Bộ lọc sản phẩm trên mobile ---------- */
	var filters = document.querySelector( '.shop-filters' );
	if ( filters ) {
		var ft = filters.querySelector( '.shop-filters__toggle' );
		ft && ft.addEventListener( 'click', function () {
			var open = ! filters.classList.contains( 'is-open' );
			filters.classList.toggle( 'is-open', open );
			ft.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		} );
	}
}() );
