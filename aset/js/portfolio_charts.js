/**
 * Grafik Kinerja untuk Portofolio
 * - Growth Performance
 * - Histogram Profit/Loss
 */

class PortfolioCharts {
    constructor() {
        this.growthChart = null;
        this.histogramChart = null;
        this.apiUrl = 'api/portfolio_data.php';
    }
    
    init() {
        this.createGrowthPerformance();
        this.createProfitLossHistogram();
    }
    
    /**
     * Growth Performance - Grafik pertumbuhan profit
     */
    createGrowthPerformance() {
        const ctx = document.getElementById('growthChart');
        if (!ctx) return;
        
        this.fetchPortfolioData().then(data => {
            const growthData = this.calculateGrowth(data.transactions);
            
            this.growthChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: growthData.labels,
                    datasets: [{
                        label: 'Profit/Loss',
                        data: growthData.values,
                        backgroundColor: growthData.values.map(v => v >= 0 ? 'rgba(40, 167, 69, 0.7)' : 'rgba(220, 53, 69, 0.7)'),
                        borderColor: growthData.values.map(v => v >= 0 ? '#28a745' : '#dc3545'),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y;
                                    return (value >= 0 ? '+' : '') + value.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#999' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        },
                        y: {
                            ticks: { 
                                color: '#999',
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    }
                }
            });
        });
    }
    
    /**
     * Histogram Profit/Loss - Distribusi profit dan loss
     */
    createProfitLossHistogram() {
        const ctx = document.getElementById('histogramChart');
        if (!ctx) return;
        
        this.fetchPortfolioData().then(data => {
            const histogramData = this.calculateHistogram(data.transactions);
            
            this.histogramChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: histogramData.labels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: histogramData.values,
                        backgroundColor: [
                            'rgba(220, 53, 69, 0.7)',  // Loss
                            'rgba(255, 193, 7, 0.7)',  // Breakeven
                            'rgba(40, 167, 69, 0.7)'   // Win
                        ],
                        borderColor: [
                            '#dc3545',
                            '#ffc107',
                            '#28a745'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' transaksi';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#999' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        },
                        y: {
                            ticks: { color: '#999' },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    }
    
    /**
     * Fetch data dari API
     */
    async fetchPortfolioData() {
        try {
            const response = await fetch(this.apiUrl);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching portfolio data:', error);
            // Return dummy data
            return {
                transactions: this.getDummyData()
            };
        }
    }
    
    /**
     * Calculate growth
     */
    calculateGrowth(transactions) {
        const labels = [];
        const values = [];
        
        transactions.forEach((tx, index) => {
            labels.push(tx.tanggal || `Trade ${index + 1}`);
            values.push(parseFloat(tx.profit_loss || 0));
        });
        
        return { labels, values };
    }
    
    /**
     * Calculate histogram
     */
    calculateHistogram(transactions) {
        const win = transactions.filter(tx => tx.hasil === 'WIN').length;
        const loss = transactions.filter(tx => tx.hasil === 'LOSS').length;
        const breakeven = transactions.filter(tx => tx.hasil === 'BREAKEVEN').length;
        
        return {
            labels: ['Loss', 'Breakeven', 'Win'],
            values: [loss, breakeven, win]
        };
    }
    
    /**
     * Dummy data untuk demo
     */
    getDummyData() {
        return [
            { tanggal: '2024-12-01', profit_loss: 50, hasil: 'WIN' },
            { tanggal: '2024-12-02', profit_loss: -30, hasil: 'LOSS' },
            { tanggal: '2024-12-03', profit_loss: 75, hasil: 'WIN' },
            { tanggal: '2024-12-04', profit_loss: 100, hasil: 'WIN' },
            { tanggal: '2024-12-05', profit_loss: -25, hasil: 'LOSS' },
            { tanggal: '2024-12-06', profit_loss: 60, hasil: 'WIN' },
            { tanggal: '2024-12-07', profit_loss: 80, hasil: 'WIN' },
            { tanggal: '2024-12-08', profit_loss: -40, hasil: 'LOSS' },
            { tanggal: '2024-12-09', profit_loss: 90, hasil: 'WIN' },
            { tanggal: '2024-12-10', profit_loss: 120, hasil: 'WIN' }
        ];
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        const portfolioCharts = new PortfolioCharts();
        portfolioCharts.init();
    } else {
        console.error('Chart.js is not loaded');
    }
});



