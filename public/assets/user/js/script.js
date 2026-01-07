/* -----------------------------------------------------------------------------



File:           JS Core
Version:        1.0
Last change:    00/00/00 
-------------------------------------------------------------------------------- */
;(function($) {

	"use strict";

	gsap.config({
		nullTargetWarn: false,
	});

// lenis-smooth-scroll
	const lenis = new Lenis({
		duration: 1.3, 
		easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), 
		direction: 'vertical', 
		smooth: true, 
		smoothTouch: false, 
	});

	function raf(time) {
		lenis.raf(time);
		requestAnimationFrame(raf);
	}
	requestAnimationFrame(raf);

	if($('.wow').length){
		var wow = new WOW({
			boxClass:     'wow',
			animateClass: 'animated',
			offset:       0,
			mobile:       true,
			live:         true
		});
		wow.init();
	};
	jQuery('.video_box').magnificPopup({
		disableOn: 200,
		type: 'iframe',
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,
		fixedContentPos: false,
	});
	function TXTheaderSticky() {
		var $window = $(window);
		var lastScrollTop = 0;
		var $header = $('.txa_sticky_header');
		var headerHeight = $header.outerHeight() + 30;

		$window.scroll(function () {
			var windowTop = $window.scrollTop();

			if (windowTop >= headerHeight) {
				$header.addClass('txa_sticky');
			} else {
				$header.removeClass('txa_sticky');
				$header.removeClass('txa_sticky_show');
			}

			if ($header.hasClass('txa_sticky')) {
				if (windowTop < lastScrollTop) {
					$header.addClass('txa_sticky_show');
				} else {
					$header.removeClass('txa_sticky_show');
				}
			}

			lastScrollTop = windowTop;
		});
	}
	TXTheaderSticky();
	$(window).on("scroll", function() {
		if ($(this).scrollTop() > 200) {
			$('.ed-scrollup').fadeIn();
		} else {
			$('.ed-scrollup').fadeOut();
		}
	});
	$('.ed-scrollup').on("click", function()  {
		$("html, body").animate({
			scrollTop: 0
		}, 800);
		return false;
	});
	$('.cart_close_btn, .body-overlay').on('click', function () {
		$('.cart_sidebar').removeClass('active');
		$('.body-overlay').removeClass('active');
	});

	$('.header-cart-btn').on('click', function () {
		$('.cart_sidebar').addClass('active');
		$('.body-overlay').addClass('active');
	});
	$('.open_mobile_menu').on("click", function() {
		$('.mobile_menu_wrap').toggleClass("mobile_menu_on");
	});
	$('.open_mobile_menu').on('click', function () {
		$('body').toggleClass('mobile_menu_overlay_on');
	});
	jQuery(".mobile-main-navigation li.dropdown").append('<span class="dropdown-btn"><i class="fas fa-angle-down"></i></span>'),
	jQuery(".mobile-main-navigation li .dropdown-btn").on("click", function () {
		jQuery(this).hasClass("active")
		? (jQuery(this).closest("ul").find(".dropdown-btn.active").toggleClass("active"), jQuery(this).closest("ul").find(".dropdown-menu.active").toggleClass("active").slideToggle())
		: (jQuery(this).closest("ul").find(".dropdown-btn.active").toggleClass("active"),
			jQuery(this).closest("ul").find(".dropdown-menu.active").toggleClass("active").slideToggle(),
			jQuery(this).toggleClass("active"),
			jQuery(this).parent().find("> .dropdown-menu").toggleClass("active"),
			jQuery(this).parent().find("> .dropdown-menu").slideToggle());
	});
		// search-popup-start
	$('.search_btn_toggle').on('click', function() {
		$('.overlay, .search_box_active').addClass('active');
	});

	$('.overlay, .search_box_close').on('click', function() {
		$('.search_box_active').removeClass('active');
		$('.overlay').removeClass('active');
	});
	jQuery(document).ready(function (o) {
		0 < o(".navSidebar-button").length &&
		o(".navSidebar-button").on("click", function (e) {
			e.preventDefault(), e.stopPropagation(), o(".info-group").addClass("isActive");
		}),
		0 < o(".close-side-widget").length &&
		o(".close-side-widget").on("click", function (e) {
			e.preventDefault(), o(".info-group").removeClass("isActive");
		}),
		o(".xs-sidebar-widget").on("click", function (e) {
			e.stopPropagation();
		})
	});
	if($('.ed-footer-1-menu').length) {
		document.querySelector('.ed-footer-1-menu').addEventListener('click', function(event) {
			const link = event.target.closest('li a');
			if (!link) return;
			event.preventDefault();
			const dropdown = link.nextElementSibling;
			if (!dropdown || !dropdown.classList.contains('has-dropdown')) return;
			document.querySelectorAll('.has-dropdown').forEach(ul => {
				if (ul !== dropdown) ul.style.display = 'none';
			});
			dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
		});
	}
	$('.zoom-gallery').magnificPopup({
		delegate: 'a',
		type: 'image',
		closeOnContentClick: false,
		closeBtnInside: false,
		mainClass: 'mfp-with-zoom mfp-img-mobile',
		gallery: {
			enabled: true
		},
		zoom: {
			enabled: true,
			duration: 300, 
			opener: function(element) {
				return element.find('img');
			}
		}
	});
	$('.marquee-left').marquee({
		gap: 0,
		speed: 60,
		delayBeforeStart: 0,
		direction: 'left',
		duplicated: true,
		pauseOnHover: true,
		startVisible:true,
	});
	// Background Image
	$('[data-background]').each(function() {
		$(this).css('background-image', 'url('+ $(this).attr('data-background') + ')');
	});
	setTimeout(() => {
		var a = document.querySelectorAll(".btn-spin a");
		a.forEach(function (a) {
			a.addEventListener("mouseover", function () {
				var c, b, d;
				!a.classList.contains("animating") &&
				!a.classList.contains("mouseover") &&
				(a.classList.add("animating", "mouseover"),
					(c = a.innerText.split("")),
					setTimeout(function () {
						a.classList.remove("animating");
					}, (c.length + 1) * 50),
					(b = a.dataset.animation),
					b || (b = "ed-spin"),
					(a.innerText = ""),
					c.forEach(function (b) {
						b == " " && (b = "&nbsp;"), (a.innerHTML += '<span class="letter">' + b + "</span>");
					}),
					(d = a.querySelectorAll(".letter")),
					d.forEach(function (a, c) {
						setTimeout(function () {
							a.classList.add(b);
						}, 50 * c);
					}));
			}),
			a.addEventListener("mouseout", function () {
				a.classList.remove("mouseover");
			});
		});
	}, 100);
	// Select option
	$(document).ready(function() {
		$('.ed-option select').niceSelect();
	});
	if($('.ed-split-char').length) {
		var txtSplit = $('.ed-split-char');
		if(txtSplit.length == 0) return; gsap.registerPlugin(SplitText); txtSplit.each(function(index, el) {
			el.split = new SplitText(el, { 
				type: "chars",
				wordsClass: "split-char"
			});
		});
	}
	if($('.ed-split-line').length) {
		var txtSplit = $('.ed-split-line');
		if(txtSplit.length == 0) return; gsap.registerPlugin(SplitText); txtSplit.each(function(index, el) {
			el.split = new SplitText(el, { 
				type: "lines",
				linesClass: "split-line"
			});
		});
	}
	if($('.ed-split-word').length) {
		var txtSplit = $('.ed-split-word');
		if(txtSplit.length == 0) return; gsap.registerPlugin(SplitText); txtSplit.each(function(index, el) {
			el.split = new SplitText(el, { 
				type: "words",
				wordsClass: "split-word"
			});
		});
	}
	// windows-loaded-before-functions
	document.addEventListener("DOMContentLoaded", function () {
		window.addEventListener('load', function(){
			let preloader = document.querySelector("#preloader");
			if (preloader) {
				preloader.classList.add("preloaded");
				setTimeout(function () {
					preloader.remove();
				}, 1000 ) ;

			}
			setTimeout(function() {
				if($(".agt_hero_title, .banner_title, .hero_title_2").length) {
					var AGTTitleAni = $(".agt_hero_title, .banner_title, .hero_title_2");
					if(AGTTitleAni.length == 0) return; gsap.registerPlugin(SplitText); AGTTitleAni.each(function(index, el) {

						el.split = new SplitText(el, { 
							type: "lines,words,chars",
							linesClass: "split-line"
						});

						gsap.set(el, { perspective: 400 });

						if( $(el).hasClass('agt_hero_title') ){
							gsap.set(el.split.chars, {
								x: 100,
								opacity: 0,
							});
						}
						if( $(el).hasClass('agt_hero_title_2') ){
							gsap.set(el.split.words, {
								y: 100,
								scaleY: 0,
								rotateX: "180deg",
								opacity: 0,
							});
						}
						if( $(el).hasClass('agt_hero_title_3') ){
							gsap.set(el.split.chars, {
								x: 100,
								scaleX: 0,
								opacity: 0,
								rotateX: "30deg",
							});
						}
						el.anim = gsap.to(el.split.chars, {
							scrollTrigger: {
								trigger: el,
								start: "top 90%",
								toggleActions: "play reverse play reverse",
								markers: false,
							},
							x: 0,
							y: 0,
							scaleX: 1,
							opacity: 1,
							duration: 1,
							stagger: .03,
							rotateX: "0",
							delay: .1,
							ease: "power3.inOut",
						});
						el.anim = gsap.to(el.split.words, {
							scrollTrigger: {
								trigger: el,
								start: "top 90%",
								toggleActions: "play reverse play reverse",
								markers: false,
							},
							x: 0,
							y: 0,
							scaleY: 1,
							opacity: 1,
							rotateX: "0",
							duration: 1.2,
							stagger: .1,
							ease: "power3.inOut",
						});
					});
				}
				const edh1 = gsap.timeline();
				edh1
				.from(".h1-img-wrap .item-img", {  scale: .3, yPercent: 100, opacity: 0, borderRadius: 500, duration: 1.5,  ease: "power2.inOut" })
				.from(".h1-text-wrap .h1-text-area .h1_slug", {   x: 100, opacity: 0, duration: 2.5,  ease: "elastic.out(1,0.5)" }, "<=.2")
				.from(".h1-text-wrap .h1-search-box", {   y: 100, opacity: 0, duration: 1.5,  ease: "power2.inOut" }, "<=.2")
				.from(".h1-img-wrap .ed-img-shape1", { yPercent: 100, scale: .5, opacity: 0, duration: 2,  ease: "elastic.out(1,0.5)" },"<=.3")
				.from(".h1-img-wrap .ed-img-shape2", { yPercent: 100, scale: .5, opacity: 0, duration: 2.5,  ease: "elastic.out(1,0.5)" },"<=.3")
				.from(".h1-img-wrap .ed-img-shape3", { yPercent: 100, scale: .5, opacity: 0, duration: 2.5,  ease: "elastic.out(1,0.5)" },"<=.3")
				.from(".h1-img-wrap .ed-img-shape4", { yPercent: 100, scale: .5, opacity: 0, duration: 2.5,  ease: "elastic.out(1,0.5)" },"<=.3")
				.from(".h1-img-wrap .ed-img-shape5", { yPercent: 100, scale: .5, opacity: 0, duration: 2.5,  ease: "elastic.out(1,0.5)" },"<=.3")
				.from(".ed-hero-section-1 .ed-h-shape3", { yPercent: -100, duration: 1.5,  ease: "power2.inOut" },"<=.1")
				.from(".ed-hero-section-1 .ed-h-shape1", { yPercent: -100, duration: 2,  ease: "power2.inOut" },"<=.1")
				.from(".ed-hero-section-1 .ed-h-shape2", { yPercent: 100, duration: 2,  ease: "power2.inOut" },"<=.1");
				if($('.edh-text').length) {
					var edtextarea = $(".edh-text");

					if(edtextarea.length == 0) return; gsap.registerPlugin(SplitText); edtextarea.each(function(paragraph) {

						gsap.utils.toArray(".edh-text p").forEach(paragraph => {
							let timeline = gsap.timeline({
								scrollTrigger: {
									trigger: paragraph,
									start: "top 90%",
									end: "bottom 60%",
									toggleActions: "play none none none"
								}
							});
							let splitText = new SplitText(paragraph, { type: "lines" });
							gsap.set(paragraph, { perspective: 400 });
							timeline.from(splitText.lines, {
								opacity: 0,
								rotationX: -80,
								transformOrigin: "top center -50",
								force3D: true,
								duration: 1,
								delay: 0.5,
								stagger: 0.1
							});
						});

					});
				}
				if($(".ed-hs-slide-active").length) {
					var AGTh3 = new Swiper(".ed-hs-slide-active", {
						loop: true,
						speed: 1000,
						effect: "fade",
						fadeEffect: {
							crossFade: true
						},
						autoplay: {
							delay: 4000,
						},
					});
				};
				if($(".ed-hs-slide-3").length) {
					var AGTh4 = new Swiper(".ed-hs-slide-3", {
						loop: true,
						speed: 1000,
						effect: "fade",
						fadeEffect: {
							crossFade: true
						},
						navigation: {
							prevEl: ".ed-hs-prev",
							nextEl: ".ed-hs-next",
						},
						autoplay: {
							delay: 4000,
						},
					});
				};
				const active_card = gsap.utils.toArray('.txt_item_active');
				active_card.forEach(svg => {
					gsap.to(svg, {
						scrollTrigger: {
							trigger: svg,
							start: "top 100%",
							end: "bottom bottom",
							toggleClass: "active",
							duration: 3,
							delay:1,
							toggleActions: "play play play reverse",
							once: true,
						}
					});
				});
				if($(".ed-hs5-slider").length) {
					var AGTh3 = new Swiper(".ed-hs5-slider", {
						loop: true,
						speed: 1000,
						effect: "fade",
						fadeEffect: {
							crossFade: true
						},
						autoplay: {
							delay: 4000,
						},
						navigation: {
							prevEl: ".ed-hs5-prev",
							nextEl: ".ed-hs5-next",
						},
						pagination: {
							el: ".ed-hs5-pagi",
							clickable: true,
						},
					});
				};
				const EDH6 = gsap.timeline();
				EDH6
				.from(".ed-hero-content-6 .hero_img6", {  scale: 1.4,  opacity: 0, borderRadius: 500, duration: 1.5,  ease: "power2.inOut" })
				.from(".ed-hero-content-6 .ed-h6-img .item-img2", {  scale: 1.5, xPercent: -50, rotate: -90,  opacity: 0, duration: 1.5,  ease: "power2.inOut" }, "<=.3")
				.from(".ed-hero-content-6 .ed-hero-text-6 .hero-decs6 .item-text .ed-btn-6", {  scale: .5, yPercent: 50,   opacity: 0, duration: 2,  ease: "elastic.out(1,0.5)"},"<=.3")
				.from(".ed-hero-content-6 .ed-hero-text-6 .hero-decs6 .item-img .img_1", {  scale: .5, yPercent: 50,   opacity: 0, duration: 2,  ease: "elastic.out(1,0.5)"},"<=.3")
				.from(".ed-hero-content-6 .ed-hero-text-6 .hero-decs6 .item-img .img_2", {  scale: .5, yPercent: 50,   opacity: 0, duration: 2,  ease: "elastic.out(1,0.5)"},"<=.3")
				.from(".ed-hero-content-6 .ed-h6-img .item-img1", {  scale: .5, yPercent: 50,   opacity: 0, duration: 2,  ease: "elastic.out(1,0.8)"}, "<=.8")
			}, 700);
})
});

if (window.matchMedia("(min-width: 1400px)").matches) { 
	var Hero_pin = document.querySelectorAll(".ed-hero-sec-6")
	Hero_pin.forEach((item) => {
		gsap.to(item, {
			scrollTrigger: {
				trigger: item,
				markers: false,
				pin: true,
				pinSpacing: false,
				start: "top -85%",
				end: "bottom -148%",
			},
		});
	});
}
if (window.matchMedia("(min-width: 1400px)").matches) { 
	var CTA_Pin = document.querySelectorAll(".ed-cta6-sec")
	CTA_Pin.forEach((item) => {
		gsap.to(item, {
			scrollTrigger: {
				trigger: item,
				markers: false,
				pin: true,
				pinSpacing: false,
				start: "top -35%",
				end: "bottom 0%",
			},
		});
	});
}
if($('.ed-text').length) {
	var edtextarea = $(".ed-text");

	if(edtextarea.length == 0) return; gsap.registerPlugin(SplitText); edtextarea.each(function(paragraph) {

		gsap.utils.toArray(".ed-text p").forEach(paragraph => {
			let timeline = gsap.timeline({
				scrollTrigger: {
					trigger: paragraph,
					start: "top 90%",
					end: "bottom 60%",
					toggleActions: "play none none none"
				}
			});
			let splitText = new SplitText(paragraph, { type: "lines" });
			gsap.set(paragraph, { perspective: 400 });
			timeline.from(splitText.lines, {
				opacity: 0,
				rotationX: -80,
				transformOrigin: "top center -50",
				force3D: true,
				duration: 1,
				delay: 0.5,
				stagger: 0.1
			});
		});

	});
}
var EDFt = gsap.timeline({
	scrollTrigger: {
		trigger: ".ed-ft6-sec",
		start: "top 70%",
		toggleActions: "play reverse play reverse",
		markers: false,
	},
})
EDFt
.from(".ed-ft6-app", {
	yPercent: 100,
	opacity: 0,
	ease: "elastic.out(1,2.5)",
	duration: 1,
	stagger: -.3,
})
var EdProg = gsap.timeline({
	scrollTrigger: {
		trigger: ".ed-course6-content",
		start: "top 70%",
		toggleActions: "play reverse play reverse",
		markers: false,
	},
})
EdProg
.from(".ed-pg6", {
	xPercent: 100,
	opacity: 0,
	ease: "back.out(2.5)",
	duration: 1,
	stagger: -.2,
})
var enrollActive = $('.ed-h-list-img')
enrollActive.mouseover(function() {
	enrollActive.removeClass('active');
	$(this).addClass('active');
});
var teamActive = $('.ed-tm4-item')
teamActive.mouseover(function() {
	teamActive.removeClass('active');
	$(this).addClass('active');
});

// Featured Class Slider
const initializeSwipers = () => {
	var slider = new Swiper('.ed-ft-class-slider', {
		spaceBetween: 30,
		slidesPerView: 4,
		loop: true,
		speed: 1000,
		observer: true,
		observeParents: true,
		observeSlideChildren: true,
		navigation: {
			prevEl: ".ed-ft-button-prev",
			nextEl: ".ed-ft-button-next",
		},
		breakpoints: {
			'1600': {
				slidesPerView: 4,
			},
			'1500': {
				slidesPerView: 3,
			}, 
			'1400': {
				slidesPerView: 3,
			},
			'1300': {
				slidesPerView: 3,
			},
			'992': {
				slidesPerView: 2,
			},
			'650': {
				slidesPerView: 2,
			},
			'576': {
				slidesPerView: 1,
			},
			'0': {
				slidesPerView: 1,
			},
		},
	});
};
const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
tabs.forEach(tab => {
	tab.addEventListener('shown.bs.tab', () => {
		initializeSwipers();
	});
});
window.addEventListener('load', initializeSwipers);

// Team Class Slider
if($('.ed-team-slide').length) {
	let slider = new Swiper('.ed-team-slide', {
		loop: true,
		speed: 500,
		spaceBetween: 0,
		spaceBetween: 38,
		autoplay: {
			delay: 5000,
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			576: {
				slidesPerView: 2,
			},
			768: {
				slidesPerView: 2,
			},
			992: {
				slidesPerView: 3,
			},
			1200: {
				slidesPerView: 4,
			},
			1400: {
				slidesPerView: 5,
			},
			1600: {
				slidesPerView: 5,
			},
		},

	});
};
// Testimonial Slider	
if($('.ed-testimonial-slide').length) {
	let slider = new Swiper('.ed-testimonial-slide', {
		loop: true,
		spaceBetween: 48,
		speed: 700,
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			576: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 1,
			},
			992: {
				slidesPerView: 1,
			},
			1200: {
				slidesPerView: 2,
			},
			1400: {
				slidesPerView: 2,
			},
			1600: {
				slidesPerView: 2,
			},
		},

	});
};
// Course Slider Slider		
if($(".ed-course-slider-2").length) {
	const swiper = new Swiper(".ed-course-slider-2" , {
		speed: 500,
		loop: true,
		spaceBetween: 18,
		pagination: {
			el: ".ed-course-pagi",
			clickable: true,
		},
		autoplay:  {
			delay: 5000,
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			576: {
				slidesPerView: 2,
			},
			768: {
				slidesPerView: 2,
			},
			992: {
				slidesPerView: 3,
			},
			1024: {
				slidesPerView: 3,
			},
			1200: {
				slidesPerView: 4,
			},
			1400: {
				slidesPerView: 5,
			},
			1600: {
				slidesPerView: 5,
			},
			1800: {
				slidesPerView: 5,
			},
		},
	})
}
	// Team Slider		
if($(".ed-team-slider-2").length) {
	const swiper = new Swiper(".ed-team-slider-2" , {
		speed: 500,
		loop: true,
		spaceBetween: 0,
		pagination: {
			el: ".ed-team-pagi",
			clickable: true,
		},
		autoplay:  {
			delay: 5000,
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			576: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 2,
			},
			992: {
				slidesPerView: 2,
			},
			1024: {
				slidesPerView: 2,
			},
			1199: {
				slidesPerView: 3,
			},
			1400: {
				slidesPerView: 3,
			},
			1600: {
				slidesPerView: 3,
			},
			1800: {
				slidesPerView: 3,
			},
		},
	})
}
	// Team Slider		
if($(".ed-fun-slider").length) {
	const swiper = new Swiper(".ed-fun-slider" , {
		speed: 500,
		loop: true,
		spaceBetween: 30,
		navigation: {
			prevEl: ".ed-fn-prev",
			nextEl: ".ed-fn-next",
		},
		autoplay:  {
			delay: 5000,
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			576: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 2,
			},
			992: {
				slidesPerView: 2,
			},
			1024: {
				slidesPerView: 2,
			},
			1199: {
				slidesPerView: 3,
			},
			1400: {
				slidesPerView: 3,
			},
			1600: {
				slidesPerView: 3,
			},
			1800: {
				slidesPerView: 3,
			},
		},
	})
}
	// Category Slider		
if($(".ed-category5-card-slide").length) {
	const swiper = new Swiper(".ed-category5-card-slide" , {
		speed: 500,
		loop: true,
		spaceBetween: 32,
		navigation: {
			prevEl: ".ed-ct-prev",
			nextEl: ".ed-ct-next",
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},
			576: {
				slidesPerView: 1,
			},
			768: {
				slidesPerView: 2,
			},
			992: {
				slidesPerView: 2,
			},
			1024: {
				slidesPerView: 2,
			},
			1199: {
				slidesPerView: 3,
			},
			1400: {
				slidesPerView: 3,
			},
			1600: {
				slidesPerView: 3,
			},
			1800: {
				slidesPerView: 3,
			},
		},
	})
}
	// Video Client Slider	
var quick_view = new Swiper(".ed-vc-slider-nav", {
	loop: true,
	spaceBetween: 100,
	slidesPerView: 4,
	speed: 1000,
	navigation: {
		prevEl: ".vc-button-prev",
		nextEl: ".vc-button-next",
	},
	breakpoints: {  
		'1400': {
			slidesPerView: 3,
		},
		'1200': {
			slidesPerView: 3,
		},
		'1024': {
			slidesPerView: 3,
			spaceBetween: 30,
		},
		'991': {
			slidesPerView: 2,
			spaceBetween: 30,
		},
		'768': {
			slidesPerView: 2,
			spaceBetween: 30,
		},
		'577': {
			slidesPerView: 2,
			spaceBetween: 30,
		},
		'0': {
			slidesPerView: 1,
			spaceBetween: 30,
		},
	},
});
// Testimonial  Slider	
if($(".ed-tst6-slider").length) {
	var AGTh3 = new Swiper(".ed-tst6-slider", {
		loop: true,
		speed: 1000,
		effect: "fade",
		fadeEffect: {
			crossFade: true
		},
		autoplay: {
			delay: 4000,
		},
		navigation: {
			prevEl: ".ed-tst6-prev",
			nextEl: ".ed-tst6-next",
		},
		pagination: {
			el: ".ed-tst6-pagi",
			clickable: true,
		},
	});
};
var swiper2 = new Swiper(".ed-vc-slider-for", {
	loop: true,
	spaceBetween: 0,
	speed: 1000,
	slidesPerView: 1,
	navigation: {
		prevEl: ".vc-button-prev",
		nextEl: ".vc-button-next",
	},
	thumbs: {
		swiper: quick_view,
	},
});
// Testimonial Slider
if($(".ed-tst5-slider-active").length) {
	var EDT = new Swiper(".ed-tst5-slider-active", {
		loop: true,
		speed: 1000,
		effect: "fade",
		fadeEffect: {
			crossFade: true
		},
		pagination: {
			el: ".ed-tst5-pagi",
			clickable: true,
		},
	});
};
// counter-activation
$('.counter').counterUp({
	delay: 10,
	time: 5000
});
	// Grid Item Active
var $grid = $('.grid').imagesLoaded( function() {
	$grid.masonry({
		percentPosition: true,
		itemSelector: '.grid-item',
		columnWidth: '.grid-sizer'
	}); 
});
var $grid = $(".grid").isotope({
	itemSelector: ".grid-item",
	layoutMode: "fitRows"
});
var filterFns = {
	numberGreaterThan50: function() {
		var number = $(this)
		.find(".number")
		.text();
		return parseInt(number, 10) > 50;
	},
	ium: function() {
		var name = $(this)
		.find(".name")
		.text();
		return name.match(/ium$/);
	}
};
$(".button-group").on("click", "button", function() {
	var filterValue = $(this).attr("data-filter");
	filterValue = filterFns[filterValue] || filterValue;
	$grid.isotope({ filter: filterValue });
});
$(".button-group").each(function(i, buttonGroup) {
	var $buttonGroup = $(buttonGroup);
	$buttonGroup.on("click", "button", function() {
		$buttonGroup.find(".is-checked").removeClass("is-checked");
		$(this).addClass("is-checked");
	});
});
// Animation		
gsap.utils.toArray('.ed_left_img').forEach((el, index) => { 
	let edImg3 = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 1,
			start: "top 60%",
			end: "buttom 20%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	edImg3
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 1, rotateZ: -45, scale: 0.5, x: "-=200"}, {opacity: 1,  rotateZ: 0, scale: 1, x: 0, duration: 1, immediateRender: false})
})
gsap.utils.toArray('.ed_top_img').forEach((el, index) => { 
	let edImg4 = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-top-rate-content",
			scrub: 1,
			start: "top 50%",
			end: "buttom 10%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	edImg4
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 1, rotateY: -45, scale: 0.5, yPercent: 100}, {opacity: 1,  rotateY: 0, yPercent: 0, scale: 1, x: 0, duration: 1, immediateRender: false})
})
gsap.utils.toArray('.ed_top_img_2').forEach((el, index) => { 
	let edImg5 = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-top-rate-content",
			scrub: 1,
			start: "top %",
			end: "top -100%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	edImg5
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 1,  scale: 1.2, yPercent: -100}, {opacity: 1,   yPercent: 0, scale: 1, x: 0, duration: 1, immediateRender: false})
})

if(window.innerWidth> 991){
	if ($('.ed-intro-content').length > 0 ) {
		function IntroShape(e) {
			let t = (e = e || document).querySelector(".ed-intro-content"),
			item_1 = t.querySelectorAll(".intro-shape1");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 60%",
					toggleActions: "play none none reverse"
				}
			}).from(item_1, {
				y: 400,
				ease: "back.out(2.5)",
				opacity: 0,
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})
			let item_img = t.querySelectorAll(".ed_top_img_3");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 60%",
					toggleActions: "play none none reverse"
				}
			}).from(item_img, {
				y: 310,
				x: 0,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})
			let item_2 = t.querySelectorAll(".intro-shape2");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 60%",
					toggleActions: "play none none reverse"
				}
			}).from(item_2, {
				y: 310,
				x: -260,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

			let item_3 = t.querySelectorAll(".intro-shape3");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 60%",
					toggleActions: "play none none reverse"
				}
			}).from(item_3, {
				y: 250,
				x: 0,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

			let item_4 = t.querySelectorAll(".intro-shape4");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 50%",
					toggleActions: "play none none reverse"
				}
			}).from(item_4, {
				y: 250,
				x: 0,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

			let item_5 = t.querySelectorAll(".intro-shape5");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 40%",
					toggleActions: "play none none reverse"
				}
			}).from(item_5, {
				y: 150,
				x: -50,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

		}
		IntroShape();
	};
};
if(window.innerWidth> 991){
	if ($('.ed-cta6-content').length > 0 ) {
		function CtaShape(e) {
			let t = (e = e || document).querySelector(".ed-cta6-content"),
			item_1 = t.querySelectorAll(".ed-cta6-img");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 20%",
					toggleActions: "play none none reverse"
				}
			}).from(item_1, {
				y: 400,
				ease: "back.out(2.5)",
				opacity: 0,
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

			let item_2 = t.querySelectorAll(".ed-cta6-count.ver_2");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 20%",
					toggleActions: "play none none reverse"
				}
			}).from(item_2, {
				y: 310,
				x: -260,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

			let item_3 = t.querySelectorAll(".ed-cta6-count.ver_3");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 20%",
					toggleActions: "play none none reverse"
				}
			}).from(item_3, {
				y: 250,
				x: 0,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

			let item_4 = t.querySelectorAll(".ed-cta6-shape");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 20%",
					toggleActions: "play none none reverse"
				}
			}).from(item_4, {
				y: 250,
				x: 0,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

			let item_5 = t.querySelectorAll(".ed-cta6-count.ver_1");
			gsap.timeline({
				scrollTrigger: {
					trigger: t,
					start: "top 20%",
					toggleActions: "play none none reverse"
				}
			}).from(item_5, {
				y: 150,
				x: -50,
				opacity: 0,
				ease: "back.out(2.5)",
				duration: 1,
				stagger: {
					each: .05,
					from: "end"
				},
			})

		}
		CtaShape();
	};
};
// Animation
if($('.ed-sec-tt-anim').length) {
	var edtitle = $(".ed-sec-tt-anim");

	if(edtitle.length == 0) return; gsap.registerPlugin(SplitText); edtitle.each(function(index, el) {

		el.split = new SplitText(el, { 
			type: "lines,words,chars",
			linesClass: "split-line"
		});

		if( $(el).hasClass('ed-has-anim') ){
			gsap.set(el.split.words, {
				opacity: .3,
				y: "100",
			});
		};
		if( $(el).hasClass('ed-has-anim-char') ){
			gsap.set(el.split.chars, {
				opacity: .3,
				x: "20",
			});
		};
		el.anim = gsap.to(el.split.words, {
			scrollTrigger: {
				trigger: el,
				start: "top 90%",
				markers: false
			},

			x: "0",
			y: "0",
			opacity: 1,
			duration: .4,
			stagger: 0.15,
		});
		el.anim = gsap.to(el.split.chars, {
			scrollTrigger: {
				trigger: el,
				start: "top 90%",
				markers: false
			},

			x: "0",
			y: "0",
			opacity: 1,
			duration: .4,
			stagger: 0.05,
		});

	});
}
	// Animation 2
gsap.utils.toArray(' .top_view').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 2,
			start: "top 80%",
			end: "top 90%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 0,  x: "-=30"}, {opacity: 1, x: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .top_view_3').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 2,
			start: "top 80%",
			end: "top 90%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 0,  x: "+=30"}, {opacity: 1, x: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .top_view_2').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 2,
			start: "top 60%",
			end: "top 50%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 0, scale: 0,  y: "+=80"}, {opacity: 1, scale: 1, y: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .top_view_4').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 2,
			start: "top 90%",
			end: "top 70%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 0,  y: "+=150"}, {opacity: 1, y: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .left_view').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 1.5,
			end: "top 30%",
			start: "top 100%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 1, scale: .5, xPercent: "100"}, {opacity: 1, xPercent: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .upper_view').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 1.5,
			end: "top 90%",
			start: "top 110%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 1, scale: .8, yPercent: "100"}, {opacity: 1, yPercent: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .right_view').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 1.5,
			end: "top 30%",
			start: "top 100%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 1, scale: .5,  xPercent: "-100"}, {opacity: 1, xPercent: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .zoom_in').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 1.5,
			start: "top 40%",
			end: "top 100%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, {  scale: 0,}, {scale: 1, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .zoom_in_2').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-vd5-sec",
			scrub: 1.5,
			start: "top 0%",
			end: "top -10%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, {  scale: 0, xPercent: "100", yPercent: "100"}, {scale: 1, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .top_view_logo').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-footer5-sec",
			scrub: 1,
			start: "top 50%",
			end: "top 0%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 0, scale: .5,  y: "+=500"}, {opacity: 1, y: 0, scale: 1, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .ed_img_ani').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-ab6-sec",
			scrub: 2,
			start: "top 40%",
			end: "top 5%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'left'})
	.from(el, { opacity: 0, rotate: 45,  x: "-=250"}, {opacity: 1, x: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .ed_img_ani_2').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-ab6-sec",
			scrub:  2,
			start: "top 40%",
			end: "top 5%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'right'})
	.from(el, { opacity: 0, rotate: 45,  x: "+=250"}, {opacity: 1, x: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .ed_img_ani_3').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-ab6-sec",
			scrub: 3,
			start: "top 80%",
			end: "top 5%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 0,   y: "+=250"}, {opacity: 1, x: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .ed_img_ani_4').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-cont6-img-wrap",
			scrub: 2,
			start: "top 40%",
			end: "top 5%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'left'})
	.from(el, { opacity: 0, rotate: 45,  x: "-=250"}, {opacity: 1, x: 0, duration: 1, immediateRender: false})
});
gsap.utils.toArray(' .ed_img_ani_5').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-cont6-img-wrap",
			scrub:  2,
			start: "top 40%",
			end: "top 5%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'right'})
	.from(el, { opacity: 0, rotate: 45,  x: "+=250"}, {opacity: 1, x: 0, duration: 1, immediateRender: false})
});
let imageappear1 = document.querySelectorAll(".ed-image-appear1");
imageappear1.forEach((container) => {
	let image = container.querySelector(".ed-img-rvl_1");
	let ptx = gsap.timeline({
		scrollTrigger: {
			trigger: container,
			toggleActions: "play none none none",
			start: "top 90%",
			end: "top 0%",
		}
	});
	ptx.set(container, { autoAlpha: 1 });
	ptx.from(container, 1.5, {
		xPercent: 100,
		ease: Power2.out
	});
	ptx.from(image, 1.5, {
		xPercent: -100,
		scale: 1.3,
		delay: -1.5,
		ease: Power2.out
	});
});
let imageappear2 = document.querySelectorAll(".ed-image-appear2");
imageappear2.forEach((container) => {
	let image = container.querySelector(".ed-img-rvl_2");
	let rts = gsap.timeline({
		scrollTrigger: {
			trigger: container,
			toggleActions: "play none none none",
		}
	});
	rts.set(container, { autoAlpha: 1 });
	rts.from(container, 1.5, {
		xPercent: 100,
		ease: Power2.out
	});
	rts.from(image, 1.5, {
		xPercent: -100,
		scale: 1.3,
		delay: -1.5,
		ease: Power2.out
	});
});

let imageappear3 = document.querySelectorAll(".ed-image-appear3");
imageappear3.forEach((container) => {
	let image = container.querySelector(".ed-img-rvl_3");
	let rts = gsap.timeline({
		scrollTrigger: {
			trigger: container,
			toggleActions: "play none none none",
		}
	});
	rts.set(container, { autoAlpha: 1 });
	rts.from(container, 1.5, {
		xPercent: -100,
		ease: Power2.out
	});
	rts.from(image, 1.5, {
		xPercent: 100,
		scale: 1.3,
		delay: -1.5,
		ease: Power2.out
	});
});
gsap.utils.toArray(".img-parallax").forEach(function(container) {
	let image = container.querySelector("img");

	let tl = gsap.timeline({
		scrollTrigger: {
			trigger: container,
			scrub: true,
			pin: false,
		},
	}); 
	tl.from(image, {
		yPercent: -30,
		ease: "none",
	}).to(image, {
		yPercent: 30,
		ease: "none",
	}); 
});
if (window.matchMedia("(min-width: 1200px)").matches) {
	var collab = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-collaborate-sec",
			start: "top 80%",
			toggleActions: "play reverse play reverse",
			markers: false,
		},
	})
	collab
	.from(".ed-coll-item", {
		yPercent: -200,
		ease: "bounce.inOut",
		duration: 1.5,
		stagger: .1,
	})
}
if (window.matchMedia("(min-width: 1200px)").matches) {
	var collab = gsap.timeline({
		scrollTrigger: {
			trigger: ".ed-category-sec-3",
			start: "top 90%",
			toggleActions: "play reverse play reverse",
			markers: false,
		},
	})
	collab
	.from(".ed-category-item-3", {
		yPercent: -200,
		ease: "bounce.inOut",
		duration: 1.5,
		stagger: .1,
	})
}
gsap.utils.toArray(' .rotate_view').forEach((el, index) => { 
	let tlcta = gsap.timeline({
		scrollTrigger: {
			trigger: el,
			scrub: 2,
			start: "top 60%",
			end: "top 30%%",
			toggleActions: "play none none reverse",
			markers: false
		}
	})

	tlcta
	.set(el, {transformOrigin: 'center center'})
	.from(el, { opacity: 0,  x: "+=300", y: "-150", rotate: "-90deg"}, {opacity: 1, x: 0, y: 0, duration: 1, rotate: "-90deg" })
});
if(window.innerWidth> 1200){
	if ($(".ed-view-btns").length) {
		const products = $(".products");
		const allButtons = $(".ed-view-btn button");
		const grid3Button = $(".grid-3");
		const listViewButton = $(".list-view");
		allButtons.each(function () {
			const $this = $(this);
			$this.on("click", function (e) {
				e.preventDefault();
				$this.addClass("active").siblings().removeClass("active");
			});
		});
		grid3Button.on("click", function () {
			products.removeClass("default-column list-view").addClass("three-column");
		});
		listViewButton.on("click", function () {
			products.removeClass("default-column two-column").addClass("list-view");
		});
	}
};
if ($('.gt-check-list ul li').length > 5) {
	$('.gt-check-list ul li:gt(4)').hide();
	$('.show-more').show();
}
$('.gt-check-list .show-more').on('click', function() {
	$('.gt-check-list ul li:gt(4)').toggle();
	$(this).text() === 'Show more ' ? $(this).text('Show less') : $(this).text('Show more ');
});
if ($('.gt-check-list_2 ul li').length > 5) {
	$('.gt-check-list_2 ul li:gt(4)').hide();
	$('.show-more').show();
}
$('.gt-check-list_2 .show-more').on('click', function() {
	$('.gt-check-list_2 ul li:gt(4)').toggle();
	$(this).text() === 'Show more ' ? $(this).text('Show less') : $(this).text('Show more ');
});
$(".gt-check-list ul li, .gt-check-list_2 ul li, .gt-check-wrap ul li").click(function () {
	$(".gt-check-list ul li, .gt-check-list_2 ul li, .gt-check-wrap ul li").removeClass("active");
	$(this).addClass("active");   
});
if ($('.blog-item-img-slide').length > 0 ) {
	var blog_Slider = new Swiper(".blog-item-img-slide", {
		loop: true,
		slidesPerView: 1,
		centeredSlides: true,
		speed: 1000,
		navigation: {
			nextEl: ".log-blog-button-prev",
			prevEl: ".log-blog-button-next",
		},
	});
}
if ($('.ed-gallery-feed').length > 0 ) {
	var $grid = $('.ed-gallery-feed').imagesLoaded( function() {
		$grid.masonry({
			percentPosition: true,
			itemSelector: '.grid-item',
			columnWidth: '.grid-sizer'
		}); 
	});
}
})(jQuery);