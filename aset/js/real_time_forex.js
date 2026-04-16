// Real-time Forex Data Integration
class RealTimeForex {
    constructor() {
        this.updateInterval = 30000; // 30 seconds
        this.apiEndpoint = 'api/forex_data.php';
        this.isUpdating = false;
        this.lastUpdate = null;
        this.data = {};
        
        this.init();
    }
    
    init() {
        this.loadInitialData();
        this.startAutoUpdate();
        this.setupEventListeners();
    }
    
    async loadInitialData() {
        try {
            const response = await fetch(this.apiEndpoint);
            const result = await response.json();
            
            if (result.success) {
                this.data = result.data;
                this.updateUI();
                this.lastUpdate = new Date();
                this.showNotification('Data forex berhasil dimuat', 'success');
            } else {
                throw new Error('Failed to load forex data');
            }
        } catch (error) {
            console.error('Error loading forex data:', error);
            this.showNotification('Gagal memuat data forex, menggunakan data simulasi', 'warning');
            this.loadFallbackData();
        }
    }
    
    async updateData() {
        if (this.isUpdating) return;
        
        this.isUpdating = true;
        this.showLoadingIndicator();
        
        try {
            const response = await fetch(this.apiEndpoint + '?t=' + Date.now());
            const result = await response.json();
            
            if (result.success) {
                this.data = result.data;
                this.updateUI();
                this.lastUpdate = new Date();
                this.hideLoadingIndicator();
            } else {
                throw new Error('Failed to update forex data');
            }
        } catch (error) {
            console.error('Error updating forex data:', error);
            this.showNotification('Gagal update data forex', 'error');
            this.hideLoadingIndicator();
        } finally {
            this.isUpdating = false;
        }
    }
    
    updateUI() {
        // Update market overview cards
        Object.keys(this.data).forEach(pair => {
            const data = this.data[pair];
            this.updatePairCard(pair, data);
        });
        
        // Update market sentiment
        this.updateMarketSentiment();
        
        // Update last update time
        this.updateLastUpdateTime();
    }
    
    updatePairCard(pair, data) {
        const card = document.querySelector(`[data-pair="${pair}"]`);
        if (!card) return;
        
        // Update price
        const priceElement = card.querySelector('.price');
        if (priceElement) {
            priceElement.textContent = this.formatPrice(data.price, pair);
        }
        
        // Update change
        const changeElement = card.querySelector('.change');
        if (changeElement) {
            changeElement.textContent = data.change;
            changeElement.className = `badge bg-${data.trend === 'up' ? 'success' : 'danger'}`;
        }
        
        // Update trend arrow
        const trendElement = card.querySelector('.trend');
        if (trendElement) {
            trendElement.className = `fa-solid fa-arrow-${data.trend === 'up' ? 'up' : 'down'} text-${data.trend === 'up' ? 'success' : 'danger'}`;
        }
    }
    
    updateMarketSentiment() {
        // Calculate overall market sentiment
        const pairs = Object.values(this.data);
        const bullishCount = pairs.filter(p => p.trend === 'up').length;
        const totalCount = pairs.length;
        const sentimentPercentage = Math.round((bullishCount / totalCount) * 100);
        
        const progressBar = document.querySelector('.sentiment-progress .progress-bar');
        const sentimentText = document.querySelector('.sentiment-text');
        
        if (progressBar && sentimentText) {
            progressBar.style.width = sentimentPercentage + '%';
            progressBar.className = `progress-bar bg-${sentimentPercentage >= 60 ? 'success' : sentimentPercentage >= 40 ? 'warning' : 'danger'}`;
            sentimentText.textContent = `${sentimentPercentage}% Bullish`;
        }
    }
    
    updateLastUpdateTime() {
        const timeElement = document.querySelector('.last-update-time');
        if (timeElement && this.lastUpdate) {
            timeElement.textContent = `Update terakhir: ${this.lastUpdate.toLocaleTimeString('id-ID')} WIB`;
        }
    }
    
    formatPrice(price, pair) {
        if (pair === 'BTCUSD' || pair === 'ETHUSD') {
            return '$' + price.toLocaleString('en-US');
        } else if (pair === 'XAUUSD') {
            return '$' + price.toFixed(2);
        } else {
            return price.toFixed(4);
        }
    }
    
    startAutoUpdate() {
        setInterval(() => {
            this.updateData();
        }, this.updateInterval);
    }
    
    setupEventListeners() {
        // Manual refresh button
        const refreshBtn = document.querySelector('.refresh-forex-data');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.updateData();
            });
        }
        
        // Pause/Resume auto-update
        const pauseBtn = document.querySelector('.pause-auto-update');
        if (pauseBtn) {
            pauseBtn.addEventListener('click', () => {
                this.toggleAutoUpdate();
            });
        }
    }
    
    toggleAutoUpdate() {
        if (this.updateInterval === 0) {
            this.updateInterval = 30000;
            this.startAutoUpdate();
            this.showNotification('Auto-update diaktifkan', 'success');
        } else {
            this.updateInterval = 0;
            this.showNotification('Auto-update dihentikan', 'warning');
        }
    }
    
    showLoadingIndicator() {
        const indicators = document.querySelectorAll('.loading-indicator');
        indicators.forEach(indicator => {
            indicator.style.display = 'block';
        });
    }
    
    hideLoadingIndicator() {
        const indicators = document.querySelectorAll('.loading-indicator');
        indicators.forEach(indicator => {
            indicator.style.display = 'none';
        });
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `toast align-items-center text-white bg-${type} border-0`;
        notification.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        const container = document.getElementById('toast-container') || this.createToastContainer();
        container.appendChild(notification);
        
        const toast = new bootstrap.Toast(notification);
        toast.show();
        
        notification.addEventListener('hidden.bs.toast', () => {
            notification.remove();
        });
    }
    
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
    
    loadFallbackData() {
        // Fallback data jika API gagal
        this.data = {
            'XAUUSD': { price: 2025.50, change: '+0.25%', trend: 'up' },
            'EURUSD': { price: 1.0850, change: '-0.15%', trend: 'down' },
            'GBPUSD': { price: 1.2650, change: '+0.35%', trend: 'up' },
            'USDJPY': { price: 149.25, change: '+0.20%', trend: 'up' },
            'AUDUSD': { price: 0.6520, change: '-0.10%', trend: 'down' },
            'BTCUSD': { price: 42500, change: '+2.15%', trend: 'up' },
            'ETHUSD': { price: 2650, change: '+1.85%', trend: 'up' }
        };
        this.updateUI();
    }
}

// Initialize real-time forex data
document.addEventListener('DOMContentLoaded', function() {
    window.realTimeForex = new RealTimeForex();
});







