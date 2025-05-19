// Chart Configuration
const chartConfig = {
    // Common chart options
    defaults: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20,
                    font: {
                        size: 12,
                        family: "'Inter', sans-serif"
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.8)',
                titleFont: {
                    size: 13,
                    family: "'Inter', sans-serif",
                    weight: '600'
                },
                bodyFont: {
                    size: 12,
                    family: "'Inter', sans-serif"
                },
                padding: 12,
                cornerRadius: 8,
                displayColors: true
            }
        }
    },

    // Color schemes
    colors: {
        primary: ['#9333EA', '#A855F7', '#C084FC'],
        secondary: ['#EC4899', '#F472B6', '#F9A8D4'],
        accent: ['#3B82F6', '#60A5FA', '#93C5FD'],
        neutral: ['#6B7280', '#9CA3AF', '#D1D5DB']
    },

    // Gradient backgrounds
    createGradient(ctx, color) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, `${color}30`);
        gradient.addColorStop(1, `${color}05`);
        return gradient;
    }
};

// Chart Types
const chartTypes = {
    // Line Chart
    createLineChart(ctx, data, options = {}) {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets.map((dataset, index) => ({
                    label: dataset.label,
                    data: dataset.data,
                    borderColor: chartConfig.colors.primary[index % 3],
                    backgroundColor: chartConfig.createGradient(ctx, chartConfig.colors.primary[index % 3]),
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: chartConfig.colors.primary[index % 3],
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }))
            },
            options: {
                ...chartConfig.defaults,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            },
                            callback: function(value) {
                                return new Intl.NumberFormat('en-US', {                                    style: 'currency',                                    currency: 'TSH',                                    minimumFractionDigits: 0,                                    maximumFractionDigits: 0                                }).format(value);
                            }
                        }
                    }
                },
                ...options
            }
        });
    },

    // Bar Chart
    createBarChart(ctx, data, options = {}) {
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: data.datasets.map((dataset, index) => ({
                    label: dataset.label,
                    data: dataset.data,
                    backgroundColor: chartConfig.colors.primary[index % 3],
                    borderRadius: 4,
                    barThickness: 'flex',
                    maxBarThickness: 32
                }))
            },
            options: {
                ...chartConfig.defaults,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            }
                        }
                    }
                },
                ...options
            }
        });
    },

    // Doughnut Chart
    createDoughnutChart(ctx, data, options = {}) {
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: chartConfig.colors.primary,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                ...chartConfig.defaults,
                cutout: '75%',
                plugins: {
                    ...chartConfig.defaults.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12,
                                family: "'Inter', sans-serif"
                            }
                        }
                    }
                },
                ...options
            }
        });
    },

    // Area Chart
    createAreaChart(ctx, data, options = {}) {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets.map((dataset, index) => ({
                    label: dataset.label,
                    data: dataset.data,
                    borderColor: chartConfig.colors.primary[index % 3],
                    backgroundColor: chartConfig.createGradient(ctx, chartConfig.colors.primary[index % 3]),
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6
                }))
            },
            options: {
                ...chartConfig.defaults,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)',
                            drawBorder: false
                        }
                    }
                },
                ...options
            }
        });
    }
};

// Chart Utilities
const chartUtils = {
    // Format currency
        formatCurrency(value) {        return new Intl.NumberFormat('en-US', {            style: 'currency',            currency: 'TSH'        }).format(value);    },

    // Format percentage
    formatPercentage(value) {
        return new Intl.NumberFormat('en-US', {
            style: 'percent',
            minimumFractionDigits: 1,
            maximumFractionDigits: 1
        }).format(value / 100);
    },

    // Format large numbers
    formatNumber(value) {
        return new Intl.NumberFormat('en-US', {
            notation: 'compact',
            compactDisplay: 'short'
        }).format(value);
    },

    // Generate random data
    generateRandomData(count, min, max) {
        return Array.from({ length: count }, () => 
            Math.floor(Math.random() * (max - min + 1)) + min
        );
    },

    // Generate date labels
    generateDateLabels(count, interval = 'day') {
        const labels = [];
        const date = new Date();
        
        for (let i = 0; i < count; i++) {
            switch (interval) {
                case 'day':
                    date.setDate(date.getDate() - 1);
                    labels.unshift(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
                    break;
                case 'month':
                    date.setMonth(date.getMonth() - 1);
                    labels.unshift(date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }));
                    break;
                case 'year':
                    date.setFullYear(date.getFullYear() - 1);
                    labels.unshift(date.getFullYear().toString());
                    break;
            }
        }
        
        return labels;
    }
};

// Export for use in other files
window.chartTypes = chartTypes;
window.chartUtils = chartUtils; 