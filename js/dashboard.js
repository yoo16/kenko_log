const MEAL_TYPE_LABEL = {
    breakfast: '朝食',
    lunch: '昼食',
    dinner: '夕食',
    snack: '間食',
};

const url = 'api/dashboard/';

function formatMinutes(minutes) {
    if (!minutes) return '0h 00m';
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return `${h}h ${String(m).padStart(2, '0')}m`;
}

function el(id) {
    return document.getElementById(id);
}

function renderStats(data) {
    const h = data.latest_health;
    el('stat-weight').innerHTML = h ? `${h.weight}<span class="text-base text-slate-400"> kg</span>` : '-';
    el('stat-weight-date').textContent = h ? h.recorded_at : '記録なし';
    el('stat-heart').innerHTML = h ? `${h.heart_rate}<span class="text-base text-slate-400"> bpm</span>` : '-';
    el('stat-bp').textContent = h ? `血圧 ${h.systolic ?? '-'}/${h.diastolic ?? '-'}` : '血圧 -';

    const ex = data.exercise_summary;
    el('stat-exercise').innerHTML = `${ex?.total_minutes ?? 0}<span class="text-base text-slate-400"> min</span>`;
    el('stat-exercise-sub').textContent = `${ex?.record_count ?? 0}件 / ${ex?.total_calories ?? 0} kcal`;

    const s = data.latest_sleep;
    el('stat-sleep').textContent = s ? formatMinutes(parseInt(s.sleep_duration_minutes)) : '-';
    el('stat-sleep-sub').textContent = `睡眠品質 ${s?.sleep_quality ?? '-'} / 5`;

    el('dashboard-title').textContent = `${data.user.name}さんの健康ダッシュボード`;
}

function renderExercises(exercises) {
    const container = el('recent-exercises');
    if (!exercises || exercises.length === 0) {
        container.innerHTML = '<p class="rounded-lg bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">運動記録はまだありません。</p>';
        return;
    }
    container.innerHTML = exercises.map(e => `
        <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-3">
            <div>
                <a href="activity/edit.php?id=${e.id}" class="text-sm font-semibold text-slate-800 transition hover:text-sky-700">${escHtml(e.exercise_type)}</a>
                <p class="mt-1 text-xs text-slate-400">${escHtml(e.exercise_date)}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-sky-700">${parseInt(e.duration_minutes)} min</p>
                <p class="mt-1 text-xs text-slate-400">${parseInt(e.calories_burned ?? 0)} kcal</p>
            </div>
        </div>
    `).join('');
}

function renderMeals(meals, summary) {
    el('meal-total-calories').textContent = `合計 ${parseInt(summary?.total_calories ?? 0)} kcal`;
    const container = el('recent-meals');
    if (!meals || meals.length === 0) {
        container.innerHTML = '<p class="rounded-lg bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">食事記録はまだありません。</p>';
        return;
    }
    container.innerHTML = meals.map(m => `
        <div class="rounded-lg bg-slate-50 px-4 py-3">
            <div class="flex items-center justify-between gap-4">
                <p class="text-sm font-semibold text-slate-800">${escHtml(m.food_name)}</p>
                <p class="shrink-0 text-sm font-bold text-sky-700">${parseInt(m.calories ?? 0)} kcal</p>
            </div>
            <p class="mt-1 text-xs text-slate-400">
                ${escHtml(m.meal_date)} / ${MEAL_TYPE_LABEL[m.meal_type] ?? escHtml(m.meal_type)}
            </p>
        </div>
    `).join('');
}

function showSkeleton(show) {
    document.querySelectorAll('.skeleton').forEach(el => {
        el.classList.toggle('animate-pulse', show);
        el.classList.toggle('bg-slate-200', show);
        el.classList.toggle('rounded', show);
        el.classList.toggle('text-transparent', show);
    });
}

function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

async function loadDashboard() {
    showSkeleton(true);
    try {
        // APIからデータを取得
        const res = await fetch(url);
        if (!res.ok) {
            showMessage(`API通信エラー: ${url}`);
            return;
        }
        // JSONをパースして返す
        const data = await res.json();
        if (data.status !== 'ok') throw new Error();

        renderStats(data);
        renderExercises(data.recent_exercises);
        renderMeals(data.recent_meals, data.meal_summary);
    } catch {
        el('dashboard-error').classList.remove('hidden');
    } finally {
        showSkeleton(false);
    }
}

loadDashboard();
