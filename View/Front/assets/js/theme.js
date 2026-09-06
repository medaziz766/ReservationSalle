(function () {
    const root = document.documentElement;
    const saved = localStorage.getItem('roombooking-theme') || 'light';
    root.setAttribute('data-theme', saved);

    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('themeToggle');
        if (!btn) return;
        updateIcon(saved);

        btn.addEventListener('click', function () {
            const current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('roombooking-theme', next);
            updateIcon(next);
        });

        function updateIcon(theme) {
            btn.textContent = theme === 'dark' ? '☀️' : '🌙';
        }
    });
})();
