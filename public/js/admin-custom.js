/**
 * WJHTAK Admin Panel - Custom JavaScript
 * Professional Travel & Tourism Admin Dashboard
 *
 * DO NOT write inline JS in Blade templates - use this file instead
 */

(function () {
    "use strict";

    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener("DOMContentLoaded", function () {
        initSidebar();
        initCards();
        initTables();
        initForms();
        initTooltips();
        initAnimations();
        initToastr();
    });

    // ============================================
    // TOASTR CONFIGURATION
    // ============================================
    function initToastr() {
        if (typeof toastr !== "undefined") {
            toastr.options = {
                closeButton: true,
                debug: false,
                newestOnTop: true,
                progressBar: true,
                positionClass: "toast-top-center",
                preventDuplicates: false,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                timeOut: "5000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut",
                rtl: document.dir === "rtl",
            };
        }
    }

    // Use jQuery for delegation to be consistent with template scripts
    jQuery(document).on("click", ".nav-control", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        jQuery("#main-wrapper").toggleClass("menu-toggle");
        jQuery(".hamburger").toggleClass("is-active");
    });

    // ============================================
    // SIDEBAR FUNCTIONALITY
    // ============================================
    function initSidebar() {
        const sidebar = document.querySelector(".dlabnav");
        const menuItems = document.querySelectorAll(".metismenu > li > a");

        // Add active class based on current URL
        const currentPath = window.location.pathname;

        menuItems.forEach(function (item) {
            const href = item.getAttribute("href");
            if (href && currentPath.includes(href)) {
                item.parentElement.classList.add("mm-active");

                // If it's a submenu item, expand parent
                const parentUl = item.closest("ul.sub-menu");
                if (parentUl) {
                    parentUl.classList.add("mm-show");
                    parentUl.parentElement.classList.add("mm-active");
                }
            }
        });
    }

    // ============================================
    // CARD ANIMATIONS
    // ============================================
    function initCards() {
        const cards = document.querySelectorAll(".card");

        // Fade in animation on scroll
        const observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("fade-in-up");
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1 },
        );

        cards.forEach(function (card) {
            observer.observe(card);
        });
    }

    // ============================================
    // TABLE ENHANCEMENTS
    // ============================================
    function initTables() {
        const tables = document.querySelectorAll(".table");

        tables.forEach(function (table) {
            // Add hover effect to rows
            const rows = table.querySelectorAll("tbody tr");
            rows.forEach(function (row) {
                row.addEventListener("mouseenter", function () {
                    this.style.backgroundColor = "rgba(19, 88, 70, 0.02)";
                });

                row.addEventListener("mouseleave", function () {
                    this.style.backgroundColor = "";
                });
            });
        });
    }

    // ============================================
    // FORM ENHANCEMENTS
    // ============================================
    function initForms() {
        // File Upload Preview
        const fileInputs = document.querySelectorAll('input[type="file"]');

        fileInputs.forEach(function (input) {
            input.addEventListener("change", function (e) {
                const wrapper = this.closest(".file-upload-wrapper");
                if (wrapper && this.files.length > 0) {
                    const fileName = this.files[0].name;
                    const label = wrapper.querySelector(".file-upload-text");
                    if (label) {
                        label.textContent = fileName;
                    }

                    // Image preview
                    if (this.files[0].type.startsWith("image/")) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            let preview =
                                wrapper.querySelector(".file-preview");
                            if (!preview) {
                                preview = document.createElement("img");
                                preview.className = "file-preview mt-3";
                                preview.style.maxWidth = "200px";
                                preview.style.borderRadius = "10px";
                                wrapper.appendChild(preview);
                            }
                            preview.src = e.target.result;
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                }
            });
        });

        // Form Validation Styling
        const forms = document.querySelectorAll("form");

        forms.forEach(function (form) {
            form.addEventListener("submit", function (e) {
                const requiredFields = form.querySelectorAll("[required]");
                let isValid = true;

                // Clear previous frontend empty field errors
                form.querySelectorAll(
                    ".invalid-feedback.frontend-error",
                ).forEach((el) => el.remove());

                requiredFields.forEach(function (field) {
                    if (!field.value.trim() && field.type !== "hidden") {
                        isValid = false;
                        field.classList.add("is-invalid");
                        field.classList.remove("is-valid");

                        // Inject error text
                        let errorMsg = document.createElement("div");
                        errorMsg.className =
                            "invalid-feedback d-block frontend-error";
                        errorMsg.innerHTML =
                            '<i class="fas fa-exclamation-circle me-1"></i> ' +
                            ((window.Translations &&
                                window.Translations.required_field) ||
                                "This field is required");

                        if (
                            field.parentElement.classList.contains(
                                "input-wrapper",
                            ) ||
                            field.parentElement.classList.contains(
                                "input-group",
                            )
                        ) {
                            field.parentElement.after(errorMsg);
                        } else {
                            field.after(errorMsg);
                        }
                    } else {
                        field.classList.remove("is-invalid");
                        field.classList.add("is-valid");
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    // Form is valid - Check if we should apply loading state
                    // Only apply if the event hasn't been prevented (standard submit)
                    if (!e.defaultPrevented) {
                        const submitBtn = form.querySelector(
                            'button[type="submit"]',
                        );
                        if (
                            submitBtn &&
                            !submitBtn.disabled &&
                            !submitBtn.classList.contains("no-loading")
                        ) {
                            const text =
                                submitBtn.dataset.loadingText ||
                                (window.Translations &&
                                    window.Translations.loading_text) ||
                                "Loading...";

                            // Preserve width to avoid layout jump
                            const w = submitBtn.offsetWidth;
                            submitBtn.style.minWidth = w + "px";

                            submitBtn.dataset.originalHtml =
                                submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            // Add Spinner
                            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ${text}`;
                        }
                    }
                }
            });
        });

        // Live validation
        const inputs = document.querySelectorAll(".form-control, .form-select");
        inputs.forEach(function (input) {
            input.addEventListener("blur", function () {
                if (this.hasAttribute("required") && !this.value.trim()) {
                    this.classList.add("is-invalid");
                } else if (this.value.trim()) {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                }
            });

            input.addEventListener("input", function () {
                this.classList.remove("is-invalid");
                this.classList.remove("is-valid");
                // Remove sibling frontend or ajax errors
                let wrapper = this.parentElement;
                if (
                    wrapper.classList.contains("input-wrapper") ||
                    wrapper.classList.contains("input-group")
                ) {
                    if (
                        wrapper.nextElementSibling &&
                        wrapper.nextElementSibling.classList.contains(
                            "invalid-feedback",
                        )
                    ) {
                        wrapper.nextElementSibling.remove();
                    }
                } else if (
                    this.nextElementSibling &&
                    this.nextElementSibling.classList.contains(
                        "invalid-feedback",
                    )
                ) {
                    this.nextElementSibling.remove();
                }
            });
        });

        // Global AJAX Handlers for Forms
        if (typeof jQuery !== "undefined") {
            jQuery(document).ajaxComplete(function (event, xhr, settings) {
                // Reset loading buttons
                document
                    .querySelectorAll('button[type="submit"][disabled]')
                    .forEach(function (btn) {
                        if (
                            btn.hasAttribute("data-original-html") ||
                            btn.dataset.originalHtml
                        ) {
                            btn.innerHTML = btn.dataset.originalHtml;
                            btn.disabled = false;
                            delete btn.dataset.originalHtml;
                        }
                    });
            });

            jQuery(document).ajaxError(
                function (event, jqxhr, settings, thrownError) {
                    if (
                        jqxhr.status === 422 &&
                        jqxhr.responseJSON &&
                        jqxhr.responseJSON.errors
                    ) {
                        let errors = jqxhr.responseJSON.errors;

                        // Clear previous AJAX errors
                        jQuery(".is-invalid").removeClass("is-invalid");
                        jQuery(".invalid-feedback.ajax-error").remove();

                        let firstErrorField = null;

                        Object.keys(errors).forEach(function (key) {
                            let inputName = key;
                            if (inputName.includes(".")) {
                                let parts = inputName.split(".");
                                inputName =
                                    parts[0] +
                                    "[" +
                                    parts.slice(1).join("][") +
                                    "]";
                            }

                            let input = jQuery(
                                '[name="' +
                                    inputName +
                                    '"], [name="' +
                                    inputName +
                                    '[]"]',
                            );
                            if (input.length) {
                                input.addClass("is-invalid");
                                let errorMsg = jQuery(
                                    '<div class="invalid-feedback d-block ajax-error"><i class="fas fa-exclamation-circle me-1"></i> ' +
                                        errors[key][0] +
                                        "</div>",
                                );

                                if (
                                    input.parent().hasClass("input-wrapper") ||
                                    input.parent().hasClass("input-group")
                                ) {
                                    input.parent().after(errorMsg);
                                } else {
                                    input.after(errorMsg);
                                }

                                if (!firstErrorField) firstErrorField = input;
                            } else {
                                if (typeof toastr !== "undefined") {
                                    toastr.error(errors[key][0]);
                                }
                            }
                        });

                        if (firstErrorField && firstErrorField.length) {
                            jQuery("html, body").animate(
                                {
                                    scrollTop:
                                        firstErrorField.offset().top - 100,
                                },
                                500,
                            );
                            firstErrorField.focus();
                        }
                    }
                },
            );
        }
    }

    // ============================================
    // TOOLTIPS & POPOVERS
    // ============================================
    function initTooltips() {
        // Initialize Bootstrap tooltips if available
        if (typeof bootstrap !== "undefined") {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]'),
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const popoverTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="popover"]'),
            );
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }
    }

    // ============================================
    // SCROLL ANIMATIONS
    // ============================================
    function initAnimations() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener("click", function (e) {
                e.preventDefault();
                const target = document.querySelector(
                    this.getAttribute("href"),
                );
                if (target) {
                    target.scrollIntoView({
                        behavior: "smooth",
                    });
                }
            });
        });

        // Counter animation for stat cards
        const counters = document.querySelectorAll(".stat-value[data-count]");

        counters.forEach(function (counter) {
            const target = parseInt(counter.getAttribute("data-count"));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = function () {
                current += step;
                if (current < target) {
                    counter.textContent = Math.round(current).toLocaleString();
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString();
                }
            };

            // Start animation when visible
            const observer = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    updateCounter();
                    observer.disconnect();
                }
            });

            observer.observe(counter);
        });
    }

    // ============================================
    // UTILITY FUNCTIONS
    // ============================================
    window.WJHTAKAdmin = {
        // Show toast notification
        toast: function (message, type) {
            type = type || "success";
            if (typeof toastr !== "undefined") {
                toastr[type](message);
            } else {
                alert(message);
            }
        },

        // Confirm dialog
        confirm: function (message, callback) {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    title: window.Translations.confirm_title || "Confirmation",
                    text: message,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#135846",
                    cancelButtonColor: "#d33",
                    confirmButtonText: window.Translations.confirm_yes || "Yes",
                    cancelButtonText:
                        window.Translations.confirm_cancel || "Cancel",
                }).then(function (result) {
                    if (result.isConfirmed && callback) {
                        callback();
                    }
                });
            } else if (confirm(message)) {
                callback();
            }
        },

        // Format currency
        formatCurrency: function (amount, currency) {
            currency = currency || "SAR";
            return new Intl.NumberFormat("ar-SA", {
                style: "currency",
                currency: currency,
            }).format(amount);
        },

        // Format date
        formatDate: function (date) {
            return new Intl.DateTimeFormat("ar-SA", {
                year: "numeric",
                month: "long",
                day: "numeric",
            }).format(new Date(date));
        },

        /**
         * Set button loading state
         * @param {jQuery|HTMLElement} btn
         * @param {boolean} isLoading
         * @param {string} loadingText
         */
        btnLoading: function (btn, isLoading, loadingText) {
            const $btn = jQuery(btn);
            if (isLoading) {
                const originalContent = $btn.html();
                $btn.data("original-content", originalContent);
                $btn.prop("disabled", true);
                const text =
                    loadingText ||
                    window.Translations.loading_text ||
                    "Loading...";
                $btn.html(
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> ${text}`,
                );
            } else {
                const originalContent = $btn.data("original-content");
                if (originalContent) {
                    $btn.html(originalContent);
                }
                $btn.prop("disabled", false);
            }
        },
    };
})();
