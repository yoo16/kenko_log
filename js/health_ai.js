const btn = document.getElementById('ai-chat-btn');
const box = document.getElementById('ai-result');

btn.addEventListener('click', async () => {
    if (!confirm('診断を開始しますか？')) {
        return;
    }
    btn.disabled = true;
    box.innerHTML = '<p>診断中…少々お待ちください。</p>';

    try {
        const userId = btn.dataset.userId;
        // API から診断結果を取得
        const res = await fetch('api/health/ai/');
        // JSONをJavaScriptオブジェクトに変換
        const json = await res.json();
        console.log(json);

        if (json.status === 'ok') {
            // Markdown を HTML に変換して表示
            const html = marked.parse(json.advice);
            box.innerHTML = html;
        } else {
            box.innerHTML = '<p class="text-red-600">診断の取得に失敗しました。</p>';
        }
    } catch (e) {
        box.innerHTML = '<p class="text-red-600">通信エラーが発生しました。</p>';
    } finally {
        btn.disabled = false;
    }
});
