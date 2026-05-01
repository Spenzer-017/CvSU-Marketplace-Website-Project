// Menu Toggle Variables (Guest header)
const menuToggle = document.querySelector("header .menu-toggle");
const nav = document.querySelector("header nav");

// Theme Switch Variables
let darkmode = localStorage.getItem("darkmode");
const themeSwitch = document.getElementById("theme-switch");

// Darkmode Enable Function
const enableDarkmode = () => {
    document.body.classList.add("darkmode");
    localStorage.setItem("darkmode", "active");
}

// Darkmode Disable Function
const disableDarkmode = () => {
    document.body.classList.remove("darkmode");
    localStorage.setItem("darkmode", null);
}

// Guest Menu Toggle Event
if (menuToggle && nav) {
    menuToggle.addEventListener("click", () => {
        nav.classList.toggle("open");
        menuToggle.classList.toggle("open");
    });
}

if (darkmode === "active") enableDarkmode();

// Theme Switch Event
if (themeSwitch) {
    themeSwitch.addEventListener("click", () => {
        darkmode = localStorage.getItem("darkmode");
        darkmode !== "active" ? enableDarkmode() : disableDarkmode();
    });
}

// Theme Switch Button Event
const sidebarThemeBtn = document.getElementById("sidebarThemeBtn");
sidebarThemeBtn.addEventListener("click", () => {
    darkmode = localStorage.getItem("darkmode");
    darkmode !== "active" ? enableDarkmode() : disableDarkmode();
});

// ============================================================
// SIDEBAR (Logged-in users)
// ============================================================
(function () {
    const sidebar = document.getElementById("sidebar");
    if (!sidebar) return;

    // Mark body for layout shift
    document.body.classList.add("has-sidebar");

    const backdrop = document.getElementById("sidebarBackdrop");
    const mobileToggle = document.getElementById("menuToggle");

    // ---- Mobile sidebar open/close ----
    function openSidebar() {
        sidebar.classList.add("expanded");
        if (backdrop) {
            backdrop.style.display = "block";
            requestAnimationFrame(() => backdrop.classList.add("visible"));
        }
        if (mobileToggle) mobileToggle.classList.add("open");
    }

    function closeSidebar() {
        sidebar.classList.remove("expanded");
        if (backdrop) {
            backdrop.classList.remove("visible");
            setTimeout(() => { backdrop.style.display = "none"; }, 260);
        }
        if (mobileToggle) mobileToggle.classList.remove("open");
    }

    function isMobile() {
        return window.innerWidth <= 1000;
    }

    if (mobileToggle) {
        mobileToggle.addEventListener("click", () => {
            if (sidebar.classList.contains("expanded")) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener("click", closeSidebar);
    }

    // Close sidebar on resize back to desktop
    window.addEventListener("resize", () => {
        if (!isMobile()) {
            closeSidebar();
        }
    });

    // ---- Submenu toggle ----
    const submenuWraps = document.querySelectorAll(".sidebar-submenu-wrap");
    submenuWraps.forEach(wrap => {
        const toggle = wrap.querySelector(".sidebar-submenu-toggle");
        if (!toggle) return;
        toggle.addEventListener("click", () => {
            wrap.classList.toggle("open");
        });
    });

    // ---- Profile dropdown ----
    const profileWrap = document.getElementById("profileDropdownWrap");
    const profileBtn = document.getElementById("profileAvatarBtn");
    const profileDropdown = document.getElementById("profileDropdown");

    if (profileBtn && profileWrap) {
        profileBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            profileWrap.classList.toggle("open");
        });

        document.addEventListener("click", (e) => {
            if (!profileWrap.contains(e.target)) {
                profileWrap.classList.remove("open");
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") profileWrap.classList.remove("open");
        });
    }
})();