/* TAU Campus Virtual - Preloader Script */
(function() {
    "use strict";

    function mountPreloader() {
        if (document.getElementById("tau-preloader")) {
            return;
        }
        var preloader = document.createElement("div");
        preloader.id = "tau-preloader";
        preloader.innerHTML = '<div class="tau-pl-inner">' +
            '<div class="tau-pl-ring-wrap">' +
                '<svg class="tau-pl-svg" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                    '<circle cx="50" cy="50" r="44" stroke="rgba(63,118,195,0.16)" stroke-width="2"/>' +
                    '<circle class="tau-pl-arc" cx="50" cy="50" r="44" stroke="#c62b3a" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="188 88"/>' +
                '</svg>' +
                '<img class="tau-pl-icon" src="/pluginfile.php/1/theme_moove/favicon/0/tau-official-icon.png" alt="TAU">' +
            '</div>' +
            '<div class="tau-pl-label">TAU CAMPUS VIRTUAL</div>' +
        '</div>';
        document.documentElement.appendChild(preloader);

        return preloader;
    }

    function hideLoader() {
        var el = document.getElementById("tau-preloader");
        if (!el || el.classList.contains("tau-pl-out")) {
            return;
        }
        el.classList.add("tau-pl-out");
        setTimeout(function() {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }, 320);
    }

    var isLogin = window.location.pathname.indexOf("/login") !== -1;
    var fromGoogle = sessionStorage.getItem("tau-google-auth") === "1";
    var shouldMount = isLogin || fromGoogle || document.readyState !== "complete";

    if (shouldMount) {
        sessionStorage.setItem("tau-s", "1");
        sessionStorage.removeItem("tau-google-auth");

        mountPreloader();

        window.addEventListener("load", function() {
            requestAnimationFrame(function() {
                requestAnimationFrame(hideLoader);
            });
        }, { once: true });

        window.addEventListener("pageshow", function() {
            hideLoader();
        }, { once: true });

        document.addEventListener("visibilitychange", function() {
            if (document.visibilityState === "visible" && document.readyState === "complete") {
                hideLoader();
            }
        });

        setTimeout(function() {
            if (document.readyState === "complete") {
                hideLoader();
            }
        }, 15000);
    }

    document.addEventListener("click", function(e) {
        var link = e.target.closest("a[href]");
        if (!link) {
            return;
        }

        var href = link.getAttribute("href") || "";
        if (!href || href.charAt(0) === "#" || href.indexOf("javascript:") === 0) {
            return;
        }
        if (link.target === "_blank" || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) {
            return;
        }

        var url;
        try {
            url = new URL(link.href, window.location.href);
        } catch (err) {
            return;
        }

        if (url.origin !== window.location.origin) {
            return;
        }

        if (url.href === window.location.href) {
            return;
        }

        var preloader = mountPreloader();
        if (preloader) {
            preloader.classList.remove("tau-pl-out");
        }
    }, true);
})();
