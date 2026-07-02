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

    function getRoot() {
        var marker = document.querySelector('[data-tau-recordings-section="1"]');
        if (marker) {
            // Find the closest parent that contains the section content. In Boost it might be .content or the marker's parent.
            var content = marker.closest(".content") || marker.parentElement;
            if (content) return content;
        }
        return document.querySelector("#region-main") || document.querySelector("[role='main']");
    }

    function buildModal() {
        if (document.getElementById("tau-recordings-modal")) {
            return document.getElementById("tau-recordings-modal");
        }

        var modal = document.createElement("div");
        modal.id = "tau-recordings-modal";
        modal.className = "tau-recordings-modal";
        modal.innerHTML =
            '<div class="tau-recordings-modal__backdrop" data-action="close"></div>' +
            '<div class="tau-recordings-modal__dialog" role="dialog" aria-modal="true" aria-label="Reproductor de clase">' +
                '<button type="button" class="tau-recordings-modal__close" data-action="close" aria-label="Cerrar">&times;</button>' +
                '<div class="tau-recordings-modal__framewrap">' +
                    '<iframe class="tau-recordings-modal__frame" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe>' +
                '</div>' +
                '<div class="tau-recordings-modal__footer">' +
                    '<a class="tau-recordings-modal__external" target="_blank" rel="noopener">Abrir enlace original</a>' +
                '</div>' +
            '</div>';

        modal.addEventListener("click", function(e) {
            if (e.target.getAttribute("data-action") === "close") {
                closeModal();
            }
        });

        document.body.appendChild(modal);
        return modal;
    }

    function openModal(embedurl, videourl) {
        var modal = buildModal();
        var frame = modal.querySelector(".tau-recordings-modal__frame");
        var external = modal.querySelector(".tau-recordings-modal__external");
        frame.src = embedurl || videourl;
        external.href = videourl;
        modal.classList.add("is-open");
        document.body.classList.add("tau-recordings-modal-open");
    }

    function closeModal() {
        var modal = document.getElementById("tau-recordings-modal");
        if (!modal) {
            return;
        }
        var frame = modal.querySelector(".tau-recordings-modal__frame");
        frame.src = "";
        modal.classList.remove("is-open");
        document.body.classList.remove("tau-recordings-modal-open");
    }

    function renderRecordings() {
        var data = Array.isArray(window.tauCourseRecordingsData) ? window.tauCourseRecordingsData : [];
        var root = getRoot();
        if (!root) {
            return;
        }

        var shell = document.getElementById("tau-course-recordings");
        if (!shell) {
            shell = document.createElement("section");
            shell.id = "tau-course-recordings";
            shell.className = "tau-course-recordings";
            root.appendChild(shell);
        }

        var grouped = {};
        data.forEach(function(item) {
            if (!grouped[item.modulelabel]) {
                grouped[item.modulelabel] = {};
            }
            if (!grouped[item.modulelabel][item.sectionlabel]) {
                grouped[item.modulelabel][item.sectionlabel] = [];
            }
            grouped[item.modulelabel][item.sectionlabel].push(item);
        });

        var manageButton = "<style>.tau-streaming-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); border-color: #d8d8d8 !important; } .tau-streaming-card:active { transform: translateY(0); } @keyframes tauPulseBtn { 0% { box-shadow: 0 0 0 0 rgba(141, 24, 42, 0.6); } 70% { box-shadow: 0 0 0 10px rgba(141, 24, 42, 0); } 100% { box-shadow: 0 0 0 0 rgba(141, 24, 42, 0); } } .tau-btn-pulse { animation: tauPulseBtn 2s infinite; background-color: #8d182a !important; color: #fff !important; border: none !important; transition: all 0.2s; } .tau-btn-pulse:hover { background-color: #6e1224 !important; } .tau-help-btn { background: #fff; color: #8d182a; border-radius: 50%; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; cursor: default; border: 1px solid #8d182a; margin-left: 8px; vertical-align: middle; transition: all 0.2s; } .tau-help-wrapper { position: relative; display: inline-block; } .tau-help-wrapper::after { content: attr(data-tooltip); position: absolute; bottom: 120%; right: 0; width: 220px; background: #222; color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 500; line-height: 1.4; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.2); opacity: 0; visibility: hidden; transition: all 0.2s ease; z-index: 100; pointer-events: none; } .tau-help-wrapper:hover::after { opacity: 1; visibility: visible; bottom: 135%; } .tau-help-wrapper:hover .tau-help-btn { background: #8d182a; color: #fff; }</style>";
        if (window.tauCourseRecordingsCanManage && window.tauCourseRecordingsManageUrl) {
            manageButton += '<div style="margin-bottom:16px;text-align:right;">' +
                            '<a class="btn btn-sm tau-btn-pulse" style="border-radius:20px;font-weight:600;font-size:0.85rem;padding:6px 16px;" href="' + escapeHtml(window.tauCourseRecordingsManageUrl) + '">Gestionar grabaciones</a>' +
                            '<div class="tau-help-wrapper" data-tooltip="Este botón es para subir tus grabaciones de clase.">' +
                            '<span class="tau-help-btn">?</span>' +
                            '</div>' +
                            '</div>';
        }

        var html = manageButton;

        if (!data.length) {
            html += '<div class="tau-course-recordings__empty" style="padding:16px;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;text-align:center;color:#666;font-size:0.9rem;">' +
                (window.tauCourseRecordingsCanManage
                    ? 'Aún no hay grabaciones registradas. Usa el botón de gestión para subir el primer video.'
                    : 'Aún no hay grabaciones disponibles para este curso.') +
                '</div>';
            shell.innerHTML = html;
            return;
        }

        html += '<div class="tau-course-recordings__groups" style="display:flex;flex-direction:column;gap:16px;">';
        Object.keys(grouped).forEach(function(modulelabel) {
            html += '<div class="tau-course-recordings__module" style="background:#fff;border:1px solid rgba(0,0,0,0.06);border-radius:12px;padding:16px;box-shadow:0 2px 8px rgba(0,0,0,0.02);">';
            html += '<h4 style="margin:0 0 12px;font-size:1rem;font-weight:800;color:#8d182a;">' + escapeHtml(modulelabel) + '</h4>';

            Object.keys(grouped[modulelabel]).forEach(function(sectionlabel) {
                html += '<div class="tau-course-recordings__section" style="margin-bottom:12px;">';
                if (sectionlabel !== "") {
                    html += '<div style="font-size:0.85rem;font-weight:700;color:#555;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.04em;">' + escapeHtml(sectionlabel) + '</div>';
                }
                html += '<div class="tau-course-recordings__cards" style="display:flex;flex-direction:column;gap:12px;">';

                grouped[modulelabel][sectionlabel].forEach(function(item) {
                    var provider = item.provider === "youtube" ? "YouTube" : (item.provider === "drive" ? "Google Drive" : "Video");
                    
                    var thumbUrl = "";
                    if (item.provider === "youtube") {
                        var match = item.embedurl.match(/\/embed\/([^?]+)/);
                        if (match && match[1]) {
                            thumbUrl = "https://img.youtube.com/vi/" + match[1] + "/hqdefault.jpg";
                        }
                    }

                    var thumbHtml = "";
                    if (thumbUrl) {
                        thumbHtml = '<div style="width:160px;height:90px;border-radius:10px;background:url(' + thumbUrl + ') center/cover;position:relative;flex-shrink:0;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.1);">' +
                                      '<div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:36px;height:36px;background:rgba(0,0,0,0.75);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;box-shadow:0 4px 12px rgba(0,0,0,0.3);"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="8 5 19 12 8 19"></polygon></svg></div>' +
                                    '</div>';
                    } else {
                        var icon = item.provider === "youtube" ? "▶" : "☁";
                        thumbHtml = '<div style="width:160px;height:90px;border-radius:10px;background:linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);color:#8d182a;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:2rem;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05);">' + icon + '</div>';
                    }

                    html += '<article class="tau-course-recordings__play tau-streaming-card" data-embedurl="' + escapeHtml(item.embedurl) + '" data-videourl="' + escapeHtml(item.videourl) + '" style="display:flex;align-items:center;justify-content:space-between;gap:20px;padding:16px;background:#fff;border:1px solid #ebebeb;border-radius:14px;cursor:pointer;transition:all 0.2s ease;">' +
                        '<div style="flex-grow:1;min-width:0;padding-left:4px;">' +
                            '<div style="font-size:0.85rem;font-weight:700;color:#c62b3a;text-transform:uppercase;margin-bottom:6px;letter-spacing:0.02em;">' + escapeHtml(provider) + '</div>' +
                            '<h5 style="margin:0 0 8px;font-size:1.15rem;font-weight:800;color:#222;line-height:1.4;">' + escapeHtml(item.title) + '</h5>' +
                            '<div style="font-size:0.9rem;font-weight:500;color:#666;display:flex;align-items:center;gap:6px;">' +
                                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> Reproducir ahora' +
                            '</div>' +
                        '</div>' +
                        thumbHtml +
                    '</article>';
                });

                html += '</div></div>';
            });

            html += '</div>';
        });
        html += '</div>';

        shell.innerHTML = html;
    }

    document.addEventListener("click", function(e) {
        var trigger = e.target.closest(".tau-course-recordings__play");
        if (trigger) {
            openModal(trigger.getAttribute("data-embedurl"), trigger.getAttribute("data-videourl"));
        }
    });

    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", renderRecordings);
    } else {
        renderRecordings();
    }
})();
