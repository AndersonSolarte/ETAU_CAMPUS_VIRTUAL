/* TAU Campus Virtual - Dashboard Script */
(function() {
    "use strict";

    var ICO_ENROLLED   = '<svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>';
    var ICO_COMPLETED  = '<svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
    var ICO_DONE       = '<svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
    var ICO_PENDING    = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
    var ICO_INSTRUCTOR = '<svg viewBox="0 0 24 24"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>';

    var TAU_CARD_ICONS = [ICO_ENROLLED, ICO_COMPLETED, ICO_DONE, ICO_PENDING];
    var TAU_G = [
        ["#c62b3a", "#7f0d1c"],
        ["#1e3a8a", "#0a1854"],
        ["#0f766e", "#064e3b"],
        ["#7c3aed", "#3b0d8f"],
        ["#c2410c", "#7c2d12"],
        ["#047857", "#022c22"],
        ["#1d4ed8", "#1e3a8a"],
        ["#be185d", "#6b0c37"]
    ];

    function tauMakeCardsClickable() {
        document.querySelectorAll(".card.dashboard-card, .coursebox, .course-card, .card[data-course-id]").forEach(function(card) {
            if (card.dataset.tauClick) return;
            card.dataset.tauClick = "1";
            var link = card.querySelector("a.aalink[href*='/course/view.php'], a[href*='/course/view.php'], h3 a, h4 a, .coursename a");
            if (!link) return;
            card.style.cursor = "pointer";
            card.addEventListener("click", function(e) {
                if (e.target.closest("a, button, [role=button], .dropdown, .action-menu")) return;
                window.location.href = link.href;
            });
        });
    }

    function tauEnhanceCards() {
        document.querySelectorAll(".card.dashboard-card").forEach(function(card) {
            var link = card.querySelector("a[href*='/course/view.php']");
            var cid = 0;
            if (link) {
                var m = link.href.match(/[?&]id=(\d+)/);
                if (m) cid = parseInt(m[1]);
            }

            // Teacher row
            var body = card.querySelector(".card-body");
            if (body && !body.querySelector(".tau-teacher-row") && window.tauCourseTeachers && cid && window.tauCourseTeachers[cid]) {
                var tchs = window.tauCourseTeachers[cid];
                if (tchs && tchs.length) {
                    var tr = document.createElement("div");
                    tr.className = "tau-teacher-row";
                    tr.innerHTML = '<span class="tau-tr-icon">' + ICO_INSTRUCTOR + '</span><span class="tau-tr-name">' + tchs.slice(0, 2).join(' · ') + '</span>';
                    body.appendChild(tr);
                }
            }

            if (card.dataset.tauEnh) return;
            card.dataset.tauEnh = "1";
            var idx = cid ? cid % TAU_G.length : Array.from(document.querySelectorAll(".card.dashboard-card")).indexOf(card) % TAU_G.length;

            // Detect real image
            var imgDiv = card.querySelector(".card-img-top, .dashboard-card-img, .card-img");
            var imgEl = card.querySelector(".card-img-top img, img.card-img-top, .card-img img");
            var realImg = false;
            if (imgEl && imgEl.src && imgEl.src.indexOf("pluginfile.php") !== -1) {
                realImg = true;
            } else if (imgDiv && imgDiv.style.backgroundImage && imgDiv.style.backgroundImage.indexOf("pluginfile.php") !== -1) {
                realImg = true;
            }

            if (!realImg) {
                var gh = document.createElement("div");
                gh.className = "tau-gradient-header";
                gh.style.background = "linear-gradient(135deg, " + TAU_G[idx][0] + " 0%, " + TAU_G[idx][1] + " 100%)";
                gh.innerHTML = '<div class="tau-gh-pattern"></div><span class="tau-gh-icon">' + TAU_CARD_ICONS[idx % 4] + '</span>';
                if (imgDiv) {
                    var parent = imgDiv.parentNode;
                    if (parent) {
                        parent.replaceChild(gh, imgDiv);
                    } else {
                        card.insertBefore(gh, card.firstChild);
                    }
                } else {
                    card.insertBefore(gh, card.firstChild);
                }
            }

            // Category badge
            if (body && !body.querySelector(".tau-cat-badge")) {
                var catEl = card.querySelector(".coursecat, .course-category, [data-region='coursecategory']");
                var catTxt = catEl ? catEl.textContent.trim() : null;
                if (catTxt) {
                    var badge = document.createElement("div");
                    badge.className = "tau-cat-badge";
                    badge.textContent = catTxt;
                    body.insertBefore(badge, body.firstChild);
                }
            }
        });
    }

    function tauHideConectiMe() {
        document.querySelectorAll("[data-region='footer-content-popover'] .footer-section, [data-region='footer-content-popover'] .footer-section + *").forEach(function(el) {
            if (el.querySelector("a[href*='conecti.me'], img[alt*='Conecti']") || el.textContent.indexOf("conecti") !== -1) {
                el.style.setProperty("display", "none", "important");
            }
        });
    }

    function initDashboard() {
        var isDashboardPage = document.body.id.indexOf("page-my-") === 0;
        
        // Dynamic stats widgets
        if (isDashboardPage && !document.getElementById("tau-personal-dashboard")) {
            fetch("/local/tau_course_creator_ai/user_stats.php?_=" + Date.now())
                .then(function(r) { return r.ok ? r.json() : null; })
                .then(function(d) {
                    if (!d) return;

                    if (d.course_teachers) {
                        window.tauCourseTeachers = d.course_teachers;
                        tauEnhanceCards();
                    }

                    var progress = d.total_activities > 0 ? Math.round(d.completed_activities * 100 / d.total_activities) : 0;
                    function nv(v) { return v || 0; }

                    var getStatIcon = function(lbl, idx) {
                        var l = (lbl || "").toLowerCase();
                        if (l.includes("ia")) return '<svg viewBox="0 0 24 24"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg>';
                        if (l.includes("curso")) return TAU_CARD_ICONS[0];
                        if (l.includes("complet") || l.includes("alumn") || l.includes("estudiant")) return TAU_CARD_ICONS[1];
                        if (l.includes("actividad") || l.includes("ok") || l.includes("hech")) return TAU_CARD_ICONS[2];
                        if (l.includes("pendiente") || l.includes("calific") || l.includes("espera")) return TAU_CARD_ICONS[3];
                        if (l.includes("docent") || l.includes("profe") || l.includes("foro") || l.includes("debate")) return ICO_INSTRUCTOR;
                        return TAU_CARD_ICONS[idx % 4];
                    };

                    var panel = document.createElement("div");
                    panel.id = "tau-personal-dashboard";

                    var h = '<div class="tau-pd-header">';
                    var greeting = "¡Hola, " + d.firstname + "! <span class='wave'>👋</span>";
                    var subtitle = "Tu progreso académico en TAU Campus Virtual";

                    if (d.role === "teacher") {
                        subtitle = "Panel de Labor Docente — Resumen de tus clases";
                    } else if (d.role === "admin") {
                        subtitle = "Panel de Control de la Plataforma — Estado y salud general de Moodle";
                    }

                    h += '<div class="tau-pd-greeting">' + greeting + '</div>';
                    h += '<div class="tau-pd-subtitle">' + subtitle + '</div>';

                    if (d.role === "student") {
                        h += '<div class="tau-pd-prog-wrap">' +
                                '<div class="tau-pd-prog-label"><span>Progreso general</span><span>' + progress + '%</span></div>' +
                                '<div class="tau-pd-prog-bar"><div class="tau-pd-prog-fill" id="tau-pd-fill"></div></div>' +
                             '</div>';
                    }
                    h += '</div>';

                    h += '<div class="tau-pd-stats">' +
                        '<div class="tau-pd-stat tau-stat-card-1">' +
                            '<div class="tau-pd-stat-badge">' + getStatIcon(d.stat_1_lbl, 0) + '</div>' +
                            '<div class="tau-pd-stat-number" data-n="' + nv(d.stat_1_val) + '">0</div>' +
                            '<div class="tau-pd-stat-label">' + (d.stat_1_lbl || "Cursos") + '</div>' +
                        '</div>' +
                        '<div class="tau-pd-stat tau-stat-card-2">' +
                            '<div class="tau-pd-stat-badge">' + getStatIcon(d.stat_2_lbl, 1) + '</div>' +
                            '<div class="tau-pd-stat-number" data-n="' + nv(d.stat_2_val) + '">0</div>' +
                            '<div class="tau-pd-stat-label">' + (d.stat_2_lbl || "Completados") + '</div>' +
                        '</div>' +
                        '<div class="tau-pd-stat tau-stat-card-3">' +
                            '<div class="tau-pd-stat-badge">' + getStatIcon(d.stat_3_lbl, 2) + '</div>' +
                            '<div class="tau-pd-stat-number" data-n="' + nv(d.stat_3_val) + '">0</div>' +
                            '<div class="tau-pd-stat-label">' + (d.stat_3_lbl || "Actividades") + '</div>' +
                        '</div>' +
                        '<div class="tau-pd-stat tau-stat-card-4">' +
                            '<div class="tau-pd-stat-badge">' + getStatIcon(d.stat_4_lbl, 3) + '</div>' +
                            '<div class="tau-pd-stat-number" data-n="' + nv(d.stat_4_val) + '">0</div>' +
                            '<div class="tau-pd-stat-label">' + (d.stat_4_lbl || "Pendientes") + '</div>' +
                        '</div>' +
                    '</div>';

                    panel.innerHTML = h;

                    var mainRegion = document.querySelector("#region-main") || document.querySelector('[role="main"]');
                    if (mainRegion) {
                        var skipIds = ["region-main-settings-menu", "tertiary-navigation"];
                        var anchor = null;
                        for (var ci = 0; ci < mainRegion.children.length; ci++) {
                            var ch = mainRegion.children[ci];
                            if (!skipIds.some(function(s) { return ch.id === s || ch.classList.contains(s); })) {
                                anchor = ch;
                                break;
                            }
                        }
                        mainRegion.insertBefore(panel, anchor || mainRegion.firstElementChild);

                        // Hide native welcome greetings
                        if (d.firstname) {
                            window.tauUserFirstname = d.firstname;
                            tauHideWelcomeGreetings(d.firstname);
                        }

                        // Anim progress and stats counting
                        setTimeout(function() {
                            var fill = document.getElementById("tau-pd-fill");
                            if (fill) fill.style.width = progress + "%";

                            panel.querySelectorAll(".tau-pd-stat-number").forEach(function(nm) {
                                var target = parseInt(nm.dataset.n) || 0;
                                if (!target) {
                                    nm.textContent = "0";
                                    return;
                                }
                                var t0 = performance.now();
                                (function tick(now) {
                                    var frac = Math.min((now - t0) / 900, 1);
                                    var ease = 1 - Math.pow(1 - frac, 3);
                                    nm.textContent = Math.round(ease * target);
                                    if (frac < 1) requestAnimationFrame(tick);
                                })(performance.now());
                            });
                        }, 150);
                    }
                }).catch(function() {});
        }

        // Initialize general card enhancements
        try {
            tauMakeCardsClickable();
            tauEnhanceCards();
            tauHideConectiMe();
            if (window.tauUserFirstname) {
                tauHideWelcomeGreetings(window.tauUserFirstname);
            }
        } catch (e) {}

        var tauCardObs = new MutationObserver(function() {
            try {
                tauMakeCardsClickable();
                tauEnhanceCards();
                tauHideConectiMe();
                if (window.tauUserFirstname) {
                    tauHideWelcomeGreetings(window.tauUserFirstname);
                }
            } catch (e) {}
        });
        tauCardObs.observe(document.body, { childList: true, subtree: true });
    }

    function tauHideWelcomeGreetings(firstname) {
        if (!firstname) return;
        var greetings = document.querySelectorAll("h1, h2, h3, h4, h5, h6, .h2, span.h2, .welcome-message, [class*='welcome'], [class*='greeting'], #page-header h1, #page-header h2, .page-header h1, .page-header h2");
        greetings.forEach(function(c) {
            if (c.closest("#tau-personal-dashboard")) return;
            var txt = c.textContent || "";
            if (txt.toLowerCase().includes("hola") && txt.toLowerCase().includes(firstname.toLowerCase())) {
                c.style.setProperty("display", "none", "important");
                // Also hide parent wrapper div if it's just wrapping the greeting
                var parent = c.parentElement;
                if (parent && parent.classList.contains("mb-2") && parent.children.length === 1) {
                    parent.style.setProperty("display", "none", "important");
                }
            }
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initDashboard);
    } else {
        initDashboard();
    }
})();
