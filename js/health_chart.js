// メッセージ表示エリア
const message = document.getElementById('message');

const themeColor = "rgba(0, 148, 254, 0.6)"
const upperNormalColor = "rgba(201, 135, 149, 0.15)"
const lowerNormalColor = "rgba(99, 149, 255, 0.15)"
const upperDangerColor = "rgba(249, 150, 150, 0.15)"
const lowerDangerColor = "rgba(249, 150, 150, 0.15)"
const dangerColor = "rgba(249, 150, 150, 0.6)"

// ChartJS プラグインの登録
Chart.register(window['chartjs-plugin-annotation']); 

// グラフレンダリング
renderCharts();

// データ取得関数（共通）
async function fetchHealthData() {
    // APIのURLを指定
    const url = 'api/health/get/';
    // Fetch APIを使用してデータを取得
    const response = await fetch(url);
    if (!response || !response.ok) {
        showMessage(`API通信エラー: ${url}`);
        return;
    }
    try {
        // レスポンスのJSONをパース
        const data = await response.json();
        console.log(data);
        return data;
    } catch (error) {
        showMessage('JSONパースエラー');
    }
    showMessage('APIデータ取得エラー');
}

// 体重グラフ
function renderWeightChart(data) {
    // グラフのラベル
    const labels = data.map(item => item.recorded_at);
    // 体重データ
    const weights = data.map(item => parseFloat(item.weight));

    // 体重データの最小値と最大値を取得
    const minWeight = Math.min(...weights);
    const maxWeight = Math.max(...weights);

    // 体重グラフの描画
    new Chart(document.getElementById('weightChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '体重 (kg)',
                data: weights,
                backgroundColor: themeColor,
                borderWidth: 0,
                fill: false,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { title: { display: true, text: '体重の推移' } },
            scales: { y: { beginAtZero: false } },
            scales: {
                y: {
                    beginAtZero: false,
                    min: Math.floor(minWeight - 1), // 下に1kg余白
                    max: Math.ceil(maxWeight + 1), // 上に1kg余白
                    title: {
                        display: true,
                        text: 'kg'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '記録日'
                    }
                }
            }
        }
    });
}


// 心拍数グラフ
function renderHeartRateChart(data) {
    const labels = data.map(item => item.recorded_at);
    const heartRates = data.map(item => parseInt(item.heart_rate));

    new Chart(document.getElementById('heartRateChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: '心拍数 (bpm)',
                data: heartRates,
                borderColor: themeColor,
                borderWidth: 2,
                fill: false,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: { display: true, text: '心拍数の推移' },
                annotation: {
                    annotations: {
                        line60: {
                            type: 'line',
                            yMin: 60,
                            yMax: 60,
                            borderColor: themeColor,
                            borderWidth: 1,
                            borderDash: [6, 4],
                            label: {
                                enabled: true,
                                position: 'end',
                                backgroundColor: 'transparent',
                            }
                        },
                        line100: {
                            type: 'line',
                            yMin: 100,
                            yMax: 100,
                            borderColor: dangerColor,
                            borderWidth: 1,
                            borderDash: [6, 4],
                            label: {
                                enabled: true,
                                position: 'end',
                                backgroundColor: 'transparent',
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 40,
                    max: 120,
                    ticks: {
                        stepSize: 5,
                    },
                    title: {
                        display: true,
                        text: 'bpm'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '記録日'
                    }
                }
            }
        }
    });
}


// 血圧グラフ
Chart.register(window['chartjs-plugin-annotation']);

function renderBloodPressureChart(data) {
    const labels = data.map(item => item.recorded_at);
    const bpRanges = data.map(item => ({
        x: item.recorded_at,
        y: [parseInt(item.diastolic), parseInt(item.systolic)]
    }));

    new Chart(document.getElementById('bpChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '血圧範囲 (mmHg)',
                data: bpRanges,
                backgroundColor: themeColor,
                borderColor: themeColor,
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: '血圧の推移（拡張期〜収縮期）'
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const [low, high] = ctx.raw.y;
                            return `拡張期 ${low} mmHg - 収縮期 ${high} mmHg`;
                        }
                    }
                },
                annotation: {
                    annotations: {
                        hypertensionZone: {
                            type: 'box',
                            yMin: 140,
                            yMax: 200,
                            backgroundColor: upperDangerColor,
                            borderWidth: 0,
                            label: {
                                content: '高血圧域',
                                color: 'red',
                                enabled: true,
                                position: 'start'
                            }
                        },
                        lowBloodPressureZone: {
                            type: 'box',
                            yMin: 0,
                            yMax: 60,
                            backgroundColor: lowerDangerColor,
                            borderWidth: 0,
                            label: {
                                content: '低血圧域',
                                color: 'blue',
                                enabled: true,
                                position: 'end'
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    max: 200,
                    min: 0,
                    ticks: {
                        stepSize: 25
                    },
                    title: {
                        display: true,
                        text: 'mmHg'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '記録日'
                    }
                }
            }
        }
    });
}

// 実行エントリーポイント
async function renderCharts() {
    try {
        const data = await fetchHealthData();
        renderWeightChart(data);
        renderHeartRateChart(data);
        renderBloodPressureChart(data);
    } catch (error) {
        console.log('エラー:', error);
    }
}

// グラフのダウンロード
function downloadChart() {
    const canvasIds = ['weightChart', 'heartRateChart', 'bpChart'];
    const canvases = canvasIds.map(id => document.getElementById(id));

    const width = Math.max(...canvases.map(c => c.width));
    const height = canvases.reduce((sum, c) => sum + c.height, 0);

    const combinedCanvas = document.createElement('canvas');
    combinedCanvas.width = width;
    combinedCanvas.height = height;
    const ctx = combinedCanvas.getContext('2d');

    // 白背景で塗りつぶし
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, width, height);

    let y = 0;
    canvases.forEach(canvas => {
        if (canvas) {
            ctx.drawImage(canvas, 0, y);
            y += canvas.height;
        }
    });

    const link = document.createElement('a');
    link.href = combinedCanvas.toDataURL('image/png');
    link.download = 'health_chart.png';
    link.click();
}

function showMessage(msg) {
    message.classList.remove('hidden');
    message.innerText = msg;
    setTimeout(() => {
        message.classList.add('hidden');
        message.innerText = '';
    }, 3000);
}
