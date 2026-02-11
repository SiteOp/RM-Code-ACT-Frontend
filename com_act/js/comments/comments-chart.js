/**
 * Comments Chart - Chart.js Implementation
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 */

(function() {
    'use strict';
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }
    
    function initChart() {
        const canvas = document.getElementById('commentsChart');
        
        if (!canvas) {
            console.error('ACT Chart: Canvas nicht gefunden');
            return;
        }
        
        if (typeof Chart === 'undefined') {
            console.error('ACT Chart: Chart.js nicht geladen');
            return;
        }
        
        // Prüfe ChartDataLabels Plugin
        const hasDataLabels = typeof ChartDataLabels !== 'undefined';
        console.log('ACT Chart: Chart.js Version:', Chart.version);
        console.log('ACT Chart: ChartDataLabels verfügbar:', hasDataLabels);
        
        // Hole Daten aus globalem Objekt
        if (typeof actCommentsChartData === 'undefined') {
            console.error('ACT Chart: Daten nicht gefunden');
            return;
        }
        
        const data = actCommentsChartData;
        
        // Validierung
        if (!data.labels || data.labels.length === 0 || !data.totalData || data.totalData.length === 0) {
            console.error('ACT Chart: Keine gültigen Daten vorhanden');
            return;
        }
        
        // Plugin zur Legende-Abstand hinzufügen
        const legendSpacingPlugin = {
            id: 'legendSpacing',
            beforeInit(chart) {
                const originalFit = chart.legend.fit;
                chart.legend.fit = function fit() {
                    originalFit.bind(chart.legend)();
                    this.height += 20; // Extra Abstand nach unten
                };
            }
        };
        
        // Datasets
        const datasets = [
            {
                data: data.totalData,
                label: '12 Monate gesamt (' + data.totalSum + ')',
                borderColor: data.primaryColor,
                backgroundColor: 'transparent',
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: data.primaryColor,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                borderWidth: 3,
                fill: false
            }
        ];
        
        // Filter-Dataset
        if (data.showFilter && data.filterData && data.filterData.length > 0) {
            datasets.push({
                data: data.filterData,
                label: 'Filter: ' + data.filterLabel + ' (' + data.filterSum + ')',
                borderColor: data.secondaryColor,
                backgroundColor: 'transparent',
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: data.secondaryColor,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                borderWidth: 3,
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
                // Mehr Platz oben für Datalabels
                layout: {
                    padding: {
                        top: 30,
                        right: 20,
                        bottom: 0,
                        left: 10
                    }
                },
                interaction: {
                    mode: 'nearest',   // Nur nächster Punkt
                    intersect: true    // Nur bei direktem Hover auf Punkt
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 15,
                                weight: '600'
                            },
                            padding: 25,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 10,
                            boxHeight: 10
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 12,
                        titleFont: {
                            size: 15,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        displayColors: false, // Kein Farbkästchen
                        callbacks: {
                            // Nur Monat anzeigen (z.B. "02/26")
                            title: function(context) {
                                return context[0].label.replace(/['"]/g, '');
                            },
                            // Nur Anzahl Kommentare des Monats
                            label: function(context) {
                                return context.parsed.y + ' Kommentare';
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        align: 'top',
                        anchor: 'end',
                        offset: 6,
                        clip: false,
                        color: function(context) {
                            return context.dataset.borderColor;
                        },
                        backgroundColor: 'rgba(255, 255, 255, 0.0)', // Kein Hintergrund
                        borderRadius: 0,
                        padding: {
                            top: 0,
                            right: 4,
                            bottom: 0,
                            left: 4
                        },
                        font: {
                            size: 16,       // Größere Schrift
                            weight: 'bold',
                            family: 'Arial, sans-serif'
                        },
                        formatter: function(value) {
                            return value;
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: true
                        },
                        ticks: {
                            display: true,
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            maxRotation: 0,
                            minRotation: 0,
                            padding: 10,
                            autoSkip: false,
                            callback: function(value, index, ticks) {
                                return this.getLabelForValue(value).replace(/['"]/g, '');
                            }
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: false, // Nicht bei 0 starten - bessere Skalierung
                        grid: {
                            color: 'rgba(0, 0, 0, 0.08)',
                            drawBorder: false,
                            lineWidth: 1
                        },
                        ticks: {
                            font: {
                                size: 13
                            },
                            precision: 0,
                            padding: 10,
                            callback: function(value) {
                                return value.toLocaleString('de-DE');
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        };
        
        // Registriere Datalabels Plugin wenn verfügbar
        const pluginsArray = [legendSpacingPlugin];
        if (hasDataLabels) {
            pluginsArray.push(ChartDataLabels);
        } else {
            console.warn('ACT Chart: ChartDataLabels Plugin nicht verfügbar - Labels werden nicht angezeigt');
        }
        config.plugins = pluginsArray;
        
        // Chart erstellen
        try {
            const chartInstance = new Chart(canvas, config);
            console.log('ACT Chart: Erfolgreich initialisiert mit ' + data.totalData.length + ' Datenpunkten');
        } catch (error) {
            console.error('ACT Chart: Fehler beim Erstellen', error);
        }
    }
})();