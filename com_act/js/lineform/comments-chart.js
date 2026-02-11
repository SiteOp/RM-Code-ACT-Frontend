/**
 * Comments Chart - Chart.js Implementation
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 */

(function() {
    'use strict';
    
    /**
     * Initialisiert das Comments Chart
     */
    function initCommentsChart() {
        const canvas = document.getElementById('commentsChart');
        
        if (!canvas) {
            console.error('ACT Comments Chart: Canvas element not found');
            return;
        }
        
        // Prüfe ob Chart.js verfügbar ist
        if (typeof Chart === 'undefined') {
            console.error('ACT Comments Chart: Chart.js is not loaded');
            return;
        }
        
        // Prüfe ob Daten verfügbar sind
        if (typeof actCommentsChartData === 'undefined') {
            console.error('ACT Comments Chart: Chart data not found');
            return;
        }
        
        const data = actCommentsChartData;
        
        // Datasets vorbereiten
        const datasets = [
            {
                data: data.totalData,
                label: data.totalLabel + ' (' + data.totalSum + ')',
                borderColor: data.totalColor,
                backgroundColor: 'transparent',
                tension: 0.1,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2,
                fill: false
            }
        ];
        
        // Filter-Dataset hinzufügen wenn aktiv
        if (data.hasFilter && data.filterData.length > 0) {
            datasets.push({
                data: data.filterData,
                label: data.filterLabel + ' (' + data.filterSum + ')',
                borderColor: data.filterColor,
                backgroundColor: 'transparent',
                tension: 0.1,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2,
                fill: false
            });
        }
        
        // Chart Konfiguration
        const config = {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: '500'
                            },
                            padding: 15,
                            usePointStyle: true,
                            boxWidth: 8,
                            boxHeight: 8
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label = label.split('(')[0].trim() + ': ';
                                }
                                label += context.parsed.y;
                                return label;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        align: 'top',
                        anchor: 'end',
                        offset: 4,
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        formatter: function(value) {
                            return value > 0 ? value : '';
                        },
                        color: function(context) {
                            return context.dataset.borderColor;
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            precision: 0,
                            stepSize: 1
                        }
                    }
                },
                animation: {
                    duration: 750,
                    easing: 'easeInOutQuart'
                }
            }
        };
        
        // Chart erstellen
        try {
            new Chart(canvas, config);
            console.log('ACT Comments Chart: Successfully initialized');
        } catch (error) {
            console.error('ACT Comments Chart: Error creating chart', error);
        }
    }
    
    // Chart initialisieren wenn DOM bereit ist
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCommentsChart);
    } else {
        initCommentsChart();
    }
    
})();