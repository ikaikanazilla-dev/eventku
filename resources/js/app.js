document.querySelectorAll('[data-print]').forEach((el) => el.addEventListener('click', () => window.print()));

if (document.querySelector('[data-checkin-page]')) {
    import('@zxing/browser')
        .then(({ BrowserQRCodeReader }) => {
            window.EventkuQrReader = BrowserQRCodeReader;
            document.dispatchEvent(new CustomEvent('eventku:qr-reader-ready'));
        })
        .catch(() => {
            window.EventkuQrReaderLoadError = true;
        });
}

const mobileToggle = document.querySelector('[data-mobile-toggle]');
const mobilePanel = document.querySelector('[data-mobile-panel]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebar = document.querySelector('.sidebar');
const sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');

const closeMobileMenu = () => {
    if (!mobileToggle || !mobilePanel) return;
    mobilePanel.hidden = true;
    mobileToggle.setAttribute('aria-expanded', 'false');
    mobileToggle.setAttribute('aria-label', 'Buka menu navigasi');
};

mobileToggle?.addEventListener('click', () => {
    const open = mobileToggle.getAttribute('aria-expanded') === 'true';
    mobileToggle.setAttribute('aria-expanded', String(!open));
    mobilePanel.hidden = open;
    mobileToggle.setAttribute('aria-label', open ? 'Buka menu navigasi' : 'Tutup menu navigasi');
});

mobilePanel?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMobileMenu));

const closeSidebar = () => {
    if (!sidebarToggle || !sidebar || !sidebarBackdrop) return;
    sidebar.classList.remove('is-open');
    sidebarBackdrop.hidden = true;
    sidebarToggle.setAttribute('aria-expanded', 'false');
};

sidebarToggle?.addEventListener('click', () => {
    const open = sidebarToggle.getAttribute('aria-expanded') === 'true';
    sidebarToggle.setAttribute('aria-expanded', String(!open));
    sidebar?.classList.toggle('is-open', !open);
    if (sidebarBackdrop) sidebarBackdrop.hidden = open;
});

sidebarBackdrop?.addEventListener('click', closeSidebar);
sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeSidebar));

const profileMenu = document.querySelector('[data-profile-menu]');
const profileToggle = document.querySelector('[data-profile-toggle]');
const profileDropdown = document.querySelector('[data-profile-dropdown]');

const closeProfileMenu = () => {
    if (!profileToggle || !profileDropdown) return;
    profileDropdown.hidden = true;
    profileToggle.setAttribute('aria-expanded', 'false');
};

profileToggle?.addEventListener('click', () => {
    const open = profileToggle.getAttribute('aria-expanded') === 'true';
    profileToggle.setAttribute('aria-expanded', String(!open));
    profileDropdown.hidden = open;
});

document.addEventListener('click', (event) => {
    if (profileMenu && !profileMenu.contains(event.target)) closeProfileMenu();
    if (mobilePanel && !mobilePanel.hidden && !mobilePanel.contains(event.target) && !mobileToggle?.contains(event.target)) closeMobileMenu();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeProfileMenu();
        closeMobileMenu();
        closeSidebar();
    }
});

document.querySelectorAll('form[data-submit-once]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.dataset.submitted === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.submitted = 'true';
        const submitButton = form.querySelector('button[type="submit"]');

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.dataset.originalLabel = submitButton.textContent;
            submitButton.textContent = 'Memproses...';
        }
    });
});
