const aiBtn         = document.getElementById('meal-ai-btn');
const foodNameInput = document.getElementById('food_name');
const aiMessage     = document.getElementById('meal-ai-message');

const calories = document.getElementById('calories');
const protein_g = document.getElementById('protein_g');
const fat_g = document.getElementById('fat_g');
const carbohydrate_g = document.getElementById('carbohydrate_g');

function setLoading(loading) {
    aiBtn.disabled = loading;
    aiBtn.textContent = loading ? '予測中…' : 'AI で栄養を予測';
}

function showMessage(text, isError = false) {
    aiMessage.textContent = text;
    aiMessage.className = [
        'mt-2 text-xs',
        isError ? 'text-rose-600' : 'text-sky-600',
    ].join(' ');
}

aiBtn.addEventListener('click', async () => {
    const foodName = foodNameInput.value.trim();
    if (!foodName) {
        showMessage('先にメニュー名を入力してください。', true);
        return;
    }

    setLoading(true);
    showMessage('');

    try {
        // API URL
        const url = 'api/meal/ai/';
        // TODO: APIにPOSTリクエスト
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ food_name: foodName }),
        });
        if (!res.ok) throw new Error();
        // JSONをパース
        const json = await res.json();
        if (json.status !== 'ok') {
            showMessage(json.message ?? '取得に失敗しました。', true);
            return;
        }

        // フォームの各フィールドに値をセット
        calories.textContent = json.calories ?? 'N/A';
        protein_g.textContent = json.protein_g ?? 'N/A';
        fat_g.textContent = json.fat_g ?? 'N/A';
        carbohydrate_g.textContent = json.carbohydrate_g ?? 'N/A';

        showMessage(`「${foodName}」の栄養成分を反映しました。値は目安です。`);
    } catch {
        showMessage('通信エラーが発生しました。', true);
    } finally {
        setLoading(false);
    }
});
