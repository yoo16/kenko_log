const aiBtn = document.getElementById('meal-ai-btn');
const foodNameInput = document.getElementById('food_name');
const aiMessage = document.getElementById('meal-ai-message');
const aiModal = document.getElementById('ai-loading-modal');

const calories = document.getElementById('calories');
const protein_g = document.getElementById('protein_g');
const fat_g = document.getElementById('fat_g');
const carbohydrate_g = document.getElementById('carbohydrate_g');

function setLoading(loading) {
    aiBtn.disabled = loading;
    aiModal.classList.toggle('hidden', !loading);
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
        // APIにPOSTリクエスト
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ food_name: foodName }),
        });
        if (!response || !response.ok) {
            showMessage(`API通信エラー: ${url}`);
            return;
        }
        // JSONをパース
        const json = await response.json();
        if (json.status !== 'ok') {
            showMessage(json.message ?? '取得に失敗しました。', true);
            return;
        }

        calories.value = json.calories ?? '';
        protein_g.value = json.protein_g ?? '';
        fat_g.value = json.fat_g ?? '';
        carbohydrate_g.value = json.carbohydrate_g ?? '';

        showMessage(`「${foodName}」の栄養成分を反映しました。値は目安です。`);
    } catch {
        showMessage('通信エラーが発生しました。', true);
    } finally {
        setLoading(false);
    }
});
