(function ($) {
    'use strict';

    ///////////////////////////////////////////////////////
    // Preloader
    $('.preloader').delay(800).fadeOut('slow');
    // Preloader End

    $(window).on('scroll', function () {
        if ($(window).scrollTop() > 50) {
            $('#sticky-menu').addClass('sticky-menu');
        } else {
            $('#sticky-menu').removeClass('sticky-menu');
        }
    });

    // Mobile menu js
    if (jQuery('.prebuilt-header').length > 0) {

        // header sub menu class no-boder js /////
        document.querySelectorAll(".sub-menu li.menu-item-has-children > a").forEach(function (menuItem) {
            menuItem.classList.add("no-border");
        });

        
        $.fn.vsmobilemenu = function (options) {
            var opt = $.extend({
                menuToggleBtn: ".quanto-menu-toggle",
                bodyToggleClass: "quanto-body-visible",
                subMenuClass: "quanto-submenu",
                subMenuParent: "quanto-item-has-children",
                subMenuParentToggle: "quanto-active",
                meanExpandClass: "quanto-mean-expand",
                appendElement: '<span class="quanto-mean-expand"></span>',
                subMenuToggleClass: "quanto-open",
                toggleSpeed: 400,
              },
              options
            );
        
            return this.each(function () {
                var menu = $(this); // Select menu
        
                // Menu Show & Hide
                function menuToggle() {
                    menu.toggleClass(opt.bodyToggleClass);
            
                    // collapse submenu on menu hide or show
                    var subMenu = "." + opt.subMenuClass;
                    $(subMenu).each(function () {
                    if ($(this).hasClass(opt.subMenuToggleClass)) {
                        $(this).removeClass(opt.subMenuToggleClass);
                        $(this).css("display", "none");
                        $(this).parent().removeClass(opt.subMenuParentToggle);
                    }
                    });
                }
        
                // Class Set Up for every submenu
                menu.find("li").each(function () {
                    var submenu = $(this).find("ul");
                    submenu.addClass(opt.subMenuClass);
                    submenu.css("display", "none");
                    submenu.parent().addClass(opt.subMenuParent);
                    submenu.prev("a").append(opt.appendElement);
                    submenu.next("a").append(opt.appendElement);
                });

                function toggleDropDown($element) {
                    var $submenu = $element.next("ul");

                    if ($submenu.length === 0) {
                        $submenu = $element.prev("ul"); // Fallback if .mean-expand is after the link
                    }

                    if ($submenu.length > 0) {
                        var $parent = $element.parent();
                        var $siblings = $parent.siblings();

                        // Close only sibling submenus
                        $siblings.find("ul." + opt.subMenuClass).slideUp(opt.toggleSpeed).removeClass(opt.subMenuToggleClass);
                        $siblings.removeClass(opt.subMenuParentToggle);

                        // Toggle current submenu
                        $parent.toggleClass(opt.subMenuParentToggle);
                        $submenu.slideToggle(opt.toggleSpeed).toggleClass(opt.subMenuToggleClass);
                    }
                }

        
                // Submenu toggle Button
                menu.find("." + opt.meanExpandClass).each(function () {
                    $(this).on("click", function (e) {
                        e.preventDefault();
                        toggleDropDown($(this).parent());
                    });
                });

                // Menu Show & Hide On Toggle Btn click
                $(opt.menuToggleBtn).each(function () {
                    $(this).on("click", function () {
                    menuToggle();
                    });
                });

                // Hide Menu On out side click
                menu.on("click", function (e) {
                    e.stopPropagation();
                    menuToggle();
                });
            
                // Stop Hide full menu on menu click
                menu.find("div").on("click", function (e) {
                    e.stopPropagation();
                });
            });
        };
        $(".quanto-menu-wrapper").vsmobilemenu();
    }

    /**************************************
     *****  Set Background Image *****
     **************************************/
    if ($('[data-bg-src]').length > 0) {
        $('[data-bg-src]').each(function () {
            var src = $(this).attr('data-bg-src');
            $(this).css('background-image', 'url(' + src + ')');
            $(this).removeAttr('data-bg-src').addClass('background-image');
        });
    }

    ///////////////////////////////////////////////////////
    // Sticky Menu
    $(window).on('scroll', function () {
        var scroll = $(window).scrollTop();
        if (scroll >= 20) {
            $('.quanto-menu-area').addClass('sticky');
        } else {
            $('.quanto-menu-area').removeClass('sticky');
        }
    });
    // Sticky Menu End

    // Custom Cursor
    const cursor = document.querySelector('.cursor');

    if (cursor) {
        const editCursor = (e) => {
            const { clientX: x, clientY: y } = e;
            cursor.style.left = x + 'px';
            cursor.style.top = y + 'px';
        };
        window.addEventListener('mousemove', editCursor);

        document.querySelectorAll('a, .cursor-pointer').forEach((item) => {
            item.addEventListener('mouseover', () => {
                cursor.classList.add('cursor-active');
            });

            item.addEventListener('mouseout', () => {
                cursor.classList.remove('cursor-active');
            });
        });
    }
    // Custom Cursor End


    // Check if thumbnail slider exists
    var thumbSliderElement = document.querySelector('.quanto-testimonial__thumb-slider');

    // Initialize content slider configuration
    var contentSliderConfig = {
        spaceBetween: 24,
        slidesPerView: 1,
        loop: true,
        speed: 800,
        navigation: {
            nextEl: '.quanto-testimonial__next',
            prevEl: '.quanto-testimonial__prev',
        },
    };

    // If thumbnail slider exists, initialize it and connect to content slider
    if (thumbSliderElement) {
        var thumbSlider = new Swiper('.quanto-testimonial__thumb-slider', {
            fadeEffect: { crossFade: true },
            effect: 'fade',
            loop: true,
            allowTouchMove: false,
        });

        // Add thumbs configuration only when thumbnail slider exists
        contentSliderConfig.thumbs = { swiper: thumbSlider };
        contentSliderConfig.allowTouchMove = false;
    } else {
        // For standalone slider, enable touch
        contentSliderConfig.allowTouchMove = true;
    }

    // Initialize the content slider with the appropriate configuration
    var testimonialInfo = new Swiper('.quanto-testimonial__content-slider', contentSliderConfig);


    // Register GSAP Plugins
    let device_width = window.innerWidth;
    gsap.registerPlugin(ScrollTrigger, ScrollSmoother);
    gsap.config({
        nullTargetWarn: false,
        trialWarn: false,
    });

    window.addEventListener('DOMContentLoaded', () => {
        // Set default GSAP configuration

        // Initialize defaults
        gsap.defaults({
            ease: 'power2.out',
            duration: 0.5,
        });
    });

    // ========================== Testimonial3 Slider ===========================
    // Store dependencies in variables
    const testimonial3Element = document.querySelector('.testimonial3-slider');
    const testimonial3NextButton = document.querySelector('.testimonial3-navigation > .next-btn');
    const testimonial3PrevButton = document.querySelector('.testimonial3-navigation > .prev-btn');

    // Check if all required elements are available
    if (testimonial3Element && testimonial3NextButton && testimonial3PrevButton) {
        const testimonial3Slider = new Swiper(testimonial3Element, {
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: testimonial3NextButton,
                prevEl: testimonial3PrevButton,
            },
            loop: true,
            // autoplay: {
            //   delay: 5000,
            //   disableOnInteraction: false,
            // },
        });
    }


    let sp = gsap.matchMedia();
    sp.add('(min-width: 1200px)', () => {
        if ($('.quanto-service2-section').length > 0) {
            ScrollTrigger.create({
                trigger: '.quanto-service2-section',
                start: 'top -1%',
                end: 'bottom 110.5%',
                pin: '.quanto-service2-section .quanto__header',
                pinSpacing: true,
            });
        }
    });

    //  sticky of Blog Details

    ScrollTrigger.create({

        trigger: '.social-links-scroll',

        start: 'top 120px',

        end: 'bottom 470px',

        pin: '.blog-item-details .social-links',

        pinSpacing: false,

        markers: false,

    });

    // Section Jump start
    const links = document.querySelectorAll('.section-link');

    if (links && links.length > 0) {
        links.forEach((link) => {
            link.addEventListener('click', function (event) {
                event.preventDefault();

                const targetID = this.getAttribute('href');

                if (targetID === '#header') {
                    gsap.to(window, {
                        duration: 1.5,
                        scrollTo: { y: 0 },
                        ease: 'power2.inOut',
                    });
                } else {
                    const targetSection = document.querySelector(targetID);
                    if (targetSection) {
                        gsap.to(window, {
                            duration: 1,
                            scrollTo: {
                                y: targetSection,
                                offsetY: 50,
                            },
                        });
                    } else {
                        console.error(`Section with ID ${targetID} does not exist.`);
                    }
                }
            });
        });
    }
    // Section Jump End

    function initTeamAnimations() {
        if (window.innerWidth >= 992) {
            // Select all elements with the class .gsap-sticky
            const stickyElements = document.querySelectorAll(".gsap-sticky");

            // Loop through each element and apply the ScrollTrigger animation
            stickyElements.forEach((element) => {
                ScrollTrigger.create({
                trigger: element,
                start: "top 150px",
                end: "100% bottom",
                pin: element,
                pinSpacing: false,
                markers: false,
                });
            });
        }
    }
    // Initialize animations
    initTeamAnimations();


})(jQuery);
