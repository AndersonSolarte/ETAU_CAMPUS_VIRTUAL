/* E-TAU Campus Virtual - Preloader Script */
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
                    '<defs>' +
                        '<linearGradient id="tau-pl-grad" x1="0%" y1="0%" x2="100%" y2="100%">' +
                            '<stop offset="0%" stop-color="#c62b3a"/>' +
                            '<stop offset="100%" stop-color="#e87a84"/>' +
                        '</linearGradient>' +
                    '</defs>' +
                    '<circle cx="50" cy="50" r="44" stroke="rgba(198,43,58,0.08)" stroke-width="1.8"/>' +
                    '<circle class="tau-pl-arc" cx="50" cy="50" r="44" stroke="url(#tau-pl-grad)" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="90 186"/>' +
                '</svg>' +
                '<img class="tau-pl-icon" src="/theme/tau_branding/assets/official/tau-official-icon.png" alt="" onload="this.style.opacity=1" style="opacity:0; transition:opacity 0.15s ease;">' +
            '</div>' +
            '<div class="tau-pl-label"><span class="e-tau-design" style="font-size:1.1em;">E</span>-TAU CAMPUS VIRTUAL</div>' +
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
        }, 60000);
    }

    window.addEventListener("beforeunload", function() {
        var preloader = mountPreloader();
        if (preloader) {
            preloader.classList.remove("tau-pl-out");
        }
    });

    document.addEventListener("click", function(e) {
        if (e.defaultPrevented) {
            return;
        }
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
    }, false);

})();
