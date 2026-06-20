const themeColor = 'rgba(249, 115, 22, 0.75)';   // orange-500
const fillColor  = 'rgba(249, 115, 22, 0.10)';
const goalColor  = 'rgba(99, 149, 255, 0.55)';    // 目標ライン

Chart.register(window['chartjs-plugin-annotation']);

renderCharts();

async function fetchActivityData() {
    // TODO: APIのURLを指定
    const url = '';
    // APIからデータを取得
    const res = await fetch(url);
    if (!res || !res.ok) {
        showMessage(`API通信エラー`);
        return;
    }
    try {
        // JSONをパースして返す
        return await res.json();
    } catch {
        showMessage('JSONパースエラー');
    }
}

function renderCaloriesChart(data) {
    const labels   = data.map(d => d.exercise_date);
    const calories = data.map(d => parseInt(d.total_calories));
    const durations = data.map(d => parseInt(d.total_duration));

    const maxCal = Math.max(...calories, 400);

    new Chart(document.getElementById('caloriesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: '消費カロリー（kcal）',
                    data: calories,
                    borderColor: themeColor,
                    backgroundColor: fillColor,
                    borderWidth: 2.5,
                    pointBackgroundColor: themeColor,
                    pointRadius: 4,
                    fill: true,
                    tension: 0.35,
                    yAxisID: 'yCalories',
                },
                {
                    label: '運動時間（分）',
                    data: durations,
                    borderColor: 'rgba(99, 149, 255, 0.6)',
                    backgroundColor: 'transparent',
                    borderWidth: 1.5,
                    pointBackgroundColor: 'rgba(99, 149, 255, 0.6)',
                    pointRadius: 3,
                    fill: false,
                    tension: 0.35,
                    borderDash: [5, 4],
                    yAxisID: 'yDuration',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                title: {
                    display: true,
                    text: '消費カロリーの推移（日別合計）',
                    font: { size: 14 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            if (ctx.datasetIndex === 0) return `消費カロリー: ${ctx.parsed.y} kcal`;
                            return `運動時間: ${ctx.parsed.y} 分`;
                        }
                    }
                },
                annotation: {
                    annotations: {
                        goalLine: {
                            type: 'line',
                            yScaleID: 'yCalories',
                            yMin: 300,
                            yMax: 300,
                            borderColor: goalColor,
                            borderWidth: 1.5,
                            borderDash: [6, 4],
                            label: {
                                content: '目安 300 kcal',
                                display: true,
                                position: 'end',
                                backgroundColor: 'transparent',
                                color: goalColor,
                                font: { size: 11 }
                            }
                        }
                    }
                }
            },
            scales: {
                yCalories: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    max: Math.ceil((maxCal + 100) / 100) * 100,
                    title: { display: true, text: 'kcal' },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                yDuration: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    title: { display: true, text: '分' },
                    grid: { drawOnChartArea: false }
                },
                x: {
                    title: { display: true, text: '運動日' },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                }
            }
        }
    });
}

async function renderCharts() {
    try {
        const data = await fetchActivityData();
        if (!data || data.length === 0) {
            showMessage('グラフを表示するデータがありません。');
            return;
        }
        renderCaloriesChart(data);
    } catch (e) {
        showMessage('グラフの描画中にエラーが発生しました。');
        console.error(e);
    }
}

function downloadChart() {
    const canvas = document.getElementById('caloriesChart');
    const combined = document.createElement('canvas');
    combined.width  = canvas.width;
    combined.height = canvas.height;

    // コンテキストを取得
    const ctx = combined.getContext('2d');
    // 背景を白で塗りつぶす
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, combined.width, combined.height);
    ctx.drawImage(canvas, 0, 0);

    // ダウンロードリンクを作成してクリック
    const link = document.createElement('a');
    link.href     = combined.toDataURL('image/png');
    link.download = 'activity_chart.png';
    link.click();
}

