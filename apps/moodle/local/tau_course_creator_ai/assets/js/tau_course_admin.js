/* TAU Campus Virtual - Course Admin Buttons Injections */
(function() {
    "use strict";

    // Reusable Custom Styled Modals (replacing basic browser alerts/confirms)
    function showTauConfirm(title, message, onConfirm, onCancel) {
        var backdrop = document.createElement('div');
        backdrop.className = 'tau-modal-backdrop';
        backdrop.innerHTML = `
            <div class="tau-modal-box">
                <div class="tau-modal-icon tau-modal-icon-danger">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                </div>
                <div class="tau-modal-title">${title}</div>
                <div class="tau-modal-message">${message}</div>
                <div class="tau-modal-buttons">
                    <button class="tau-modal-btn tau-modal-btn-cancel" id="tau-confirm-cancel">Cancelar</button>
                    <button class="tau-modal-btn tau-modal-btn-danger" id="tau-confirm-ok">Eliminar</button>
                </div>
            </div>
        `;
        document.body.appendChild(backdrop);
        backdrop.offsetHeight; // Force reflow
        backdrop.classList.add('show');

        backdrop.querySelector('#tau-confirm-cancel').addEventListener('click', function() {
            closeModal(backdrop, onCancel);
        });
        backdrop.querySelector('#tau-confirm-ok').addEventListener('click', function() {
            closeModal(backdrop, onConfirm);
        });
    }

    function showTauAlert(title, message, type, onOk) {
        var iconClass = type === 'success' ? 'tau-modal-icon-success' : 'tau-modal-icon-danger';
        var iconHtml = type === 'success' ? 
            `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>` : 
            `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;

        var btnClass = type === 'success' ? 'tau-modal-btn-primary' : 'tau-modal-btn-danger';

        var backdrop = document.createElement('div');
        backdrop.className = 'tau-modal-backdrop';
        backdrop.innerHTML = `
            <div class="tau-modal-box">
                <div class="tau-modal-icon ${iconClass}">
                    ${iconHtml}
                </div>
                <div class="tau-modal-title">${title}</div>
                <div class="tau-modal-message">${message}</div>
                <div class="tau-modal-buttons">
                    <button class="tau-modal-btn ${btnClass}" id="tau-alert-ok">Aceptar</button>
                </div>
            </div>
        `;
        document.body.appendChild(backdrop);
        backdrop.offsetHeight; // Force reflow
        backdrop.classList.add('show');

        backdrop.querySelector('#tau-alert-ok').addEventListener('click', function() {
            closeModal(backdrop, onOk);
        });
    }

    function showTauLoading(message) {
        var backdrop = document.createElement('div');
        backdrop.id = 'tau-loading-overlay';
        backdrop.className = 'tau-modal-backdrop';
        backdrop.innerHTML = `
            <div class="tau-modal-box" style="padding: 40px !important;">
                <div class="tau-spinner"></div>
                <div class="tau-modal-title" style="margin-bottom: 8px !important;">Por favor, espere</div>
                <div class="tau-modal-message" style="margin-bottom: 0 !important;">${message}</div>
            </div>
        `;
        document.body.appendChild(backdrop);
        backdrop.offsetHeight;
        backdrop.classList.add('show');
    }

    function hideTauLoading() {
        var overlay = document.getElementById('tau-loading-overlay');
        if (overlay) {
            overlay.classList.remove('show');
            setTimeout(function() {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 250);
        }
    }

    function closeModal(modalEl, callback) {
        modalEl.classList.remove('show');
        setTimeout(function() {
            if (modalEl.parentNode) {
                modalEl.parentNode.removeChild(modalEl);
            }
            if (typeof callback === 'function') {
                callback();
            }
        }, 250);
    }

    function initCourseAdmin() {
        var path = window.location.pathname;
        
        // Only run on course index/management/my pages
        if (!(path === '/course/index.php' || path === '/course/management.php' || path === '/' || path === '/my/' || path === '/my/index.php' || path === '/my/courses.php')) {
            return;
        }

        function injectCustomStyles() {
            if (document.getElementById('tau-management-custom-css')) {
                return;
            }
            var style = document.createElement('style');
            style.id = 'tau-management-custom-css';
            style.textContent = `
                /* Stack columns vertically */
                .grid-start.row {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 30px !important;
                }

                .grid-start.row > .grid_column_start {
                    flex: 0 0 100% !important;
                    max-width: 100% !important;
                    width: 100% !important;
                }

                /* Modern Card Styling */
                .category-listing.card, .course-listing.card {
                    border: 1px solid #eaeaea !important;
                    border-radius: 12px !important;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04) !important;
                    background: #ffffff !important;
                    overflow: hidden !important;
                    transition: all 0.3s ease !important;
                    padding: 0 !important;
                }

                /* Card Header Styling */
                .category-listing.card .card-header, .course-listing.card .card-header {
                    background: #ffffff !important;
                    border-bottom: 2px solid #f4f5f7 !important;
                    padding: 20px 24px !important;
                    font-size: 1.3rem !important;
                    font-weight: 700 !important;
                    color: #c62b3a !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                }

                /* Card Body Styling */
                .category-listing.card .card-body, .course-listing.card .card-body {
                    padding: 24px !important;
                }

                /* Clean up Category list items */
                .category-listing .category-list .listitem {
                    border: none !important;
                    background: transparent !important;
                    box-shadow: none !important;
                    padding: 0 !important;
                    margin-bottom: 8px !important;
                }

                .category-listing .category-list .listitem > .clearfix {
                    border: 1px solid #f1f3f5 !important;
                    border-radius: 8px !important;
                    padding: 12px 16px !important;
                    background: #ffffff !important;
                    transition: all 0.2s ease !important;
                    display: flex !important;
                    align-items: center !important;
                    gap: 12px !important;
                    box-sizing: border-box !important;
                }

                .category-listing .category-list .listitem > .clearfix:hover {
                    background: #fdf8f9 !important;
                    border-color: #f5c2c6 !important;
                    transform: translateY(-1.5px) !important;
                    box-shadow: 0 4px 12px rgba(198, 43, 58, 0.04) !important;
                }

                /* Category Checkboxes styling */
                .category-listing .category-list input[name="bcat[]"] {
                    width: 18px !important;
                    height: 18px !important;
                    cursor: pointer !important;
                    accent-color: #c62b3a !important;
                    margin-top: 0 !important;
                }

                /* Handle nested list padding and layout tree */
                .category-listing .category-list .category-list {
                    margin-top: 8px !important;
                    padding-left: 24px !important;
                    border-left: 2px dashed #eaeaea !important;
                }

                /* Clean up Course list items */
                .course-listing .course-list .listitem {
                    border: 1px solid #f1f3f5 !important;
                    border-radius: 8px !important;
                    margin-bottom: 8px !important;
                    padding: 14px 18px !important;
                    transition: all 0.2s ease !important;
                    background: #ffffff !important;
                }

                .course-listing .course-list .listitem:hover {
                    background: #fdf8f9 !important;
                    border-color: #f5c2c6 !important;
                    transform: translateY(-1px) !important;
                }

                /* Active category highlights */
                .category-listing .category-list .listitem[data-selected="1"] > .clearfix {
                    background: #fff2f3 !important;
                    border-color: #c62b3a !important;
                    font-weight: 700 !important;
                }

                /* Custom Modal Backdrop */
                .tau-modal-backdrop {
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 100vw !important;
                    height: 100vh !important;
                    background: rgba(0, 0, 0, 0.4) !important;
                    backdrop-filter: blur(4px) !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    z-index: 999999 !important;
                    opacity: 0;
                    transition: opacity 0.25s ease-in-out !important;
                }

                .tau-modal-backdrop.show {
                    opacity: 1;
                }

                /* Custom Modal Box */
                .tau-modal-box {
                    background: #ffffff !important;
                    border-radius: 16px !important;
                    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.15) !important;
                    max-width: 480px !important;
                    width: 90% !important;
                    padding: 30px !important;
                    text-align: center !important;
                    transform: scale(0.9) !important;
                    transition: transform 0.25s ease-in-out !important;
                    box-sizing: border-box !important;
                }

                .tau-modal-backdrop.show .tau-modal-box {
                    transform: scale(1) !important;
                }

                /* Modal Icon */
                .tau-modal-icon {
                    width: 60px !important;
                    height: 60px !important;
                    margin: 0 auto 20px auto !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    border-radius: 50% !important;
                }

                .tau-modal-icon-danger {
                    background: #fff2f3 !important;
                    color: #c62b3a !important;
                }

                .tau-modal-icon-success {
                    background: #e6fcf5 !important;
                    color: #0ca678 !important;
                }

                /* Modal Typography */
                .tau-modal-title {
                    font-size: 1.25rem !important;
                    font-weight: 700 !important;
                    color: #212529 !important;
                    margin-bottom: 12px !important;
                }

                .tau-modal-message {
                    font-size: 0.95rem !important;
                    color: #495057 !important;
                    line-height: 1.5 !important;
                    margin-bottom: 24px !important;
                }

                /* Modal Buttons Row */
                .tau-modal-buttons {
                    display: flex !important;
                    justify-content: center !important;
                    gap: 12px !important;
                }

                .tau-modal-btn {
                    height: 42px !important;
                    padding: 0 24px !important;
                    border-radius: 8px !important;
                    font-size: 0.9rem !important;
                    font-weight: 700 !important;
                    border: none !important;
                    cursor: pointer !important;
                    transition: all 0.2s !important;
                }

                .tau-modal-btn-cancel {
                    background: #f1f3f5 !important;
                    color: #495057 !important;
                }

                .tau-modal-btn-cancel:hover {
                    background: #e9ecef !important;
                }

                .tau-modal-btn-danger {
                    background: #c62b3a !important;
                    color: #ffffff !important;
                }

                .tau-modal-btn-danger:hover {
                    background: #b02230 !important;
                }

                .tau-modal-btn-primary {
                    background: #0ca678 !important;
                    color: #ffffff !important;
                }

                .tau-modal-btn-primary:hover {
                    background: #099268 !important;
                }

                /* Custom Loading Spinner */
                .tau-spinner {
                    width: 40px !important;
                    height: 40px !important;
                    border: 4px solid #f1f3f5 !important;
                    border-top: 4px solid #c62b3a !important;
                    border-radius: 50% !important;
                    animation: tau-spin 1s linear infinite !important;
                    margin: 0 auto 20px auto !important;
                }

                @keyframes tau-spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }

        if (path === '/course/management.php') {
            injectCustomStyles();
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

        // Inject Bulk Toolbar (Select All & Delete Selected) on course management page
        (function() {
            var isBulkUpdating = false;

            var toolbarInterval = setInterval(function() {
                var path = window.location.pathname;
                if (path !== '/course/management.php') {
                    clearInterval(toolbarInterval);
                    return;
                }
                
                var courseList = document.querySelector('.course-listing .course-list');
                if (!courseList) return; // Wait until course list exists

                // If toolbar already exists, just update state and exit
                if (document.getElementById('tau-course-bulk-toolbar')) {
                    return;
                }

                var toolbar = document.createElement('div');
                toolbar.id = 'tau-course-bulk-toolbar';
                toolbar.className = 'd-flex align-items-center justify-content-between p-3 mb-3 bg-light border rounded';
                toolbar.style.setProperty('font-size', '0.9rem', 'important');
                toolbar.style.setProperty('gap', '12px', 'important');
                toolbar.style.setProperty('border-radius', '8px', 'important');

                toolbar.innerHTML = `
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <div class="form-check d-flex align-items-center mb-0">
                            <input type="checkbox" id="tau-select-all-courses" class="form-check-input" style="margin-top: 0; cursor: pointer; width: 18px; height: 18px; accent-color: #c62b3a;">
                            <label for="tau-select-all-courses" class="form-check-label ms-2 fw-bold" style="cursor: pointer; user-select: none; margin-bottom: 0; color: #495057;">Seleccionar todos</label>
                        </div>
                        <span id="tau-selected-courses-count" class="badge bg-secondary d-none" style="font-size: 0.8rem; padding: 5px 10px; border-radius: 12px; background-color: #6c757d !important; color: white;">0 seleccionados</span>
                    </div>
                    <button type="button" id="tau-bulk-delete-btn" class="btn btn-danger d-none" style="background-color: #c62b3a !important; border-color: #c62b3a !important; color: #ffffff !important; height: 38px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; padding: 0 16px; display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: background-color 0.2s;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Borrar seleccionados
                    </button>
                `;

                // Insert the toolbar right before the course list
                courseList.parentNode.insertBefore(toolbar, courseList);

                var selectAllCb = toolbar.querySelector('#tau-select-all-courses');
                var deleteBtn = toolbar.querySelector('#tau-bulk-delete-btn');
                var countBadge = toolbar.querySelector('#tau-selected-courses-count');

                function updateBulkState() {
                    if (isBulkUpdating) return;

                    // Checkboxes inside the current list
                    var checkboxes = courseList.querySelectorAll('input[name="bc[]"]');
                    var checked = courseList.querySelectorAll('input[name="bc[]"]:checked');
                    
                    if (checkboxes.length === 0) {
                        toolbar.style.setProperty('display', 'none', 'important');
                        return;
                    } else {
                        toolbar.style.setProperty('display', 'flex', 'important');
                    }

                    countBadge.textContent = checked.length + ' seleccionado' + (checked.length !== 1 ? 's' : '');
                    if (checked.length > 0) {
                        countBadge.classList.remove('d-none');
                        deleteBtn.classList.remove('d-none');
                    } else {
                        countBadge.classList.add('d-none');
                        deleteBtn.classList.add('d-none');
                    }

                    selectAllCb.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
                }

                selectAllCb.addEventListener('change', function() {
                    var targetState = selectAllCb.checked;
                    isBulkUpdating = true;
                    var checkboxes = courseList.querySelectorAll('input[name="bc[]"]');
                    checkboxes.forEach(function(cb) {
                        if (cb.checked !== targetState) {
                            cb.click();
                        }
                    });
                    isBulkUpdating = false;
                    updateBulkState();
                });

                // Monitor individual checkbox changes
                courseList.addEventListener('change', function(e) {
                    if (e.target && e.target.name === 'bc[]') {
                        updateBulkState();
                    }
                });

                // Periodically update state to sync with external/YUI actions if any
                var syncInterval = setInterval(function() {
                    if (!document.getElementById('tau-course-bulk-toolbar') || !courseList.parentNode) {
                        clearInterval(syncInterval);
                        return;
                    }
                    updateBulkState();
                }, 1000);

                deleteBtn.addEventListener('click', function() {
                    var checked = courseList.querySelectorAll('input[name="bc[]"]:checked');
                    if (checked.length === 0) {
                        showTauAlert('Atención', 'Por favor, selecciona al menos un curso para borrar.', 'error');
                        return;
                    }
                    
                    var ids = Array.from(checked).map(function(cb) { return cb.value; });
                    var confirmMsg = '¿Está seguro de que desea borrar los ' + ids.length + ' cursos seleccionados y todo su contenido de forma permanente? Esta acción no se puede deshacer y el proceso puede tardar unos segundos.';
                    
                    showTauConfirm('Confirmar eliminación', confirmMsg, async function() {
                        showTauLoading('Eliminando cursos seleccionados de forma permanente... Por favor, espere.');
                        
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
                            hideTauLoading();
                            
                            if (!response.ok || result.error) {
                                throw new Error(result.error || 'Error en la petición de borrado.');
                            }
                            
                            if (result.errors && result.errors.length > 0) {
                                showTauAlert('Error al borrar', 'Algunos cursos no se pudieron borrar:\n' + result.errors.join('\n'), 'error', function() {
                                    window.location.reload();
                                });
                            } else {
                                showTauAlert('Éxito', '¡Cursos borrados exitosamente!', 'success', function() {
                                    window.location.reload();
                                });
                            }
                        } catch (err) {
                            hideTauLoading();
                            showTauAlert('Error', 'Error al borrar los cursos: ' + err.message, 'error');
                        }
                    });
                });

                // Initial update
                updateBulkState();

            }, 500);
        })();

        // Inject Bulk Toolbar (Select All & Delete Selected) for Categories on course management page
        (function() {
            var isCatBulkUpdating = false;

            var catToolbarInterval = setInterval(function() {
                var path = window.location.pathname;
                if (path !== '/course/management.php') {
                    clearInterval(catToolbarInterval);
                    return;
                }
                
                var categoryList = document.querySelector('.category-listing .category-list');
                if (!categoryList) return; // Wait until category list exists

                // If toolbar already exists, just update state and exit
                if (document.getElementById('tau-category-bulk-toolbar')) {
                    return;
                }

                var catToolbar = document.createElement('div');
                catToolbar.id = 'tau-category-bulk-toolbar';
                catToolbar.className = 'd-flex align-items-center justify-content-between p-3 mb-3 bg-light border rounded';
                catToolbar.style.setProperty('font-size', '0.9rem', 'important');
                catToolbar.style.setProperty('gap', '12px', 'important');
                catToolbar.style.setProperty('border-radius', '8px', 'important');

                catToolbar.innerHTML = `
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <div class="form-check d-flex align-items-center mb-0">
                            <input type="checkbox" id="tau-select-all-categories" class="form-check-input" style="margin-top: 0; cursor: pointer; width: 18px; height: 18px; accent-color: #c62b3a;">
                            <label for="tau-select-all-categories" class="form-check-label ms-2 fw-bold" style="cursor: pointer; user-select: none; margin-bottom: 0; color: #495057;">Seleccionar todos</label>
                        </div>
                        <span id="tau-selected-categories-count" class="badge bg-secondary d-none" style="font-size: 0.8rem; padding: 5px 10px; border-radius: 12px; background-color: #6c757d !important; color: white;">0 seleccionadas</span>
                    </div>
                    <button type="button" id="tau-bulk-delete-categories-btn" class="btn btn-danger d-none" style="background-color: #c62b3a !important; border-color: #c62b3a !important; color: #ffffff !important; height: 38px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; padding: 0 16px; display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: background-color 0.2s;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: middle;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Borrar seleccionadas
                    </button>
                `;

                // Insert the toolbar right before the category list
                categoryList.parentNode.insertBefore(catToolbar, categoryList);

                var selectAllCategoriesCb = catToolbar.querySelector('#tau-select-all-categories');
                var deleteCategoriesBtn = catToolbar.querySelector('#tau-bulk-delete-categories-btn');
                var countCategoriesBadge = catToolbar.querySelector('#tau-selected-categories-count');

                function updateCatBulkState() {
                    if (isCatBulkUpdating) return;

                    var checkboxes = categoryList.querySelectorAll('input[name="bcat[]"]');
                    var checked = categoryList.querySelectorAll('input[name="bcat[]"]:checked');
                    
                    if (checkboxes.length === 0) {
                        catToolbar.style.setProperty('display', 'none', 'important');
                        return;
                    } else {
                        catToolbar.style.setProperty('display', 'flex', 'important');
                    }

                    countCategoriesBadge.textContent = checked.length + ' seleccionada' + (checked.length !== 1 ? 's' : '');
                    if (checked.length > 0) {
                        countCategoriesBadge.classList.remove('d-none');
                        deleteCategoriesBtn.classList.remove('d-none');
                    } else {
                        countCategoriesBadge.classList.add('d-none');
                        deleteCategoriesBtn.classList.add('d-none');
                    }

                    selectAllCategoriesCb.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
                }

                selectAllCategoriesCb.addEventListener('change', function() {
                    var targetState = selectAllCategoriesCb.checked;
                    isCatBulkUpdating = true;
                    var checkboxes = categoryList.querySelectorAll('input[name="bcat[]"]');
                    checkboxes.forEach(function(cb) {
                        if (cb.checked !== targetState) {
                            cb.click();
                        }
                    });
                    isCatBulkUpdating = false;
                    updateCatBulkState();
                });

                // Monitor individual checkbox changes
                categoryList.addEventListener('change', function(e) {
                    if (e.target && e.target.name === 'bcat[]') {
                        updateCatBulkState();
                    }
                });

                // Periodically update state to sync
                var syncCatInterval = setInterval(function() {
                    if (!document.getElementById('tau-category-bulk-toolbar') || !categoryList.parentNode) {
                        clearInterval(syncCatInterval);
                        return;
                    }
                    updateCatBulkState();
                }, 1000);

                deleteCategoriesBtn.addEventListener('click', function() {
                    var checked = categoryList.querySelectorAll('input[name="bcat[]"]:checked');
                    if (checked.length === 0) {
                        showTauAlert('Atención', 'Por favor, selecciona al menos una categoría para borrar.', 'error');
                        return;
                    }
                    
                    var ids = Array.from(checked).map(function(cb) { return cb.value; });
                    var confirmMsg = '¿Está seguro de que desea borrar las ' + ids.length + ' categorías seleccionadas, incluyendo todas sus subcategorías, cursos y archivos de forma permanente? Esta acción no se puede deshacer y el proceso puede tardar unos segundos.';
                    
                    showTauConfirm('Confirmar eliminación', confirmMsg, async function() {
                        showTauLoading('Eliminando categorías seleccionadas de forma permanente... Por favor, espere.');
                        
                        try {
                            var sesskey = M.cfg.sesskey;
                            var ajaxurl = '/local/tau_course_creator_ai/ajax.php';
                            var response = await fetch(ajaxurl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    action: 'bulkdeletecategories',
                                    categoryids: ids,
                                    sesskey: sesskey
                                })
                            });
                            
                            var result = await response.json();
                            hideTauLoading();
                            
                            if (!response.ok || result.error) {
                                throw new Error(result.error || 'Error en la petición de borrado.');
                            }
                            
                            if (result.errors && result.errors.length > 0) {
                                showTauAlert('Error al borrar', 'Algunas categorías no se pudieron borrar:\n' + result.errors.join('\n'), 'error', function() {
                                    window.location.reload();
                                });
                            } else {
                                showTauAlert('Éxito', '¡Categorías borradas exitosamente!', 'success', function() {
                                    window.location.reload();
                                });
                            }
                        } catch (err) {
                            hideTauLoading();
                            showTauAlert('Error', 'Error al borrar las categorías: ' + err.message, 'error');
                        }
                    });
                });

                // Initial update
                updateCatBulkState();

            }, 500);
        })();
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initCourseAdmin);
    } else {
        initCourseAdmin();
    }
})();
