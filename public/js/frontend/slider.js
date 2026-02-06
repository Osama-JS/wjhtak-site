/**
 * =============================================================================
 * WJHTAK Tourism Platform - Slider/Carousel Component
 * =============================================================================
 * Lightweight, RTL-compatible slider with touch support
 */

(function () {
    "use strict";

    class WjhtakSlider {
        constructor(container, options = {}) {
            this.container = container;
            this.options = {
                autoplay: true,
                autoplaySpeed: 5000,
                pauseOnHover: true,
                dots: true,
                arrows: true,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                gap: 0,
                rtl: document.documentElement.dir === "rtl",
                responsive: [],
                ...options,
            };

            this.track = null;
            this.slides = [];
            this.currentIndex = 0;
            this.slideCount = 0;
            this.isPlaying = false;
            this.autoplayInterval = null;
            this.isDragging = false;
            this.startX = 0;
            this.currentX = 0;
            this.dragOffset = 0;

            this.init();
        }

        init() {
            this.buildStructure();
            this.calculateDimensions();
            this.bindEvents();

            if (this.options.autoplay) {
                this.startAutoplay();
            }

            // Handle responsive
            this.handleResponsive();
            window.addEventListener(
                "resize",
                this.debounce(() => {
                    this.handleResponsive();
                    this.calculateDimensions();
                    this.goTo(this.currentIndex, false);
                }, 250),
            );
        }

        buildStructure() {
            // Get slides
            this.slides = Array.from(
                this.container.querySelectorAll(".slider-slide"),
            );
            this.slideCount = this.slides.length;

            if (this.slideCount === 0) return;

            // Create track wrapper
            this.track = document.createElement("div");
            this.track.className = "slider-track";

            // Move slides into track
            this.slides.forEach((slide) => {
                this.track.appendChild(slide);
            });

            // Create wrapper structure
            this.wrapper = document.createElement("div");
            this.wrapper.className = "slider-wrapper";
            this.wrapper.appendChild(this.track);

            this.container.appendChild(this.wrapper);

            // Add arrows
            if (
                this.options.arrows &&
                this.slideCount > this.options.slidesToShow
            ) {
                this.createArrows();
            }

            // Add dots
            if (
                this.options.dots &&
                this.slideCount > this.options.slidesToShow
            ) {
                this.createDots();
            }

            // Add slider class
            this.container.classList.add("slider-initialized");
        }

        createArrows() {
            const prevArrow = document.createElement("button");
            prevArrow.className = "slider-arrow slider-prev";
            prevArrow.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            `;
            prevArrow.addEventListener("click", () => this.prev());

            const nextArrow = document.createElement("button");
            nextArrow.className = "slider-arrow slider-next";
            nextArrow.innerHTML = `
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            `;
            nextArrow.addEventListener("click", () => this.next());

            this.container.appendChild(prevArrow);
            this.container.appendChild(nextArrow);

            this.prevArrow = prevArrow;
            this.nextArrow = nextArrow;
        }

        createDots() {
            const dotsContainer = document.createElement("div");
            dotsContainer.className = "slider-dots";

            const dotCount = Math.ceil(
                (this.slideCount - this.options.slidesToShow + 1) /
                    this.options.slidesToScroll,
            );

            for (let i = 0; i < dotCount; i++) {
                const dot = document.createElement("button");
                dot.className = "slider-dot";
                if (i === 0) dot.classList.add("active");
                dot.addEventListener("click", () =>
                    this.goTo(i * this.options.slidesToScroll),
                );
                dotsContainer.appendChild(dot);
            }

            this.container.appendChild(dotsContainer);
            this.dotsContainer = dotsContainer;
        }

        calculateDimensions() {
            if (this.slideCount === 0) return;

            const containerWidth = this.wrapper.offsetWidth;
            const totalGap = this.options.gap * (this.options.slidesToShow - 1);
            const slideWidth =
                (containerWidth - totalGap) / this.options.slidesToShow;

            this.slideWidth = slideWidth;

            this.slides.forEach((slide) => {
                slide.style.width = `${slideWidth}px`;
                slide.style.marginRight = this.options.rtl
                    ? "0"
                    : `${this.options.gap}px`;
                slide.style.marginLeft = this.options.rtl
                    ? `${this.options.gap}px`
                    : "0";
            });

            // Remove margin from last visible slide
            if (this.slides[this.slideCount - 1]) {
                this.slides[this.slideCount - 1].style.marginRight = "0";
                this.slides[this.slideCount - 1].style.marginLeft = "0";
            }

            this.track.style.width = `${(slideWidth + this.options.gap) * this.slideCount}px`;
        }

        bindEvents() {
            // Touch events
            this.track.addEventListener(
                "touchstart",
                (e) => this.onDragStart(e),
                { passive: true },
            );
            this.track.addEventListener(
                "touchmove",
                (e) => this.onDragMove(e),
                { passive: false },
            );
            this.track.addEventListener("touchend", (e) => this.onDragEnd(e));

            // Mouse events
            this.track.addEventListener("mousedown", (e) =>
                this.onDragStart(e),
            );
            this.track.addEventListener("mousemove", (e) => this.onDragMove(e));
            this.track.addEventListener("mouseup", (e) => this.onDragEnd(e));
            this.track.addEventListener("mouseleave", (e) => this.onDragEnd(e));

            // Pause autoplay on hover
            if (this.options.pauseOnHover && this.options.autoplay) {
                this.container.addEventListener("mouseenter", () =>
                    this.pauseAutoplay(),
                );
                this.container.addEventListener("mouseleave", () =>
                    this.resumeAutoplay(),
                );
            }
        }

        onDragStart(e) {
            this.isDragging = true;
            this.startX =
                e.type === "touchstart" ? e.touches[0].clientX : e.clientX;
            this.dragOffset = 0;
            this.track.style.transition = "none";
        }

        onDragMove(e) {
            if (!this.isDragging) return;

            e.preventDefault();

            const currentX =
                e.type === "touchmove" ? e.touches[0].clientX : e.clientX;
            this.dragOffset = currentX - this.startX;

            if (this.options.rtl) {
                this.dragOffset = -this.dragOffset;
            }

            const currentTranslate = this.getTranslateValue();
            this.setTranslate(currentTranslate + this.dragOffset);
        }

        onDragEnd() {
            if (!this.isDragging) return;

            this.isDragging = false;
            this.track.style.transition = `transform ${this.options.speed}ms ease`;

            const threshold = this.slideWidth / 4;

            if (Math.abs(this.dragOffset) > threshold) {
                if (this.dragOffset > 0) {
                    this.prev();
                } else {
                    this.next();
                }
            } else {
                this.goTo(this.currentIndex, true);
            }
        }

        getTranslateValue() {
            const offset =
                this.currentIndex * (this.slideWidth + this.options.gap);
            return this.options.rtl ? offset : -offset;
        }

        setTranslate(value) {
            this.track.style.transform = `translate3d(${value}px, 0, 0)`;
        }

        goTo(index, animate = true) {
            // Handle boundaries
            const maxIndex = this.slideCount - this.options.slidesToShow;

            if (this.options.infinite) {
                if (index < 0) index = maxIndex;
                if (index > maxIndex) index = 0;
            } else {
                index = Math.max(0, Math.min(index, maxIndex));
            }

            this.currentIndex = index;

            // Animate
            this.track.style.transition = animate
                ? `transform ${this.options.speed}ms ease`
                : "none";
            this.setTranslate(this.getTranslateValue());

            // Update dots
            this.updateDots();

            // Update arrows state (for non-infinite)
            if (!this.options.infinite) {
                this.updateArrows();
            }
        }

        next() {
            this.goTo(this.currentIndex + this.options.slidesToScroll);
        }

        prev() {
            this.goTo(this.currentIndex - this.options.slidesToScroll);
        }

        updateDots() {
            if (!this.dotsContainer) return;

            const dots = this.dotsContainer.querySelectorAll(".slider-dot");
            const activeDotIndex = Math.floor(
                this.currentIndex / this.options.slidesToScroll,
            );

            dots.forEach((dot, i) => {
                dot.classList.toggle("active", i === activeDotIndex);
            });
        }

        updateArrows() {
            if (!this.prevArrow || !this.nextArrow) return;

            const maxIndex = this.slideCount - this.options.slidesToShow;

            this.prevArrow.disabled = this.currentIndex === 0;
            this.nextArrow.disabled = this.currentIndex >= maxIndex;
        }

        startAutoplay() {
            if (this.isPlaying) return;

            this.isPlaying = true;
            this.autoplayInterval = setInterval(() => {
                this.next();
            }, this.options.autoplaySpeed);
        }

        pauseAutoplay() {
            this.isPlaying = false;
            clearInterval(this.autoplayInterval);
        }

        resumeAutoplay() {
            if (this.options.autoplay) {
                this.startAutoplay();
            }
        }

        handleResponsive() {
            if (!this.options.responsive.length) return;

            const windowWidth = window.innerWidth;
            let newOptions = { ...this.options };

            // Sort breakpoints descending
            const breakpoints = [...this.options.responsive].sort(
                (a, b) => b.breakpoint - a.breakpoint,
            );

            for (const bp of breakpoints) {
                if (windowWidth <= bp.breakpoint) {
                    newOptions = { ...newOptions, ...bp.settings };
                }
            }

            // Apply new settings
            Object.assign(this.options, newOptions);
        }

        debounce(func, wait) {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        destroy() {
            this.pauseAutoplay();
            // Remove event listeners and cleanup
        }
    }

    // ==========================================================================
    // BANNER SLIDER (Full-width hero slider)
    // ==========================================================================

    class BannerSlider extends WjhtakSlider {
        constructor(container, options = {}) {
            const defaultOptions = {
                autoplay: true,
                autoplaySpeed: 6000,
                arrows: true,
                dots: true,
                slidesToShow: 1,
                speed: 700,
                ...options,
            };

            super(container, defaultOptions);
        }

        buildStructure() {
            super.buildStructure();

            // Add progress bar for autoplay
            if (this.options.autoplay) {
                this.createProgressBar();
            }
        }

        createProgressBar() {
            const progressBar = document.createElement("div");
            progressBar.className = "slider-progress";
            progressBar.innerHTML = '<div class="slider-progress-bar"></div>';
            this.container.appendChild(progressBar);
            this.progressBar = progressBar.querySelector(
                ".slider-progress-bar",
            );
        }

        startAutoplay() {
            if (this.isPlaying) return;

            this.isPlaying = true;
            this.animateProgress();

            this.autoplayInterval = setInterval(() => {
                this.next();
                this.animateProgress();
            }, this.options.autoplaySpeed);
        }

        animateProgress() {
            if (!this.progressBar) return;

            this.progressBar.style.transition = "none";
            this.progressBar.style.width = "0%";

            // Trigger reflow
            this.progressBar.offsetHeight;

            this.progressBar.style.transition = `width ${this.options.autoplaySpeed}ms linear`;
            this.progressBar.style.width = "100%";
        }

        pauseAutoplay() {
            super.pauseAutoplay();

            if (this.progressBar) {
                const computedStyle = getComputedStyle(this.progressBar);
                this.progressBar.style.width = computedStyle.width;
            }
        }

        resumeAutoplay() {
            if (this.options.autoplay) {
                this.animateProgress();
                this.startAutoplay();
            }
        }
    }

    // ==========================================================================
    // AUTO-INITIALIZE
    // ==========================================================================

    document.addEventListener("DOMContentLoaded", () => {
        // Initialize banner sliders
        document
            .querySelectorAll('[data-slider="banner"]')
            .forEach((container) => {
                new BannerSlider(container);
            });

        // Initialize trip sliders
        document
            .querySelectorAll('[data-slider="trips"]')
            .forEach((container) => {
                new WjhtakSlider(container, {
                    slidesToShow: 3,
                    gap: 24,
                    autoplay: false,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: { slidesToShow: 2 },
                        },
                        {
                            breakpoint: 640,
                            settings: { slidesToShow: 1 },
                        },
                    ],
                });
            });

        // Initialize destination sliders
        document
            .querySelectorAll('[data-slider="destinations"]')
            .forEach((container) => {
                new WjhtakSlider(container, {
                    slidesToShow: 4,
                    gap: 20,
                    autoplay: false,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: { slidesToShow: 3 },
                        },
                        {
                            breakpoint: 768,
                            settings: { slidesToShow: 2 },
                        },
                        {
                            breakpoint: 480,
                            settings: { slidesToShow: 1 },
                        },
                    ],
                });
            });

        // Initialize testimonial sliders
        document
            .querySelectorAll('[data-slider="testimonials"]')
            .forEach((container) => {
                new WjhtakSlider(container, {
                    slidesToShow: 1,
                    autoplay: true,
                    autoplaySpeed: 5000,
                    dots: true,
                    arrows: false,
                });
            });
    });

    // Expose classes globally
    window.WjhtakSlider = WjhtakSlider;
    window.BannerSlider = BannerSlider;
})();
