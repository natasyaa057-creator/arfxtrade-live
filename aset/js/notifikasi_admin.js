// Notifikasi admin untuk testimoni baru
class NotifikasiAdmin {
    constructor() {
        this.checkInterval = 30000; // 30 detik
        this.lastCheck = Date.now();
        this.init();
    }
    
    init() {
        // Cek notifikasi saat halaman dimuat
        this.cekNotifikasi();
        
        // Cek notifikasi secara berkala
        setInterval(() => {
            this.cekNotifikasi();
        }, this.checkInterval);
    }
    
    async cekNotifikasi() {
        try {
            const response = await fetch('api/cek_testimoni_pending.php');
            const data = await response.json();
            
            if (data.success && data.jumlah > 0) {
                this.tampilkanNotifikasi(data.jumlah);
            }
        } catch (error) {
            console.log('Error cek notifikasi:', error);
        }
    }
    
    tampilkanNotifikasi(jumlah) {
        // Hapus notifikasi lama
        const existingNotification = document.querySelector('.notifikasi-testimoni');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Buat notifikasi baru
        const notification = document.createElement('div');
        notification.className = 'notifikasi-testimoni position-fixed top-0 end-0 m-3';
        notification.style.zIndex = '9999';
        notification.innerHTML = `
            <div class="alert alert-warning alert-dismissible fade show shadow" role="alert">
                <i class="fa-solid fa-bell me-2"></i>
                <strong>${jumlah} testimoni baru</strong> menunggu persetujuan!
                <a href="${basis_url('admin/testimoni.php')}" class="btn btn-sm btn-warning ms-2">
                    Lihat
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-hide setelah 10 detik
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 10000);
    }
}

// Inisialisasi notifikasi admin
if (window.location.pathname.includes('admin/') || window.location.pathname.includes('dashboard.php')) {
    document.addEventListener('DOMContentLoaded', function() {
        new NotifikasiAdmin();
    });
}








