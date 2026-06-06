function showMessage(msg) {
    const el = document.getElementById('message');
    if (!el) return;
    el.classList.remove('hidden');
    el.innerText = msg;
    setTimeout(() => {
        el.classList.add('hidden');
        el.innerText = '';
    }, 3000);
}
