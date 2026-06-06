const themeColor = "rgba(0, 148, 254, 0.6)";
const optimalColor = "rgba(99, 149, 255, 0.12)";
const dangerColor = "rgba(249, 150, 150, 0.6)";

Chart.register(window['chartjs-plugin-annotation']);

renderSleepChart();

async function fetchSleepData() {
    const url = 'api/sleep/get/';
    const response = await fetch(url);
    if (!response || !response.ok) {
        showMessage(`API通信エラー: ${url}`);
        return;
    }
    try {
        return await response.json();
    } catch {
        showMessage('JSONパースエラー');
    }
}

async function renderSleepChart() {
    try {
        const data = await fetchSleepData();
        if (!data || data.length === 0) {
            showMessage('睡眠データがありません');
            return;
        }

        const labels = data.map(item => item.sleep_date);
        // 分 → 時間（小数）に変換
        const hours = data.map(item => parseFloat((item.sleep_duration_minutes / 60).toFixed(2)));

        new Chart(document.getElementById('sleepChart').getContext('2d'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: '睡眠時間 (時間)',
                    data: hours,
                    borderColor: themeColor,
                    backgroundColor: "rgba(0, 148, 254, 0.08)",
                    borderWidth: 2,
                    pointBackgroundColor: hours.map(h =>
                        h < 7 || h > 9 ? dangerColor : themeColor
                    ),
                    pointRadius: 4,
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: true, text: '睡眠時間の推移' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const h = Math.floor(ctx.parsed.y);
                                const m = Math.round((ctx.parsed.y - h) * 60);
                                return `睡眠時間: ${h}時間${m}分`;
                            }
                        }
                    },
                    annotation: {
                        annotations: {
                            optimalZone: {
                                type: 'box',
                                yMin: 7,
                                yMax: 9,
                                backgroundColor: optimalColor,
                                borderWidth: 0,
                                label: {
                                    content: '推奨睡眠時間 (7〜9時間)',
                                    color: 'rgba(99, 149, 255, 0.8)',
                                    enabled: true,
                                    position: 'start',
                                    font: { size: 11 }
                                }
                            },
                            line7: {
                                type: 'line',
                                yMin: 7,
                                yMax: 7,
                                borderColor: 'rgba(99, 149, 255, 0.5)',
                                borderWidth: 1,
                                borderDash: [6, 4],
                            },
                            line9: {
                                type: 'line',
                                yMin: 9,
                                yMax: 9,
                                borderColor: 'rgba(99, 149, 255, 0.5)',
                                borderWidth: 1,
                                borderDash: [6, 4],
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 12,
                        ticks: {
                            stepSize: 1,
                            callback: (v) => `${v}h`
                        },
                        title: { display: true, text: '時間 (h)' }
                    },
                    x: {
                        title: { display: true, text: '記録日' }
                    }
                }
            }
        });
    } catch (error) {
        console.log('エラー:', error);
    }
}

function downloadChart() {
    const canvas = document.getElementById('sleepChart');
    const combined = document.createElement('canvas');
    combined.width  = canvas.width;
    combined.height = canvas.height;
    const ctx = combined.getContext('2d');
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, combined.width, combined.height);
    ctx.drawImage(canvas, 0, 0);

    const link = document.createElement('a');
    link.href     = combined.toDataURL('image/png');
    link.download = 'sleep_chart.png';
    link.click();
}

