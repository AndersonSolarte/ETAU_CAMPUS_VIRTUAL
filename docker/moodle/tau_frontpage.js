/* TAU Campus Virtual - Frontpage Script */
(function() {
    "use strict";

    function escapeHtml(value) {
        return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function stripHtml(value) {
        return String(value || "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
    }

    function getInstitutionalLoginUrl() {
        var sesskey = window.M && M.cfg && M.cfg.sesskey ? M.cfg.sesskey : "";
        if (sesskey) {
            return "/auth/oauth2/login.php?id=1&sesskey=" + encodeURIComponent(sesskey);
        }
        return "/login/index.php";
    }

    function getProgramIcon(name) {
        var n = String(name || "").toLowerCase();
        var icons = {
            briefcase: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="7" width="18" height="13" rx="2"></rect><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><path d="M3 12h18"></path></svg>',
            cpu: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="7" y="7" width="10" height="10" rx="2"></rect><path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3"></path></svg>',
            heart: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 21s-7-4.4-9.5-9A5.6 5.6 0 0 1 12 5.7 5.6 5.6 0 0 1 21.5 12C19 16.6 12 21 12 21z"></path></svg>',
            scale: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 3v18"></path><path d="M7 21h10"></path><path d="M4 7h16"></path><path d="M7 7 4 12h6L7 7zm10 0-3 5h6l-3-5z"></path></svg>',
            book: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 0 4 22z"></path><path d="M4 5.5v16"></path></svg>',
            globe: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"></path></svg>'
        };

        if (n.indexOf("ingen") !== -1 || n.indexOf("sistemas") !== -1 || n.indexOf("datos") !== -1 || n.indexOf("tic") !== -1) return icons.cpu;
        if (n.indexOf("salud") !== -1 || n.indexOf("med") !== -1 || n.indexOf("enfer") !== -1) return icons.heart;
        if (n.indexOf("dere") !== -1 || n.indexOf("jur") !== -1) return icons.scale;
        if (n.indexOf("edu") !== -1 || n.indexOf("huma") !== -1 || n.indexOf("social") !== -1) return icons.book;
        if (n.indexOf("idioma") !== -1 || n.indexOf("inter") !== -1) return icons.globe;
        return icons.briefcase;
    }

    function getSupportCategoryIcon(name) {
        var n = String(name || "").toLowerCase();
        var award = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>';
        var laptop = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>';
        var activity = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>';
        var users = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
        var cpu = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line></svg>';
        var briefcase = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>';
        var compass = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>';
        var bookOpen = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7"></path></svg>';
        var graduation = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>';

        if (n.indexOf("virtual") !== -1 || n.indexOf("tecnolog") !== -1 || n.indexOf("sistemas") !== -1 || n.indexOf("computac") !== -1) return laptop;
        if (n.indexOf("salud") !== -1 || n.indexOf("medicin") !== -1 || n.indexOf("enfermer") !== -1 || n.indexOf("deport") !== -1) return activity;
        if (n.indexOf("humana") !== -1 || n.indexOf("social") !== -1 || n.indexOf("psicolog") !== -1 || n.indexOf("educac") !== -1) return users;
        if (n.indexOf("ingenier") !== -1 || n.indexOf("exacta") !== -1 || n.indexOf("ciencia") !== -1 || n.indexOf("fisic") !== -1 || n.indexOf("matemat") !== -1) return cpu;
        if (n.indexOf("empresa") !== -1 || n.indexOf("administr") !== -1 || n.indexOf("econom") !== -1 || n.indexOf("negocio") !== -1 || n.indexOf("contad") !== -1) return briefcase;
        if (n.indexOf("apoyo") !== -1 || n.indexOf("tutor") !== -1 || n.indexOf("induc") !== -1 || n.indexOf("orientac") !== -1) return award;
        if (n.indexOf("idioma") !== -1 || n.indexOf("ingles") !== -1 || n.indexOf("lengua") !== -1) return compass;
        if (n.indexOf("lectura") !== -1 || n.indexOf("escritura") !== -1 || n.indexOf("bibliotec") !== -1) return bookOpen;
        return graduation;
    }

        function renderBannerOverlay() {
        var banner = document.querySelector("#mooveslideshow .carousel-inner");
        if (!banner || document.querySelector(".tau-banner-overlay")) {
            return;
        }

        var sliderImgs = document.querySelectorAll("#mooveslideshow .carousel-item img");
        sliderImgs.forEach(function(img) {
            img.style.setProperty("display", "none", "important");
            img.style.setProperty("opacity", "0", "important");
        });

        // Hide any native captions inline to prevent double text rendering / overlapping
        var sliderCaptions = document.querySelectorAll("#mooveslideshow .carousel-caption");
        sliderCaptions.forEach(function(caption) {
            caption.style.setProperty("display", "none", "important");
            caption.style.setProperty("visibility", "hidden", "important");
            caption.style.setProperty("opacity", "0", "important");
        });

        banner.insertAdjacentHTML("beforeend",
            '<div class="tau-banner-overlay">' +
                '<div class="tau-banner-card">' +
                    '<span class="tau-banner-pretitle">Universidad CESMAG</span>' +
                    '<h1 class="tau-banner-title">TAU <span class="tau-accent-text">Campus Virtual</span></h1>' +
                    '<span class="tau-banner-subtitle">UNICESMAG</span>' +
                    '<p class="tau-banner-desc">Tu plataforma de educacion y aprendizaje en linea para conectar tu talento con el futuro profesional.</p>' +
                    '<a href="#apoyo-academico" class="btn-tau-banner-explore">Explorar Cursos</a>' +
                '</div>' +
                '<div class="tau-banner-deco tau-logo-deco">' +
                    '<img src="/theme/tau_branding/assets/official/tau-official-icon.png" class="tau-deco-logo" alt="TAU">' +
                    '<div class="tau-ins-wrap">' +
                        '<span class="tau-ins-line"></span>' +
                        '<p class="tau-ins-text">Hombres nuevos<br>para tiempos nuevos</p>' +
                        '<cite class="tau-ins-attr">Fray Guillermo de Castellana, OFMCap.</cite>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );


        var checkGsapLoader = setInterval(function() {
            if (!window.gsap) {
                return;
            }
            clearInterval(checkGsapLoader);
            gsap.from(document.querySelector(".tau-banner-card"), {
                opacity: 0,
                x: -50,
                duration: 1.2,
                ease: "power3.out"
            });
            gsap.from(document.querySelector(".tau-logo-deco"), {
                opacity: 0,
                x: 50,
                duration: 1.2,
                ease: "power3.out"
            });
        }, 100);
    }
    function renderPrivatePrograms() {
        var featureSection = document.getElementById("feature");
        if (!featureSection) {
            return;
        }

        fetch("/local/tau_course_creator_ai/private_programs.php?_=" + Date.now())
            .then(function(res) { return res.ok ? res.json() : []; })
            .then(function(programs) {
                if (!Array.isArray(programs) || programs.length === 0) {
                    return;
                }

                var oauthLoginUrl = getInstitutionalLoginUrl();
                var host = document.createElement("section");
                host.className = "tau-programs-shell";
                host.innerHTML =
                    '<div class="tau-programs-surface">' +
                        '<div class="tau-programs-hero">' +
                            '<div class="tau-programs-hero__content">' +
                                '<div class="tau-programs-hero__main">' +
                                    '<span class="tau-programs-kicker">Socios Academicos Activos</span>' +
                                    '<h2 class="tau-programs-title">Cursos del Campus Virtual</h2>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="tau-programs-searchpanel tau-programs-searchpanel--full">' +
                            '<div class="tau-programs-searchhead">' +
                                '<div>' +
                                    '<label class="tau-programs-searchlabel" for="tauProgramsSearch">Filtro inteligente</label>' +
                                    '<p class="tau-programs-searchhint">Busca por socio academico, semestre o curso.</p>' +
                                '</div>' +
                            '</div>' +
                            '<div class="tau-programs-searchbox">' +
                                '<span class="tau-programs-searchicon">' +
                                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>' +
                                '</span>' +
                                '<input id="tauProgramsSearch" class="tau-programs-searchinput" type="search" placeholder="Ejemplo: Big Data, Semestre 1, investigacion" autocomplete="off">' +
                            '</div>' +
                        '</div>' +
                        '<div class="tau-programs-toolbar">' +
                            '<div class="tau-programs-toolbar__title">Socios academicos</div>' +
                            '<div class="tau-programs-toolbar__meta" id="tauProgramsToolbarMeta"></div>' +
                        '</div>' +
                        '<div class="tau-programs-tabs" id="tauProgramsRoot"></div>' +
                        '<div class="tau-programs-stage" id="tauProgramsPanel">' +
                            '<div class="tau-programs-emptyhero" id="tauProgramsEmpty">' +
                                '<strong>Selecciona un socio academico</strong>' +
                                '<span>O usa el filtro inteligente.</span>' +
                            '</div>' +
                            '<div class="tau-programs-searchresults" id="tauProgramsSearchResults" hidden></div>' +
                            '<div class="tau-programs-flow" id="tauProgramsFlow" hidden>' +
                                '<div class="tau-programs-level" id="tauProgramsSecond"></div>' +
                                '<div class="tau-programs-level" id="tauProgramsThird"></div>' +
                                '<div class="tau-programs-preview" id="tauProgramsPreview"></div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';

                featureSection.innerHTML = "";
                featureSection.appendChild(host);

                var rootGrid = host.querySelector("#tauProgramsRoot");
                var panel = host.querySelector("#tauProgramsPanel");
                var emptyState = host.querySelector("#tauProgramsEmpty");
                var searchInput = host.querySelector("#tauProgramsSearch");
                var searchResults = host.querySelector("#tauProgramsSearchResults");
                var toolbarMeta = host.querySelector("#tauProgramsToolbarMeta");
                var flow = host.querySelector("#tauProgramsFlow");
                var secondLevel = host.querySelector("#tauProgramsSecond");
                var thirdLevel = host.querySelector("#tauProgramsThird");
                var preview = host.querySelector("#tauProgramsPreview");
                var selectedRootName = "";
                var allSearchEntries = [];

                function collectSearchEntries() {
                    var entries = [];
                    programs.forEach(function(rootItem) {
                        entries.push({
                            type: "category",
                            label: rootItem.name,
                            meta: (rootItem.totalcourses || 0) + " cursos",
                            keywords: [rootItem.name],
                            root: rootItem,
                            second: null,
                            third: null,
                            course: null
                        });

                        (rootItem.subcategories || []).forEach(function(secondItem) {
                            entries.push({
                                type: "subcategory",
                                label: secondItem.name,
                                meta: rootItem.name,
                                keywords: [rootItem.name, secondItem.name],
                                root: rootItem,
                                second: secondItem,
                                third: null,
                                course: null
                            });

                            var thirds = secondItem.subcategories || [];
                            if (thirds.length) {
                                thirds.forEach(function(thirdItem) {
                                    entries.push({
                                        type: "route",
                                        label: thirdItem.name,
                                        meta: rootItem.name + " / " + secondItem.name,
                                        keywords: [rootItem.name, secondItem.name, thirdItem.name],
                                        root: rootItem,
                                        second: secondItem,
                                        third: thirdItem,
                                        course: null
                                    });

                                    (thirdItem.courses || []).forEach(function(course) {
                                        entries.push({
                                            type: "course",
                                            label: course.fullname,
                                            meta: rootItem.name + " / " + secondItem.name + " / " + thirdItem.name,
                                            keywords: [rootItem.name, secondItem.name, thirdItem.name, course.fullname, stripHtml(course.summary || "")],
                                            root: rootItem,
                                            second: secondItem,
                                            third: thirdItem,
                                            course: course
                                        });
                                    });
                                });
                            } else {
                                (secondItem.courses || []).forEach(function(course) {
                                    entries.push({
                                        type: "course",
                                        label: course.fullname,
                                        meta: rootItem.name + " / " + secondItem.name,
                                        keywords: [rootItem.name, secondItem.name, course.fullname, stripHtml(course.summary || "")],
                                        root: rootItem,
                                        second: secondItem,
                                        third: null,
                                        course: course
                                    });
                                });
                            }
                        });
                    });
                    return entries;
                }

                function updateToolbarMeta(text) {
                    toolbarMeta.textContent = text || (programs.length + " socios academicos activos");
                }

                function renderRootCards() {
                    rootGrid.innerHTML = "";
                    programs.forEach(function(item) {
                        var card = document.createElement("button");
                        card.type = "button";
                        card.className = "tau-program-card";
                        card.innerHTML =
                            '<span class="tau-program-card__icon">' + getProgramIcon(item.name) + '</span>' +
                            '<span class="tau-program-card__body">' +
                                '<strong>' + escapeHtml(item.name) + '</strong>' +
                                '<small>' + (item.totalcourses || 0) + ' cursos</small>' +
                            '</span>';
                        card.addEventListener("click", function() {
                            rootGrid.querySelectorAll(".tau-program-card").forEach(function(node) {
                                node.classList.remove("is-active");
                            });
                            card.classList.add("is-active");
                            renderSecondLevel(item);
                        });
                        rootGrid.appendChild(card);
                    });
                    updateToolbarMeta();
                }

                function openFromSearch(entry) {
                    if (!entry || !entry.root) {
                        return;
                    }

                    if (searchInput) {
                        searchInput.value = "";
                    }

                    searchResults.hidden = true;
                    searchResults.innerHTML = "";
                    flow.hidden = false;
                    emptyState.hidden = true;

                    Array.prototype.forEach.call(rootGrid.querySelectorAll(".tau-program-card"), function(node) {
                        node.classList.toggle("is-active", node.textContent.indexOf(entry.root.name) !== -1);
                    });

                    renderSecondLevel(entry.root);

                    if (entry.second) {
                        var secondButtons = secondLevel.querySelectorAll(".tau-program-leaf");
                        Array.prototype.forEach.call(secondButtons, function(node) {
                            if (node.textContent.indexOf(entry.second.name) !== -1) {
                                node.classList.add("is-active");
                            } else {
                                node.classList.remove("is-active");
                            }
                        });
                        renderThirdLevel(entry.root, entry.second);
                    }

                    if (entry.third) {
                        var thirdButtons = thirdLevel.querySelectorAll(".tau-program-final");
                        Array.prototype.forEach.call(thirdButtons, function(node) {
                            if (node.textContent.indexOf(entry.third.name) !== -1) {
                                node.classList.add("is-active");
                            } else {
                                node.classList.remove("is-active");
                            }
                        });
                        renderProgramPreview(entry.third, [entry.root.name, entry.second.name, entry.third.name], "third");
                    } else if (entry.second && entry.course) {
                        renderProgramPreview(entry.second, [entry.root.name, entry.second.name], "second");
                    } else if (entry.root && !entry.second) {
                        renderProgramPreview(entry.root, [entry.root.name], "direct");
                    }
                }

                function renderSearchResults(query) {
                    var normalized = String(query || "").toLowerCase().trim();
                    if (!normalized) {
                        searchResults.hidden = true;
                        searchResults.innerHTML = "";
                        flow.hidden = true;
                        emptyState.hidden = false;
                        updateToolbarMeta();
                        return;
                    }

                    var matches = allSearchEntries.filter(function(entry) {
                        return entry.keywords.join(" ").toLowerCase().indexOf(normalized) !== -1;
                    }).slice(0, 18);

                    emptyState.hidden = true;
                    flow.hidden = true;
                    searchResults.hidden = false;
                    updateToolbarMeta(matches.length + " resultados para \"" + query + "\"");

                    if (!matches.length) {
                        searchResults.innerHTML =
                            '<div class="tau-program-empty">' +
                                'No encontramos coincidencias. Prueba con otro socio academico, semestre o palabra clave.' +
                            '</div>';
                        return;
                    }

                    var html = '<div class="tau-programs-resultshead">' +
                        '<h3>Resultados encontrados</h3>' +
                        '<p>Selecciona un resultado para abrir su ruta academica.</p>' +
                    '</div>' +
                    '<div class="tau-programs-resultsgrid">';

                    matches.forEach(function(entry) {
                        html +=
                            '<button type="button" class="tau-program-result" data-kind="' + escapeHtml(entry.type) + '" data-label="' + escapeHtml(entry.label) + '">' +
                                '<span class="tau-program-result__type">' + escapeHtml(entry.type === "course" ? "Curso" : entry.type === "route" ? "Ruta final" : entry.type === "subcategory" ? "Subnivel" : "Socio academico") + '</span>' +
                                '<strong>' + escapeHtml(entry.label) + '</strong>' +
                                '<small>' + escapeHtml(entry.meta || "") + '</small>' +
                            '</button>';
                    });

                    html += '</div>';
                    searchResults.innerHTML = html;

                    Array.prototype.forEach.call(searchResults.querySelectorAll(".tau-program-result"), function(button, index) {
                        button.addEventListener("click", function() {
                            openFromSearch(matches[index]);
                        });
                    });
                }

                function renderSecondLevel(rootItem) {
                    panel.hidden = false;
                    emptyState.hidden = true;
                    searchResults.hidden = true;
                    flow.hidden = false;
                    preview.innerHTML = "";
                    thirdLevel.innerHTML = "";
                    selectedRootName = rootItem.name;
                    updateToolbarMeta((rootItem.totalcourses || 0) + " cursos en " + rootItem.name);

                    var children = Array.isArray(rootItem.subcategories) ? rootItem.subcategories : [];
                    secondLevel.innerHTML =
                        '<div class="tau-programs-levelhead">' +
                            '<h3>' + escapeHtml(rootItem.name) + '</h3>' +
                            '<p>Subprogramas disponibles</p>' +
                        '</div>' +
                        '<div class="tau-programs-levelgrid" id="tauProgramsSecondGrid"></div>';

                    var secondGrid = secondLevel.querySelector("#tauProgramsSecondGrid");
                    if (children.length === 0) {
                        renderProgramPreview(rootItem, [rootItem.name], "direct");
                        return;
                    }

                    children.forEach(function(item) {
                        var button = document.createElement("button");
                        button.type = "button";
                        button.className = "tau-program-leaf";
                        button.innerHTML =
                            '<strong>' + escapeHtml(item.name) + '</strong>' +
                            '<small>' + (item.totalcourses || item.coursecount || 0) + ' cursos</small>';
                        button.addEventListener("click", function() {
                            secondGrid.querySelectorAll(".tau-program-leaf").forEach(function(node) {
                                node.classList.remove("is-active");
                            });
                            button.classList.add("is-active");
                            renderThirdLevel(rootItem, item);
                        });
                        secondGrid.appendChild(button);
                    });
                }

                function renderThirdLevel(rootItem, secondItem) {
                    var thirdItems = Array.isArray(secondItem.subcategories) ? secondItem.subcategories : [];
                    preview.innerHTML = "";
                    updateToolbarMeta((secondItem.totalcourses || secondItem.coursecount || 0) + " cursos en " + secondItem.name);

                    if (thirdItems.length === 0) {
                        thirdLevel.innerHTML = "";
                        renderProgramPreview(secondItem, [rootItem.name, secondItem.name], "second");
                        return;
                    }

                    thirdLevel.innerHTML =
                        '<div class="tau-programs-levelhead">' +
                            '<h3>' + escapeHtml(secondItem.name) + '</h3>' +
                            '<p>Rutas finales</p>' +
                        '</div>' +
                        '<div class="tau-programs-finalgrid" id="tauProgramsThirdGrid"></div>';

                    var thirdGrid = thirdLevel.querySelector("#tauProgramsThirdGrid");
                    thirdItems.forEach(function(item) {
                        var button = document.createElement("button");
                        button.type = "button";
                        button.className = "tau-program-final";
                        button.innerHTML =
                            '<span class="tau-program-final__title">' + escapeHtml(item.name) + '</span>' +
                            '<span class="tau-program-final__meta">' + (item.totalcourses || item.coursecount || 0) + ' cursos del nivel final</span>' +
                            '<span class="tau-program-final__cta">Ver detalle</span>';
                        button.addEventListener("click", function() {
                            thirdGrid.querySelectorAll(".tau-program-final").forEach(function(node) {
                                node.classList.remove("is-active");
                            });
                            button.classList.add("is-active");
                            renderProgramPreview(item, [rootItem.name, secondItem.name, item.name], "third");
                        });
                        thirdGrid.appendChild(button);
                    });
                }

                function renderProgramPreview(targetItem, trail, levelType) {
                    var courses = Array.isArray(targetItem.courses) ? targetItem.courses : [];
                    var title = trail[trail.length - 1] || "Ruta academica";
                    var html =
                        '<div class="tau-program-previewhead">' +
                            '<h3>' + escapeHtml(title) + '</h3>' +
                            '<p>' + (levelType === "third"
                                ? 'Ingresa para abrir los cursos de esta ruta.'
                                : 'Cursos disponibles en esta seleccion.') + '</p>' +
                        '</div>';

                    if (courses.length > 0) {
                        html += '<div class="tau-program-coursegrid">';
                        courses.forEach(function(course) {
                            html +=
                                '<article class="tau-program-coursecard">' +
                                    '<span class="tau-program-coursecard__bar"></span>' +
                                    '<h4>' + escapeHtml(course.fullname) + '</h4>' +
                                    '<p>' + escapeHtml(stripHtml(course.summary || "Curso disponible en el Campus Virtual.")) + '</p>' +
                                    (window.tauIsLoggedIn
                                        ? '<a class="tau-program-coursebtn" href="/course/view.php?id=' + course.id + '">Entrar al curso</a>'
                                        : '<a class="tau-program-coursebtn is-login" href="' + oauthLoginUrl + '">Ingresar con cuenta institucional</a>') +
                                '</article>';
                        });
                        html += '</div>';
                    } else {
                        html += '<div class="tau-program-empty">No hay cursos visibles en esta ruta por el momento.</div>';
                    }

                    preview.innerHTML = html;
                    preview.scrollIntoView({ behavior: "smooth", block: "nearest" });
                }

                allSearchEntries = collectSearchEntries();
                renderRootCards();
                if (searchInput) {
                    searchInput.addEventListener("input", function() {
                        renderSearchResults(searchInput.value);
                    });
                }
            })
            .catch(function(err) {
                console.error("Failed to load private programs hierarchy:", err);
            });
    }

    function renderSupportCoursesSection() {
        var categoriesContainer = document.getElementById("frontpage-category-names") ||
            document.getElementById("frontpage-category-combo") ||
            document.querySelector(".frontpage-category-names, .category_subcategories, .course-category-listing");

        if (categoriesContainer) {
            categoriesContainer.style.display = "none";
        }

        var nativeCourses = document.querySelector(".frontpage-course-list-all") || document.querySelector(".courses");
        if (nativeCourses) {
            nativeCourses.style.display = "none";
            var nativeHeading = nativeCourses.previousElementSibling;
            if (nativeHeading && (nativeHeading.tagName === "H2" || nativeHeading.tagName === "H3" || nativeHeading.classList.contains("frontpage-course-list-all-title"))) {
                nativeHeading.style.display = "none";
            }
        }

        fetch("/local/tau_course_creator_ai/public_courses.php?_=" + Date.now())
            .then(function(res) { return res.ok ? res.json() : []; })
            .then(function(data) {
                if (!Array.isArray(data) || data.length === 0) {
                    return;
                }

                var section = document.createElement("div");
                section.className = "tau-cat-section";

                var h = '<div class="tau-cat-header">' +
                    '<div class="tau-cat-kicker">' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>' +
                        'Formacion Complementaria' +
                    '</div>' +
                    '<h2 class="tau-cat-htitle">Cursos de Apoyo Academico</h2>' +
                    '<p class="tau-cat-sub">Explora nuestra oferta academica abierta organizada por areas de conocimiento para fortalecer tus competencias profesionales.</p>' +
                '</div>' +
                '<div class="tau-cat-body">' +
                    '<div class="tau-cat-grid">';

                var colors = [
                    ["#c62b3a", "rgba(198,43,58,.07)"],
                    ["#1e3a8a", "rgba(30,58,138,.07)"],
                    ["#0f766e", "rgba(15,118,110,.07)"],
                    ["#7c3aed", "rgba(124,58,237,.07)"],
                    ["#c2410c", "rgba(194,65,12,.07)"],
                    ["#be185d", "rgba(190,24,93,.07)"],
                    ["#047857", "rgba(4,120,87,.07)"]
                ];

                data.forEach(function(cat, idx) {
                    var col = colors[idx % colors.length];
                    h += '<div class="tau-cat-card" data-cat-id="' + cat.id + '" style="cursor:pointer;">' +
                        '<div class="tau-cat-ico" style="color:' + col[0] + ';background:' + col[1] + '">' + getSupportCategoryIcon(cat.name) + '</div>' +
                        '<span class="tau-cat-nm">' + escapeHtml(cat.name) + '</span>' +
                        '<span class="tau-cat-arr">&#8595;</span>' +
                    '</div>';
                });

                h += '</div>' +
                    '<div class="tau-cat-details-container" id="tauCatDetailsContainer" style="display:none;">' +
                        '<div class="tau-cat-details-header">' +
                            '<h3 class="tau-cat-details-title" id="tauCatDetailsTitle"></h3>' +
                        '</div>' +
                        '<div class="tau-cat-details-content" id="tauCatDetailsContent"></div>' +
                    '</div>' +
                '</div>';

                section.innerHTML = h;

                if (categoriesContainer && categoriesContainer.parentNode) {
                    categoriesContainer.parentNode.insertBefore(section, categoriesContainer);
                }

                var cards = section.querySelectorAll(".tau-cat-card");
                var detailsContainer = section.querySelector("#tauCatDetailsContainer");
                var detailsTitle = section.querySelector("#tauCatDetailsTitle");
                var detailsContent = section.querySelector("#tauCatDetailsContent");
                var activeCatId = null;

                function renderCourseCards(coursesList) {
                    var grid = document.createElement("div");
                    grid.className = "tau-courses-subgrid";

                    if (!Array.isArray(coursesList) || coursesList.length === 0) {
                        grid.innerHTML = '<div class="tau-no-courses">No hay cursos disponibles actualmente.</div>';
                        return grid;
                    }

                    coursesList.forEach(function(course) {
                        var card = document.createElement("div");
                        card.className = "tau-course-item-card";
                        card.innerHTML =
                            '<div class="tau-course-header-color"></div>' +
                            '<div class="tau-course-body-wrap">' +
                                '<h5 class="tau-course-title">' + escapeHtml(course.fullname) + '</h5>' +
                                '<div class="tau-course-summary">' + (course.summary || 'Sin descripcion disponible.') + '</div>' +
                                '<div class="tau-course-footer">' +
                                    '<a href="/course/view.php?id=' + course.id + '" class="tau-course-action-btn">Acceder al Curso</a>' +
                                '</div>' +
                            '</div>';

                        var actionBtn = card.querySelector(".tau-course-action-btn");
                        actionBtn.addEventListener("click", function(e) {
                            if (!window.tauIsLoggedIn) {
                                e.preventDefault();
                                window.location.href = "/login/index.php?loginredirect=1";
                            }
                        });

                        grid.appendChild(card);
                    });

                    return grid;
                }

                cards.forEach(function(card) {
                    card.addEventListener("click", function() {
                        var catId = parseInt(card.dataset.catId, 10);

                        if (activeCatId === catId) {
                            detailsContainer.style.display = "none";
                            card.classList.remove("active");
                            activeCatId = null;
                            return;
                        }

                        cards.forEach(function(node) { node.classList.remove("active"); });
                        card.classList.add("active");
                        activeCatId = catId;

                        var catData = data.find(function(item) { return item.id === catId; });
                        if (!catData) {
                            return;
                        }

                        detailsTitle.textContent = catData.name;
                        detailsContent.innerHTML = "";

                        if (Array.isArray(catData.courses) && catData.courses.length > 0) {
                            var directCoursesTitle = document.createElement("h4");
                            directCoursesTitle.className = "tau-subcategory-title";
                            directCoursesTitle.textContent = "Cursos en esta area";
                            detailsContent.appendChild(directCoursesTitle);
                            detailsContent.appendChild(renderCourseCards(catData.courses));
                        }

                        if (Array.isArray(catData.subcategories) && catData.subcategories.length > 0) {
                            var subcatWrapper = document.createElement("div");
                            subcatWrapper.className = "tau-subcategories-accordion";

                            catData.subcategories.forEach(function(subcat) {
                                var subcatItem = document.createElement("div");
                                subcatItem.className = "tau-subcat-item";

                                var subcatHeader = document.createElement("button");
                                subcatHeader.className = "tau-subcat-header";
                                subcatHeader.type = "button";
                                subcatHeader.innerHTML =
                                    '<span class="tau-subcat-name">' + escapeHtml(subcat.name) + '</span>' +
                                    '<span class="tau-subcat-chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></span>';

                                var subcatBody = document.createElement("div");
                                subcatBody.className = "tau-subcat-body";
                                subcatBody.style.display = "none";
                                subcatBody.appendChild(renderCourseCards(subcat.courses));

                                subcatHeader.addEventListener("click", function() {
                                    var isCollapsed = subcatBody.style.display === "none";
                                    subcatBody.style.display = isCollapsed ? "block" : "none";
                                    subcatHeader.classList.toggle("active", isCollapsed);
                                });

                                subcatItem.appendChild(subcatHeader);
                                subcatItem.appendChild(subcatBody);
                                subcatWrapper.appendChild(subcatItem);
                            });

                            detailsContent.appendChild(subcatWrapper);
                        }

                        detailsContainer.style.display = "block";
                        setTimeout(function() {
                            detailsContainer.scrollIntoView({ behavior: "smooth", block: "nearest" });
                        }, 100);
                    });
                });
            })
            .catch(function(err) {
                console.error("Failed to load public courses hierarchy:", err);
            });
    }

    function initScrollAnimations() {
        var gsapCheck = setInterval(function() {
            if (!window.gsap || !window.ScrollTrigger) {
                return;
            }

            clearInterval(gsapCheck);
            gsap.registerPlugin(ScrollTrigger);

            var carouselImg = document.querySelector("#mooveslideshow img, .carousel-item img");
            if (carouselImg) {
                gsap.to(carouselImg, {
                    scale: 1.12,
                    ease: "none",
                    scrollTrigger: {
                        trigger: "#mooveslideshow",
                        start: "top top",
                        end: "bottom top",
                        scrub: true
                    }
                });
            }

            document.querySelectorAll(".tau-program-card, .tau-cat-card, .rate-box").forEach(function(node, idx) {
                gsap.from(node, {
                    opacity: 0,
                    y: 24,
                    duration: 0.65,
                    delay: idx * 0.04,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: node,
                        start: "top 88%",
                        toggleActions: "play none none none"
                    }
                });
            });
        }, 100);
    }

    function initFrontpage() {
        var isSiteIndex = document.body.id === "page-site-index";

        document.querySelectorAll("a").forEach(function(a) {
            if (a.href && a.href.indexOf("/course/index.php") !== -1 && a.href.indexOf("categoryid=") === -1) {
                a.href = "/#apoyo-academico";
            }
        });

        if (window.location.pathname.indexOf("/course/index.php") !== -1 && window.location.search.indexOf("categoryid=") === -1) {
            window.location.href = "/#apoyo-academico";
            return;
        }

        if (!isSiteIndex) {
            return;
        }

        renderBannerOverlay();
        renderPrivatePrograms();
        renderSupportCoursesSection();
        initScrollAnimations();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initFrontpage);
    } else {
        initFrontpage();
    }
})();

