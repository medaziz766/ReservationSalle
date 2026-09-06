(function () {
    const root = document.documentElement;
    const storedTheme = localStorage.getItem('roombooking-theme');
    const saved = storedTheme === 'dark' || storedTheme === 'light' ? storedTheme : 'light';
    root.setAttribute('data-theme', saved);

    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('themeToggle');
        if (!btn) return;
        updateIcon(root.getAttribute('data-theme'));

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
