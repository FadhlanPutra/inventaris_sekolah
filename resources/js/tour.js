// Kode ini ada karena terkadang package filament-tour tidak otomatis menyembunyikan tour setelah pertama kali ditampilkan.
//  config\filament-tour.php

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname === '/dashboard') {
        const tourKey = 'tours';
        const tourSeen = localStorage.getItem(tourKey);

        if (tourSeen !== '["tour_dashboard-tour"]' || tourSeen === null) {
            // console.log('⏳ Belum ada localStorage, tunggu sebentar...');

            setTimeout(() => {
                // console.log('🚀 Tour dimulai di domain:', window.location.hostname);

                // Simpan value berupa array JSON
                const tourValue = ["tour_dashboard-tour"];
                localStorage.setItem(tourKey, JSON.stringify(tourValue));

                // console.log('✅ LocalStorage diset:', tourKey, '=', JSON.stringify(tourValue));
            }, 500);
        } else {
            // console.log('✅ Sudah ada localStorage:', tourKey, '=', tourSeen);
        }
    }
});
