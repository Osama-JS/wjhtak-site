/**
 * WJHTAK Auth Login - JavaScript
 * Professional Travel & Tourism Login Page
 *
 * DO NOT write inline JS in Blade templates - use this file instead
 */

(function () {
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        initPasswordToggle();
        initFormEnhancements();
        initAnimations();
    });

    // ============================================
    // PASSWORD VISIBILITY TOGGLE
    // ============================================
    function initPasswordToggle() {
        const toggleButtons = document.querySelectorAll(".password-toggle");

        toggleButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                const input = this.parentElement.querySelector("input");
                const icon = this.querySelector("i");

                if (input.type === "password") {
                    input.type = "text";
                    if (icon) {
                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");
                    }
                } else {
                    input.type = "password";
                    if (icon) {
                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");
                    }
                }
            });
        });
    }

    // ============================================
    // FORM ENHANCEMENTS
    // ============================================
    function initFormEnhancements() {
        const form = document.querySelector(".auth-form");
        if (!form) return;

        const inputs = form.querySelectorAll(
            'input:not([type="checkbox"]):not([type="submit"])',
        );

        inputs.forEach(function (input) {
            // Focus animation
            input.addEventListener("focus", function () {
                this.parentElement.classList.add("focused");
            });

            input.addEventListener("blur", function () {
                if (!this.value) {
                    this.parentElement.classList.remove("focused");
                }
            });

            // Check for pre-filled values
            if (input.value) {
                input.parentElement.classList.add("focused");
            }
        });

        // Submit button loading state
        form.addEventListener("submit", function (e) {
            const submitBtn = this.querySelector(".auth-submit-btn");
            if (submitBtn) {
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i> جاري تسجيل الدخول...';
                submitBtn.disabled = true;
            }
        });
    }

    // ============================================
    // PAGE ANIMATIONS
    // ============================================
    function initAnimations() {
        // Animate form elements on load
        const formElements = document.querySelectorAll(".auth-card > *");

        formElements.forEach(function (element, index) {
            element.style.opacity = "0";
            element.style.transform = "translateY(20px)";

            setTimeout(function () {
                element.style.transition = "all 0.5s ease";
                element.style.opacity = "1";
                element.style.transform = "translateY(0)";
            }, 100 * index);
        });

        // Animate travel icon
        const travelIcon = document.querySelector(".travel-icon");
        if (travelIcon) {
            travelIcon.style.animation = "float 3s ease-in-out infinite";
        }

        // Parallax effect on image section
        const imageSection = document.querySelector(".auth-travel-image");
        if (imageSection) {
            window.addEventListener("scroll", function () {
                const scrolled = window.pageYOffset;
                imageSection.style.backgroundPositionY =
                    -(scrolled * 0.3) + "px";
            });
        }
    }

    // ============================================
    // KEYBOARD NAVIGATION
    // ============================================
    document.addEventListener("keydown", function (e) {
        // Enter on inputs to submit form
        if (e.key === "Enter") {
            const activeElement = document.activeElement;
            if (activeElement.tagName === "INPUT") {
                const form = activeElement.closest("form");
                if (form) {
                    const nextInput = form.querySelector(
                        'input:not([type="hidden"]):not([type="submit"]):focus ~ input:not([type="hidden"]):not([type="submit"])',
                    );
                    if (nextInput) {
                        e.preventDefault();
                        nextInput.focus();
                    }
                }
            }
        }
    });
})();
