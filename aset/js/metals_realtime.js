/**
 * JavaScript untuk update harga XAUUSD realtime dari Metals-API
 */

class MetalsRealtime {
    constructor() {
        this.apiUrl = 'api/metals_api.php?symbol=XAUUSD';
        this.updateInterval = 30000; // 30 detik
        this.intervalId = null;
        this.isPaused = false;
        this.lastPrice = null;
    }
    
    /**
     * Initialize realtime updates
     */
    init() {
        // Load initial data
        this.updateXAUUSD();
        
        // Set auto-update interval
        this.startAutoUpdate();
        
        // Setup refresh button
        document.querySelectorAll('.refresh-forex-data').forEach(btn => {
            btn.addEventListener('click', () => {
                this.updateXAUUSD();
            });
        });
        
        // Setup pause button
        document.querySelectorAll('.pause-auto-update').forEach(btn => {
            btn.addEventListener('click', () => {
                this.togglePause(btn);
            });
        });
    }
    
    /**
     * Update XAUUSD price from API
     */
    async updateXAUUSD() {
        const priceElement = document.querySelector('[data-pair="XAUUSD"]');
        const changeElement = document.querySelector('.change[data-pair="XAUUSD"]');
        const trendElement = document.querySelector('.trend[data-pair="XAUUSD"]');
        const loadingIndicator = document.querySelector('.loading-indicator');
        
        if (loadingIndicator) {
            loadingIndicator.style.display = 'block';
        }
        
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            
            if (data.success && data.data) {
                const xauusd = data.data;
                
                // Update price
                if (priceElement) {
                    const newPrice = parseFloat(xauusd.price);
                    const oldPrice = parseFloat(priceElement.dataset.price || newPrice);
                    
                    // Animate price change
                    this.animatePriceChange(priceElement, oldPrice, newPrice);
                    priceElement.textContent = newPrice.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    priceElement.dataset.price = newPrice;
                    
                    // Store last price for comparison
                    this.lastPrice = newPrice;
                }
                
                // Update change
                if (changeElement) {
                    const change = parseFloat(xauusd.change_percent || xauusd.change || 0);
                    const isPositive = change >= 0;
                    
                    changeElement.textContent = (isPositive ? '+' : '') + change.toFixed(2) + '%';
                    changeElement.className = 'badge bg-' + (isPositive ? 'success' : 'danger');
                    changeElement.dataset.change = xauusd.change || 0;
                    changeElement.dataset.changePercent = change;
                }
                
                // Update trend arrow
                if (trendElement) {
                    const isUp = (xauusd.change || 0) >= 0;
                    trendElement.className = 'fa-solid fa-arrow-' + (isUp ? 'up' : 'down') + 
                                            ' text-' + (isUp ? 'success' : 'danger') + 
                                            ' trend';
                    trendElement.setAttribute('data-pair', 'XAUUSD');
                }
                
                // Update timestamp
                const timeElement = document.querySelector('.last-update-time');
                if (timeElement) {
                    const now = new Date();
                    timeElement.innerHTML = '<i class="fa-solid fa-clock me-1"></i>' +
                                          'Update terakhir: ' + now.toLocaleTimeString('id-ID');
                }
            }
        } catch (error) {
            console.error('Error fetching XAUUSD data:', error);
        } finally {
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
        }
    }
    
    /**
     * Animate price change
     */
    animatePriceChange(element, oldPrice, newPrice) {
        if (oldPrice === newPrice) return;
        
        const isUp = newPrice > oldPrice;
        element.style.transition = 'all 0.3s ease';
        element.style.color = isUp ? '#28a745' : '#dc3545';
        
        setTimeout(() => {
            element.style.color = '';
        }, 1000);
    }
    
    /**
     * Start auto-update
     */
    startAutoUpdate() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }
        
        this.intervalId = setInterval(() => {
            if (!this.isPaused) {
                this.updateXAUUSD();
            }
        }, this.updateInterval);
    }
    
    /**
     * Toggle pause
     */
    togglePause(button) {
        this.isPaused = !this.isPaused;
        
        if (this.isPaused) {
            button.innerHTML = '<i class="fa-solid fa-play"></i>';
            button.title = 'Resume Auto-update';
            if (this.intervalId) {
                clearInterval(this.intervalId);
            }
        } else {
            button.innerHTML = '<i class="fa-solid fa-pause"></i>';
            button.title = 'Pause Auto-update';
            this.startAutoUpdate();
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const metalsRealtime = new MetalsRealtime();
    metalsRealtime.init();
});

// Add CSS for pulse animation
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .fa-circle.fa-xs {
        animation: pulse 2s infinite;
    }
`;
document.head.appendChild(style);








