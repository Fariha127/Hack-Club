(function () {
    var storageKey = "hack-theme";
    var body = document.body;
    var toggle = document.querySelector(".theme-toggle");

    if (!body || !toggle) {
        return;
    }

    function applyTheme(theme) {
        var isDark = theme === "dark";
        body.classList.toggle("dark", isDark);
        toggle.textContent = isDark ? "Light mode" : "Dark mode";
        toggle.setAttribute("aria-pressed", String(isDark));
        toggle.setAttribute("aria-label", isDark ? "Switch to light mode" : "Switch to dark mode");
    }

    var savedTheme = localStorage.getItem(storageKey);
    var initialTheme = savedTheme === "dark" ? "dark" : "light";
    applyTheme(initialTheme);

    toggle.addEventListener("click", function () {
        var nextTheme = body.classList.contains("dark") ? "light" : "dark";
        applyTheme(nextTheme);
        localStorage.setItem(storageKey, nextTheme);
    });
})();
