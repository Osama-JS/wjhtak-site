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

        // Sidebar toggle for mobile
        const toggleBtn = document.querySelector(".nav-control");
        if (toggleBtn) {
            toggleBtn.addEventListener("click", function () {
                document
                    .querySelector("#main-wrapper")
                    .classList.toggle("menu-toggle");
            });
        }
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

                requiredFields.forEach(function (field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add("is-invalid");
                        field.classList.remove("is-valid");
                    } else {
                        field.classList.remove("is-invalid");
                        field.classList.add("is-valid");
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });

        // Live validation
        const inputs = document.querySelectorAll(".form-control");
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
            });
        });
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
                    title: "تأكيد",
                    text: message,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#135846",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "نعم",
                    cancelButtonText: "إلغاء",
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
    };
})();
