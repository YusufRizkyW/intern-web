document.addEventListener('click', function (e) {
    const a = e.target.closest('a.calendar-nav');
    if (!a) return;

    // hanya intercept jika link internal dan berisi ?month=
    try {
        const url = new URL(a.href);
        if (!url.searchParams.has('month')) return;
        if (url.origin !== location.origin) return;
    } catch (err) {
        return;
    }

    e.preventDefault();
    const container = document.getElementById('calendar-container');
    if (!container) return;

    // loading UI
    const oldOpacity = container.style.opacity;
    container.style.opacity = '0.6';

    fetch(a.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
        .then((resp) => {
            if (!resp.ok) throw new Error('Network response was not ok');
            return resp.text();
        })
        .then((html) => {
            // server returns partial HTML; replace container content
            container.innerHTML = html;
            container.style.opacity = oldOpacity || '1';
            history.pushState({}, '', a.href);
        })
        .catch((err) => {
            console.error(err);
            container.style.opacity = oldOpacity || '1';
            // fallback: full navigation
            location.href = a.href;
        });
});

// handle back/forward
window.addEventListener('popstate', function (e) {
    // on back/forward, reload calendar from current URL (AJAX)
    const container = document.getElementById('calendar-container');
    if (!container) {
        location.reload();
        return;
    }

    fetch(location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then((r) => r.text())
        .then((html) => {
            container.innerHTML = html;
        })
        .catch(() => location.reload());
});