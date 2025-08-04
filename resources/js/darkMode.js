document.addEventListener('DOMContentLoaded', function () {
    // Elemen select dan icon
    const themeSelectors = document.querySelectorAll('.themed'); // Menggunakan class
    const iconDisplay = document.getElementById('icon-display');

    // Fungsi untuk mengupdate icon
    function updateIcon(theme) {
        if (!iconDisplay) return;

        switch (theme) {
            case 'light':
                iconDisplay.className = 'fas fa-sun';
                break;
            case 'dark':
                iconDisplay.className = 'fas fa-moon';
                break;
            default:
                iconDisplay.className = 'fas fa-desktop';
        }
    }

    // Fungsi untuk menerapkan tema
    function applyTheme(theme) {
        const html = document.documentElement;

        if (theme === 'dark') {
            html.classList.add('dark');
            html.classList.remove('light');
            localStorage.setItem('color-theme', 'dark');
        } else if (theme === 'light') {
            html.classList.add('light');
            html.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            html.classList.remove('dark', 'light');
            localStorage.removeItem('color-theme');

            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark');
            } else {
                html.classList.add('light');
            }
        }

        updateIcon(theme);
    }

    // Ambil tema dari localStorage atau preferensi sistem
    const currentTheme =
        localStorage.getItem('color-theme') ||
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    // Terapkan tema awal
    applyTheme(currentTheme);

    // Sinkronkan semua elemen select
    themeSelectors.forEach((selector) => {
        selector.value = currentTheme;

        // Debugging untuk memverifikasi elemen
        // console.log('Select ditemukan:', selector);

        // Event listener untuk perubahan tema
        selector.addEventListener('change', function () {
            // console.log('Tema dipilih:', this.value); // Debugging
            applyTheme(this.value);
        });
    });

    // Dengarkan perubahan sistem
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem('color-theme')) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });
});
