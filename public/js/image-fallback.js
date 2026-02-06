/**
 * Image Fallback Handler
 * Handles broken images by replacing them with a default placeholder
 */
(function () {
    "use strict";

    // Default placeholder image path
    const DEFAULT_IMAGE = "/images/default-placeholder.svg";

    /**
     * Handle image error by replacing src with default placeholder
     * @param {HTMLImageElement} img - The image element that failed to load
     */
    function handleImageError(img) {
        // Prevent infinite loop if placeholder also fails
        if (img.dataset.fallbackApplied) return;

        img.dataset.fallbackApplied = "true";
        img.src = DEFAULT_IMAGE;
        img.alt = img.alt || "Image not available";

        // Add a subtle visual indicator
        img.style.objectFit = "contain";
        img.style.backgroundColor = "var(--color-surface-hover, #f3f4f6)";
    }

    /**
     * Attach error handler to all images on the page
     */
    function initImageFallbacks() {
        // Handle existing images
        document.querySelectorAll("img").forEach(function (img) {
            img.onerror = function () {
                handleImageError(this);
            };

            // Check if image is already broken (cached broken images)
            if (img.complete && img.naturalHeight === 0 && img.src) {
                handleImageError(img);
            }
        });

        // Handle dynamically added images using MutationObserver
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeName === "IMG") {
                        node.onerror = function () {
                            handleImageError(this);
                        };
                    }
                    // Check for images inside added elements
                    if (node.querySelectorAll) {
                        node.querySelectorAll("img").forEach(function (img) {
                            img.onerror = function () {
                                handleImageError(this);
                            };
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initImageFallbacks);
    } else {
        initImageFallbacks();
    }

    // Expose globally for manual use if needed
    window.handleImageError = handleImageError;
})();
