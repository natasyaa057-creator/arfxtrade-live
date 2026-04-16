// Upload gambar dengan preview dan validasi
class UploadGambar {
    constructor(inputElement, previewElement, progressElement) {
        this.input = inputElement;
        this.preview = previewElement;
        this.progress = progressElement;
        this.maxSize = 5 * 1024 * 1024; // 5MB
        this.allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        this.init();
    }
    
    init() {
        this.input.addEventListener('change', (e) => this.handleFileSelect(e));
    }
    
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        // Validasi tipe file
        if (!this.allowedTypes.includes(file.type)) {
            this.showError('Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, atau WebP');
            return;
        }
        
        // Validasi ukuran file
        if (file.size > this.maxSize) {
            this.showError('Ukuran file terlalu besar. Maksimal 5MB');
            return;
        }
        
        // Preview gambar
        this.previewImage(file);
        
        // Upload gambar
        this.uploadImage(file);
    }
    
    previewImage(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.preview.innerHTML = `
                <div class="position-relative">
                    <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="this.closest('.position-relative').remove()">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
    
    async uploadImage(file) {
        const formData = new FormData();
        formData.append('gambar', file);
        formData.append('token_csrf', document.querySelector('input[name="token_csrf"]').value);
        
        try {
            this.showProgress(true);
            
            const response = await fetch('unggahan/upload_gambar.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccess(result.message);
                // Simpan URL gambar ke input hidden
                const hiddenInput = document.querySelector('input[name="gambar_url"]');
                if (hiddenInput) {
                    hiddenInput.value = result.url;
                }
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            this.showError('Gagal mengupload gambar: ' + error.message);
        } finally {
            this.showProgress(false);
        }
    }
    
    showProgress(show) {
        if (this.progress) {
            this.progress.style.display = show ? 'block' : 'none';
            if (show) {
                this.progress.innerHTML = '<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div></div>';
            }
        }
    }
    
    showSuccess(message) {
        this.showAlert('success', message);
    }
    
    showError(message) {
        this.showAlert('danger', message);
    }
    
    showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Hapus alert sebelumnya
        const existingAlert = this.input.parentNode.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        this.input.parentNode.appendChild(alertDiv);
        
        // Auto-hide setelah 5 detik
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}

// Inisialisasi upload gambar
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    fileInputs.forEach(input => {
        const preview = input.parentNode.querySelector('.preview-gambar');
        const progress = input.parentNode.querySelector('.progress-upload');
        
        if (preview) {
            new UploadGambar(input, preview, progress);
        }
    });
});








