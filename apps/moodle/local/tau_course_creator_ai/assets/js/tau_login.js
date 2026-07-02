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

        var officialLogoHtml = "" +
            "<div id='loginlogo' class='login-logo tau-login-official-logo'>" +
                "<img id='logoimage' src='/theme/tau_branding/assets/official/tau-official-icon.png' class='img-fluid' alt='TAU Campus Virtual'>" +
                "<h1 class='login-heading visually-hidden'>Entrar a TAU Campus Virtual</h1>" +
            "</div>";

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
            languages.push("<a href='" + window.location.pathname + "?lang=es_co'>Espanol (Internacional)</a>");
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

        function buildWelcomeBlock(brandlabel, description) {
            return "<div class='tau-login-welcome'>" +
                "<div class='tau-campus-brand'>" + brandlabel + "</div>" +
                "<div class='tau-campus-inst'>UNICESMAG</div>" +
                "<p>" + description + "</p>" +
                "<div class='tau-campus-meta'>Vigilada Mineducacion</div>" +
            "</div>";
        }

        // Create Presencial Campus Card Setup
        var presencialCard = document.createElement("div");
        presencialCard.id = "tau-presencial-card";
        presencialCard.className = "login-container tau-login-card-presencial";

        presencialCard.innerHTML = officialLogoHtml.replace("id='loginlogo'", "id='tau-presencial-loginlogo'") +
            buildWelcomeBlock("TAU Campus Presencial", "Accede a la plataforma academica presencial") +
            "<div class='tau-login-btnwrap'><div class='tau-login-btnbox'>" +
                "<a href='https://www.unicesmag.edu.co/tau/' class='login-identityprovider-btn btn'>" +
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
        virtualCard.innerHTML = officialLogoHtml +
            buildWelcomeBlock("TAU Campus Virtual", "Inicia sesion con tu cuenta Google institucional") +
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
                m: "Este correo no esta registrado en TAU Campus Virtual. Debes ingresar con un correo institucional autorizado y previamente creado en la plataforma."
            },
            "wrong-pass": {
                t: "Credenciales incorrectas",
                m: "El acceso solo esta permitido con tu cuenta Google institucional <b>@unicesmag.edu.co</b>. No uses cuentas personales como Gmail."
            },
            "blocked": {
                t: "Cuenta bloqueada",
                m: "Tu cuenta existe, pero esta suspendida para ingreso. Si necesitas acceso como docente o estudiante, solicita la habilitacion al administrador."
            },
            "no-auth": {
                t: "Metodo no permitido",
                m: "Solo se permite el ingreso con <b>Google institucional</b> y con usuarios autorizados dentro de la plataforma."
            },
            "unauthorized-email": {
                t: "Correo no autorizado",
                m: "Este correo no tiene autorizacion de ingreso en TAU Campus Virtual. Debes usar tu cuenta institucional <b>@unicesmag.edu.co</b> registrada previamente en la plataforma."
            },
            "generic": {
                t: "Error de acceso",
                m: "No fue posible validar tu acceso. Verifica que tu correo institucional este autorizado en TAU Campus Virtual e intenta nuevamente."
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
            } else if (m.includes("contras") || m.includes("incorrecta") || m.includes("incorrectos")) {
                k = "wrong-pass";
            } else if (m.includes("bloqueado") || m.includes("bloqueada") || m.includes("suspendid")) {
                k = "blocked";
            } else if (m.includes("autorizad") || m.includes("permitid") || m.includes("dominio") || m.includes("institucional")) {
                k = "unauthorized-email";
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
