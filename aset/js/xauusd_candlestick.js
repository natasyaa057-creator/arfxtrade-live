/**
 * Candlestick Chart XAUUSD menggunakan TradingView Lightweight Charts
 * Mirip dengan tampilan TradingView.com
 */

class XAUUSDCandlestick {
    constructor() {
        this.apiUrl = 'api/metals_api.php?symbol=XAUUSD';
        this.updateInterval = 10000; // 10 detik
        this.chart = null;
        this.series = null;
        this.candles = [];
        this.currentCandle = null;
        this.isPaused = false;
        this.intervalId = null;
        this.timeframe = 15; // 15 menit per candle
        this.lastUpdateTime = null;
    }
    
    /**
     * Initialize chart
     */
    init() {
        this.createChart();
        this.loadInitialData();
        this.startAutoUpdate();
        this.setupControls();
    }
    
    /**
     * Create TradingView Lightweight Chart
     */
    createChart() {
        const chartContainer = document.getElementById('xauusdChartContainer');
        if (!chartContainer) {
            console.error('Chart container not found!');
            return;
        }
        
        // Check if library is loaded
        if (typeof LightweightCharts === 'undefined') {
            console.error('LightweightCharts library is not loaded!');
            chartContainer.innerHTML = 
                '<div class="alert alert-danger p-4 text-center">' +
                '<i class="fa-solid fa-exclamation-triangle fa-2x mb-2"></i><br>' +
                'Error: Chart library tidak dapat dimuat. Silakan refresh halaman.' +
                '</div>';
            return;
        }
        
        console.log('Creating chart...');
        console.log('Container width:', chartContainer.clientWidth);
        console.log('Container height:', chartContainer.clientHeight);
        
        // Ensure container has dimensions
        if (chartContainer.clientWidth === 0 || chartContainer.clientHeight === 0) {
            console.warn('Container has no dimensions, setting default...');
            chartContainer.style.width = '100%';
            chartContainer.style.height = '500px';
        }
        
        // Create chart
        try {
            this.chart = LightweightCharts.createChart(chartContainer, {
                layout: {
                    background: { color: '#0a0a0a' },
                    textColor: '#d4af37',
                },
                grid: {
                    vertLines: { color: 'rgba(255, 255, 255, 0.1)' },
                    horzLines: { color: 'rgba(255, 255, 255, 0.1)' },
                },
                crosshair: {
                    mode: LightweightCharts.CrosshairMode.Normal,
                },
                rightPriceScale: {
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                },
                timeScale: {
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    timeVisible: true,
                    secondsVisible: false,
                },
                width: chartContainer.clientWidth || 800,
                height: 500,
            });
            console.log('Chart object created:', this.chart);
        } catch (error) {
            console.error('Error creating chart:', error);
            chartContainer.innerHTML = 
                '<div class="alert alert-danger p-4 text-center">' +
                '<i class="fa-solid fa-exclamation-triangle fa-2x mb-2"></i><br>' +
                'Error creating chart: ' + error.message +
                '</div>';
            return;
        }
        
        // Create candlestick series
        this.series = this.chart.addCandlestickSeries({
            upColor: '#26a69a',
            downColor: '#ef5350',
            borderVisible: false,
            wickUpColor: '#26a69a',
            wickDownColor: '#ef5350',
        });
        
        console.log('Chart created successfully');
        
        // Handle resize
        const resizeObserver = new ResizeObserver(entries => {
            if (this.chart && entries.length > 0) {
                const width = entries[0].contentRect.width;
                this.chart.applyOptions({ width: width });
            }
        });
        resizeObserver.observe(chartContainer);
        
        // Fallback for older browsers
        window.addEventListener('resize', () => {
            if (this.chart) {
                this.chart.applyOptions({ width: chartContainer.clientWidth });
            }
        });
    }
    
    /**
     * Load initial data
     */
    async loadInitialData() {
        // Get current price first
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            // Update initial price ke level yang lebih realistis (sekitar 4000an)
            const initialPrice = data.success && data.data ? parseFloat(data.data.price) : 4200.00;
            
            // Generate initial candles (last 4 hours)
            const now = Math.floor(Date.now() / 1000);
            const candlesCount = Math.floor((4 * 60) / this.timeframe); // 4 hours
            
            for (let i = candlesCount; i >= 0; i--) {
                const candleTime = Math.floor((now - (i * this.timeframe * 60)) / (this.timeframe * 60)) * (this.timeframe * 60);
                const variation = (Math.random() - 0.5) * 20;
                const open = initialPrice + variation - (i * 0.5);
                const close = open + (Math.random() - 0.5) * 10;
                const high = Math.max(open, close) + Math.random() * 5;
                const low = Math.min(open, close) - Math.random() * 5;
                
                this.candles.push({
                    time: candleTime,
                    open: parseFloat(open.toFixed(2)),
                    high: parseFloat(high.toFixed(2)),
                    low: parseFloat(low.toFixed(2)),
                    close: parseFloat(close.toFixed(2)),
                });
            }
            
            this.series.setData(this.candles);
        } catch (error) {
            console.error('Error loading initial data:', error);
        }
        
        // Load real data and start current candle
        await this.updateChart();
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
                const now = Math.floor(Date.now() / 1000);
                const currentCandleTime = Math.floor(now / (this.timeframe * 60)) * (this.timeframe * 60);
                
                // Check if we need a new candle
                if (!this.currentCandle || this.currentCandle.time !== currentCandleTime) {
                    // Close previous candle if exists
                    if (this.currentCandle) {
                        // Finalize previous candle
                        this.currentCandle.close = parseFloat(data.data.price);
                        this.series.update(this.currentCandle);
                        this.candles.push({...this.currentCandle});
                        
                        // Keep only last 200 candles
                        if (this.candles.length > 200) {
                            this.candles.shift();
                        }
                    }
                    
                    // Create new candle
                    this.currentCandle = {
                        time: currentCandleTime,
                        open: price,
                        high: price,
                        low: price,
                        close: price,
                    };
                } else {
                    // Update current candle
                    this.currentCandle.high = Math.max(this.currentCandle.high, price);
                    this.currentCandle.low = Math.min(this.currentCandle.low, price);
                    this.currentCandle.close = price;
                }
                
                // Update series
                this.series.update(this.currentCandle);
                
                // Update price display
                this.updatePriceDisplay(data.data, this.currentCandle);
                
                this.lastUpdateTime = new Date();
            }
        } catch (error) {
            console.error('Error updating chart:', error);
        }
    }
    
    /**
     * Format price dengan pemisah ribuan
     */
    formatPrice(price) {
        return price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    /**
     * Update price display (O, H, L, C)
     */
    updatePriceDisplay(data, candle) {
        if (!candle) return;
        
        // Update header OHLC
        document.getElementById('candleOpen').textContent = this.formatPrice(candle.open);
        document.getElementById('candleHigh').textContent = this.formatPrice(candle.high);
        document.getElementById('candleLow').textContent = this.formatPrice(candle.low);
        document.getElementById('candleClose').textContent = this.formatPrice(candle.close);
        
        // Update current price (header)
        const priceElement = document.getElementById('currentPrice');
        if (priceElement) {
            const oldPrice = parseFloat(priceElement.dataset.price || candle.close);
            const newPrice = candle.close;
            
            this.animatePrice(priceElement, oldPrice, newPrice);
            priceElement.textContent = '$' + this.formatPrice(newPrice);
            priceElement.dataset.price = newPrice;
        }
        
        // Update change (header)
        const changeElementHeader = document.getElementById('priceChangeHeader');
        const changePercentElementHeader = document.getElementById('priceChangePercentHeader');
        
        if (changeElementHeader && data.change !== undefined) {
            const change = parseFloat(data.change || 0);
            const isPositive = change >= 0;
            changeElementHeader.textContent = (isPositive ? '+' : '') + change.toFixed(2);
            changeElementHeader.className = 'badge bg-' + (isPositive ? 'success' : 'danger');
        }
        
        if (changePercentElementHeader && data.change_percent !== undefined) {
            const changePercent = parseFloat(data.change_percent || 0);
            const isPositive = changePercent >= 0;
            changePercentElementHeader.textContent = (isPositive ? '+' : '') + changePercent.toFixed(2) + '%';
            changePercentElementHeader.className = 'text-' + (isPositive ? 'success' : 'danger');
        }
        
        // Update change (chart header)
        const changeElement = document.getElementById('priceChange');
        const changePercentElement = document.getElementById('priceChangePercent');
        
        if (changeElement && data.change !== undefined) {
            const change = parseFloat(data.change || 0);
            const isPositive = change >= 0;
            changeElement.textContent = (isPositive ? '+' : '') + change.toFixed(2);
            changeElement.className = 'badge bg-' + (isPositive ? 'success' : 'danger');
        }
        
        if (changePercentElement && data.change_percent !== undefined) {
            const changePercent = parseFloat(data.change_percent || 0);
            const isPositive = changePercent >= 0;
            changePercentElement.textContent = (isPositive ? '+' : '') + changePercent.toFixed(2) + '%';
            changePercentElement.className = 'text-' + (isPositive ? 'success' : 'danger');
        }
        
        // Update timestamp
        const timestampElement = document.getElementById('lastUpdate');
        if (timestampElement && this.lastUpdateTime) {
            timestampElement.textContent = this.lastUpdateTime.toLocaleString('id-ID');
        }
    }
    
    /**
     * Animate price change
     */
    animatePrice(element, oldPrice, newPrice) {
        if (oldPrice === newPrice) return;
        
        const isUp = newPrice > oldPrice;
        element.style.transition = 'all 0.3s ease';
        element.style.color = isUp ? '#26a69a' : '#ef5350';
        element.style.transform = 'scale(1.1)';
        
        setTimeout(() => {
            element.style.color = '#d4af37';
            element.style.transform = 'scale(1)';
        }, 500);
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
        
        // Timeframe selector
        const timeframeSelect = document.getElementById('timeframeSelect');
        if (timeframeSelect) {
            timeframeSelect.addEventListener('change', (e) => {
                this.timeframe = parseInt(e.target.value);
                this.candles = [];
                this.currentCandle = null;
                this.loadInitialData();
            });
        }
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
function initChart() {
    const chartContainer = document.getElementById('xauusdChartContainer');
    if (!chartContainer) {
        console.error('Chart container not found, retrying...');
        setTimeout(initChart, 100);
        return;
    }
    
    // Check if LightweightCharts is loaded
    if (typeof LightweightCharts === 'undefined') {
        console.error('TradingView Lightweight Charts is not loaded');
        chartContainer.innerHTML = 
            '<div class="chart-loading">' +
            '<div class="text-center">' +
            '<div class="spinner-border text-warning mb-3" role="status">' +
            '<span class="visually-hidden">Loading...</span>' +
            '</div>' +
            '<p>Memuat chart library...</p>' +
            '</div>' +
            '</div>';
        // Retry after 1 second
        setTimeout(initChart, 1000);
        return;
    }
    
    console.log('Initializing XAUUSD Candlestick Chart...');
    try {
        window.xauusdCandlestick = new XAUUSDCandlestick();
        window.xauusdCandlestick.init();
        console.log('Chart initialized successfully');
    } catch (error) {
        console.error('Error initializing chart:', error);
        chartContainer.innerHTML = 
            '<div class="alert alert-danger p-4 text-center">' +
            '<i class="fa-solid fa-exclamation-triangle fa-2x mb-2"></i><br>' +
            'Error: ' + error.message +
            '</div>';
    }
}

// Try to initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChart);
} else {
    // DOM is already ready
    initChart();
}

