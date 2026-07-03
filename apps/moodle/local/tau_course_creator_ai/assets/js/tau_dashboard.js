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
    var tauFilterState = {
        search: "",
        faculty: [],
        program: [],
        semester: []
    };
    var tauFilterConfig = [
        { key: "faculty", label: "Facultad" },
        { key: "program", label: "Programas academicos" },
        { key: "semester", label: "Semestres" }
    ];
    var tauCurrentRole = "";
    var tauFilterApplying = false;

    function tauGetCurrentPath() {
        return (window.location.pathname || "").replace(/\/+$/, "").toLowerCase();
    }

    function tauIsCoursesPage() {
        var path = tauGetCurrentPath();
        return path === "/my/courses.php";
    }

    function tauIsStudentView() {
        return tauCurrentRole === "student" || document.body.classList.contains("tau-role-student");
    }

    function tauHideStudentNavigation() {
        if (!tauIsStudentView()) {
            return;
        }

        document.body.classList.add("tau-role-student");

        document.querySelectorAll(".primary-navigation a, .moremenu.navigation a, .navbar a").forEach(function(link) {
            var label = tauNormalizeText(link.textContent || "");
            if (label === "pagina principal") {
                var item = link.closest(".nav-item, li, .moremenu-navigation-link, .nav-link-container") || link;
                item.style.setProperty("display", "none", "important");
            }
        });
    }

    function tauApplyStudentCourseRestrictions() {
        if (!tauIsStudentView() || !tauIsCoursesPage()) {
            return;
        }

        document.body.classList.add("tau-student-courses-minimal");

        var smartFilters = document.getElementById("tau-smart-filters");
        if (smartFilters) {
            smartFilters.remove();
        }

        var semesterTabs = document.getElementById("tau-semester-tabs");
        if (semesterTabs) {
            semesterTabs.remove();
        }

        document.querySelectorAll(
            ".block-myoverview [data-region='filter-bar'], " +
            ".block-myoverview .tau-native-toolbar, " +
            "#region-main [data-region='filter-bar']"
        ).forEach(function(node) {
            node.classList.add("tau-student-hidden-control");
            node.style.setProperty("display", "none", "important");
        });
    }

    function tauShowNavbarUserName(name) {
        if (!name) {
            return;
        }

        var menuToggle = document.querySelector(".usermenu .dropdown-toggle, .usermenu [data-bs-toggle='dropdown'], .usermenu .login a");
        if (!menuToggle) {
            return;
        }

        var existing = menuToggle.querySelector(".tau-navbar-username");
        if (!existing) {
            existing = document.createElement("span");
            existing.className = "tau-navbar-username";
            menuToggle.insertBefore(existing, menuToggle.lastChild);
        }

        var hand = existing.querySelector(".tau-navbar-hand");
        if (!hand) {
            hand = document.createElement("span");
            hand.className = "tau-navbar-hand";
            hand.textContent = "👋";
            existing.appendChild(hand);
        }

        var text = existing.querySelector(".tau-navbar-username-text");
        if (!text) {
            text = document.createElement("span");
            text.className = "tau-navbar-username-text";
            existing.appendChild(text);
        }

        text.textContent = tauGetCompactNavbarName(name);
        existing.title = String(name);
        menuToggle.title = String(name);
        document.body.classList.add("tau-navbar-name-visible");
    }

    function tauGetCompactNavbarName(name) {
        var fullname = String(name || "").trim().replace(/\s+/g, " ");
        if (!fullname) {
            return "";
        }

        var parts = fullname.split(" ");
        var viewport = window.innerWidth || document.documentElement.clientWidth || 0;
        var maxLength = 24;

        if (viewport <= 1500) {
            maxLength = 20;
        }
        if (viewport <= 1320) {
            maxLength = 16;
        }
        if (viewport <= 1180) {
            maxLength = 12;
        }

        var preferred = fullname;
        if (parts.length >= 2) {
            preferred = parts[0] + " " + parts[parts.length - 1];
        } else if (parts.length === 1) {
            preferred = parts[0];
        }

        var compact = preferred.toUpperCase();
        if (compact.length > maxLength && parts.length >= 2) {
            compact = (parts[0] + " " + parts[1]).toUpperCase();
        }

        if (compact.length > maxLength) {
            compact = compact.slice(0, Math.max(8, maxLength - 1)).trim() + "…";
        }

        return compact;
    }

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

    function tauNormalizeText(value) {
        return String(value || "")
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase()
            .trim();
    }

    function tauEscapeHtml(value) {
        return String(value || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function tauGetCourseId(card) {
        var link = card.querySelector("a[href*='/course/view.php']");
        if (!link) return 0;
        var m = link.href.match(/[?&]id=(\d+)/);
        return m ? parseInt(m[1], 10) : 0;
    }

    function tauFindDirectChild(card, selector) {
        for (var i = 0; i < card.children.length; i++) {
            if (card.children[i].matches(selector)) {
                return card.children[i];
            }
        }
        return null;
    }

    function tauRestoreCardStructure(card) {
        var cover = tauFindDirectChild(card, ".tau-book-cover");
        if (!cover) return;

        var pages = tauFindDirectChild(card, ".tau-book-pages");
        var menu = tauFindDirectChild(card, ".dropdown, .action-menu");
        var fragment = document.createDocumentFragment();

        while (cover.firstChild) {
            fragment.appendChild(cover.firstChild);
        }

        if (menu) {
            card.insertBefore(fragment, menu);
        } else {
            card.appendChild(fragment);
        }

        cover.remove();
        if (pages) {
            pages.remove();
        }
    }

    function tauGetCourseMeta(cid) {
        var meta = window.tauCourseCategories && window.tauCourseCategories[cid] ? window.tauCourseCategories[cid] : {};
        var path = Array.isArray(meta.path) ? meta.path : [];
        return {
            faculty: meta.faculty || path[0] || "",
            program: meta.program || path[1] || "",
            semester: meta.semester || path[path.length - 1] || ""
        };
    }

    function tauSyncCardMeta() {
        document.querySelectorAll(".card.dashboard-card, .card.course-card").forEach(function(card) {
            var cid = tauGetCourseId(card);
            if (!cid) return;

            var body = card.querySelector(".card-body");
            var titleNode = card.querySelector(".multiline, .coursename, .card-title, h3, h4");
            var teacherNode = card.querySelector(".tau-teacher-name");
            var meta = tauGetCourseMeta(cid);
            
            var courseNameMeta = window.tauCourseNames && window.tauCourseNames[cid] ? window.tauCourseNames[cid] : null;
            var customTitle = courseNameMeta ? String(courseNameMeta.fullname || courseNameMeta.shortname || "") : "";

            card.dataset.tauCourseId = String(cid);
            card.dataset.tauFaculty = meta.faculty || "";
            card.dataset.tauProgram = meta.program || "";
            card.dataset.tauSemester = meta.semester || "";
            card.dataset.tauSearch = tauNormalizeText([
                titleNode ? titleNode.textContent : "",
                customTitle,
                teacherNode ? teacherNode.textContent : "",
                meta.faculty,
                meta.program,
                meta.semester,
                body ? body.textContent : ""
            ].join(" "));
        });
    }

    function tauGetRealCards() {
        var cards = [];
        document.querySelectorAll(".card.dashboard-card, .card.course-card").forEach(function(card) {
            if (tauGetCourseId(card)) {
                cards.push(card);
            }
        });
        return cards;
    }

    function tauGetGridItemNode(card) {
        if (!card) {
            return null;
        }

        // Try to find common wrappers like Bootstrap columns or list items
        var col = card.closest("li, [class*='col-']");
        if (col) {
            return col;
        }

        // Fallback: traverse up until we hit a known container
        var node = card;
        while (node && node.parentElement) {
            var p = node.parentElement;
            if (p.classList && (p.classList.contains("tau-course-grid") || p.classList.contains("card-deck") || p.hasAttribute("data-region"))) {
                return node;
            }
            node = p;
        }

        return card;
    }

    function tauGetFilteredCards(excludeKey) {
        var searchTerms = tauNormalizeText(tauFilterState.search).split(/\s+/).filter(function(w) { return w; });
        return tauGetRealCards().filter(function(card) {
            if (searchTerms.length > 0) {
                var searchData = card.dataset.tauSearch || "";
                var matchesSearch = searchTerms.every(function(term) {
                    return searchData.indexOf(term) !== -1;
                });
                if (!matchesSearch) {
                    return false;
                }
            }

            return tauFilterConfig.every(function(group) {
                if (excludeKey && group.key === excludeKey) {
                    return true;
                }
                var selected = tauFilterState[group.key] || [];
                if (!selected.length) {
                    return true;
                }
                var value = card.dataset["tau" + group.key.charAt(0).toUpperCase() + group.key.slice(1)] || "";
                return selected.indexOf(value) !== -1;
            });
        });
    }

    function tauGetOptionsForGroup(groupKey) {
        var counts = Object.create(null);
        tauGetFilteredCards(groupKey).forEach(function(card) {
            var value = card.dataset["tau" + groupKey.charAt(0).toUpperCase() + groupKey.slice(1)] || "";
            if (!value) {
                return;
            }
            counts[value] = (counts[value] || 0) + 1;
        });

        (tauFilterState[groupKey] || []).forEach(function(value) {
            if (value && counts[value] == null) {
                counts[value] = 0;
            }
        });

        return Object.keys(counts).sort(function(a, b) {
            return a.localeCompare(b, undefined, { numeric: true, sensitivity: "base" });
        }).map(function(value) {
            return { value: value, count: counts[value] || 0 };
        });
    }

    function tauCloseFilterMenus(currentKey) {
        var root = document.getElementById("tau-smart-filters");
        if (!root) return;
        root.querySelectorAll(".tau-filter-dropdown").forEach(function(dropdown) {
            if (!currentKey || dropdown.dataset.key !== currentKey) {
                dropdown.classList.remove("is-open");
            }
        });
    }

    function tauRemoveLegacySemesterTabs() {
        document.querySelectorAll("#tau-semester-tabs").forEach(function(node) {
            node.parentNode && node.parentNode.removeChild(node);
        });
    }

    function tauHideNativeOverviewSearch(overviewRegion) {
        if (!overviewRegion) {
            return;
        }

        overviewRegion.querySelectorAll("input[type='search'], input[type='text']").forEach(function(input) {
            if (input.closest("#tau-smart-filters")) {
                return;
            }

            var placeholder = (input.getAttribute("placeholder") || "").toLowerCase();
            var ariaLabel = (input.getAttribute("aria-label") || "").toLowerCase();
            var looksLikeSearch = placeholder.indexOf("buscar") !== -1 ||
                placeholder.indexOf("search") !== -1 ||
                ariaLabel.indexOf("buscar") !== -1 ||
                ariaLabel.indexOf("search") !== -1;

            if (!looksLikeSearch) {
                return;
            }

            var wrapper = input.closest(".input-group, .simplesearchform, .form-group, .col-auto, .col, [role='search']") || input;
            wrapper.classList.add("tau-native-search-hidden");
        });
    }

    function tauNormalizeCourseGrid() {
        var containers = document.querySelectorAll(
            ".block-myoverview .card-deck, " +
            ".block-myoverview .cardscontainer, " +
            ".block-myoverview [data-region='courses-view'], " +
            ".block-myoverview [data-region='course-content'], " +
            "#region-main [data-region='course-events-container'], " +
            "#region-main .card-deck"
        );

        containers.forEach(function(container) {
            container.classList.add("tau-course-grid");
        });
    }

    function tauCompactCourseGrid() {
        document.querySelectorAll(".tau-course-grid").forEach(function(container) {
            var items = [];

            Array.prototype.forEach.call(container.children, function(child) {
                var card = child.matches(".card.dashboard-card, .card.course-card")
                    ? child
                    : child.querySelector(".card.dashboard-card, .card.course-card");

                if (!card || !tauGetCourseId(card)) {
                    return;
                }

                items.push({
                    node: child,
                    visible: child.style.display !== "none" && !child.classList.contains("tau-grid-item-hidden")
                });
            });

            if (!items.length) {
                return;
            }

            items
                .sort(function(a, b) {
                    if (a.visible === b.visible) {
                        return 0;
                    }
                    return a.visible ? -1 : 1;
                })
                .forEach(function(item) {
                    container.appendChild(item.node);
                });
        });
    }

    function tauOrganizeNativeToolbar(overviewRegion) {
        if (!overviewRegion) {
            return;
        }

        overviewRegion.querySelectorAll("[data-region='filter-bar'], [data-region='filter']").forEach(function(toolbar) {
            toolbar.classList.add("tau-native-toolbar");

            Array.prototype.forEach.call(toolbar.children, function(child) {
                if (!child.classList.contains("tau-native-search-hidden")) {
                    child.classList.add("tau-native-toolbar__item");
                }
            });
        });
    }

    function tauRenderFilterDropdown(group) {
        var selected = tauFilterState[group.key] || [];
        var options = tauGetOptionsForGroup(group.key);
        var buttonLabel = "Seleccionar";
        if (selected.length === 1) {
            buttonLabel = selected[0];
        } else if (selected.length > 1) {
            buttonLabel = selected.length + " seleccionadas";
        }

        var html = '<div class="tau-filter-dropdown" data-key="' + group.key + '">' +
            '<button type="button" class="tau-filter-trigger" data-role="toggle">' +
                '<span class="tau-filter-trigger__label">' + tauEscapeHtml(group.label) + '</span>' +
                '<strong class="tau-filter-trigger__value">' + tauEscapeHtml(buttonLabel) + '</strong>' +
                '<span class="tau-filter-trigger__chevron">' +
                    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>' +
                '</span>' +
            '</button>' +
            '<div class="tau-filter-menu">';

        if (!options.length) {
            html += '<div class="tau-filter-empty">Sin opciones disponibles</div>';
        } else {
            options.forEach(function(option) {
                var checked = selected.indexOf(option.value) !== -1 ? " checked" : "";
                html += '<label class="tau-filter-option">' +
                    '<input type="checkbox" data-group="' + group.key + '" value="' + tauEscapeHtml(option.value) + '"' + checked + '>' +
                    '<span class="tau-filter-option__text">' + tauEscapeHtml(option.value) + '</span>' +
                    '<span class="tau-filter-option__count">' + option.count + '</span>' +
                '</label>';
            });
        }

        html += '</div></div>';
        return html;
    }

    function tauApplyAdvancedFilters() {
        if (tauFilterApplying) {
            return;
        }

        tauFilterApplying = true;
        var filteredCards = tauGetFilteredCards();
        var filteredIds = {};
        filteredCards.forEach(function(card) {
            filteredIds[card.dataset.tauCourseId] = true;
        });

        try {
            tauGetRealCards().forEach(function(card) {
                var visible = !!filteredIds[card.dataset.tauCourseId];
                var gridItem = tauGetGridItemNode(card);
                if (card.classList.contains("tau-card-hidden") === visible) {
                    card.classList.toggle("tau-card-hidden", !visible);
                }

                if (gridItem) {
                    gridItem.classList.toggle("tau-grid-item-hidden", !visible);
                }
                
                var wrapper = gridItem || card;
                var nextDisplay = visible ? "" : "none";
                var nextOpacity = visible ? "1" : "0";
                
                if (visible) {
                    wrapper.classList.remove("tau-card-hidden");
                    wrapper.style.display = nextDisplay;
                    wrapper.style.opacity = nextOpacity;
                } else {
                    wrapper.classList.add("tau-card-hidden");
                    wrapper.style.display = "none";
                }
            });

            tauNormalizeCourseGrid();
            tauCompactCourseGrid();

            var empty = document.getElementById("tau-smart-filters-empty");
            if (!empty) {
                empty = document.createElement("div");
                empty.id = "tau-smart-filters-empty";
                empty.className = "tau-smart-filters-empty";
                empty.innerHTML = "<strong>Sin coincidencias</strong><span>Ajusta la busqueda o limpia uno de los filtros.</span>";
            }

            var anchor = document.querySelector('[data-region="course-content"]') ||
                         document.querySelector(".block-myoverview [data-region='courses-view']") ||
                         document.querySelector(".block-myoverview");
            if (anchor && !empty.parentNode) {
                anchor.appendChild(empty);
            }
            if (anchor) {
                var nextEmptyDisplay = filteredCards.length ? "none" : "flex";
                if (empty.style.display !== nextEmptyDisplay) {
                    empty.style.display = nextEmptyDisplay;
                }
            }

            var meta = document.getElementById("tau-smart-filters-meta");
            if (meta) {
                var nextMeta = filteredCards.length + " curso" + (filteredCards.length === 1 ? "" : "s") + " visible" + (filteredCards.length === 1 ? "" : "s");
                if (meta.textContent !== nextMeta) {
                    meta.textContent = nextMeta;
                }
            }
        } finally {
            tauFilterApplying = false;
        }
    }

    function tauBindAdvancedFilterEvents(root) {
        if (!root || root.dataset.tauBound === "1") {
            return;
        }
        root.dataset.tauBound = "1";

        root.addEventListener("click", function(e) {
            var toggle = e.target.closest("[data-role='toggle']");
            if (toggle) {
                e.preventDefault();
                e.stopPropagation();
                var dropdown = toggle.closest(".tau-filter-dropdown");
                var isOpen = dropdown.classList.contains("is-open");
                tauCloseFilterMenus();
                if (!isOpen) {
                    dropdown.classList.add("is-open");
                }
                return;
            }

            var clearBtn = e.target.closest("[data-role='clear-all']");
            if (clearBtn) {
                e.preventDefault();
                tauFilterState.search = "";
                tauFilterConfig.forEach(function(group) {
                    tauFilterState[group.key] = [];
                });
                tauRenderAdvancedFilters();
            }
        });

        root.addEventListener("change", function(e) {
            var checkbox = e.target.closest(".tau-filter-option input[type='checkbox']");
            if (!checkbox) {
                return;
            }
            var list = tauFilterState[checkbox.dataset.group] || [];
            if (checkbox.checked) {
                if (list.indexOf(checkbox.value) === -1) {
                    list.push(checkbox.value);
                }
            } else {
                tauFilterState[checkbox.dataset.group] = list.filter(function(value) {
                    return value !== checkbox.value;
                });
            }
            if (checkbox.checked) {
                tauFilterState[checkbox.dataset.group] = list;
            }
            tauRenderAdvancedFilters();
        });

        root.addEventListener("input", function(e) {
            var input = e.target.closest("#tau-smart-search");
            if (!input) {
                return;
            }
            tauFilterState.search = input.value || "";
            tauApplyAdvancedFilters();
        });

        document.addEventListener("click", function(e) {
            if (!e.target.closest("#tau-smart-filters")) {
                tauCloseFilterMenus();
            }
        });
    }

    function tauRenderAdvancedFilters() {
        if (tauIsStudentView()) {
            tauApplyStudentCourseRestrictions();
            return;
        }

        if (!tauIsCoursesPage()) {
            var orphanRoot = document.getElementById("tau-smart-filters");
            if (orphanRoot && orphanRoot.parentNode) {
                orphanRoot.parentNode.removeChild(orphanRoot);
            }
            var orphanEmpty = document.getElementById("tau-smart-filters-empty");
            if (orphanEmpty && orphanEmpty.parentNode) {
                orphanEmpty.parentNode.removeChild(orphanEmpty);
            }
            return;
        }

        tauRemoveLegacySemesterTabs();
        tauSyncCardMeta();

        var overviewRegion = document.querySelector('[data-region="course-overview"]') ||
                             document.querySelector(".block-myoverview") ||
                             document.querySelector("#region-main");
        if (!overviewRegion) return;

        tauHideNativeOverviewSearch(overviewRegion);
        tauNormalizeCourseGrid();
        tauOrganizeNativeToolbar(overviewRegion);

        var target = overviewRegion.querySelector("[data-region='filter-bar']") ||
                     overviewRegion.querySelector(".card-deck") ||
                     overviewRegion.querySelector(".card-grid") ||
                     overviewRegion.querySelector(".courses-view") ||
                     overviewRegion.firstElementChild;
        if (!target) return;

        var root = document.getElementById("tau-smart-filters");
        if (!root) {
            root = document.createElement("section");
            root.id = "tau-smart-filters";
            root.className = "tau-smart-filters";
            target.parentNode.insertBefore(root, target);
        }

        var activeElement = document.activeElement;
        var preserveSearchFocus = activeElement && activeElement.id === "tau-smart-search";
        var preservedSelectionStart = preserveSearchFocus && typeof activeElement.selectionStart === "number" ? activeElement.selectionStart : null;
        var preservedSelectionEnd = preserveSearchFocus && typeof activeElement.selectionEnd === "number" ? activeElement.selectionEnd : null;

        var html = '<div class="tau-smart-filters__top">' +
            '<div class="tau-smart-filters__heading">' +
                '<span class="tau-smart-filters__eyebrow">Filtros de cursos</span>' +
                '<h3>Filtra por facultad, programa y semestre</h3>' +
            '</div>' +
            '<div class="tau-smart-filters__actions">' +
                '<span id="tau-smart-filters-meta" class="tau-smart-filters__meta"></span>' +
                '<button type="button" class="tau-smart-filters__clear" data-role="clear-all">Limpiar filtros</button>' +
            '</div>' +
        '</div>' +
        '<div class="tau-smart-filters__search">' +
            '<span class="tau-smart-filters__searchicon">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>' +
            '</span>' +
            '<input id="tau-smart-search" type="text" inputmode="search" autocomplete="off" spellcheck="false" placeholder="Buscar por curso, docente, programa, facultad o semestre" value="' + tauEscapeHtml(tauFilterState.search) + '">' +
        '</div>' +
        '<div class="tau-smart-filters__grid">' +
            tauFilterConfig.map(tauRenderFilterDropdown).join("") +
        '</div>';

        root.innerHTML = html;
        tauBindAdvancedFilterEvents(root);
        tauApplyAdvancedFilters();

        if (preserveSearchFocus) {
            var searchInput = root.querySelector("#tau-smart-search");
            if (searchInput) {
                searchInput.focus({ preventScroll: true });
                if (preservedSelectionStart !== null && preservedSelectionEnd !== null) {
                    searchInput.setSelectionRange(preservedSelectionStart, preservedSelectionEnd);
                }
            }
        }
    }

    function tauEnhanceCards() {
        var cards = document.querySelectorAll(".card.dashboard-card, .card.course-card");
        if (cards.length > 0) {
            logDebug("tauEnhanceCards(): Analyzing " + cards.length + " cards");
        }
        
        cards.forEach(function(card, index) {
            tauRestoreCardStructure(card);

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
                    gh.style.background = "linear-gradient(135deg, #5e1021 0%, #85192d 52%, #c62b3a 100%)";
                    // Add ?v=timestamp query parameter to bypass aggressive browser caching of the new logo image!
                    gh.innerHTML = '<div class="tau-card-logo-shell"><img class="tau-card-logo" src="/theme/tau_branding/assets/official/cesmag-tau-card-logo-transparent.png?v=' + Date.now() + '" alt="CESMAG TAU"></div>';
                    
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

            // 1b. Keep the header clean: logo only
            var ghEl = card.querySelector(".tau-gradient-header");
            if (ghEl) {
                var legacyProgramTitle = ghEl.querySelector(".tau-card-program-title");
                if (legacyProgramTitle) {
                    legacyProgramTitle.remove();
                }
            }

            // 2. Category Badge Enhancement
            var body = card.querySelector(".card-body");
            if (body) {
                var catEl = card.querySelector(".coursecat, .course-category, [data-region='coursecategory'], .categoryname");
                var catTxt = catEl ? catEl.textContent.trim() : null;
                if (catEl) {
                    catEl.style.setProperty("display", "none", "important");
                    catEl.classList.add("tau-native-category-hidden");
                }
                
                var badge = body.querySelector(".tau-cat-badge");
                if (badge) {
                    badge.remove();
                }

                var semesterText = catTxt;
                if (window.tauCourseCategories && window.tauCourseCategories[cid]) {
                    semesterText = window.tauCourseCategories[cid].semester || catTxt;
                }

                var metaStrip = body.querySelector(".tau-card-meta-strip");
                if (!metaStrip) {
                    metaStrip = document.createElement("div");
                    metaStrip.className = "tau-card-meta-strip";
                    body.appendChild(metaStrip);
                }

                var metaParts = [];
                var cardMeta = window.tauCourseCategories && window.tauCourseCategories[cid] ? window.tauCourseCategories[cid] : null;
                if (cardMeta && cardMeta.program) {
                    metaParts.push(
                        '<div class="tau-card-meta-block tau-card-meta-block--program">' +
                            '<strong class="tau-card-meta-value tau-card-meta-value--program">' + tauEscapeHtml(cardMeta.program) + '</strong>' +
                        '</div>'
                    );
                }
                if (semesterText) {
                    metaParts.push(
                        '<div class="tau-card-meta-block tau-card-meta-block--semester">' +
                            '<span class="tau-card-meta-value tau-card-meta-value--semester">' + tauEscapeHtml(semesterText) + '</span>' +
                        '</div>'
                    );
                }
                metaStrip.innerHTML = metaParts.join("");

                body.querySelectorAll(".card-text, .text-muted, .small, .course-summary, .summary, .contentafterlink").forEach(function(node) {
                    if (!node.closest(".tau-card-teacher-section")) {
                        node.style.setProperty("display", "none", "important");
                    }
                });

                var titleNode = body.querySelector(".multiline, .coursename, .card-title, h3, h4");
                var courseNameMeta = window.tauCourseNames && window.tauCourseNames[cid] ? window.tauCourseNames[cid] : null;
                var titleText = "";
                if (courseNameMeta) {
                    titleText = String(courseNameMeta.fullname || courseNameMeta.shortname || "").trim();
                }
                if (!titleText) {
                    titleText = titleNode ? String(titleNode.textContent || "").trim() : "";
                }
                if (titleNode) {
                    titleNode.classList.add("tau-native-course-title");
                    titleNode.style.setProperty("display", "none", "important");
                }

                if (titleText) {
                    var customTitle = body.querySelector(".tau-card-course-title");
                    if (!customTitle) {
                        customTitle = document.createElement("div");
                        customTitle.className = "tau-card-course-title";
                        body.appendChild(customTitle);
                    }
                    customTitle.textContent = titleText;
                    customTitle.setAttribute("title", titleText);
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

            // 5. Custom 3D Progress Bar Enhancement
            if (body && !card.dataset.tauProgEnh) {
                var progressNodes = card.querySelectorAll("span, div, p");
                var percent = -1;
                var foundNative = null;
                
                // Find native text like "25% completado" or "25 %"
                for (var i = 0; i < progressNodes.length; i++) {
                    var n = progressNodes[i];
                    if (n.textContent && n.children.length === 0) {
                        var txt = n.textContent.trim().toLowerCase();
                        if (txt.indexOf("%") !== -1 && txt.indexOf("completado") !== -1) {
                            var match = txt.match(/(\d+)\s*%/);
                            if (match) {
                                percent = parseInt(match[1], 10);
                                foundNative = n;
                                break;
                            }
                        }
                    }
                }
                
                // If Moodle doesn't provide progress natively, default to 0% to keep design consistent for EVERYONE
                if (percent === -1) {
                    percent = 0;
                }
                
                // Hide Moodle 4 native progress container if it exists
                var nativeContainer = card.querySelector(".course-progressbar, .progress-chart-container, .project-progress, .progress");
                if (nativeContainer) {
                    nativeContainer.style.setProperty("display", "none", "important");
                }
                
                if (percent !== -1) {
                    if (foundNative) {
                        var nativeParent = foundNative.closest(".d-flex, .align-items-center, .mt-2, .mt-3, .pt-2, .progress-text, .text-muted, .small");
                        // Don't hide parent if it contains the action menu
                        var menuInside = nativeParent ? nativeParent.querySelector(".dropdown, .action-menu") : null;
                        
                        if (nativeParent && nativeParent !== card && nativeParent !== body && !menuInside) {
                            nativeParent.style.setProperty("display", "none", "important");
                        } else {
                            foundNative.style.setProperty("display", "none", "important");
                        }
                    }
                    
                    var oldProg = body.querySelector(".tau-3d-progress-wrapper");
                    if (oldProg) oldProg.parentNode.removeChild(oldProg);

                    var pbar = document.createElement("div");
                    pbar.className = "tau-3d-progress-wrapper";
                    
                    var colorClass = percent === 100 ? "tau-prog-success" : (percent > 0 ? "tau-prog-info" : "tau-prog-empty");
                    
                    pbar.innerHTML = 
                        '<div class="tau-3d-progress-header">' +
                            '<span class="tau-3d-progress-label">Tu progreso</span>' +
                            '<span class="tau-3d-progress-value ' + colorClass + '-text">' + percent + '% completado</span>' +
                        '</div>' +
                        '<div class="tau-3d-progress-track">' +
                            '<div class="tau-3d-progress-fill ' + colorClass + '" style="width: ' + percent + '%;"></div>' +
                        '</div>';
                        
                    body.appendChild(pbar);
                }
                card.dataset.tauProgEnh = "1";
            }
            
            // ALWAYS Safely ensure action menu is at the top level so CSS child selector works
            var nativeMenu = card.querySelector(".dropdown, .action-menu");
            if (nativeMenu && nativeMenu.parentNode !== card) {
                card.appendChild(nativeMenu);
            }
            
            // 6. Admin/Teacher Tools Injection (Backup & Restore)
            if (!card.dataset.tauToolsEnh && cid && !tauIsStudentView()) {
                var dropMenu = card.querySelector(".dropdown-menu");
                if (dropMenu) {
                    var divider = document.createElement("div");
                    divider.className = "dropdown-divider";
                    
                    var backupLink = document.createElement("a");
                    backupLink.className = "dropdown-item tau-tool-backup";
                    backupLink.href = "/backup/backup.php?id=" + cid;
                    backupLink.innerHTML = "📦 Copia de Seguridad";
                    backupLink.title = "Guardar respaldo de este curso";
                    
                    var restoreLink = document.createElement("a");
                    restoreLink.className = "dropdown-item tau-tool-restore";
                    restoreLink.href = "/local/tau_course_creator_ai/restore_course.php?id=" + cid;
                    restoreLink.innerHTML = "♻️ Clonar para Semestre";
                    restoreLink.title = "Restaurar como un curso nuevo";
                    
                    dropMenu.appendChild(divider);
                    dropMenu.appendChild(backupLink);
                    dropMenu.appendChild(restoreLink);
                }
                card.dataset.tauToolsEnh = "1";
            }

            
            
            // 4. 3D Book Layout Wrapper
            if (false && !card.querySelector(".tau-book-cover")) {
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

    function tauInitCardDropdownLayering() {
        if (document.body.dataset.tauDropdownLayeringInit === "1") {
            return;
        }
        document.body.dataset.tauDropdownLayeringInit = "1";

        function tauCloseOtherCardMenus(currentCard) {
            document.querySelectorAll(".card.dashboard-card.tau-menu-open, .card.course-card.tau-menu-open").forEach(function(card) {
                if (card !== currentCard) {
                    card.classList.remove("tau-menu-open");
                }
            });

            document.querySelectorAll(".card.dashboard-card .dropdown-menu.show, .card.course-card .dropdown-menu.show").forEach(function(menu) {
                var ownerCard = menu.closest(".card.dashboard-card, .card.course-card");
                if (!ownerCard || ownerCard === currentCard) {
                    return;
                }

                menu.classList.remove("show");
                var parentDropdown = menu.closest(".dropdown, .action-menu");
                if (parentDropdown) {
                    parentDropdown.classList.remove("show");
                }

                var toggle = ownerCard.querySelector("[data-bs-toggle='dropdown'][aria-expanded='true']");
                if (toggle) {
                    toggle.setAttribute("aria-expanded", "false");
                }
            });
        }

        document.addEventListener("show.bs.dropdown", function(e) {
            var card = e.target && e.target.closest ? e.target.closest(".card.dashboard-card, .card.course-card") : null;
            if (!card) {
                return;
            }

            tauCloseOtherCardMenus(card);
            card.classList.add("tau-menu-open");
        });

        document.addEventListener("hide.bs.dropdown", function(e) {
            var card = e.target && e.target.closest ? e.target.closest(".card.dashboard-card, .card.course-card") : null;
            if (!card) {
                return;
            }

            window.setTimeout(function() {
                if (!card.querySelector(".dropdown-menu.show")) {
                    card.classList.remove("tau-menu-open");
                }
            }, 10);
        });

        document.addEventListener("click", function(e) {
            if (e.target.closest(".card.dashboard-card .dropdown, .card.dashboard-card .action-menu, .card.course-card .dropdown, .card.course-card .action-menu")) {
                return;
            }

            document.querySelectorAll(".card.dashboard-card.tau-menu-open, .card.course-card.tau-menu-open").forEach(function(card) {
                card.classList.remove("tau-menu-open");
            });
        });
    }

    function initDashboard() {
        logDebug("=== initDashboard() Executed ===");
        var isDashboardPage = (document.body.id && document.body.id.indexOf("page-my-") === 0) || window.location.pathname.indexOf("/my/") !== -1;
        logDebug("isDashboardPage: " + isDashboardPage + " | bodyID: " + document.body.id + " | path: " + window.location.pathname);

        createDebugUI();
        tauInitCardDropdownLayering();

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
                    if (d.fullname) {
                        window.tauUserFullname = d.fullname;
                        tauShowNavbarUserName(d.fullname);
                    }

                    if (d.course_categories) {
                        window.tauCourseCategories = d.course_categories;
                        logDebug("Registered categories database in window.tauCourseCategories");
                    }
                    if (d.course_names) {
                        window.tauCourseNames = d.course_names;
                        logDebug("Registered course names database in window.tauCourseNames");
                    }

                    if (d.course_teachers) {
                        window.tauCourseTeachers = d.course_teachers;
                        logDebug("Registered teachers database in window.tauCourseTeachers");
                        tauEnhanceCards();
                    }

                    tauCurrentRole = d.role || "";
                    document.body.classList.remove("tau-role-student", "tau-role-teacher", "tau-role-admin");
                    if (tauCurrentRole) {
                        document.body.classList.add("tau-role-" + tauCurrentRole);
                    }
                    tauHideStudentNavigation();
                    tauApplyStudentCourseRestrictions();

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
                    var greetingName = d.fullname || d.firstname || "";
                    var greeting = "¡Hola, " + greetingName + "! <span class='wave'>👋</span>";
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
            tauRenderAdvancedFilters();
            tauHideConectiMe();
            tauHideStudentNavigation();
            tauApplyStudentCourseRestrictions();
            if (window.tauUserFullname) {
                tauShowNavbarUserName(window.tauUserFullname);
            }
            if (window.tauUserFirstname) {
                tauHideWelcomeGreetings(window.tauUserFirstname);
            }
        } catch (e) {
            logDebug("TAU INIT ERROR: " + e.message);
        }

        var tauCardObs = new MutationObserver(function(mutations) {
            try {
                if (tauFilterApplying) {
                    return;
                }

                var onlyFilterMutations = mutations.length > 0 && mutations.every(function(mutation) {
                    var target = mutation.target && mutation.target.nodeType === 1 ? mutation.target : mutation.target && mutation.target.parentElement;
                    return !!(target && target.closest && target.closest("#tau-smart-filters, #tau-smart-filters-empty"));
                });
                if (onlyFilterMutations) {
                    return;
                }

                var activeElement = document.activeElement;
                if (activeElement && activeElement.id === "tau-smart-search") {
                    return;
                }

                // Fail-safe: disconnect observer during DOM updates to prevent recursive infinite loops
                tauCardObs.disconnect();
                
                tauMakeCardsClickable();
                tauEnhanceCards();
                tauRenderAdvancedFilters();
                tauHideConectiMe();
                tauHideStudentNavigation();
                tauApplyStudentCourseRestrictions();
                if (window.tauUserFullname) {
                    tauShowNavbarUserName(window.tauUserFullname);
                }
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
