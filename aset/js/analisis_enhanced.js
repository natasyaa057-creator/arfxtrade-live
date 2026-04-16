// Enhanced Analytics Features untuk Halaman Analisis Pasar

class AnalisisEnhanced {
    constructor() {
        this.bookmarks = JSON.parse(localStorage.getItem('bookmarked_analisis') || '[]');
        this.favorites = JSON.parse(localStorage.getItem('favorite_pairs') || '[]');
        this.init();
    }
    
    init() {
        this.setupBookmarkButtons();
        this.setupFavoritePairs();
        this.setupRealTimeUpdates();
        this.setupMarketAlerts();
        this.setupAnalyticsTracking();
    }
    
    // Setup bookmark buttons
    setupBookmarkButtons() {
        document.querySelectorAll('.bookmark-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                this.toggleBookmark(id);
            });
        });
    }
    
    // Toggle bookmark
    toggleBookmark(id) {
        if (this.bookmarks.includes(id)) {
            this.bookmarks = this.bookmarks.filter(b => b !== id);
            this.showNotification('Analisis dihapus dari bookmark', 'warning');
        } else {
            this.bookmarks.push(id);
            this.showNotification('Analisis ditambahkan ke bookmark', 'success');
        }
        localStorage.setItem('bookmarked_analisis', JSON.stringify(this.bookmarks));
        this.updateBookmarkUI();
    }
    
    // Update bookmark UI
    updateBookmarkUI() {
        document.querySelectorAll('.bookmark-btn').forEach(btn => {
            const id = btn.dataset.id;
            if (this.bookmarks.includes(id)) {
                btn.classList.add('active');
                btn.innerHTML = '<i class="fa-solid fa-bookmark"></i>';
            } else {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fa-regular fa-bookmark"></i>';
            }
        });
    }
    
    // Setup favorite pairs
    setupFavoritePairs() {
        const favoritePairsContainer = document.getElementById('favoritePairs');
        if (favoritePairsContainer) {
            this.renderFavoritePairs();
        }
    }
    
    // Render favorite pairs
    renderFavoritePairs() {
        const container = document.getElementById('favoritePairs');
        if (!container) return;
        
        container.innerHTML = this.favorites.map(pair => `
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <span class="fw-semibold">${pair}</span>
                <div class="d-flex gap-2">
                    <span class="badge bg-success">+0.25%</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeFavoritePair('${pair}')">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    // Add favorite pair
    addFavoritePair(pair) {
        if (!this.favorites.includes(pair)) {
            this.favorites.push(pair);
            localStorage.setItem('favorite_pairs', JSON.stringify(this.favorites));
            this.renderFavoritePairs();
            this.showNotification(`${pair} ditambahkan ke favorit`, 'success');
        }
    }
    
    // Remove favorite pair
    removeFavoritePair(pair) {
        this.favorites = this.favorites.filter(p => p !== pair);
        localStorage.setItem('favorite_pairs', JSON.stringify(this.favorites));
        this.renderFavoritePairs();
        this.showNotification(`${pair} dihapus dari favorit`, 'warning');
    }
    
    // Setup real-time updates
    setupRealTimeUpdates() {
        // Simulasi update market data setiap 30 detik
        setInterval(() => {
            this.updateMarketData();
        }, 30000);
        
        // Update sentiment widget
        this.updateSentimentWidget();
    }
    
    // Update market data
    updateMarketData() {
        // Simulasi data market
        const pairs = ['XAUUSD', 'EURUSD', 'GBPUSD', 'USDJPY', 'AUDUSD'];
        pairs.forEach(pair => {
            const change = (Math.random() - 0.5) * 2; // -1% to +1%
            const element = document.querySelector(`[data-pair="${pair}"]`);
            if (element) {
                element.textContent = `${change > 0 ? '+' : ''}${change.toFixed(2)}%`;
                element.className = `badge ${change > 0 ? 'bg-success' : 'bg-danger'}`;
            }
        });
    }
    
    // Update sentiment widget
    updateSentimentWidget() {
        const sentiment = Math.floor(Math.random() * 40) + 30; // 30-70%
        const progressBar = document.querySelector('.sentiment-progress');
        const sentimentText = document.querySelector('.sentiment-text');
        
        if (progressBar && sentimentText) {
            progressBar.style.width = sentiment + '%';
            sentimentText.textContent = `${sentiment}% Bullish`;
        }
    }
    
    // Setup market alerts
    setupMarketAlerts() {
        // Simulasi alert market
        setTimeout(() => {
            this.showMarketAlert('EURUSD mencapai level resistance penting!', 'warning');
        }, 10000);
    }
    
    // Show market alert
    showMarketAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            <i class="fa-solid fa-bell me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-hide setelah 5 detik
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Setup analytics tracking
    setupAnalyticsTracking() {
        // Track page views
        this.trackEvent('page_view', 'analisis_pasar');
        
        // Track filter usage
        document.querySelectorAll('select, input[type="text"], input[type="date"]').forEach(element => {
            element.addEventListener('change', () => {
                this.trackEvent('filter_used', element.name);
            });
        });
    }
    
    // Track event
    trackEvent(event, value) {
        // Simulasi tracking analytics
        console.log(`Analytics: ${event} - ${value}`);
        
        // Simpan ke localStorage untuk demo
        const analytics = JSON.parse(localStorage.getItem('analytics') || '{}');
        analytics[event] = (analytics[event] || 0) + 1;
        localStorage.setItem('analytics', JSON.stringify(analytics));
    }
    
    // Show notification
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `toast align-items-center text-white bg-${type} border-0`;
        notification.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        toastContainer.appendChild(notification);
        
        const toast = new bootstrap.Toast(notification);
        toast.show();
        
        // Remove element after hide
        notification.addEventListener('hidden.bs.toast', () => {
            notification.remove();
        });
    }
    
    // Create toast container
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
    
    // Export bookmarks
    exportBookmarks() {
        const bookmarkedAnalisis = this.bookmarks.map(id => {
            const element = document.querySelector(`[data-id="${id}"]`);
            return element ? element.closest('.kartu-gelap').querySelector('h5').textContent : '';
        }).filter(title => title);
        
        const data = {
            bookmarks: bookmarkedAnalisis,
            exportDate: new Date().toISOString()
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'arfxtrade_bookmarks.json';
        a.click();
        URL.revokeObjectURL(url);
    }
    
    // Share analysis
    shareAnalysis(title, url) {
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url,
                text: `Analisis menarik dari ARFXTRADE: ${title}`
            });
        } else {
            // Fallback
            navigator.clipboard.writeText(url).then(() => {
                this.showNotification('Link berhasil disalin ke clipboard!', 'success');
            });
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.analisisEnhanced = new AnalisisEnhanced();
});

// Global functions for HTML onclick
function bookmarkAnalisis(id) {
    window.analisisEnhanced.toggleBookmark(id);
}

function shareAnalisis(title, url) {
    window.analisisEnhanced.shareAnalysis(title, url);
}

function addFavoritePair(pair) {
    window.analisisEnhanced.addFavoritePair(pair);
}

function removeFavoritePair(pair) {
    window.analisisEnhanced.removeFavoritePair(pair);
}

function exportBookmarks() {
    window.analisisEnhanced.exportBookmarks();
}







