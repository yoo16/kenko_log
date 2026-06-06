const btn    = document.getElementById('ai-chat-btn');
const box    = document.getElementById('ai-result');
const modal  = document.getElementById('ai-loading-modal');

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
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `health_ai_${new Date().toISOString().slice(0, 10)}.md`;
        a.click();
        URL.revokeObjectURL(url);
    });

    wrapper.appendChild(dlBtn);
    box.appendChild(wrapper);
}

btn.addEventListener('click', async () => {
    showModal();
    btn.disabled = true;

    try {
        // API URL
        const url = 'api/health/ai/';
        // TODO: APIにPOSTリクエスト
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
        });
        if (!res.ok) throw new Error('Network response was not ok');
        // JSONをパース
        const json = await res.json();

        if (json.status === 'ok') {
            box.innerHTML = marked.parse(json.advice);
            addDownloadButton(json.advice);
            box.classList.remove('hidden');
        } else {
            box.innerHTML = '<p class="text-red-600">診断の取得に失敗しました。</p>';
            box.classList.remove('hidden');
        }
    } catch (e) {
        box.innerHTML = '<p class="text-red-600">通信エラーが発生しました。</p>';
        box.classList.remove('hidden');
    } finally {
        hideModal();
        btn.disabled = false;
    }
});
