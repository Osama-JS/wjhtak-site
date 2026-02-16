/**
 * =============================================================================
 * WJHTAK Tourism Platform - Main JavaScript
 * =============================================================================
 */

(function () {
    "use strict";

    // ==========================================================================
    // UTILITIES
    // ==========================================================================

    const Utils = {
        /**
         * Debounce function
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Throttle function
         */
        throttle(func, limit) {
            let inThrottle;
            return function (...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => (inThrottle = false), limit);
                }
            };
        },

        /**
         * Check if element is in viewport
         */
        isInViewport(element, offset = 0) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top <=
                    (window.innerHeight ||
                        document.documentElement.clientHeight) -
                        offset && rect.bottom >= 0
            );
        },

        /**
         * Get current locale
         */
        getLocale() {
            return document.documentElement.lang || "en";
        },

        /**
         * Check if RTL
         */
        isRTL() {
            return document.documentElement.dir === "rtl";
        },

        /**
         * Format number with locale
         */
        formatNumber(number, locale = null) {
            locale = locale || this.getLocale();
            return new Intl.NumberFormat(
                locale === "ar" ? "ar-SA" : "en-US",
            ).format(number);
        },

        /**
         * Format currency
         */
        formatCurrency(amount, currency = "USD", locale = null) {
            locale = locale || this.getLocale();
            return new Intl.NumberFormat(locale === "ar" ? "ar-SA" : "en-US", {
                style: "currency",
                currency: currency,
            }).format(amount);
        },
    };

    // ==========================================================================
    // NAVBAR
    // ==========================================================================

    const Navbar = {
        navbar: null,
        mobileMenu: null,
        mobileMenuOverlay: null,
        mobileMenuToggle: null,
        scrollThreshold: 50,

        init() {
            this.navbar = document.querySelector(".navbar");
            this.mobileMenu = document.querySelector(".mobile-menu");
            this.mobileMenuOverlay = document.querySelector(
                ".mobile-menu-overlay",
            );
            this.mobileMenuToggle = document.querySelector(
                ".mobile-menu-toggle",
            );

            if (!this.navbar) return;

            this.bindEvents();
            this.handleScroll();
        },

        bindEvents() {
            // Scroll event
            window.addEventListener(
                "scroll",
                Utils.throttle(() => {
                    this.handleScroll();
                }, 100),
            );

            // Mobile menu toggle
            if (this.mobileMenuToggle) {
                this.mobileMenuToggle.addEventListener("click", () => {
                    this.toggleMobileMenu();
                });
            }

            // Mobile menu close
            const closeBtn = document.querySelector(".mobile-menu-close");
            if (closeBtn) {
                closeBtn.addEventListener("click", () => {
                    this.closeMobileMenu();
                });
            }

            // Overlay click
            if (this.mobileMenuOverlay) {
                this.mobileMenuOverlay.addEventListener("click", () => {
                    this.closeMobileMenu();
                });
            }

            // Close on escape
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    this.closeMobileMenu();
                }
            });
        },

        handleScroll() {
            if (window.scrollY > this.scrollThreshold) {
                this.navbar.classList.add("scrolled");
            } else {
                this.navbar.classList.remove("scrolled");
            }
        },

        toggleMobileMenu() {
            if (this.mobileMenu && this.mobileMenuOverlay) {
                this.mobileMenu.classList.toggle("active");
                this.mobileMenuOverlay.classList.toggle("active");
                this.mobileMenuToggle.classList.toggle("active");
                document.body.style.overflow =
                    this.mobileMenu.classList.contains("active")
                        ? "hidden"
                        : "";
            }
        },

        closeMobileMenu() {
            if (this.mobileMenu && this.mobileMenuOverlay) {
                this.mobileMenu.classList.remove("active");
                this.mobileMenuOverlay.classList.remove("active");
                this.mobileMenuToggle.classList.remove("active");
                document.body.style.overflow = "";
            }
        },
    };

    // ==========================================================================
    // SCROLL ANIMATIONS
    // ==========================================================================

    const ScrollAnimations = {
        elements: [],
        observer: null,

        init() {
            this.elements = document.querySelectorAll(
                ".scroll-animate, .scroll-animate-left, .scroll-animate-right",
            );

            if (this.elements.length === 0) return;

            this.createObserver();
            this.observe();
        },

        createObserver() {
            const options = {
                root: null,
                rootMargin: "0px 0px -100px 0px",
                threshold: 0.1,
            };

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        // Optionally unobserve after animation
                        // this.observer.unobserve(entry.target);
                    }
                });
            }, options);
        },

        observe() {
            this.elements.forEach((element) => {
                this.observer.observe(element);
            });
        },
    };

    // ==========================================================================
    // COUNTER ANIMATION
    // ==========================================================================

    const CounterAnimation = {
        counters: [],

        init() {
            this.counters = document.querySelectorAll("[data-counter]");

            if (this.counters.length === 0) return;

            this.observeCounters();
        },

        observeCounters() {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            this.animateCounter(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.5 },
            );

            this.counters.forEach((counter) => {
                observer.observe(counter);
            });
        },

        animateCounter(element) {
            const target = parseInt(element.dataset.counter, 10);
            const duration = parseInt(element.dataset.duration, 10) || 2000;
            const suffix = element.dataset.suffix || "";
            const prefix = element.dataset.prefix || "";

            let startTime = null;
            const startValue = 0;

            const animate = (timestamp) => {
                if (!startTime) startTime = timestamp;
                const progress = Math.min(
                    (timestamp - startTime) / duration,
                    1,
                );

                // Easing function (ease-out)
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const currentValue = Math.floor(
                    easeOut * (target - startValue) + startValue,
                );

                element.textContent =
                    prefix + Utils.formatNumber(currentValue) + suffix;

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    element.textContent =
                        prefix + Utils.formatNumber(target) + suffix;
                }
            };

            requestAnimationFrame(animate);
        },
    };

    // ==========================================================================
    // LAZY LOADING
    // ==========================================================================

    const LazyLoading = {
        images: [],
        observer: null,

        init() {
            this.images = document.querySelectorAll("img[data-src]");

            if (this.images.length === 0) return;

            if ("IntersectionObserver" in window) {
                this.createObserver();
                this.observe();
            } else {
                // Fallback for older browsers
                this.loadAllImages();
            }
        },

        createObserver() {
            this.observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            this.loadImage(entry.target);
                            this.observer.unobserve(entry.target);
                        }
                    });
                },
                {
                    rootMargin: "50px 0px",
                },
            );
        },

        observe() {
            this.images.forEach((image) => {
                this.observer.observe(image);
            });
        },

        loadImage(image) {
            const src = image.dataset.src;
            const srcset = image.dataset.srcset;

            if (src) {
                image.src = src;
            }
            if (srcset) {
                image.srcset = srcset;
            }

            image.removeAttribute("data-src");
            image.removeAttribute("data-srcset");
            image.classList.add("loaded");
        },

        loadAllImages() {
            this.images.forEach((image) => {
                this.loadImage(image);
            });
        },
    };

    // ==========================================================================
    // SMOOTH SCROLL
    // ==========================================================================

    const SmoothScroll = {
        init() {
            document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
                anchor.addEventListener("click", (e) => {
                    const href = anchor.getAttribute("href");

                    if (href === "#") return;

                    const target = document.querySelector(href);

                    if (target) {
                        e.preventDefault();

                        const headerOffset = 80;
                        const elementPosition =
                            target.getBoundingClientRect().top;
                        const offsetPosition =
                            elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: "smooth",
                        });
                    }
                });
            });
        },
    };

    // ==========================================================================
    // ACCORDION
    // ==========================================================================

    const Accordion = {
        init() {
            const accordionHeaders =
                document.querySelectorAll(".accordion-header");

            accordionHeaders.forEach((header) => {
                header.addEventListener("click", () => {
                    const item = header.parentElement;
                    const isActive = item.classList.contains("active");

                    // Close all other items (optional - for single open accordion)
                    const accordion = item.parentElement;
                    if (accordion.dataset.singleOpen === "true") {
                        accordion
                            .querySelectorAll(".accordion-item")
                            .forEach((i) => {
                                i.classList.remove("active");
                            });
                    }

                    // Toggle current item
                    if (!isActive) {
                        item.classList.add("active");
                    } else {
                        item.classList.remove("active");
                    }
                });
            });
        },
    };

    // ==========================================================================
    // FAVORITES
    // ==========================================================================

    const Favorites = {
        init() {
            const favoriteButtons = document.querySelectorAll(
                ".trip-card-favorite",
            );

            favoriteButtons.forEach((button) => {
                button.addEventListener("click", (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    button.classList.toggle("active");

                    const tripId = button.dataset.tripId;
                    if (tripId) {
                        this.toggleFavorite(
                            tripId,
                            button.classList.contains("active"),
                        );
                    }
                });
            });
        },

        async toggleFavorite(tripId, isFavorite) {
            try {
                const response = await fetch("/api/favorites/toggle", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        )?.content,
                    },
                    body: JSON.stringify({ trip_id: tripId }),
                });

                if (!response.ok) {
                    console.error("Failed to toggle favorite");
                }
            } catch (error) {
                console.error("Error toggling favorite:", error);
            }
        },
    };

    // ==========================================================================
    // SEARCH
    // ==========================================================================

    const Search = {
        form: null,
        inputs: {},

        init() {
            this.form = document.querySelector(".hero-search-form");
            if (!this.form) return;

            this.bindEvents();
        },

        bindEvents() {
            this.form.addEventListener("submit", (e) => {
                this.handleSearch(e);
            });

            // Date inputs - set min date to today
            const dateInputs = this.form.querySelectorAll('input[type="date"]');
            const today = new Date().toISOString().split("T")[0];
            dateInputs.forEach((input) => {
                input.setAttribute("min", today);
            });
        },

        handleSearch(e) {
            // Form will submit normally
            // Add any validation or pre-processing here
        },
    };

    // ==========================================================================
    // BACK TO TOP
    // ==========================================================================

    const BackToTop = {
        button: null,

        init() {
            this.button = document.querySelector(".back-to-top");
            if (!this.button) return;

            this.bindEvents();
        },

        bindEvents() {
            window.addEventListener(
                "scroll",
                Utils.throttle(() => {
                    this.toggleVisibility();
                }, 100),
            );

            this.button.addEventListener("click", () => {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth",
                });
            });
        },

        toggleVisibility() {
            if (window.scrollY > 500) {
                this.button.classList.add("visible");
            } else {
                this.button.classList.remove("visible");
            }
        },
    };

    // ==========================================================================
    // THEME SWITCHER
    // ==========================================================================

    const ThemeSwitcher = {
        init() {
            const toggle = document.querySelector(".theme-toggle");
            if (!toggle) return;

            // Check for saved theme
            const savedTheme = localStorage.getItem("theme");
            if (savedTheme) {
                document.documentElement.setAttribute("data-theme", savedTheme);
            } else if (
                window.matchMedia("(prefers-color-scheme: dark)").matches
            ) {
                document.documentElement.setAttribute("data-theme", "dark");
            }

            toggle.addEventListener("click", () => {
                const currentTheme =
                    document.documentElement.getAttribute("data-theme");
                const newTheme = currentTheme === "dark" ? "light" : "dark";

                document.documentElement.setAttribute("data-theme", newTheme);
                localStorage.setItem("theme", newTheme);
            });
        },
    };

    // ==========================================================================
    // PARALLAX
    // ==========================================================================

    const Parallax = {
        elements: [],

        init() {
            this.elements = document.querySelectorAll("[data-parallax]");
            if (this.elements.length === 0) return;

            window.addEventListener(
                "scroll",
                Utils.throttle(() => {
                    this.update();
                }, 16),
            );
        },

        update() {
            const scrollY = window.pageYOffset;

            this.elements.forEach((element) => {
                const speed = parseFloat(element.dataset.parallax) || 0.5;
                const rect = element.getBoundingClientRect();

                if (rect.bottom > 0 && rect.top < window.innerHeight) {
                    const yPos = -(scrollY * speed);
                    element.style.transform = `translate3d(0, ${yPos}px, 0)`;
                }
            });
        },
    };

    // ==========================================================================
    // INITIALIZE
    // ==========================================================================

    document.addEventListener("DOMContentLoaded", () => {
        Navbar.init();
        ScrollAnimations.init();
        CounterAnimation.init();
        LazyLoading.init();
        SmoothScroll.init();
        Accordion.init();
        Favorites.init();
        Search.init();
        BackToTop.init();
        ThemeSwitcher.init();
        Parallax.init();

        // Add loaded class to body
        document.body.classList.add("loaded");
    });

    // Expose utilities globally
    window.WjhtakUtils = Utils;
})();
