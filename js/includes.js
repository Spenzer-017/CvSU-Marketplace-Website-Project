const menuToggle = document.querySelector(".menu-toggle");
const nav = document.querySelector("nav");

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

// Menu Toggle Event
menuToggle.addEventListener("click", () => {
    nav.classList.toggle("open");
});

if (darkmode === "active") enableDarkmode();

//Theme Swtich Event
if (themeSwitch) {
    themeSwitch.addEventListener("click", () => {
        darkmode = localStorage.getItem("darkmode");
        darkmode !== "active" ? enableDarkmode() : disableDarkmode();
    });
}