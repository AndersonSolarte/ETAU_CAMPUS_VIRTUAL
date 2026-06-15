<?php
defined('MOODLE_INTERNAL') || die();

class block_tau_recommended_courses extends block_base {

    public function init(): void {
        $this->title = get_string('pluginname', 'block_tau_recommended_courses');
    }

    public function applicable_formats(): array {
        return ['my' => true, 'site' => true, 'course-view' => true];
    }

    public function has_config(): bool {
        return false;
    }

    public function get_content(): stdClass {
        global $CFG, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $userid    = $USER->id;
        $ajax_url  = $CFG->wwwroot . '/blocks/tau_recommended_courses/recommend.php';
        $sesskey   = sesskey();

        ob_start();
        ?>
        <div class="tau-recommended-block" id="tau-rec-<?php echo $userid; ?>">
          <div class="text-center py-3" id="tau-rec-loading-<?php echo $userid; ?>">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <p class="small text-muted mt-1"><?php echo get_string('no_recommendations', 'block_tau_recommended_courses'); ?></p>
          </div>
          <div id="tau-rec-content-<?php echo $userid; ?>" style="display:none;"></div>
        </div>

        <style>
        .tau-rec-card{border:1px solid #eee;border-radius:8px;padding:10px;margin-bottom:8px;transition:box-shadow .2s}
        .tau-rec-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.1)}
        .tau-rec-title{font-weight:600;font-size:.9rem;margin-bottom:4px;color:#333}
        .tau-rec-reason{font-size:.78rem;color:#666;margin-bottom:6px}
        .tau-rec-match{font-size:.78rem}
        .tau-rec-btn{font-size:.78rem;padding:3px 10px;border-radius:4px;background:#c62b3a;color:#fff;border:none;text-decoration:none}
        .tau-rec-btn:hover{background:#a32230;color:#fff}
        </style>

        <script>
        (function(){
          const uid  = <?php echo $userid; ?>;
          const AJAX = '<?php echo $ajax_url; ?>';
          const SK   = '<?php echo $sesskey; ?>';

          (async function load() {
            try {
              const res = await fetch(AJAX, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sesskey: SK, userid: uid }),
              });
              const data = await res.json();
              document.getElementById('tau-rec-loading-' + uid).style.display = 'none';

              const container = document.getElementById('tau-rec-content-' + uid);
              const courses   = data.recommendations || [];
              if (!courses.length) {
                container.innerHTML = '<p class="small text-muted">Sin recomendaciones por ahora.</p>';
              } else {
                container.innerHTML = courses.map(c => `
                  <div class="tau-rec-card">
                    <div class="tau-rec-title">${escapeHtml(c.courseName)}</div>
                    <div class="tau-rec-reason">${escapeHtml(c.reason)}</div>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="tau-rec-match badge bg-success">${c.matchScore || 0}% compatible</span>
                      <a href="${escapeHtml(c.courseUrl || '#')}" class="tau-rec-btn">
                        <?php echo get_string('enroll_now', 'block_tau_recommended_courses'); ?>
                      </a>
                    </div>
                  </div>`).join('');
              }
              container.style.display = '';
            } catch(e) {
              document.getElementById('tau-rec-loading-' + uid).innerHTML =
                '<p class="small text-danger">Error cargando recomendaciones.</p>';
            }
          })();

          function escapeHtml(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
        })();
        </script>
        <?php
        $this->content->text   = ob_get_clean();
        $this->content->footer = '';
        return $this->content;
    }
}
