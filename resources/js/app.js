import "./bootstrap";

// Template Scripts (Depend on global jQuery and Plugins)
import "./template/settings.js";
import "./template/custom.js";
import "./template/dlabnav-init.js";
import "./template/demo.js";
import "./template/styleSwitcher.js";
import "./template/dashboard/dashboard-1.js";

// Global AJAX Setup
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});
