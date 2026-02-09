"use strict";
function addSwitcher() {
    var dzSwitcher = `
    <div class="sidebar-right">
		<div class="bg-overlay"></div>
		<a href="javascript:void(0);" class="sidebar-right-trigger wave-effect wave-effect-x" data-bs-toggle="tooltip" data-placement="left" data-original-title="Settings">
			<span><i class="fa fa-cog fa-spin"></i></span>
		</a>
		<div class="sidebar-right-inner">
			<div class="admin-settings">
				<div class="opt-header-logo">
					<img src="images/logo.png" alt="" class="logo-abbr">
					<img src="images/logo-text.png" alt="" class="brand-title">
				</div>
				<div class="opt-header">
					<p>Customize your dashboard</p>
				</div>
				<div class="opt-body">
                    <div class="opt-body-inner">
                        <div class="opt-simple-bar">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Typography</h4>
                                        <p>Choose the font for your dashboard</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="typography">
                                            <option value="poppins" selected>Poppins</option>
                                            <option value="roboto">Roboto</option>
                                            <option value="opensans">Open Sans</option>
                                            <option value="helvetica">Helvetica</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Version</h4>
                                        <p>Choose the light or dark version</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="theme_version">
                                            <option value="light" selected>Light</option>
                                            <option value="dark">Dark</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Layout</h4>
                                        <p>Choose the Vertical or Horizontal layout</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="theme_layout">
                                            <option value="vertical" selected>Vertical</option>
                                            <option value="horizontal">Horizontal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Sidebar Style</h4>
                                        <p>Choose the sidebar style</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="sidebar_style">
                                            <option value="full" selected>Full</option>
                                            <option value="mini">Mini</option>
                                            <option value="compact">Compact</option>
                                            <option value="modern">Modern</option>
                                            <option value="overlay">Overlay</option>
                                            <option value="icon-hover">Icon-over</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Sidebar Position</h4>
                                        <p>Choose the sidebar position</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="sidebar_position">
                                            <option value="fixed" selected>Fixed</option>
                                            <option value="static">Static</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Header Position</h4>
                                        <p>Choose the header position</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="header_position">
                                            <option value="fixed" selected>Fixed</option>
                                            <option value="static">Static</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Container Layout</h4>
                                        <p>Choose the container layout</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="container_layout">
                                            <option value="full" selected>Full</option>
                                            <option value="wide">Wide</option>
                                            <option value="boxed">Boxed</option>
                                            <option value="wide-boxed">Wide Bag</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="opt-set-title">
                                        <h4>Direction</h4>
                                        <p>Choose the direction</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <select class="default-select wide form-control" id="theme_direction">
                                            <option value="ltr" selected>LTR</option>
                                            <option value="rtl">RTL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="opt-set-title">
                                        <h4>Primary Color</h4>
                                        <p>Choose the primary color</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <div class="opt-color-list">
                                            <span class="opt-color bg-color-1">
                                                <input type="radio" name="primary_bg" value="color_1" class="filled-in chk-col-primary" id="primary_color_1" checked>
                                                <label for="primary_color_1"></label>
                                            </span>
                                            <span class="opt-color bg-color-2">
                                                <input type="radio" name="primary_bg" value="color_2" class="filled-in chk-col-primary" id="primary_color_2">
                                                <label for="primary_color_2"></label>
                                            </span>
                                            <span class="opt-color bg-color-3">
                                                <input type="radio" name="primary_bg" value="color_3" class="filled-in chk-col-primary" id="primary_color_3">
                                                <label for="primary_color_3"></label>
                                            </span>
                                            <span class="opt-color bg-color-4">
                                                <input type="radio" name="primary_bg" value="color_4" class="filled-in chk-col-primary" id="primary_color_4">
                                                <label for="primary_color_4"></label>
                                            </span>
                                            <span class="opt-color bg-color-5">
                                                <input type="radio" name="primary_bg" value="color_5" class="filled-in chk-col-primary" id="primary_color_5">
                                                <label for="primary_color_5"></label>
                                            </span>
                                            <span class="opt-color bg-color-6">
                                                <input type="radio" name="primary_bg" value="color_6" class="filled-in chk-col-primary" id="primary_color_6">
                                                <label for="primary_color_6"></label>
                                            </span>
                                            <span class="opt-color bg-color-7">
                                                <input type="radio" name="primary_bg" value="color_7" class="filled-in chk-col-primary" id="primary_color_7">
                                                <label for="primary_color_7"></label>
                                            </span>
                                            <span class="opt-color bg-color-8">
                                                <input type="radio" name="primary_bg" value="color_8" class="filled-in chk-col-primary" id="primary_color_8">
                                                <label for="primary_color_8"></label>
                                            </span>
                                            <span class="opt-color bg-color-9">
                                                <input type="radio" name="primary_bg" value="color_9" class="filled-in chk-col-primary" id="primary_color_9">
                                                <label for="primary_color_9"></label>
                                            </span>
                                            <span class="opt-color bg-color-10">
                                                <input type="radio" name="primary_bg" value="color_10" class="filled-in chk-col-primary" id="primary_color_10">
                                                <label for="primary_color_10"></label>
                                            </span>
                                            <span class="opt-color bg-color-11">
                                                <input type="radio" name="primary_bg" value="color_11" class="filled-in chk-col-primary" id="primary_color_11">
                                                <label for="primary_color_11"></label>
                                            </span>
                                            <span class="opt-color bg-color-12">
                                                <input type="radio" name="primary_bg" value="color_12" class="filled-in chk-col-primary" id="primary_color_12">
                                                <label for="primary_color_12"></label>
                                            </span>
                                            <span class="opt-color bg-color-13">
                                                <input type="radio" name="primary_bg" value="color_13" class="filled-in chk-col-primary" id="primary_color_13">
                                                <label for="primary_color_13"></label>
                                            </span>
                                            <span class="opt-color bg-color-14">
                                                <input type="radio" name="primary_bg" value="color_14" class="filled-in chk-col-primary" id="primary_color_14">
                                                <label for="primary_color_14"></label>
                                            </span>
                                            <span class="opt-color bg-color-15">
                                                <input type="radio" name="primary_bg" value="color_15" class="filled-in chk-col-primary" id="primary_color_15">
                                                <label for="primary_color_15"></label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="opt-set-title">
                                        <h4>Navigation Header</h4>
                                        <p>Choose the navigation header background</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <div class="opt-color-list">
                                            <span class="opt-color bg-color-1">
                                                <input type="radio" name="navigation_header" value="color_1" class="filled-in chk-col-primary" id="nav_header_color_1" checked>
                                                <label for="nav_header_color_1"></label>
                                            </span>
                                            <span class="opt-color bg-color-2">
                                                <input type="radio" name="navigation_header" value="color_2" class="filled-in chk-col-primary" id="nav_header_color_2">
                                                <label for="nav_header_color_2"></label>
                                            </span>
                                            <span class="opt-color bg-color-3">
                                                <input type="radio" name="navigation_header" value="color_3" class="filled-in chk-col-primary" id="nav_header_color_3">
                                                <label for="nav_header_color_3"></label>
                                            </span>
                                            <span class="opt-color bg-color-4">
                                                <input type="radio" name="navigation_header" value="color_4" class="filled-in chk-col-primary" id="nav_header_color_4">
                                                <label for="nav_header_color_4"></label>
                                            </span>
                                            <span class="opt-color bg-color-5">
                                                <input type="radio" name="navigation_header" value="color_5" class="filled-in chk-col-primary" id="nav_header_color_5">
                                                <label for="nav_header_color_5"></label>
                                            </span>
                                            <span class="opt-color bg-color-6">
                                                <input type="radio" name="navigation_header" value="color_6" class="filled-in chk-col-primary" id="nav_header_color_6">
                                                <label for="nav_header_color_6"></label>
                                            </span>
                                            <span class="opt-color bg-color-7">
                                                <input type="radio" name="navigation_header" value="color_7" class="filled-in chk-col-primary" id="nav_header_color_7">
                                                <label for="nav_header_color_7"></label>
                                            </span>
                                            <span class="opt-color bg-color-8">
                                                <input type="radio" name="navigation_header" value="color_8" class="filled-in chk-col-primary" id="nav_header_color_8">
                                                <label for="nav_header_color_8"></label>
                                            </span>
                                            <span class="opt-color bg-color-9">
                                                <input type="radio" name="navigation_header" value="color_9" class="filled-in chk-col-primary" id="nav_header_color_9">
                                                <label for="nav_header_color_9"></label>
                                            </span>
                                            <span class="opt-color bg-color-10">
                                                <input type="radio" name="navigation_header" value="color_10" class="filled-in chk-col-primary" id="nav_header_color_10">
                                                <label for="nav_header_color_10"></label>
                                            </span>
                                            <span class="opt-color bg-color-11">
                                                <input type="radio" name="navigation_header" value="color_11" class="filled-in chk-col-primary" id="nav_header_color_11">
                                                <label for="nav_header_color_11"></label>
                                            </span>
                                            <span class="opt-color bg-color-12">
                                                <input type="radio" name="navigation_header" value="color_12" class="filled-in chk-col-primary" id="nav_header_color_12">
                                                <label for="nav_header_color_12"></label>
                                            </span>
                                            <span class="opt-color bg-color-13">
                                                <input type="radio" name="navigation_header" value="color_13" class="filled-in chk-col-primary" id="nav_header_color_13">
                                                <label for="nav_header_color_13"></label>
                                            </span>
                                            <span class="opt-color bg-color-14">
                                                <input type="radio" name="navigation_header" value="color_14" class="filled-in chk-col-primary" id="nav_header_color_14">
                                                <label for="nav_header_color_14"></label>
                                            </span>
                                            <span class="opt-color bg-color-15">
                                                <input type="radio" name="navigation_header" value="color_15" class="filled-in chk-col-primary" id="nav_header_color_15">
                                                <label for="nav_header_color_15"></label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="opt-set-title">
                                        <h4>Header Background</h4>
                                        <p>Choose the header background</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <div class="opt-color-list">
                                            <span class="opt-color bg-color-1">
                                                <input type="radio" name="header_bg" value="color_1" class="filled-in chk-col-primary" id="header_bg_color_1" checked>
                                                <label for="header_bg_color_1"></label>
                                            </span>
                                            <span class="opt-color bg-color-2">
                                                <input type="radio" name="header_bg" value="color_2" class="filled-in chk-col-primary" id="header_bg_color_2">
                                                <label for="header_bg_color_2"></label>
                                            </span>
                                            <span class="opt-color bg-color-3">
                                                <input type="radio" name="header_bg" value="color_3" class="filled-in chk-col-primary" id="header_bg_color_3">
                                                <label for="header_bg_color_3"></label>
                                            </span>
                                            <span class="opt-color bg-color-4">
                                                <input type="radio" name="header_bg" value="color_4" class="filled-in chk-col-primary" id="header_bg_color_4">
                                                <label for="header_bg_color_4"></label>
                                            </span>
                                            <span class="opt-color bg-color-5">
                                                <input type="radio" name="header_bg" value="color_5" class="filled-in chk-col-primary" id="header_bg_color_5">
                                                <label for="header_bg_color_5"></label>
                                            </span>
                                            <span class="opt-color bg-color-6">
                                                <input type="radio" name="header_bg" value="color_6" class="filled-in chk-col-primary" id="header_bg_color_6">
                                                <label for="header_bg_color_6"></label>
                                            </span>
                                            <span class="opt-color bg-color-7">
                                                <input type="radio" name="header_bg" value="color_7" class="filled-in chk-col-primary" id="header_bg_color_7">
                                                <label for="header_bg_color_7"></label>
                                            </span>
                                            <span class="opt-color bg-color-8">
                                                <input type="radio" name="header_bg" value="color_8" class="filled-in chk-col-primary" id="header_bg_color_8">
                                                <label for="header_bg_color_8"></label>
                                            </span>
                                            <span class="opt-color bg-color-9">
                                                <input type="radio" name="header_bg" value="color_9" class="filled-in chk-col-primary" id="header_bg_color_9">
                                                <label for="header_bg_color_9"></label>
                                            </span>
                                            <span class="opt-color bg-color-10">
                                                <input type="radio" name="header_bg" value="color_10" class="filled-in chk-col-primary" id="header_bg_color_10">
                                                <label for="header_bg_color_10"></label>
                                            </span>
                                            <span class="opt-color bg-color-11">
                                                <input type="radio" name="header_bg" value="color_11" class="filled-in chk-col-primary" id="header_bg_color_11">
                                                <label for="header_bg_color_11"></label>
                                            </span>
                                            <span class="opt-color bg-color-12">
                                                <input type="radio" name="header_bg" value="color_12" class="filled-in chk-col-primary" id="header_bg_color_12">
                                                <label for="header_bg_color_12"></label>
                                            </span>
                                            <span class="opt-color bg-color-13">
                                                <input type="radio" name="header_bg" value="color_13" class="filled-in chk-col-primary" id="header_bg_color_13">
                                                <label for="header_bg_color_13"></label>
                                            </span>
                                            <span class="opt-color bg-color-14">
                                                <input type="radio" name="header_bg" value="color_14" class="filled-in chk-col-primary" id="header_bg_color_14">
                                                <label for="header_bg_color_14"></label>
                                            </span>
                                            <span class="opt-color bg-color-15">
                                                <input type="radio" name="header_bg" value="color_15" class="filled-in chk-col-primary" id="header_bg_color_15">
                                                <label for="header_bg_color_15"></label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="opt-set-title">
                                        <h4>Sidebar Background</h4>
                                        <p>Choose the sidebar background</p>
                                    </div>
                                    <div class="opt-set-body">
                                        <div class="opt-color-list">
                                            <span class="opt-color bg-color-1">
                                                <input type="radio" name="sidebar_bg" value="color_1" class="filled-in chk-col-primary" id="sidebar_bg_color_1" checked>
                                                <label for="sidebar_bg_color_1"></label>
                                            </span>
                                            <span class="opt-color bg-color-2">
                                                <input type="radio" name="sidebar_bg" value="color_2" class="filled-in chk-col-primary" id="sidebar_bg_color_2">
                                                <label for="sidebar_bg_color_2"></label>
                                            </span>
                                            <span class="opt-color bg-color-3">
                                                <input type="radio" name="sidebar_bg" value="color_3" class="filled-in chk-col-primary" id="sidebar_bg_color_3">
                                                <label for="sidebar_bg_color_3"></label>
                                            </span>
                                            <span class="opt-color bg-color-4">
                                                <input type="radio" name="sidebar_bg" value="color_4" class="filled-in chk-col-primary" id="sidebar_bg_color_4">
                                                <label for="sidebar_bg_color_4"></label>
                                            </span>
                                            <span class="opt-color bg-color-5">
                                                <input type="radio" name="sidebar_bg" value="color_5" class="filled-in chk-col-primary" id="sidebar_bg_color_5">
                                                <label for="sidebar_bg_color_5"></label>
                                            </span>
                                            <span class="opt-color bg-color-6">
                                                <input type="radio" name="sidebar_bg" value="color_6" class="filled-in chk-col-primary" id="sidebar_bg_color_6">
                                                <label for="sidebar_bg_color_6"></label>
                                            </span>
                                            <span class="opt-color bg-color-7">
                                                <input type="radio" name="sidebar_bg" value="color_7" class="filled-in chk-col-primary" id="sidebar_bg_color_7">
                                                <label for="sidebar_bg_color_7"></label>
                                            </span>
                                            <span class="opt-color bg-color-8">
                                                <input type="radio" name="sidebar_bg" value="color_8" class="filled-in chk-col-primary" id="sidebar_bg_color_8">
                                                <label for="sidebar_bg_color_8"></label>
                                            </span>
                                            <span class="opt-color bg-color-9">
                                                <input type="radio" name="sidebar_bg" value="color_9" class="filled-in chk-col-primary" id="sidebar_bg_color_9">
                                                <label for="sidebar_bg_color_9"></label>
                                            </span>
                                            <span class="opt-color bg-color-10">
                                                <input type="radio" name="sidebar_bg" value="color_10" class="filled-in chk-col-primary" id="sidebar_bg_color_10">
                                                <label for="sidebar_bg_color_10"></label>
                                            </span>
                                            <span class="opt-color bg-color-11">
                                                <input type="radio" name="sidebar_bg" value="color_11" class="filled-in chk-col-primary" id="sidebar_bg_color_11">
                                                <label for="sidebar_bg_color_11"></label>
                                            </span>
                                            <span class="opt-color bg-color-12">
                                                <input type="radio" name="sidebar_bg" value="color_12" class="filled-in chk-col-primary" id="sidebar_bg_color_12">
                                                <label for="sidebar_bg_color_12"></label>
                                            </span>
                                            <span class="opt-color bg-color-13">
                                                <input type="radio" name="sidebar_bg" value="color_13" class="filled-in chk-col-primary" id="sidebar_bg_color_13">
                                                <label for="sidebar_bg_color_13"></label>
                                            </span>
                                            <span class="opt-color bg-color-14">
                                                <input type="radio" name="sidebar_bg" value="color_14" class="filled-in chk-col-primary" id="sidebar_bg_color_14">
                                                <label for="sidebar_bg_color_14"></label>
                                            </span>
                                            <span class="opt-color bg-color-15">
                                                <input type="radio" name="sidebar_bg" value="color_15" class="filled-in chk-col-primary" id="sidebar_bg_color_15">
                                                <label for="sidebar_bg_color_15"></label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
`;
    jQuery("body").append(dzSwitcher);
}

window.addSwitcher = addSwitcher;

$(document).ready(function () {
    if (typeof window.addSwitcher === "function") {
        window.addSwitcher();
    }
});

(function ($) {
    "use strict";
    addSwitcher();

    const body = $("body");
    const html = $("html");

    //get the DOM elements from right sidebar
    const typographySelect = $("#typography");
    const versionSelect = $("#theme_version");
    const layoutSelect = $("#theme_layout");
    const sidebarStyleSelect = $("#sidebar_style");
    const sidebarPositionSelect = $("#sidebar_position");
    const headerPositionSelect = $("#header_position");
    const containerLayoutSelect = $("#container_layout");
    const themeDirectionSelect = $("#theme_direction");

    //change the theme typography controller
    typographySelect.on("change", function () {
        body.attr("data-typography", this.value);

        setCookie("typography", this.value);
    });

    //change the theme version controller
    versionSelect.on("change", function () {
        body.attr("data-theme-version", this.value);

        setCookie("version", this.value);
    });

    //change the sidebar position controller
    sidebarPositionSelect.on("change", function () {
        this.value === "fixed" &&
        body.attr("data-sidebar-style") === "modern" &&
        body.attr("data-layout") === "vertical"
            ? alert(
                  "Sorry, Modern sidebar layout dosen't support fixed position!",
              )
            : body.attr("data-sidebar-position", this.value);
        setCookie("sidebarPosition", this.value);
    });

    //change the header position controller
    headerPositionSelect.on("change", function () {
        body.attr("data-header-position", this.value);
        setCookie("headerPosition", this.value);
    });

    //change the theme direction (rtl, ltr) controller
    themeDirectionSelect.on("change", function () {
        html.attr("dir", this.value);
        html.attr("class", "");
        html.addClass(this.value);
        body.attr("direction", this.value);
        setCookie("direction", this.value);
    });

    //change the theme layout controller
    layoutSelect.on("change", function () {
        if (body.attr("data-sidebar-style") === "overlay") {
            body.attr("data-sidebar-style", "full");
            body.attr("data-layout", this.value);
            return;
        }

        body.attr("data-layout", this.value);
        setCookie("layout", this.value);
    });

    //change the container layout controller
    containerLayoutSelect.on("change", function () {
        if (this.value === "boxed") {
            if (
                body.attr("data-layout") === "vertical" &&
                body.attr("data-sidebar-style") === "full"
            ) {
                body.attr("data-sidebar-style", "overlay");
                body.attr("data-container", this.value);

                setTimeout(function () {
                    $(window).trigger("resize");
                }, 200);

                return;
            }
        }

        body.attr("data-container", this.value);
        setCookie("containerLayout", this.value);
    });

    //change the sidebar style controller
    sidebarStyleSelect.on("change", function () {
        if (body.attr("data-layout") === "horizontal") {
            if (this.value === "overlay") {
                alert("Sorry! Overlay is not possible in Horizontal layout.");
                return;
            }
        }

        if (body.attr("data-layout") === "vertical") {
            if (
                body.attr("data-container") === "boxed" &&
                this.value === "full"
            ) {
                alert(
                    "Sorry! Full menu is not available in Vertical Boxed layout.",
                );
                return;
            }

            if (
                this.value === "modern" &&
                body.attr("data-sidebar-position") === "fixed"
            ) {
                alert(
                    "Sorry! Modern sidebar layout is not available in the fixed position. Please change the sidebar position into Static.",
                );
                return;
            }
        }

        body.attr("data-sidebar-style", this.value);

        if (body.attr("data-sidebar-style") === "icon-hover") {
            $(".dlabnav").on(
                "hover",
                function () {
                    $("#main-wrapper").addClass("iconhover-toggle");
                },
                function () {
                    $("#main-wrapper").removeClass("iconhover-toggle");
                },
            );
        }

        setCookie("sidebarStyle", this.value);
    });

    //change the nav-header background controller
    $('input[name="navigation_header"]').on("click", function () {
        body.attr("data-nav-headerbg", this.value);
        setCookie("navheaderBg", this.value);
    });

    //change the header background controller
    $('input[name="header_bg"]').on("click", function () {
        body.attr("data-headerbg", this.value);
        setCookie("headerBg", this.value);
    });

    //change the sidebar background controller
    $('input[name="sidebar_bg"]').on("click", function () {
        body.attr("data-sibebarbg", this.value);
        setCookie("sidebarBg", this.value);
    });

    //change the primary color controller
    $('input[name="primary_bg"]').on("click", function () {
        body.attr("data-primary", this.value);
        setCookie("primary", this.value);
    });
})(jQuery);
