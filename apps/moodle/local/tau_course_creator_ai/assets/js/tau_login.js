/* TAU Campus Virtual - Login Page Script */
(function() {
    "use strict";

    function initLogin() {
        if (document.body.id !== "page-login-index") return;
        
        // Manual login backdoor for admin / automated testing
        if (window.location.search.indexOf("manual=1") !== -1) {
            document.body.classList.add("tau-manual-login");
            return;
        }

        var container = document.querySelector("body#page-login-index .login-container");
        var wrapper = document.querySelector("body#page-login-index .login-wrapper");
        if (!container || !wrapper) return;

        // Prevent double rendering
        if (document.getElementById("tau-virtual-card")) return;

        // Hide original container instead of replacing its innerHTML
        container.style.setProperty("display", "none", "important");

        var originalLogo = container.querySelector("#loginlogo");
        var logoHtml = originalLogo ? originalLogo.outerHTML : "";

        var googleBtn = container.querySelector(".login-identityprovider-btn");

        var languages = [];
        var nativeLangMenu = container.querySelector(".login-languagemenu") || 
                             document.querySelector(".login-languagemenu") || 
                             document.querySelector(".langmenu");

        if (nativeLangMenu) {
            nativeLangMenu.querySelectorAll("a.dropdown-item, .dropdown-menu a").forEach(function(a) {
                var txt = a.textContent.trim();
                var href = a.href;
                if (txt && href) {
                    languages.push("<a href='" + href + "'>" + txt + "</a>");
                }
            });
        }

        if (languages.length === 0) {
            languages.push("<a href='" + window.location.pathname + "?lang=es_co'>Español (Internacional)</a>");
            languages.push("<a href='" + window.location.pathname + "?lang=en'>English</a>");
        }

        var cookieUrl = "";
        var cookieText = "Aviso de Cookies";
        var nativeCookieLink = document.querySelector("a[href*='cookie']") || 
                               document.querySelector("a[href*='dataprivacy']") || 
                               document.querySelector("footer a[href*='summary']");

        if (nativeCookieLink) {
            cookieUrl = nativeCookieLink.href;
            cookieText = nativeCookieLink.textContent.trim();
        } else {
            cookieUrl = "/admin/tool/dataprivacy/summary.php";
        }

        var cardFooterHtml = "<div class='tau-login-card-foot-controls'>" +
            "<div class='tau-lang-selector-custom'>" +
                "<button type='button' class='tau-lang-btn'>" +
                    "<svg width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><circle cx='12' cy='12' r='10'></circle><line x1='2' y1='12' x2='22' y2='12'></line><path d='M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z'></path></svg>" +
                    "<span>Idioma</span>" +
                "</button>" +
                "<div class='tau-lang-dropdown-menu'>" +
                    languages.join("") +
                "</div>" +
            "</div>" +
            "<a href='" + cookieUrl + "' class='tau-cookie-btn' target='_blank'>" +
                "<svg width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'></path></svg>" +
                "<span>" + cookieText + "</span>" +
            "</a>" +
        "</div>";

        // Create Presencial Campus Card Setup
        var presencialCard = document.createElement("div");
        presencialCard.id = "tau-presencial-card";
        presencialCard.className = "login-container tau-login-card-presencial";
        
        var logoCloneHtml = "";
        if (originalLogo) {
            var lc = originalLogo.cloneNode(true);
            lc.id = "tau-presencial-loginlogo";
            logoCloneHtml = lc.outerHTML;
        }

        presencialCard.innerHTML = logoCloneHtml +
            "<div class='tau-login-welcome'>" +
                "<div class='tau-campus-inst'>Universidad CESMAG</div>" +
                "<div class='tau-campus-title'>Campus Presencial</div>" +
                "<div class='tau-campus-divider'></div>" +
                "<p>Accede a la plataforma académica presencial</p>" +
            "</div>" +
            "<div class='tau-login-btnwrap'><div class='tau-login-btnbox'>" +
                "<a href='https://uv4.unicesmag.edu.co/login/index.php' class='login-identityprovider-btn btn'>" +
                    "<svg width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='#c62b3a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>" +
                        "<path d='M22 10v6M2 10l10-5 10 5-10 5z'></path>" +
                        "<path d='M6 12v5c3 3 9 3 12 0v-5'></path>" +
                    "</svg>" +
                    "Plataforma Presencial" +
                "</a>" +
            "</div></div>" +
            cardFooterHtml;

        // Create Virtual Campus Card Setup
        var virtualCard = document.createElement("div");
        virtualCard.id = "tau-virtual-card";
        virtualCard.className = "login-container tau-login-card-virtual";
        virtualCard.innerHTML = logoHtml +
            "<div class='tau-login-welcome'>" +
                "<div class='tau-campus-inst'>Universidad CESMAG</div>" +
                "<div class='tau-campus-title'>Campus Virtual</div>" +
                "<div class='tau-campus-divider'></div>" +
                "<p>Inicia sesión con tu cuenta Google institucional</p>" +
            "</div>" +
            "<div class='tau-login-btnwrap'><div class='tau-login-btnbox'>" +
            "</div></div>" +
            cardFooterHtml;

        // Move the original Google Button into virtualCard to preserve its event handlers
        var btnBox = virtualCard.querySelector(".tau-login-btnbox");
        if (googleBtn && btnBox) {
            btnBox.appendChild(googleBtn);
        } else if (btnBox) {
            var googleBtnHtml = googleBtn ? googleBtn.outerHTML : "";
            btnBox.innerHTML = googleBtnHtml;
        }

        // Insert both cards before the hidden original container
        wrapper.insertBefore(presencialCard, container);
        wrapper.insertBefore(virtualCard, container);

        // Add language button listeners dynamically
        document.querySelectorAll(".tau-lang-btn").forEach(function(btn) {
            if (btn.dataset.tauLangEvent) return;
            btn.dataset.tauLangEvent = "1";
            btn.addEventListener("click", function(e) {
                e.stopPropagation();
                btn.parentNode.classList.toggle("show");
            });
        });

        document.addEventListener("click", function() {
            document.querySelectorAll(".tau-lang-selector-custom").forEach(function(el) {
                el.classList.remove("show");
            });
        });

        // Scan for error messages to show dynamic Toast
        var TAU_ERR = {
            "no-account": {
                t: "Cuenta no encontrada",
                m: "No encontramos ninguna cuenta TAU con este correo. Usa tu correo <b>@unicesmag.edu.co</b>."
            },
            "wrong-pass": {
                t: "Credenciales incorrectas",
                m: "Verifica que uses tu <b>cuenta Google institucional @unicesmag.edu.co</b>, no una personal."
            },
            "blocked": {
                t: "Cuenta bloqueada",
                m: "Tu cuenta está bloqueada temporalmente. Espera unos minutos o contacta soporte."
            },
            "no-auth": {
                t: "Método no permitido",
                m: "Solo se permite inicio de sesión con <b>Google institucional</b>. El acceso directo está deshabilitado."
            },
            "generic": {
                t: "Error de acceso",
                m: "No fue posible iniciar sesión. Contacta a soporte si el problema persiste."
            }
        };

        function tauShowToast(key) {
            var d = TAU_ERR[key] || TAU_ERR["generic"];
            var el = document.createElement("div");
            el.className = "tau-toast";
            el.innerHTML = '<button class="tau-toast-close" onclick="this.parentNode.remove()" aria-label="Cerrar">&times;</button>' +
                '<div class="tau-toast-title">' + d.t + '</div>' +
                '<div class="tau-toast-body">' + d.m + '</div>' +
                '<div class="tau-toast-contact">' +
                    'Soporte: <a href="mailto:tau-ayuda@unicesmag.edu.co">tau-ayuda@unicesmag.edu.co</a>' +
                '</div>';
            document.body.appendChild(el);
            setTimeout(function() {
                el.style.transition = "opacity .4s";
                el.style.opacity = "0";
                setTimeout(function() {
                    if (el.parentNode) el.remove();
                }, 420);
            }, 9000);
        }

        document.querySelectorAll(".loginerrors, .alert-danger").forEach(function(e) {
            var m = (e.textContent || "").toLowerCase().trim();
            if (!m) return;
            e.style.setProperty("display", "none", "important");
            var k = "generic";
            if (m.includes("no se pudo encontrar") || m.includes("correo electr")) {
                k = "no-account";
            } else if (m.includes("contrase") || m.includes("incorrecta") || m.includes("incorrectos")) {
                k = "wrong-pass";
            } else if (m.includes("bloqueado") || m.includes("bloqueada") || m.includes("suspendid")) {
                k = "blocked";
            } else if (m.includes("plugin") || m.includes("no est")) {
                k = "no-auth";
            }
            tauShowToast(k);
        });

        // Set session storage flag to trigger preloader before Google OAuth redirection
        document.addEventListener("click", function(e) {
            var gBtn = e.target.closest(".login-identityprovider-btn, [href*='oauth2'], [data-provider], .tau-login-google-btn");
            if (gBtn) {
                sessionStorage.setItem("tau-google-auth", "1");
            }
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initLogin);
    } else {
        initLogin();
    }
})();
