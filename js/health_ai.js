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

function addDownloadButton(markdownText) {
    const wrapper = document.createElement('div');
    wrapper.className = 'mt-5 flex justify-end border-t border-slate-100 pt-4';

    const dlBtn = document.createElement('button');
    dlBtn.textContent = 'ダウンロード (.md)';
    dlBtn.className = 'inline-flex items-center gap-2 rounded-lg border border-sky-200 px-4 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-50';
    dlBtn.addEventListener('click', () => {
        const blob = new Blob([markdownText], { type: 'text/markdown' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `health_ai_${new Date().toISOString().slice(0, 10)}.md`;
        a.click();
        URL.revokeObjectURL(url);
    });

    wrapper.appendChild(dlBtn);
    box.appendChild(wrapper);
}

btn.addEventListener('click', async () => {
    if (!confirm('AI分析を実行しますか？')) return;

    showModal();
    btn.disabled = true;

    try {
        // API URL
        const url = 'api/health/ai/';
        // TODO: APIにGETリクエスト
        const response = await fetch(url);
        if (!response || !response.ok) {
            showMessage('API リクエストに失敗しました。');
            return;
        }
        // JSONをパース
        const json = await response.json();

        if (json.status === 'ok') {
            box.innerHTML = marked.parse(json.advice);
            addDownloadButton(json.advice);
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
