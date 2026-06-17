const btn = document.getElementById('ai-chat-btn');
const box = document.getElementById('ai-result');
const modal = document.getElementById('ai-loading-modal');

function showModal() {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function hideModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

btn.addEventListener('click', async () => {
    if (!confirm('AI分析を実行しますか？')) return;

    showModal();
    btn.disabled = true;

    try {
        // API URL
        const url = 'api/health/ai/';
        // APIにGETリクエスト
        const response = await fetch(url);
        if (!response || !response.ok) {
            showMessage('API リクエストに失敗しました。');
            return;
        }
        // JSONをパース
        const json = await response.json();

        if (json.status === 'ok') {
            box.innerHTML = marked.parse(json.advice);
            box.classList.remove('hidden');
        } else {
            showMessage(json.message ?? '診断の取得に失敗しました。');
        }
    } catch (e) {
        showMessage('通信エラーが発生しました。', true);
    } finally {
        hideModal();
        btn.disabled = false;
    }
});
