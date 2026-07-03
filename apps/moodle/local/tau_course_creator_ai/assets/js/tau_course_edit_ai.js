/* E-TAU Campus Virtual - Course Edit AI Resource Creator */
(document.getElementById('mform1') || document.querySelector('form.mform') ? (function() {
    "use strict";

    function initCourseEditAI() {
        var match = window.location.search.match(/[?&]id=(\d+)/);
        var courseId = match ? parseInt(match[1], 10) : null;
        if (!courseId) return;

        var form = document.querySelector('form.mform') || document.getElementById('mform1');
        if (!form) return;

        if (document.getElementById('id_category_ai_resources')) return;

        var insertAfterEl = document.getElementById('id_category_1') || 
                            form.querySelector('fieldset:last-of-type') || 
                            form.querySelector('.fcontainer fieldset:last-child');
        if (!insertAfterEl) return;

        var fieldset = document.createElement('fieldset');
        fieldset.className = 'clearfix collapsible collapsed';
        fieldset.id = 'id_category_ai_resources';

        var fieldsetHtml = `
            <legend class="visually-hidden">Crear Recursos con IA</legend>
            <div class="d-flex align-items-center mb-2">
                <div class="position-relative d-flex ftoggler align-items-center position-relative me-1">
                    <a data-bs-toggle="collapse" href="#id_category_ai_resources_container" role="button" aria-expanded="false" aria-controls="id_category_ai_resources_container" class="btn btn-icon me-3 icons-collapse-expand stretched-link fheader collapsed" id="tau-ai-chevron">
                        <span class="expanded-icon icon-no-margin p-2" title="Colapsar">
                            <i class="icon fa fa-chevron-down fa-fw" aria-hidden="true" style="color: #c62b3a;"></i>
                        </span>
                        <span class="collapsed-icon icon-no-margin p-2" title="Expandir">
                            <span class="dir-rtl-hide"><i class="icon fa fa-chevron-right fa-fw" aria-hidden="true" style="color: #c62b3a;"></i></span>
                            <span class="dir-ltr-hide"><i class="icon fa fa-chevron-left fa-fw" aria-hidden="true" style="color: #c62b3a;"></i></span>
                        </span>
                        <span class="visually-hidden">Crear Recursos con IA</span>
                    </a>
                    <h3 class="d-flex align-self-stretch align-items-center mb-0" aria-hidden="true">
                        Crear Recursos con IA
                    </h3>
                </div>
            </div>
            <div id="id_category_ai_resources_container" class="fcontainer collapse" style="padding-left: 15px; padding-right: 15px;">
                <div style="border: 2px dashed #c62b3a; border-radius: 12px; padding: 25px; background-color: #fffafb; margin: 15px 0 25px 0;">
                    <p class="mb-4" style="font-size: 0.92rem; line-height: 1.6; color: #495057;">
                        Selecciona el módulo o sección donde deseas insertar el material educativo, escribe las instrucciones o temática y la IA generará páginas interactivas de contenido, un cuestionario y tareas prácticas de forma automática.
                    </p>
                    
                    <div class="mb-3">
                        <label for="tau-ai-section-select" style="font-weight: 700; color: #343a40; display: block; margin-bottom: 8px; font-size: 0.9rem;">
                            Módulo o Sección de Destino
                        </label>
                        <select id="tau-ai-section-select" class="form-control custom-select" style="width: 100%; height: auto; padding: 10px 15px; border-radius: 8px; border: 1px solid #ced4da;">
                            <option value="0">Cargando secciones...</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="tau-ai-prompt" style="font-weight: 700; color: #343a40; display: block; margin-bottom: 8px; font-size: 0.9rem;">
                            ¿Qué recursos deseas crear?
                        </label>
                        <textarea id="tau-ai-prompt" class="form-control" rows="4" style="width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid #ced4da; font-size: 0.92rem;" placeholder="Ej: Genera una lección y un cuestionario sobre Introducción a la Programación Orientada a Objetos, clases, instancias y herencia."></textarea>
                    </div>

                    <button type="button" id="tau-ai-generate-btn" class="btn btn-primary w-100" style="background-color: #c62b3a; border-color: #c62b3a; font-weight: 800; padding: 14px 20px; border-radius: 8px; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(198, 43, 58, 0.15);">
                        <i class="icon fa fa-magic fa-fw" aria-hidden="true" style="color: #ffffff; margin: 0;"></i> Generar Recursos con Inteligencia Artificial
                    </button>
                    
                    <div id="tau-ai-loading" style="display: none; margin-top: 20px; align-items: center; gap: 10px; justify-content: center;">
                        <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true" style="color: #c62b3a !important; display: inline-block;"></span>
                        <span id="tau-ai-status-text" style="font-weight: 600; color: #c62b3a; font-size: 0.9rem;">Creando recursos... Por favor espera un momento.</span>
                    </div>

                    <div id="tau-ai-result-msg" style="display: none; margin-top: 20px; padding: 15px; border-radius: 8px; font-weight: 600; text-align: center;"></div>
                </div>
            </div>
        `;

        fieldset.innerHTML = fieldsetHtml;
        insertAfterEl.parentNode.insertBefore(fieldset, insertAfterEl.nextSibling);

        var container = document.getElementById('id_category_ai_resources_container');
        var chevron = document.getElementById('tau-ai-chevron');

        container.addEventListener('show.bs.collapse', function() {
            fieldset.classList.remove('collapsed');
            chevron.classList.remove('collapsed');
            chevron.setAttribute('aria-expanded', 'true');
            loadCourseSections(courseId);
        });

        container.addEventListener('hide.bs.collapse', function() {
            fieldset.classList.add('collapsed');
            chevron.classList.add('collapsed');
            chevron.setAttribute('aria-expanded', 'false');
        });


        // Generate resources Event Listener
        var genBtn = document.getElementById('tau-ai-generate-btn');
        genBtn.addEventListener('click', function() {
            var promptEl = document.getElementById('tau-ai-prompt');
            var sectionSelect = document.getElementById('tau-ai-section-select');
            var loadingEl = document.getElementById('tau-ai-loading');
            var statusTextEl = document.getElementById('tau-ai-status-text');
            var resultEl = document.getElementById('tau-ai-result-msg');

            var promptText = promptEl.value.trim();
            if (!promptText) {
                alert('Por favor, escribe la temática o contenido que deseas generar.');
                promptEl.focus();
                return;
            }

            promptEl.disabled = true;
            sectionSelect.disabled = true;
            genBtn.disabled = true;
            resultEl.style.display = 'none';
            loadingEl.style.display = 'flex';

            var sesskey = M.cfg.sesskey;
            var payload = {
                action: 'generatewithia',
                courseid: courseId,
                section: parseInt(sectionSelect.value, 10),
                prompt: promptText,
                sesskey: sesskey
            };

            statusTextEl.textContent = 'Contactando proveedor de IA y redactando recursos...';

            fetch('/local/tau_course_creator_ai/ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(function(response) {
                if (!response.ok) {
                    return response.json().then(function(err) {
                        throw new Error(err.error || 'Error en el servidor de IA.');
                    });
                }
                return response.json();
            })
            .then(function(data) {
                loadingEl.style.display = 'none';
                
                resultEl.className = 'alert alert-success';
                resultEl.style.backgroundColor = '#d4edda';
                resultEl.style.color = '#155724';
                resultEl.style.border = '1px solid #c3e6cb';
                resultEl.innerHTML = `
                    <div class="d-flex align-items-center">
                        <span style="font-size: 1.5rem; margin-right: 10px;">✅</span>
                        <div>
                            <strong>¡Éxito!</strong> Recursos creados satisfactoriamente.<br>
                            <a href="/course/view.php?id=${courseId}" class="btn btn-sm btn-success mt-2" style="background:#28a745; border:none; font-weight:700;">Ver Recursos en el Curso</a>
                        </div>
                    </div>
                `;
                resultEl.style.display = 'block';
                promptEl.value = '';
            })
            .catch(function(error) {
                loadingEl.style.display = 'none';

                resultEl.className = 'alert alert-danger';
                resultEl.style.backgroundColor = '#f8d7da';
                resultEl.style.color = '#721c24';
                resultEl.style.border = '1px solid #f5c6cb';
                resultEl.innerHTML = '<strong>Error:</strong> ' + error.message;
                resultEl.style.display = 'block';
            })
            .finally(function() {
                promptEl.disabled = false;
                sectionSelect.disabled = false;
                genBtn.disabled = false;
            });
        });
    }

    var sectionsLoaded = false;
    function loadCourseSections(courseId) {
        if (sectionsLoaded) return;

        var select = document.getElementById('tau-ai-section-select');
        var sesskey = M.cfg.sesskey;

        fetch('/local/tau_course_creator_ai/ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'getsections',
                courseid: courseId,
                sesskey: sesskey
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.ok && data.sections) {
                select.innerHTML = '';
                data.sections.forEach(function(sec) {
                    var opt = document.createElement('option');
                    opt.value = sec.section;
                    opt.textContent = sec.name;
                    select.appendChild(opt);
                });
                sectionsLoaded = true;
            } else {
                select.innerHTML = '<option value="0">Error al cargar secciones</option>';
            }
        })
        .catch(function() {
            select.innerHTML = '<option value="0">Error de conexión</option>';
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initCourseEditAI);
    } else {
        initCourseEditAI();
    }
})() : null);
