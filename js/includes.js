// Menu Toggle Variables
const menuToggle = document.querySelector(".menu-toggle");
const nav = document.querySelector("nav");

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

// Modal Events
// (function () {
//     const overlay = document.getElementById('confirmModal');
//     const msgEl = document.getElementById('modalMessage');
//     const iconEl = document.getElementById('modalIcon');
//     const btnConfirm = document.getElementById('modalConfirm');
//     const btnCancel = document.getElementById('modalCancel');

//     // Safety check
//     if (!overlay || !btnConfirm || !btnCancel) return;

//     let pendingAction = null;
//     let previousActiveElement = null;

//     // Open Modal Function
//     function openModal(message, onConfirm, isGreen) {
//         previousActiveElement = document.activeElement;
        
//         msgEl.textContent = message;
//         pendingAction = onConfirm;

//         if (isGreen) {
//             btnConfirm.classList.add('modal-btn-green');
//             iconEl.classList.add('modal-icon-green');
//         } else {
//             btnConfirm.classList.remove('modal-btn-green');
//             iconEl.classList.remove('modal-icon-green');
//         }

//         overlay.classList.add('modal-open');
        
//         // Set aria-hidden on main content for screen readers
//         const mainContent = document.querySelector('main');
//         if (mainContent) mainContent.setAttribute('aria-hidden', 'true');
//         const header = document.querySelector('header');
//         if (header) header.setAttribute('aria-hidden', 'true');
//         const footer = document.querySelector('footer');
//         if (footer) footer.setAttribute('aria-hidden', 'true');
        
//         btnCancel.focus();
//     }

//     // Close Modal Function
//     function closeModal() {
//         overlay.classList.remove('modal-open');
//         pendingAction = null;
        
//         // Remove aria-hidden
//         document.querySelectorAll('[aria-hidden="true"]').forEach(function(el) {
//             el.removeAttribute('aria-hidden');
//         });
        
//         // Return focus to previous element
//         if (previousActiveElement) {
//             previousActiveElement.focus();
//             previousActiveElement = null;
//         }
//     }

//     btnConfirm.addEventListener('click', function () {
//         const action = pendingAction;
//         closeModal();
//         if (action) action();
//     });

//     btnCancel.addEventListener('click', closeModal);

//     overlay.addEventListener('click', function (e) {
//         if (e.target === overlay) closeModal();
//     });

//     document.addEventListener('keydown', function (e) {
//         if (e.key === 'Escape' && overlay.classList.contains('modal-open')) closeModal();
//     });

//     // Focus trap - keep tab focus inside modal when open
//     overlay.addEventListener('keydown', function(e) {
//         if (!overlay.classList.contains('modal-open')) return;
//         if (e.key !== 'Tab') return;
        
//         const focusableElements = overlay.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
//         if (focusableElements.length === 0) return;
        
//         const firstElement = focusableElements[0];
//         const lastElement = focusableElements[focusableElements.length - 1];
        
//         if (e.shiftKey) {
//             if (document.activeElement === firstElement) {
//                 e.preventDefault();
//                 lastElement.focus();
//             }
//         } else {
//             if (document.activeElement === lastElement) {
//                 e.preventDefault();
//                 firstElement.focus();
//             }
//         }
//     });

//     document.addEventListener('submit', function (e) {
//         const form = e.target;
//         const msg = form.dataset.confirm;
//         if (!msg || form.dataset.confirmed === "true") {
//             delete form.dataset.confirmed;
//             return;
//         }

//         e.preventDefault();
//         const isGreen = form.dataset.confirmGreen !== undefined;

//         openModal(msg, function () {
//             form.dataset.confirmed = "true";
//             form.submit();
//         }, isGreen);
//     });

//     document.addEventListener('click', function (e) {
//         const btn = e.target.closest('button[data-confirm]');
//         if (!btn) return;

//         const form = btn.closest('form');
    
//         if (form && form.dataset.confirm) {
//             return;
//         }

//         e.preventDefault();

//         const msg = btn.dataset.confirm;
//         const isGreen = btn.dataset.confirmGreen !== undefined;

//         openModal(msg, function () {
//             if (form) {
//                 form.dataset.confirmed = "true";
//                 form.submit();
//             } else {
//                 if (btn.dataset.confirming === 'true') {
//                     delete btn.dataset.confirming;
//                     return;
//                 }
//                 btn.dataset.confirming = 'true';
//                 btn.click();
//                 delete btn.dataset.confirming;
//             }
//         }, isGreen);
//     });

// })();

// // Reset confirmed flags when page loads (handles back button, form errors, etc.)
// window.addEventListener('pageshow', function() {
//     document.querySelectorAll('form[data-confirmed="true"]').forEach(function(form) {
//         delete form.dataset.confirmed;
//     });
//     document.querySelectorAll('button[data-confirming="true"]').forEach(function(btn) {
//         delete btn.dataset.confirming;
//     });
// });