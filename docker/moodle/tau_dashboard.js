/* E-TAU Campus Virtual - Dashboard Script */
(function() {
    "use strict";

    var debugLogs = [];
    function logDebug(msg) {
        console.log("[TAU_DASHBOARD] " + msg);
        debugLogs.push(msg);
        var div = document.getElementById("tau-debug-console");
        if (div) {
            div.innerHTML = debugLogs.join("<br>");
            div.scrollTop = div.scrollHeight;
        }
    }

    logDebug("=== TAU Dashboard Script Loaded ===");

    var ICO_ENROLLED   = '<svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>';
    var ICO_COMPLETED  = '<svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
    var ICO_DONE       = '<svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
    var ICO_PENDING    = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
    var ICO_INSTRUCTOR = '<svg viewBox="0 0 24 24"><path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Z"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>';

    var TAU_CARD_ICONS = [ICO_ENROLLED, ICO_COMPLETED, ICO_DONE, ICO_PENDING];

    function createDebugUI() {
        if (window.location.search.indexOf("taudebug=1") === -1) return;
        if (document.getElementById("tau-debug-console")) return;
        var div = document.createElement("div");
        div.id = "tau-debug-console";
        div.style.cssText = "position:fixed;bottom:10px;right:10px;width:450px;height:280px;background:rgba(0,0,0,0.9);color:#0f0;font-family:monospace;font-size:11px;padding:10px;overflow-y:auto;z-index:999999;border:2px solid #0f0;border-radius:5px;box-shadow:0 0 15px rgba(0,255,0,0.4);pointer-events:none;line-height:1.4;";
        div.innerHTML = debugLogs.join("<br>");
        document.body.appendChild(div);
    }

    function tauMakeCardsClickable() {
        document.querySelectorAll(".card.dashboard-card, .coursebox, .course-card, .card[data-course-id]").forEach(function(card) {
            if (card.dataset.tauClick) return;
            var link = card.querySelector("a.aalink[href*='/course/view.php'], a[href*='/course/view.php'], h3 a, h4 a, .coursename a");
            if (!link) return;
            card.dataset.tauClick = "1";
            card.style.cursor = "pointer";
            card.addEventListener("click", function(e) {
                if (e.target.closest("a, button, [role=button], .dropdown, .action-menu")) return;
                window.location.href = link.href;
            });
        });
    }

    function tauEnhanceCards() {
        var cards = document.querySelectorAll(".card.dashboard-card, .card.course-card");
        if (cards.length > 0) {
            logDebug("tauEnhanceCards(): Analyzing " + cards.length + " cards");
        }
        
        cards.forEach(function(card, index) {
            var link = card.querySelector("a[href*='/course/view.php']");
            var cid = 0;
            if (link) {
                var m = link.href.match(/[?&]id=(\d+)/);
                if (m) cid = parseInt(m[1]);
            }

            // Skip skeleton loading cards (no course ID found yet)
            if (!cid) {
                return;
            }

            // 1. Image Header Enhancement
            if (!card.dataset.tauImgEnh) {
                var imgDiv = card.querySelector(".card-img-top, .dashboard-card-img, .card-img");
                var imgEl = card.querySelector(".card-img-top img, img.card-img-top, .card-img img");
                var realImg = false;
                
                var bgImg = imgDiv ? imgDiv.style.backgroundImage : "";
                var elSrc = imgEl ? imgEl.src : "";

                if (imgEl && imgEl.src && imgEl.src.indexOf("pluginfile.php") !== -1 && imgEl.src.indexOf("/overviewfiles/") !== -1) {
                    realImg = true;
                } else if (imgDiv && imgDiv.style.backgroundImage && imgDiv.style.backgroundImage.indexOf("pluginfile.php") !== -1 && imgDiv.style.backgroundImage.indexOf("/overviewfiles/") !== -1) {
                    realImg = true;
                }

                if (!realImg) {
                    var gh = document.createElement("div");
                    gh.className = "tau-gradient-header";
                    gh.style.background = "#0c395a";
                    // Add ?v=timestamp query parameter to bypass aggressive browser caching of the new logo image!
                    gh.innerHTML = '<img class="tau-card-logo" src="/theme/tau_branding/assets/official/cesmag-tau-card-logo.png?v=' + Date.now() + '" alt="CESMAG TAU">';
                    
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
                card.dataset.tauImgEnh = "1";
                logDebug("Enhanced Image Header for card " + index + " (CID: " + cid + ", realImg: " + realImg + ")");
            }

            // 1b. Add Program Title inside the Header if available
            var ghEl = card.querySelector(".tau-gradient-header");
            if (ghEl && window.tauCourseCategories && window.tauCourseCategories[cid]) {
                var program = window.tauCourseCategories[cid].program;
                if (program && !ghEl.querySelector(".tau-card-program-title")) {
                    var prgEl = document.createElement("div");
                    prgEl.className = "tau-card-program-title";
                    prgEl.textContent = program;
                    ghEl.appendChild(prgEl);
                    logDebug("Added program title overlay for card " + index + " (CID: " + cid + ", program: " + program + ")");
                }
            }

            // 2. Category Badge Enhancement
            var body = card.querySelector(".card-body");
            if (body) {
                var catEl = card.querySelector(".coursecat, .course-category, [data-region='coursecategory'], .categoryname");
                var catTxt = catEl ? catEl.textContent.trim() : null;
                
                var semesterText = catTxt;
                if (window.tauCourseCategories && window.tauCourseCategories[cid]) {
                    semesterText = window.tauCourseCategories[cid].semester || catTxt;
                }

                if (semesterText) {
                    var badge = body.querySelector(".tau-cat-badge");
                    if (!badge) {
                        badge = document.createElement("div");
                        badge.className = "tau-cat-badge";
                        body.insertBefore(badge, body.firstChild);
                    }
                    if (badge.textContent !== semesterText) {
                        badge.textContent = semesterText;
                        logDebug("Enhanced Category Badge for card " + index + " (CID: " + cid + ", badge: " + semesterText + ")");
                    }
                }
            }

            // 3. Teacher Section Enhancement (Requires window.tauCourseTeachers)
            if (body && !card.dataset.tauTchEnh && window.tauCourseTeachers && window.tauCourseTeachers[cid]) {
                // Hide Moove theme's native contacts block to prevent duplicates or un-styled boxes
                var nativeContacts = card.parentNode.querySelector(".course-contacts") || card.querySelector(".course-contacts");
                if (nativeContacts) {
                    nativeContacts.style.setProperty("display", "none", "important");
                }

                var tchs = window.tauCourseTeachers[cid];
                if (tchs && tchs.length) {
                    var teacher = tchs[0];
                    var name = typeof teacher === 'object' ? teacher.name : teacher;
                    var avatar = typeof teacher === 'object' ? teacher.avatar : null;
                    
                    var avatarHtml = "";
                    // Filter out default silhouette placeholders returned by Moodle (/u/f1, /u/f2, f1.png, f2.png, default_course)
                    var isDefaultAvatar = !avatar || 
                                          avatar.indexOf("default_course") !== -1 || 
                                          avatar.indexOf("f1.png") !== -1 || 
                                          avatar.indexOf("f2.png") !== -1 ||
                                          avatar.indexOf("/u/f1") !== -1 || 
                                          avatar.indexOf("/u/f2") !== -1;

                    if (!isDefaultAvatar) {
                        avatarHtml = '<img src="' + avatar + '" alt="' + name + '" class="tau-teacher-avatar-img">';
                    } else {
                        avatarHtml = '<div class="tau-teacher-avatar-placeholder">' + ICO_INSTRUCTOR + '</div>';
                    }

                    // Remove old custom teacher sections
                    var oldCustom = body.querySelector(".tau-card-teacher-section");
                    if (oldCustom) {
                        oldCustom.parentNode.removeChild(oldCustom);
                    }

                    var ts = document.createElement("div");
                    ts.className = "tau-card-teacher-section";
                    ts.innerHTML = 
                        '<div class="tau-teacher-avatar-wrapper">' + avatarHtml + '</div>' +
                        '<div class="tau-teacher-info">' +
                            '<div class="tau-teacher-label">Docente</div>' +
                            '<div class="tau-teacher-name" title="' + name + '">' + name + '</div>' +
                        '</div>';
                    
                    body.appendChild(ts);
                    card.dataset.tauTchEnh = "1";
                }
            }
            
            
            // 4. 3D Book Layout Wrapper
            if (!card.querySelector(".tau-book-cover")) {
                var pages = document.createElement("div");
                pages.className = "tau-book-pages";
                pages.innerHTML = 
                    '<div class="tau-book-pages-content">' +
                        '<div class="tau-book-pages-text">Explorar módulos y contenidos</div>' +
                        '<div class="tau-book-pages-btn">Ingresar al Curso</div>' +
                    '</div>';
                
                var menu = card.querySelector(".dropdown, .action-menu");
                var cover = document.createElement("div");
                cover.className = "tau-book-cover";
                
                while (card.firstChild) {
                    var child = card.firstChild;
                    if (child !== menu) {
                        cover.appendChild(child);
                    } else {
                        card.removeChild(child);
                    }
                }
                
                card.appendChild(pages);
                card.appendChild(cover);
                if (menu) {
                    card.appendChild(menu);
                }
            }
            
            // Apply class to mark the card as ready and trigger our custom styling
            card.classList.add("tau-card-ready");
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
        logDebug("=== initDashboard() Executed ===");
        var isDashboardPage = (document.body.id && document.body.id.indexOf("page-my-") === 0) || window.location.pathname.indexOf("/my/") !== -1;
        logDebug("isDashboardPage: " + isDashboardPage + " | bodyID: " + document.body.id + " | path: " + window.location.pathname);

        createDebugUI();

        // Dynamic stats widgets
        if (isDashboardPage && !document.getElementById("tau-personal-dashboard")) {
            logDebug("Fetching user stats from /local/tau_course_creator_ai/user_stats.php");
            fetch("/local/tau_course_creator_ai/user_stats.php?_=" + Date.now())
                .then(function(r) { return r.ok ? r.json() : null; })
                .then(function(d) {
                    if (!d) {
                        logDebug("User stats request returned null or failed");
                        return;
                    }
                    logDebug("User stats received successfully for " + d.firstname);

                    if (d.course_categories) {
                        window.tauCourseCategories = d.course_categories;
                        logDebug("Registered categories database in window.tauCourseCategories");
                    }

                    if (d.course_teachers) {
                        window.tauCourseTeachers = d.course_teachers;
                        logDebug("Registered teachers database in window.tauCourseTeachers");
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
                    var subtitle = "Tu progreso académico en E-TAU Campus Virtual";

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
                        logDebug("Injected tau-personal-dashboard widget into #region-main");

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
                }).catch(function(err) {
                    logDebug("User stats API request failed: " + err);
                });
        }

        // Initialize general card enhancements
        try {
            logDebug("Initializing card enhancements");
            tauMakeCardsClickable();
            tauEnhanceCards();
            tauCreateSemesterTabs();
            tauHideConectiMe();
            if (window.tauUserFirstname) {
                tauHideWelcomeGreetings(window.tauUserFirstname);
            }
        } catch (e) {
            logDebug("TAU INIT ERROR: " + e.message);
        }

        var tauCardObs = new MutationObserver(function() {
            try {
                // Fail-safe: disconnect observer during DOM updates to prevent recursive infinite loops
                tauCardObs.disconnect();
                
                tauMakeCardsClickable();
                tauEnhanceCards();
                tauCreateSemesterTabs();
                tauHideConectiMe();
                if (window.tauUserFirstname) {
                    tauHideWelcomeGreetings(window.tauUserFirstname);
                }
            } catch (e) {
                logDebug("TAU OBSERVER ERROR: " + e.message);
            } finally {
                // Reconnect observer to detect subsequent Moodle changes
                tauCardObs.observe(document.body, { childList: true, subtree: true });
            }
        });
        tauCardObs.observe(document.body, { childList: true, subtree: true });
        logDebug("MutationObserver active on document.body");
    }

    var activeSemesterTab = "all";

    function tauCreateSemesterTabs() {
        var isDashboardPage = (document.body.id && document.body.id.indexOf("page-my-") === 0) || window.location.pathname.indexOf("/my/") !== -1;
        if (!isDashboardPage) return;

        var overviewRegion = document.querySelector('[data-region="course-overview"]') || 
                             document.querySelector('.block-myoverview') ||
                             document.querySelector('#region-main');
        if (!overviewRegion) return;

        var tabsContainer = document.getElementById("tau-semester-tabs");
        if (!tabsContainer) {
            tabsContainer = document.createElement("div");
            tabsContainer.id = "tau-semester-tabs";
            var target = overviewRegion.querySelector('[data-region="filter-bar"]') || 
                         overviewRegion.querySelector('.card-deck') ||
                         overviewRegion.querySelector('.card-grid') ||
                         overviewRegion.querySelector('.courses-view-course-item') ||
                         overviewRegion.querySelector('.courses-view');
            if (target) {
                target.parentNode.insertBefore(tabsContainer, target);
                logDebug("Created tau-semester-tabs container before " + target.className);
            } else {
                overviewRegion.insertBefore(tabsContainer, overviewRegion.firstChild);
                logDebug("Created tau-semester-tabs container as first child of overviewRegion");
            }
        }

        var cards = document.querySelectorAll(".card.dashboard-card, .card.course-card");
        var realCards = [];
        cards.forEach(function(c) {
            var link = c.querySelector("a[href*='/course/view.php']");
            if (link) {
                var m = link.href.match(/[?&]id=(\d+)/);
                if (m && parseInt(m[1]) > 0) {
                    realCards.push(c);
                }
            }
        });

        if (!realCards.length) {
            tabsContainer.style.display = "none";
            return;
        }
        tabsContainer.style.display = "flex";

        var semesters = new Set();
        realCards.forEach(function(card) {
            var badge = card.querySelector(".tau-cat-badge");
            if (badge) {
                var txt = badge.textContent.trim().toUpperCase();
                if (txt.indexOf("SEMESTRE") !== -1 || txt.indexOf("SEMESTER") !== -1) {
                    var match = txt.match(/(SEMESTRE\s*\d+|SEMESTER\s*\d+)/i);
                    if (match) {
                        semesters.add(match[1].toUpperCase());
                    } else {
                        semesters.add(txt);
                    }
                } else {
                    semesters.add("OTROS");
                }
            } else {
                semesters.add("OTROS");
            }
        });

        var semestersArr = Array.from(semesters).sort(function(a, b) {
            if (a === "OTROS") return 1;
            if (b === "OTROS") return -1;
            return a.localeCompare(b, undefined, {numeric: true, sensitivity: 'base'});
        });

        if (semestersArr.length <= 1) {
            tabsContainer.style.display = "none";
            return;
        }

        var html = '<button class="tau-tab-btn' + (activeSemesterTab === "all" ? ' active' : '') + '" data-sem="all">Todos</button>';
        semestersArr.forEach(function(sem) {
            var label = sem;
            if (sem.startsWith("SEMESTRE")) {
                var num = sem.replace(/\D/g, "");
                label = "Semestre " + num;
            } else if (sem === "OTROS") {
                label = "Otros";
            }
            html += '<button class="tau-tab-btn' + (activeSemesterTab === sem ? ' active' : '') + '" data-sem="' + sem + '">' + label + '</button>';
        });

        if (tabsContainer.dataset.renderedSemesters !== semestersArr.join(',')) {
            tabsContainer.innerHTML = html;
            tabsContainer.dataset.renderedSemesters = semestersArr.join(',');
            logDebug("Rendered " + semestersArr.length + " semester tabs: " + semestersArr.join(', '));

            tabsContainer.querySelectorAll(".tau-tab-btn").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    tabsContainer.querySelectorAll(".tau-tab-btn").forEach(function(b) { b.classList.remove("active"); });
                    btn.classList.add("active");
                    activeSemesterTab = btn.dataset.sem;
                    tauApplySemesterFilter();
                });
            });
        }

        tauApplySemesterFilter();
    }

    function tauApplySemesterFilter() {
        var cards = document.querySelectorAll(".card.dashboard-card, .card.course-card");
        cards.forEach(function(card) {
            var link = card.querySelector("a[href*='/course/view.php']");
            var cid = 0;
            if (link) {
                var m = link.href.match(/[?&]id=(\d+)/);
                if (m) cid = parseInt(m[1]);
            }
            if (!cid) return; // ignore skeleton cards

            if (activeSemesterTab === "all") {
                card.style.display = "flex";
                card.style.opacity = "1";
                return;
            }

            var badge = card.querySelector(".tau-cat-badge");
            var cardSem = "OTROS";
            if (badge) {
                var txt = badge.textContent.trim().toUpperCase();
                var match = txt.match(/(SEMESTRE\s*\d+|SEMESTER\s*\d+)/i);
                if (match) {
                    cardSem = match[1].toUpperCase();
                } else if (txt.indexOf("SEMESTRE") === -1 && txt.indexOf("SEMESTER") === -1) {
                    cardSem = "OTROS";
                } else {
                    cardSem = txt;
                }
            }

            if (cardSem === activeSemesterTab) {
                card.style.display = "flex";
                card.style.opacity = "1";
            } else {
                card.style.display = "none";
                card.style.opacity = "0";
            }
        });
    }

    function tauHideWelcomeGreetings(firstname) {
        if (!firstname) return;
        var greetings = document.querySelectorAll("h1, h2, h3, h4, h5, h6, .h2, span.h2, .welcome-message, [class*='welcome'], [class*='greeting'], #page-header h1, #page-header h2, .page-header h1, .page-header h2");
        greetings.forEach(function(c) {
            if (c.closest("#tau-personal-dashboard")) return;
            var txt = c.textContent || "";
            if (txt.toLowerCase().includes("hola") && txt.toLowerCase().includes(firstname.toLowerCase())) {
                c.style.setProperty("display", "none", "important");
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
