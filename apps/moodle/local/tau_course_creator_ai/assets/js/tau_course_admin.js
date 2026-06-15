/* TAU Campus Virtual - Course Admin Buttons Injections */
(function() {
    "use strict";

    function initCourseAdmin() {
        var path = window.location.pathname;
        
        // Only run on course index/management/my pages
        if (!(path === '/course/index.php' || path === '/course/management.php' || path === '/' || path === '/my/' || path === '/my/index.php' || path === '/my/courses.php')) {
            return;
        }

        var checkBtn = setInterval(function() {
            var createElements = document.querySelectorAll("a[href*='/course/edit.php'], form[action*='/course/edit.php']");
            if (createElements.length === 0) return; // Wait until buttons exist
            clearInterval(checkBtn);
            
            function styleBtn(btn, type) {
                if (!btn) return;
                btn.style.setProperty("height", "42px", "important");
                btn.style.setProperty("padding", "0 22px", "important");
                btn.style.setProperty("border-radius", "8px", "important");
                btn.style.setProperty("font-size", "0.88rem", "important");
                btn.style.setProperty("font-weight", "700", "important");
                btn.style.setProperty("display", "inline-flex", "important");
                btn.style.setProperty("align-items", "center", "important");
                btn.style.setProperty("justify-content", "center", "important");
                btn.style.setProperty("white-space", "nowrap", "important");
                btn.style.setProperty("text-decoration", "none", "important");
                btn.style.setProperty("box-sizing", "border-box", "important");
                btn.style.setProperty("margin", "0", "important");
                btn.style.setProperty("transition", "all 0.2s cubic-bezier(0.4, 0, 0.2, 1)", "important");
                btn.style.setProperty("flex", "1 1 0%", "important");
                btn.style.setProperty("max-width", "380px", "important");
                btn.style.setProperty("width", "100%", "important");
                btn.style.setProperty("text-align", "center", "important");

                if (type === 'manage') {
                    btn.style.setProperty("background", "#ffffff", "important");
                    btn.style.setProperty("color", "#c62b3a", "important");
                    btn.style.setProperty("border", "2px solid #c62b3a", "important");
                    btn.style.setProperty("box-shadow", "0 2px 6px rgba(198, 43, 58, 0.08)", "important");
                    
                    btn.onmouseenter = function() {
                        btn.style.setProperty("background", "#fff2f3", "important");
                        btn.style.setProperty("transform", "translateY(-1.5px)", "important");
                        btn.style.setProperty("box-shadow", "0 6px 14px rgba(198, 43, 58, 0.16)", "important");
                    };
                    btn.onmouseleave = function() {
                        btn.style.setProperty("background", "#ffffff", "important");
                        btn.style.setProperty("transform", "none", "important");
                        btn.style.setProperty("box-shadow", "0 2px 6px rgba(198, 43, 58, 0.08)", "important");
                    };
                } else if (type === 'create') {
                    btn.style.setProperty("background", "#c62b3a", "important");
                    btn.style.setProperty("color", "#ffffff", "important");
                    btn.style.setProperty("border", "2px solid #c62b3a", "important");
                    btn.style.setProperty("box-shadow", "0 4px 10px rgba(198, 43, 58, 0.18)", "important");
                    
                    btn.onmouseenter = function() {
                        btn.style.setProperty("background", "#b02230", "important");
                        btn.style.setProperty("border-color", "#b02230", "important");
                        btn.style.setProperty("transform", "translateY(-1.5px)", "important");
                        btn.style.setProperty("box-shadow", "0 8px 18px rgba(198, 43, 58, 0.28)", "important");
                    };
                    btn.onmouseleave = function() {
                        btn.style.setProperty("background", "#c62b3a", "important");
                        btn.style.setProperty("border-color", "#c62b3a", "important");
                        btn.style.setProperty("transform", "none", "important");
                        btn.style.setProperty("box-shadow", "0 4px 10px rgba(198, 43, 58, 0.18)", "important");
                    };
                } else if (type === 'ai') {
                    btn.style.setProperty("background", "linear-gradient(135deg, #c62b3a 0%, #8d182a 100%)", "important");
                    btn.style.setProperty("color", "#ffffff", "important");
                    btn.style.setProperty("border", "none", "important");
                    btn.style.setProperty("box-shadow", "0 4px 12px rgba(198, 43, 58, 0.25)", "important");
                    
                    btn.onmouseenter = function() {
                        btn.style.setProperty("transform", "translateY(-1.5px)", "important");
                        btn.style.setProperty("box-shadow", "0 8px 20px rgba(198, 43, 58, 0.4)", "important");
                    };
                    btn.onmouseleave = function() {
                        btn.style.setProperty("transform", "none", "important");
                        btn.style.setProperty("box-shadow", "0 4px 12px rgba(198, 43, 58, 0.25)", "important");
                    };
                }
            }

            var el = null;
            // Search for a candidate that is NOT in a drawer/sidebar
            for (var i = 0; i < createElements.length; i++) {
                var candidate = createElements[i];
                if (candidate.closest('.drawer') || candidate.closest('#nav-drawer') || candidate.closest('[data-region="drawer"]') || candidate.closest('.flat-navigation') || candidate.closest('.nav') || candidate.closest('#theme_moove_drawer')) {
                    continue;
                }
                if (candidate.closest('#page-header') || candidate.closest('.header-actions') || candidate.closest('.tertiary-navigation') || candidate.closest('#region-main') || candidate.closest('[role="main"]') || candidate.closest('.header-extra-actions')) {
                    el = candidate;
                    break;
                }
            }
            
            if (!el) {
                for (var i = 0; i < createElements.length; i++) {
                    var candidate = createElements[i];
                    if (!candidate.closest('.drawer') && !candidate.closest('#nav-drawer') && !candidate.closest('[data-region="drawer"]') && !candidate.closest('.flat-navigation') && !candidate.closest('#theme_moove_drawer')) {
                        el = candidate;
                        break;
                    }
                }
            }

            if (!el && createElements.length > 0) {
                el = createElements[0];
            }
            if (!el) return;
            if (el.dataset.tauAiAdded || el.closest('#tau-cb-root') || el.id === 'tau-ai-btn' || el.id === 'tau-ai-inline-btn') {
                return;
            }
            el.dataset.tauAiAdded = "1";
            
            // Create "Crear curso con IA" button
            var aiBtn = document.createElement("a");
            aiBtn.id = "tau-ai-category-btn";
            aiBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle;"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Crear curso con IA';
            
            var categoryId = "";
            var targetUrl = el.tagName === "FORM" ? el.action : el.href;
            if (targetUrl) {
                var match = targetUrl.match(/[?&]category=(\d+)/);
                if (match) {
                    categoryId = match[1];
                }
            }
            if (!categoryId) {
                var urlMatch = window.location.search.match(/[?&]categoryid=(\d+)/);
                if (urlMatch) {
                    categoryId = urlMatch[1];
                }
            }
            
            aiBtn.href = "/local/tau_course_creator_ai/index.php" + (categoryId ? "?category=" + categoryId : "");
            
            el.parentNode.insertBefore(aiBtn, el.nextSibling);
            
            var parent = el.parentNode;
            if (parent) {
                parent.style.setProperty("display", "flex", "important");
                parent.style.setProperty("align-items", "center", "important");
                parent.style.setProperty("justify-content", "flex-end", "important");
                parent.style.setProperty("gap", "12px", "important");
                parent.style.setProperty("flex-wrap", "nowrap", "important");
                parent.style.setProperty("flex", "1 1 auto", "important");
                parent.style.setProperty("width", "100%", "important");
                parent.style.setProperty("max-width", "1140px", "important");
                parent.style.setProperty("margin-left", "auto", "important");
            }
            
            // Locate "Gestionar cursos"
            var manageBtn = null;
            var candidates = document.querySelectorAll("a, button, input[type='submit'], input[type='button']");
            candidates.forEach(function(c) {
                var text = (c.textContent || c.value || "").toLowerCase();
                if (text.indexOf("gestionar") !== -1 && ((c.href && c.href.indexOf("course") !== -1) || c.closest('.header-actions') || c.parentNode === parent)) {
                    manageBtn = c;
                }
            });

            var manageEl = manageBtn;
            if (manageBtn) {
                if (manageBtn.tagName === "INPUT" || manageBtn.tagName === "BUTTON") {
                    var formAncestor = manageBtn.closest("form");
                    if (formAncestor) {
                        manageEl = formAncestor;
                    }
                }
            }

            if (manageEl && manageEl.parentNode !== parent && parent) {
                parent.insertBefore(manageEl, el);
            }
            
            var manageToStyle = manageBtn;
            if (manageEl && manageEl.tagName === "FORM") {
                manageEl.style.setProperty("display", "inline-flex", "important");
                manageEl.style.setProperty("margin", "0", "important");
                manageEl.style.setProperty("flex", "1 1 0%", "important");
                manageEl.style.setProperty("max-width", "380px", "important");
                manageEl.style.setProperty("width", "100%", "important");
                
                var mFormDiv = manageEl.querySelector("div");
                if (mFormDiv) {
                    mFormDiv.style.setProperty("width", "100%", "important");
                    mFormDiv.style.setProperty("height", "100%", "important");
                    mFormDiv.style.setProperty("display", "inline-flex", "important");
                    mFormDiv.style.setProperty("align-items", "center", "important");
                    mFormDiv.style.setProperty("justify-content", "center", "important");
                    mFormDiv.style.setProperty("margin", "0", "important");
                    mFormDiv.style.setProperty("padding", "0", "important");
                    mFormDiv.style.setProperty("box-sizing", "border-box", "important");
                }
                
                manageToStyle = manageEl.querySelector("button, input[type='submit']");
                if (manageToStyle) {
                    manageToStyle.style.setProperty("width", "100%", "important");
                    manageToStyle.style.setProperty("height", "100%", "important");
                }
            }
            
            var buttonToStyle = el;
            if (el.tagName === "FORM") {
                el.style.setProperty("display", "inline-flex", "important");
                el.style.setProperty("margin", "0", "important");
                el.style.setProperty("flex", "1 1 0%", "important");
                el.style.setProperty("max-width", "380px", "important");
                el.style.setProperty("width", "100%", "important");
                
                var formDiv = el.querySelector("div");
                if (formDiv) {
                    formDiv.style.setProperty("width", "100%", "important");
                    formDiv.style.setProperty("height", "100%", "important");
                    formDiv.style.setProperty("display", "inline-flex", "important");
                    formDiv.style.setProperty("align-items", "center", "important");
                    formDiv.style.setProperty("justify-content", "center", "important");
                    formDiv.style.setProperty("margin", "0", "important");
                    formDiv.style.setProperty("padding", "0", "important");
                    formDiv.style.setProperty("box-sizing", "border-box", "important");
                }
                
                buttonToStyle = el.querySelector("button, input[type='submit']");
                if (buttonToStyle) {
                    buttonToStyle.style.setProperty("width", "100%", "important");
                    buttonToStyle.style.setProperty("height", "100%", "important");
                }
            }
            
            styleBtn(manageToStyle, 'manage');
            styleBtn(buttonToStyle, 'create');
            styleBtn(aiBtn, 'ai');
        }, 200);

        // Inject Bulk Delete button on course management page
        (function() {
            var moveSelect = document.querySelector('select[name="movecoursesto"]');
            if (moveSelect) {
                var form = moveSelect.closest('form');
                var moveSubmit = form ? form.querySelector('input[type="submit"], button[type="submit"]') : null;
                if (moveSubmit && !document.getElementById('tau-bulk-delete-btn')) {
                    var deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.id = 'tau-bulk-delete-btn';
                    deleteBtn.className = 'btn btn-danger ml-2';
                    deleteBtn.style.setProperty('background-color', '#c62b3a', 'important');
                    deleteBtn.style.setProperty('border-color', '#c62b3a', 'important');
                    deleteBtn.style.setProperty('color', '#ffffff', 'important');
                    deleteBtn.style.setProperty('height', '40px', 'important');
                    deleteBtn.style.setProperty('border-radius', '8px', 'important');
                    deleteBtn.style.setProperty('font-size', '0.85rem', 'important');
                    deleteBtn.style.setProperty('font-weight', '700', 'important');
                    deleteBtn.style.setProperty('margin-left', '8px', 'important');
                    deleteBtn.style.setProperty('padding', '0 18px', 'important');
                    deleteBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Borrar seleccionados';
                    
                    moveSubmit.parentNode.insertBefore(deleteBtn, moveSubmit.nextSibling);
                    
                    deleteBtn.addEventListener('click', async function() {
                        var checked = document.querySelectorAll('input[name="courses[]"]:checked');
                        if (checked.length === 0) {
                            alert('Por favor, selecciona al menos un curso para borrar.');
                            return;
                        }
                        
                        var ids = Array.from(checked).map(function(cb) { return cb.value; });
                        var confirmMsg = '¿Está seguro de que desea borrar los ' + ids.length + ' cursos seleccionados y todo su contenido de forma permanente? Esta acción no se puede deshacer.';
                        if (!confirm(confirmMsg)) {
                            return;
                        }
                        
                        deleteBtn.disabled = true;
                        deleteBtn.innerHTML = 'Borrando...';
                        
                        try {
                            var sesskey = M.cfg.sesskey;
                            var ajaxurl = '/local/tau_course_creator_ai/ajax.php';
                            var response = await fetch(ajaxurl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    action: 'bulkdelete',
                                    courseids: ids,
                                    sesskey: sesskey
                                })
                            });
                            
                            var result = await response.json();
                            if (!response.ok || result.error) {
                                throw new Error(result.error || 'Error en la petición de borrado.');
                            }
                            
                            if (result.errors && result.errors.length > 0) {
                                alert('Algunos cursos no se pudieron borrar:\n' + result.errors.join('\n'));
                            } else {
                                alert('¡Cursos borrados exitosamente!');
                            }
                            
                            window.location.reload();
                        } catch (err) {
                            alert('Error al borrar los cursos: ' + err.message);
                            deleteBtn.disabled = false;
                            deleteBtn.innerHTML = 'Borrar seleccionados';
                        }
                    });
                }
            }
        })();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initCourseAdmin);
    } else {
        initCourseAdmin();
    }
})();
