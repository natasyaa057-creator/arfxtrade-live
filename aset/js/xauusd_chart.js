/**
 * Chart Realtime XAUUSD menggunakan Chart.js
 */

class XAUUSDChart {
    constructor() {
        this.apiUrl = 'api/metals_api.php?symbol=XAUUSD';
        this.updateInterval = 10000; // 10 detik
        this.chart = null;
        this.priceHistory = [];
        this.timeHistory = [];
        this.maxDataPoints = 60; // 10 menit data (60 points x 10 detik)
        this.isPaused = false;
        this.intervalId = null;
    }
    
    /**
     * Initialize chart
     */
    init() {
        this.createChart();
        this.loadInitialData();
        this.startAutoUpdate();
        
        // Setup controls
        this.setupControls();
    }
    
    /**
     * Create Chart.js instance
     */
    createChart() {
        const ctx = document.getElementById('xauusdChart');
        if (!ctx) return;
        
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'XAUUSD Price',
                    data: [],
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: '#d4af37',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#f2f2f2',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#d4af37',
                        bodyColor: '#f2f2f2',
                        borderColor: '#d4af37',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Price: $' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#999',
                            maxTicksLimit: 10
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)',
                            drawBorder: false
                        }
                    },
                    y: {
                        ticks: {
                            color: '#999',
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)',
                            drawBorder: false
                        }
                    }
                },
                animation: {
                    duration: 750
                }
            }
        });
    }
    
    /**
     * Load initial data
     */
    async loadInitialData() {
        // Load beberapa data awal untuk chart
        for (let i = 0; i < 10; i++) {
            await this.updateChart();
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
    }
    
    /**
     * Update chart with new data
     */
    async updateChart() {
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            
            if (data.success && data.data) {
                const price = parseFloat(data.data.price);
                const now = new Date();
                const timeLabel = now.toLocaleTimeString('id-ID', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
                
                // Add to history
                this.priceHistory.push(price);
                this.timeHistory.push(timeLabel);
                
                // Limit data points
                if (this.priceHistory.length > this.maxDataPoints) {
                    this.priceHistory.shift();
                    this.timeHistory.shift();
                }
                
                // Update chart
                if (this.chart) {
                    this.chart.data.labels = this.timeHistory;
                    this.chart.data.datasets[0].data = this.priceHistory;
                    this.chart.update('none'); // 'none' untuk animasi lebih smooth
                }
                
                // Update price display
                this.updatePriceDisplay(data.data);
                
                // Update statistics
                this.updateStatistics();
            }
        } catch (error) {
            console.error('Error updating chart:', error);
        }
    }
    
    /**
     * Update price display
     */
    updatePriceDisplay(data) {
        const priceElement = document.getElementById('currentPrice');
        const changeElement = document.getElementById('priceChange');
        const changePercentElement = document.getElementById('priceChangePercent');
        const highElement = document.getElementById('priceHigh');
        const lowElement = document.getElementById('priceLow');
        const timestampElement = document.getElementById('lastUpdate');
        
        if (priceElement) {
            const oldPrice = parseFloat(priceElement.dataset.price || data.price);
            const newPrice = parseFloat(data.price);
            
            // Animate price change
            this.animatePrice(priceElement, oldPrice, newPrice);
            priceElement.textContent = '$' + newPrice.toFixed(2);
            priceElement.dataset.price = newPrice;
        }
        
        if (changeElement) {
            const change = parseFloat(data.change || 0);
            const isPositive = change >= 0;
            changeElement.textContent = (isPositive ? '+' : '') + change.toFixed(2);
            changeElement.className = 'badge bg-' + (isPositive ? 'success' : 'danger');
        }
        
        if (changePercentElement) {
            const changePercent = parseFloat(data.change_percent || 0);
            const isPositive = changePercent >= 0;
            changePercentElement.textContent = (isPositive ? '+' : '') + changePercent.toFixed(2) + '%';
            changePercentElement.className = 'text-' + (isPositive ? 'success' : 'danger');
        }
        
        if (highElement && data.high) {
            highElement.textContent = '$' + parseFloat(data.high).toFixed(2);
        }
        
        if (lowElement && data.low) {
            lowElement.textContent = '$' + parseFloat(data.low).toFixed(2);
        }
        
        if (timestampElement) {
            const now = new Date();
            timestampElement.textContent = now.toLocaleString('id-ID');
        }
    }
    
    /**
     * Animate price change
     */
    animatePrice(element, oldPrice, newPrice) {
        if (oldPrice === newPrice) return;
        
        const isUp = newPrice > oldPrice;
        element.style.transition = 'all 0.3s ease';
        element.style.color = isUp ? '#28a745' : '#dc3545';
        element.style.transform = 'scale(1.1)';
        
        setTimeout(() => {
            element.style.color = '#d4af37';
            element.style.transform = 'scale(1)';
        }, 500);
    }
    
    /**
     * Update statistics
     */
    updateStatistics() {
        if (this.priceHistory.length < 2) return;
        
        const prices = this.priceHistory;
        const current = prices[prices.length - 1];
        const previous = prices[prices.length - 2];
        const min = Math.min(...prices);
        const max = Math.max(...prices);
        const avg = prices.reduce((a, b) => a + b, 0) / prices.length;
        
        // Update min/max
        const minElement = document.getElementById('statMin');
        const maxElement = document.getElementById('statMax');
        const avgElement = document.getElementById('statAvg');
        
        if (minElement) minElement.textContent = '$' + min.toFixed(2);
        if (maxElement) maxElement.textContent = '$' + max.toFixed(2);
        if (avgElement) avgElement.textContent = '$' + avg.toFixed(2);
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
                this.updateChart();
            }
        }, this.updateInterval);
    }
    
    /**
     * Setup controls
     */
    setupControls() {
        // Refresh button
        document.querySelectorAll('.refresh-chart').forEach(btn => {
            btn.addEventListener('click', () => {
                this.updateChart();
            });
        });
        
        // Pause button
        document.querySelectorAll('.pause-chart').forEach(btn => {
            btn.addEventListener('click', () => {
                this.togglePause(btn);
            });
        });
    }
    
    /**
     * Toggle pause
     */
    togglePause(button) {
        this.isPaused = !this.isPaused;
        
        if (this.isPaused) {
            button.innerHTML = '<i class="fa-solid fa-play"></i> Resume';
            button.classList.remove('btn-warning');
            button.classList.add('btn-success');
        } else {
            button.innerHTML = '<i class="fa-solid fa-pause"></i> Pause';
            button.classList.remove('btn-success');
            button.classList.add('btn-warning');
            this.startAutoUpdate();
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        window.xauusdChartInstance = new XAUUSDChart();
        window.xauusdChartInstance.init();
    } else {
        console.error('Chart.js is not loaded');
    }
});

