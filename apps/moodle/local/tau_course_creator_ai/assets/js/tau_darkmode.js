/* TAU Campus Virtual - Dark Mode Switcher Script */
(function() {
    "use strict";

    var SUN = '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1z"/></svg>';
    var MOON = '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>';

    function init() {
        if (document.getElementById("tau-theme-toggle")) return;

        var t = localStorage.getItem("tau-theme") || "light";
        document.documentElement.setAttribute("data-bs-theme", t);

        var btn = document.createElement("button");
        btn.id = "tau-theme-toggle";
        btn.title = t === "dark" ? "Modo claro" : "Modo oscuro";
        btn.setAttribute("aria-label", "Cambiar modo oscuro/claro");
        btn.innerHTML = t === "dark" ? SUN : MOON;

        document.body.appendChild(btn);

        btn.addEventListener("click", function() {
            var cur = document.documentElement.getAttribute("data-bs-theme") || "light";
            var next = cur === "dark" ? "light" : "dark";
            
            document.documentElement.setAttribute("data-bs-theme", next);
            localStorage.setItem("tau-theme", next);
            
            btn.innerHTML = next === "dark" ? SUN : MOON;
            btn.title = next === "dark" ? "Modo claro" : "Modo oscuro";
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
