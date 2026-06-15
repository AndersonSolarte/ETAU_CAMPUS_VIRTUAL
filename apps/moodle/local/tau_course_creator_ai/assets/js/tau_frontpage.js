/* TAU Campus Virtual - Frontpage Script */
(function() {
    "use strict";

    function initFrontpage() {
        var isSiteIndex = document.body.id === "page-site-index";
        
        // Rewrite all links pointing to /course/index.php (without categoryid) to home anchor
        document.querySelectorAll("a").forEach(function(a) {
            if (a.href && a.href.indexOf("/course/index.php") !== -1 && a.href.indexOf("categoryid=") === -1) {
                a.href = "/#apoyo-academico";
            }
        });

        if (window.location.pathname.indexOf("/course/index.php") !== -1 && window.location.search.indexOf("categoryid=") === -1) {
            window.location.href = "/#apoyo-academico";
            return;
        }

        if (!isSiteIndex) return;

        // ── 1. Replace marketing icons with professional non-animated SVGs ──
        var marketingCards = document.querySelectorAll("#feature .card");
        var marketingIcons = [
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1m-1.636 6.364l-.707-.707M12 21v-1m-6.364-1.636l.707.707M3 12h1m1.636-6.364l.707.707M12 7a5 5 0 0 0-5 5c0 1.25.46 2.39 1.21 3.26L9.5 17h5l1.29-1.74A4.98 4.98 0 0 0 17 12a5 5 0 0 0-5-5z"></path></svg>',
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>',
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
        ];

        marketingCards.forEach(function(card, idx) {
            var iconBox = card.querySelector(".icon-lg");
            if (iconBox && marketingIcons[idx]) {
                iconBox.innerHTML = marketingIcons[idx];
            }
        });

        // ── 2. Add slide overlay ──
        var banner = document.querySelector("#mooveslideshow .carousel-inner");
        if (banner && !document.querySelector(".tau-banner-overlay")) {
            banner.insertAdjacentHTML("beforeend",
                '<div class="tau-banner-overlay">' +
                    '<div class="tau-banner-card">' +
                        '<span class="tau-banner-pretitle">TAU Campus Virtual</span>' +
                        '<h1 class="tau-banner-title">Conectando <span class="tau-accent-text">saberes</span></h1>' +
                        '<span class="tau-banner-subtitle">Educación con Propósito</span>' +
                        '<p class="tau-banner-desc">Accede a tus cursos, recursos académicos y herramientas de aprendizaje en un entorno diseñado para tu crecimiento profesional.</p>' +
                        '<a href="/login" class="btn-tau-banner-explore">Ingresar a mi espacio</a>' +
                    '</div>' +
                '</div>'
            );

            // GSAP slide animate
            var checkGsapLoader = setInterval(function() {
                if (window.gsap) {
                    clearInterval(checkGsapLoader);
                    gsap.from(document.querySelector(".tau-banner-card"), {
                        opacity: 0,
                        x: -60,
                        duration: 1.2,
                        ease: "power3.out"
                    });
                }
            }, 100);
        }
        
        // ── 3. Categories & Public Courses Redesign ──
        var categoriesContainer = document.getElementById("frontpage-category-names") || 
                                  document.getElementById("frontpage-category-combo") || 
                                  document.querySelector(".frontpage-category-names, .category_subcategories, .course-category-listing");

        // Hide Moodle's native categories and course listings on frontpage
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

        // Fetch public courses and categories hierarchically from our new API
        fetch("/local/tau_course_creator_ai/public_courses.php?_=" + Date.now())
            .then(function(res) { return res.ok ? res.json() : []; })
            .then(function(data) {
                if (!Array.isArray(data) || data.length === 0) {
                    return; // No public categories/courses, keep section hidden
                }

                // Create custom section
                var section = document.createElement("div");
                section.className = "tau-cat-section";

                var h = '<div class="tau-cat-header">' +
                    '<div class="tau-cat-kicker">' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>' +
                        'Formación Complementaria' +
                    '</div>' +
                    '<h2 class="tau-cat-htitle">Cursos de Apoyo Académico</h2>' +
                    '<p class="tau-cat-sub">Explora nuestra oferta académica organizada por áreas de conocimiento para fortalecer tus competencias profesionales.</p>' +
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

                var getCategoryIcon = function(name) {
                    var n = name.toLowerCase();
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
                };

                // Render main category cards
                data.forEach(function(cat, idx) {
                    var col = colors[idx % colors.length];
                    h += '<div class="tau-cat-card" data-cat-id="' + cat.id + '" style="cursor: pointer;">' +
                        '<div class="tau-cat-ico" style="color:' + col[0] + ';background:' + col[1] + '">' + getCategoryIcon(cat.name) + '</div>' +
                        '<span class="tau-cat-nm">' + cat.name + '</span>' +
                        '<span class="tau-cat-arr">↓</span>' +
                    '</div>';
                });
                h += '</div>';

                // Redesigned details container (accordion panel)
                h += '<div class="tau-cat-details-container" id="tauCatDetailsContainer" style="display: none;">' +
                        '<div class="tau-cat-details-header">' +
                            '<h3 class="tau-cat-details-title" id="tauCatDetailsTitle"></h3>' +
                        '</div>' +
                        '<div class="tau-cat-details-content" id="tauCatDetailsContent"></div>' +
                     '</div>';

                h += '</div>';
                section.innerHTML = h;

                // Insert section
                if (categoriesContainer && categoriesContainer.parentNode) {
                    categoriesContainer.parentNode.insertBefore(section, categoriesContainer);
                }

                // Add click listener to main category cards
                var cards = section.querySelectorAll(".tau-cat-card");
                var detailsContainer = section.querySelector("#tauCatDetailsContainer");
                var detailsTitle = section.querySelector("#tauCatDetailsTitle");
                var detailsContent = section.querySelector("#tauCatDetailsContent");
                var activeCatId = null;

                cards.forEach(function(card) {
                    card.addEventListener("click", function() {
                        var catId = parseInt(card.dataset.catId, 10);
                        
                        // Toggle logic
                        if (activeCatId === catId) {
                            detailsContainer.style.display = "none";
                            card.classList.remove("active");
                            activeCatId = null;
                            return;
                        }

                        // Set active card classes
                        cards.forEach(function(c) { c.classList.remove("active"); });
                        card.classList.add("active");
                        activeCatId = catId;

                        // Find category data
                        var catData = data.find(function(c) { return c.id === catId; });
                        if (!catData) return;

                        detailsTitle.textContent = catData.name;
                        detailsContent.innerHTML = "";

                        // Render courses directly in main category
                        if (Array.isArray(catData.courses) && catData.courses.length > 0) {
                            var directCoursesTitle = document.createElement("h4");
                            directCoursesTitle.className = "tau-subcategory-title";
                            directCoursesTitle.textContent = "Cursos en esta área";
                            detailsContent.appendChild(directCoursesTitle);

                            var directCoursesList = renderCourseCards(catData.courses);
                            detailsContent.appendChild(directCoursesList);
                        }

                        // Render subcategories as accordions
                        if (Array.isArray(catData.subcategories) && catData.subcategories.length > 0) {
                            var subcatWrapper = document.createElement("div");
                            subcatWrapper.className = "tau-subcategories-accordion";

                            catData.subcategories.forEach(function(subcat) {
                                var subcatItem = document.createElement("div");
                                subcatItem.className = "tau-subcat-item";

                                var subcatHeader = document.createElement("button");
                                subcatHeader.className = "tau-subcat-header";
                                subcatHeader.type = "button";
                                subcatHeader.innerHTML = '<span class="tau-subcat-name">' + subcat.name + '</span>' +
                                                         '<span class="tau-subcat-chevron"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg></span>';

                                var subcatBody = document.createElement("div");
                                subcatBody.className = "tau-subcat-body";
                                subcatBody.style.display = "none";

                                var coursesList = renderCourseCards(subcat.courses);
                                subcatBody.appendChild(coursesList);

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
                        
                        // Smooth scroll to details
                        setTimeout(function() {
                            detailsContainer.scrollIntoView({ behavior: "smooth", block: "nearest" });
                        }, 100);
                    });
                });

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
                                '<h5 class="tau-course-title">' + course.fullname + '</h5>' +
                                '<div class="tau-course-summary">' + (course.summary || 'Sin descripción disponible.') + '</div>' +
                                '<div class="tau-course-footer">' +
                                    '<a href="/course/view.php?id=' + course.id + '" class="tau-course-action-btn">' +
                                        'Acceder al Curso' +
                                    '</a>' +
                                '</div>' +
                            '</div>';

                        // Intercept course click to enforce guest login redirection
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
            })
            .catch(function(err) {
                console.error("Failed to load public courses hierarchy:", err);
            });

        // ── 5. GSAP ScrollTrigger Animations ──
        var gsapCheck = setInterval(function() {
            if (window.gsap && window.ScrollTrigger) {
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

                var cards = document.querySelectorAll("#feature .card");
                if (cards.length > 0) {
                    cards.forEach(function(card) {
                        gsap.from(card, {
                            opacity: 0,
                            scale: 0.88,
                            y: 30,
                            duration: 0.7,
                            ease: "power2.out",
                            scrollTrigger: {
                                trigger: card,
                                start: "top 88%",
                                toggleActions: "play none none none"
                            }
                        });
                    });
                }

                var courseCards = document.querySelectorAll(".frontpage-course-list-all .card, .courses .card, .dashboard-card");
                if (courseCards.length > 0) {
                    courseCards.forEach(function(card) {
                        gsap.from(card, {
                            opacity: 0,
                            scale: 0.92,
                            y: 25,
                            duration: 0.6,
                            ease: "back.out(1.1)",
                            scrollTrigger: {
                                trigger: card,
                                start: "top 92%",
                                toggleActions: "play none none none"
                            }
                        });
                    });
                }

                var rateBoxes = document.querySelectorAll(".rate-box");
                if (rateBoxes.length > 0) {
                    rateBoxes.forEach(function(box, idx) {
                        gsap.from(box, {
                            opacity: 0,
                            x: idx % 2 === 0 ? -30 : 30,
                            duration: 0.7,
                            ease: "power2.out",
                            scrollTrigger: {
                                trigger: box,
                                start: "top 85%",
                                toggleActions: "play none none none"
                            }
                        });
                    });
                }
            }
        }, 100);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initFrontpage);
    } else {
        initFrontpage();
    }
})();
