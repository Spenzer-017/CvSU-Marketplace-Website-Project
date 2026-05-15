// Menu Toggle Variables
const menuToggle = document.querySelector(".menu-toggle");
const nav = document.querySelector("nav");

// Theme Switch Variables
let darkmode = localStorage.getItem("darkmode");
const themeSwitch = document.getElementById("theme-switch");

// Profile Dropdown Menu Variables
const profileBtn = document.getElementById('profileDropdownBtn');
const profileMenu = document.getElementById('profileDropdownMenu');

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

// Menu Toggle Event
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

// Profile Dropdown Event
profileBtn.addEventListener('click', function (e) {
    e.preventDefault();
    profileMenu.classList.toggle('show');
});

document.addEventListener('click', function (e) {
    if (
        !profileBtn.contains(e.target) &&
        !profileMenu.contains(e.target)
    ) {
        profileMenu.classList.remove('show');
    }
});