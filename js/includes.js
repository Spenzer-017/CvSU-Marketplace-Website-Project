/* includes.js — Kabsuhayan */

document.addEventListener('DOMContentLoaded', () => {

  const body          = document.body;
  const sidebar       = document.getElementById('sidebar');
  const overlay       = document.getElementById('sidebarOverlay');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const profileToggle = document.getElementById('profileToggle');
  const profileMenu   = document.getElementById('profileDropdown');
  const themeBtns     = document.querySelectorAll('#theme-switch');

  // ── Theme ──────────────────────────────────────────────────────
  // Supports both old key ("active") and new key ("dark") so existing
  // user preferences aren't lost after the update.
  const savedTheme = localStorage.getItem('darkmode');
  if (savedTheme === 'active' || savedTheme === 'dark') {
    body.classList.add('darkmode');
  }

  const enableDarkmode = () => {
    body.classList.add('darkmode');
    localStorage.setItem('darkmode', 'active'); // keep original key/value
  };

  const disableDarkmode = () => {
    body.classList.remove('darkmode');
    localStorage.setItem('darkmode', null);
  };

  themeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      localStorage.getItem('darkmode') === 'active'
        ? disableDarkmode()
        : enableDarkmode();
    });
  });

  // ── Mobile sidebar open/close ──────────────────────────────────
  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('open');
    if (overlay) overlay.classList.add('active');
  }

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
  }

  if (overlay) overlay.addEventListener('click', closeSidebar);

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeSidebar(); closeProfile(); }
  });

  // ── Submenu groups ─────────────────────────────────────────────
  document.querySelectorAll('.sidebar-group-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
      const group  = toggle.closest('.sidebar-group');
      if (!group) return;
      const isOpen = group.classList.contains('open');
      document.querySelectorAll('.sidebar-group.open').forEach(g => {
        if (g !== group) g.classList.remove('open');
      });
      group.classList.toggle('open', !isOpen);
      toggle.setAttribute('aria-expanded', !isOpen);
    });
  });

  // ── Profile dropdown ───────────────────────────────────────────
  function closeProfile() {
    if (profileMenu) profileMenu.classList.remove('open');
  }

  if (profileToggle && profileMenu) {
    profileToggle.addEventListener('click', e => {
      e.stopPropagation();
      profileMenu.classList.toggle('open');
    });
    document.addEventListener('click', e => {
      if (!profileMenu.contains(e.target)) closeProfile();
    });
  }

  // ── Guest hamburger (original behavior preserved) ──────────────
  const menuToggle = document.getElementById('guestMenuToggle');
  const guestNav   = document.querySelector('.guest-header nav');

  if (menuToggle && guestNav) {
    menuToggle.addEventListener('click', () => {
      const isOpen = guestNav.classList.toggle('open');
      menuToggle.classList.toggle('open', isOpen);
    });
  }

});