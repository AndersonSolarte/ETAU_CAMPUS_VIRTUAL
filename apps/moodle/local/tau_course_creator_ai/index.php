<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$context = context_system::instance();
if (!is_siteadmin() && !has_capability('local/tau_course_creator_ai:use', $context)) {
    throw new moodle_exception('nopermissiontoimportact', 'error');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_course_creator_ai/index.php'));
$PAGE->set_title('Crear curso con IA — E-TAU Campus Virtual');
$PAGE->set_heading('Crear curso con IA');
$PAGE->set_pagelayout('standard');

$categories      = core_course_category::make_categories_list();
$ajax_url        = (new moodle_url('/local/tau_course_creator_ai/ajax.php'))->out(false);
$stream_url      = (new moodle_url('/local/tau_course_creator_ai/stream.php'))->out(false);
$sesskey         = sesskey();
$active_provider = 'openai';
$openai_key_set  = !empty(get_config('local_tau_course_creator_ai', 'openai_api_key'));
$openai_model    = get_config('local_tau_course_creator_ai', 'openai_model') ?: 'gpt-4o';
$api_key_set     = $openai_key_set;
$prefill_prompt = optional_param('prompt', '', PARAM_TEXT);

echo $OUTPUT->header();
?>
<style>
/* ════════════════════════════════════════════
   TAU AI Course Creator — Wizard UI
   ════════════════════════════════════════════ */
.tau-creator { max-width: 900px; margin: 0 auto; padding: 0 0 80px; }

/* ── Custom Tab System ── */
.tau-tabs {
    display: flex; gap: 10px; margin: 0 0 22px; border-bottom: 2px solid #f0f0f0; padding-bottom: 12px;
}
[data-bs-theme="dark"] .tau-tabs { border-color: #2a2a3e; }
.tau-tab-btn {
    flex: 1; padding: 12px 18px; border-radius: 10px; border: 1.5px solid #e0e0e0;
    background: #fafafa; color: #666; font-weight: 700; font-size: .88rem;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s ease;
}
[data-bs-theme="dark"] .tau-tab-btn { background: #2a2a3e; border-color: #3a3a4e; color: #aaa; }
.tau-tab-btn:hover { border-color: #c62b3a; color: #c62b3a; background: #fff8f9; }
[data-bs-theme="dark"] .tau-tab-btn:hover { background: #2f1b20; }
.tau-tab-btn.active {
    background: linear-gradient(135deg, #c62b3a 0%, #8d182a 100%);
    color: #fff; border-color: #c62b3a; box-shadow: 0 4px 14px rgba(198, 43, 58, 0.25);
}
.tau-tab-content { display: none; }
.tau-tab-content.active { display: block; }

/* ── Wizard step indicator ── */
.tau-wizard-nav {
    display: flex; align-items: center;
    padding: 22px 28px; border-bottom: 1px solid #f0f0f0;
}
[data-bs-theme="dark"] .tau-wizard-nav { border-color: #2a2a3e; }
.tau-wstep { display: flex; align-items: center; gap: 9px; }
.tau-wstep-dot {
    width: 32px; height: 32px; border-radius: 50%;
    background: #e8e8e8; color: #aaa; font-weight: 700; font-size: .82rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    transition: background .3s, color .3s;
}
[data-bs-theme="dark"] .tau-wstep-dot { background: #2a2a3e; color: #666; }
.tau-wstep.active .tau-wstep-dot  { background: #c62b3a; color: #fff; }
.tau-wstep.done   .tau-wstep-dot  { background: #2e7d32; color: #fff; }
.tau-wstep-label { font-size: .8rem; font-weight: 600; color: #bbb; white-space: nowrap; }
.tau-wstep.active .tau-wstep-label { color: #c62b3a; }
.tau-wstep.done   .tau-wstep-label { color: #2e7d32; }
.tau-wstep-line {
    flex: 1; height: 2px; background: #e8e8e8; margin: 0 12px;
    transition: background .3s; min-width: 20px;
}
[data-bs-theme="dark"] .tau-wstep-line { background: #2a2a3e; }
.tau-wstep-line.done { background: #2e7d32; }

/* ── Card ── */
.tau-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.09); overflow: hidden; margin-bottom: 20px; }
[data-bs-theme="dark"] .tau-card { background: #1e1e2e; }
.tau-card-header { background: linear-gradient(135deg, #1a1a2e 0%, #c62b3a 100%); color: #fff; padding: 22px 28px 18px; }
.tau-card-header h2 { font-size: 1.2rem; font-weight: 800; margin: 0 0 4px; color: #fff; }
.tau-card-header p  { font-size: .87rem; opacity: .85; margin: 0; }
.tau-card-body  { padding: 26px 28px; }
.tau-card-footer{ padding: 14px 28px; border-top: 1px solid #f0f0f0; background: #fafafa; }
[data-bs-theme="dark"] .tau-card-footer { background: #16162a; border-color: #2a2a3e; }

/* ── Steps ── */
.tau-step { display: none; }
.tau-step.active { display: block; }

/* ── Form fields ── */
.tau-field { margin-bottom: 18px; }
.tau-field label { display: block; font-weight: 600; font-size: .87rem; color: #444; margin-bottom: 6px; }
[data-bs-theme="dark"] .tau-field label { color: #ccc; }
.tau-field input, .tau-field select, .tau-field textarea {
    width: 100%; padding: 10px 14px; border: 1.5px solid #e0e0e0; border-radius: 9px;
    font-size: .93rem; font-family: inherit; box-sizing: border-box;
    background: #fafafa; color: #222; transition: border-color .18s;
}
[data-bs-theme="dark"] .tau-field input,
[data-bs-theme="dark"] .tau-field select,
[data-bs-theme="dark"] .tau-field textarea { background: #2a2a3e; border-color: #3a3a4e; color: #eee; }
.tau-field input:focus, .tau-field select:focus, .tau-field textarea:focus {
    outline: none; border-color: #c62b3a; box-shadow: 0 0 0 3px rgba(198,43,58,.1);
}
.tau-field textarea { resize: vertical; min-height: 150px; }
.tau-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media(max-width:580px){ .tau-row { grid-template-columns: 1fr; } }
.tau-hint { font-size: .79rem; color: #888; margin: -4px 0 10px; }

/* ── Activity toggle cards ── */
.tau-toggle-grid { display: flex; flex-wrap: wrap; gap: 9px; }
.tau-toggle {
    display: inline-flex; flex-direction: column; align-items: center; gap: 5px;
    border: 2px solid #e0e0e0; border-radius: 12px; padding: 13px 16px;
    background: #fafafa; color: #888; cursor: pointer; font-size: .78rem;
    font-weight: 700; font-family: inherit; transition: all .15s; min-width: 90px;
    position: relative;
}
.tau-toggle .fa { font-size: 1.25rem; }
.tau-toggle:hover { border-color: #c62b3a; color: #c62b3a; background: #fef6f7; transform: translateY(-1px); }
.tau-toggle.selected { border-color: #c62b3a; color: #c62b3a; background: #fef2f3; }
.tau-toggle.locked  { border-color: #e0e0e0; color: #bbb; cursor: default; background: #f8f8f8; }
.tau-toggle.locked:hover { transform: none; border-color: #e0e0e0; color: #bbb; background: #f8f8f8; }
.tau-check-dot {
    position: absolute; top: 5px; right: 7px;
    width: 14px; height: 14px; border-radius: 50%; border: 2px solid #ddd;
    background: #fff; font-size: .55rem; color: transparent;
    display: flex; align-items: center; justify-content: center; transition: all .15s;
}
.tau-toggle.selected .tau-check-dot { background: #c62b3a; border-color: #c62b3a; color: #fff; }
.tau-toggle.locked   .tau-check-dot { background: #aaa;    border-color: #aaa;    color: #fff; }
[data-bs-theme="dark"] .tau-toggle { background: #2a2a3e; border-color: #3a3a4e; color: #777; }
[data-bs-theme="dark"] .tau-toggle.selected { border-color: #c62b3a; color: #e8717c; background: #2a1a1e; }

/* ── Advanced toggle ── */
.tau-advanced-toggle {
    background: none; border: none; color: #888; font-size: .82rem; cursor: pointer;
    padding: 0; text-decoration: underline; margin-bottom: 12px; display: block;
}
.tau-advanced-toggle:hover { color: #c62b3a; }
.tau-advanced-panel { display: none; }
.tau-advanced-panel.open { display: block; }

/* ── Buttons ── */
.tau-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    background: #c62b3a; color: #fff; border: none; border-radius: 10px;
    padding: 13px 28px; font-size: .97rem; font-weight: 700; cursor: pointer;
    transition: background .15s, transform .1s; width: 100%;
}
.tau-btn:hover:not(:disabled) { background: #a32230; transform: translateY(-1px); }
.tau-btn:disabled { background: #bbb; cursor: not-allowed; }
.tau-btn-secondary {
    display: inline-flex; align-items: center; gap: 8px;
    background: transparent; color: #c62b3a; border: 1.5px solid #c62b3a;
    border-radius: 10px; padding: 11px 22px; font-size: .9rem; font-weight: 600;
    cursor: pointer; transition: background .15s;
}
.tau-btn-secondary:hover { background: rgba(198,43,58,.07); }

/* ── Alert ── */
.tau-alert { border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; font-size: .87rem; }
.tau-alert-danger  { background: #fde8ea; color: #9e1e2a; border: 1px solid #f5c6cb; }
.tau-alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
[data-bs-theme="dark"] .tau-alert-danger  { background: #2a1a1e; color: #f5a0a8; }
[data-bs-theme="dark"] .tau-alert-success { background: #1a2a1e; color: #7cc47e; }

/* ── Plan status spinner ── */
.tau-plan-status {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 20px 28px; border-bottom: 1px solid #f0f0f0;
}
[data-bs-theme="dark"] .tau-plan-status { border-color: #2a2a3e; }
.tau-spinner {
    width: 22px; height: 22px; border: 3px solid #e0e0e0; border-top-color: #c62b3a;
    border-radius: 50%; animation: spin .7s linear infinite; flex-shrink: 0; margin-top: 2px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.tau-plan-status .tau-done-icon { color: #2e7d32; font-size: 1.3rem; flex-shrink: 0; margin-top: 2px; }
.tau-plan-status-text strong { display: block; font-size: .97rem; font-weight: 700; }
.tau-plan-status-text small  { color: #888; font-size: .82rem; }

/* ── Plan preview ── */
.tau-plan-preview { padding: 24px 28px; }
.tau-plan-preview h2 { font-size: 1.2rem; font-weight: 800; color: #1a1a2e; margin-bottom: 6px; }
[data-bs-theme="dark"] .tau-plan-preview h2 { color: #f0f0f0; }
.tau-plan-preview .course-desc { color: #666; font-size: .89rem; margin-bottom: 22px; }
[data-bs-theme="dark"] .tau-plan-preview .course-desc { color: #aaa; }
.tau-section-block { margin-bottom: 18px; }
.tau-section-block h3 { font-size: .97rem; font-weight: 700; color: #c62b3a; margin-bottom: 8px; }
.tau-section-block .section-summary { font-size: .84rem; color: #666; margin-bottom: 8px; }
[data-bs-theme="dark"] .tau-section-block .section-summary { color: #999; }
.tau-activity-list { list-style: none; padding: 0; margin: 0; }
.tau-activity-list li {
    display: flex; align-items: flex-start; gap: 8px;
    padding: 5px 0; font-size: .84rem; border-bottom: 1px solid #f5f5f5;
}
[data-bs-theme="dark"] .tau-activity-list li { border-color: #2a2a3e; }
.tau-activity-list li:last-child { border: none; }
.tau-type-badge {
    display: inline-block; border-radius: 5px; padding: 1px 7px;
    font-size: .68rem; font-weight: 700; white-space: nowrap; flex-shrink: 0; margin-top: 2px;
}
.tau-type-page     { background: #e3f0fb; color: #1565c0; }
.tau-type-quiz     { background: #e8f5e9; color: #2e7d32; }
.tau-type-forum    { background: #fff3e0; color: #e65100; }
.tau-type-assign   { background: #f3e5f5; color: #6a1b9a; }
.tau-type-url      { background: #e0f2f1; color: #006064; }
.tau-type-glossary { background: #fce4ec; color: #ad1457; }
.tau-type-feedback { background: #f3e5f5; color: #4527a0; }
.tau-type-label    { background: #f5f5f5; color: #555; }
.tau-type-resource { background: #fff8e1; color: #f57f17; }

/* ── Chat ── */
.tau-chat-section { padding: 16px 28px; border-top: 1px solid #f0f0f0; }
[data-bs-theme="dark"] .tau-chat-section { border-color: #2a2a3e; }
.tau-chat-messages { margin-bottom: 12px; display: flex; flex-direction: column; gap: 8px; }
.tau-chat-msg {
    max-width: 82%; padding: 9px 14px; border-radius: 12px; font-size: .87rem; line-height: 1.5;
}
.tau-chat-msg.user      { background: #c62b3a; color: #fff; align-self: flex-end; border-radius: 12px 12px 2px 12px; }
.tau-chat-msg.assistant { background: #f5f5f5; color: #333; align-self: flex-start; border-radius: 12px 12px 12px 2px; }
[data-bs-theme="dark"] .tau-chat-msg.assistant { background: #2a2a3e; color: #eee; }
.tau-chat-input-row { display: flex; gap: 8px; }
.tau-chat-input-row input {
    flex: 1; padding: 10px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px;
    font-size: .9rem; font-family: inherit; background: #fafafa; color: #222;
}
[data-bs-theme="dark"] .tau-chat-input-row input { background: #2a2a3e; border-color: #3a3a4e; color: #eee; }
.tau-chat-input-row input:focus { outline: none; border-color: #c62b3a; }
.tau-chat-send {
    background: #c62b3a; color: #fff; border: none; border-radius: 8px;
    padding: 10px 16px; cursor: pointer; transition: background .15s;
}
.tau-chat-send:hover { background: #a32230; }
.tau-chat-send:disabled { background: #aaa; cursor: not-allowed; }

/* ── Build log ── */
.tau-build-log { padding: 16px 28px; max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 6px; }
.tau-log-item { display: flex; align-items: center; gap: 10px; background: #f0faf0; border-radius: 8px; padding: 8px 14px; font-size: .85rem; }
[data-bs-theme="dark"] .tau-log-item { background: #1a2a1e; color: #7cc47e; }

/* ── Done screen ── */
.tau-done-center { text-align: center; padding: 44px 28px; }
.tau-done-center .done-icon { font-size: 3rem; color: #2e7d32; margin-bottom: 16px; }
.tau-done-center h2 { font-size: 1.4rem; font-weight: 800; margin-bottom: 8px; }
.tau-done-center p  { color: #666; margin-bottom: 26px; font-size: .93rem; }
[data-bs-theme="dark"] .tau-done-center p { color: #aaa; }

/* ── Plan actions ── */
.tau-plan-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.tau-plan-actions .tau-btn-secondary { flex-shrink: 0; }
.tau-plan-actions .tau-btn { flex: 1; min-width: 180px; }

/* ── Provider selector ── */
.tau-provider-grid { display: flex; gap: 10px; flex-wrap: wrap; }
.tau-provider-btn {
    display: flex; flex-direction: column; align-items: flex-start; gap: 2px;
    border: 2px solid #e0e0e0; border-radius: 13px; padding: 12px 16px;
    background: #fafafa; cursor: pointer; font-family: inherit; text-align: left;
    transition: all .18s; flex: 1; min-width: 140px; max-width: 220px;
    position: relative; overflow: hidden;
}
.tau-provider-btn::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(198,43,58,.04) 0%, transparent 60%);
    opacity: 0; transition: opacity .18s;
}
.tau-provider-btn:hover::before { opacity: 1; }
.tau-provider-btn:hover { border-color: #c62b3a; transform: translateY(-2px); box-shadow: 0 6px 18px rgba(198,43,58,.12); }
.tau-provider-btn.selected { border-color: #c62b3a; background: #fef2f3; }
.tau-provider-btn.selected::before { opacity: 1; }
[data-bs-theme="dark"] .tau-provider-btn { background: #2a2a3e; border-color: #3a3a4e; }
[data-bs-theme="dark"] .tau-provider-btn.selected { background: #2a1a1e; border-color: #c62b3a; }
.tau-prov-top { display: flex; align-items: center; gap: 8px; margin-bottom: 3px; }
.tau-prov-icon { font-size: 1.3rem; line-height: 1; }
.tau-prov-name { font-size: .93rem; font-weight: 700; color: #1a1a2e; }
[data-bs-theme="dark"] .tau-prov-name { color: #f0f0f0; }
.tau-provider-btn.selected .tau-prov-name { color: #c62b3a; }
.tau-prov-model { font-size: .72rem; color: #888; font-weight: 500; margin-bottom: 2px; }
.tau-prov-speed { display: inline-flex; align-items: center; gap: 4px; font-size: .68rem; font-weight: 700; border-radius: 20px; padding: 2px 7px; }
.tau-prov-speed.free  { background: rgba(46,125,50,.1); color: #2e7d32; }
.tau-prov-speed.fast  { background: rgba(25,118,210,.1); color: #1565c0; }
.tau-prov-speed.slow  { background: rgba(120,120,120,.1); color: #777; }
.tau-prov-check { position: absolute; top: 8px; right: 10px; width: 16px; height: 16px; border-radius: 50%; border: 2px solid #ddd; background: #fff; display: flex; align-items: center; justify-content: center; font-size: .55rem; color: transparent; transition: all .15s; }
.tau-provider-btn.selected .tau-prov-check { background: #c62b3a; border-color: #c62b3a; color: #fff; }

/* —— Predesigned structure —— */
.tau-template-shell {
    position: relative; overflow: hidden; border: 1px solid #f1d7da; border-radius: 18px;
    padding: 20px; margin: 8px 0 18px; background:
        radial-gradient(circle at top right, rgba(198,43,58,.12), transparent 34%),
        linear-gradient(135deg, #fff8f8 0%, #fff 52%, #fff6ef 100%);
}
[data-bs-theme="dark"] .tau-template-shell {
    background:
        radial-gradient(circle at top right, rgba(198,43,58,.18), transparent 34%),
        linear-gradient(135deg, #231920 0%, #1e1e2e 55%, #241f1a 100%);
    border-color: #48303a;
}
.tau-template-head { display: flex; justify-content: space-between; gap: 18px; align-items: flex-start; margin-bottom: 16px; }
.tau-template-head h3 { margin: 0 0 4px; font-size: 1rem; font-weight: 800; color: #1a1a2e; }
[data-bs-theme="dark"] .tau-template-head h3 { color: #f0f0f0; }
.tau-template-head p { margin: 0; color: #6a6570; font-size: .84rem; max-width: 560px; }
.tau-template-badge {
    display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 7px 12px;
    background: rgba(255,255,255,.8); border: 1px solid rgba(198,43,58,.18); color: #a32230;
    font-size: .75rem; font-weight: 800; white-space: nowrap;
}
[data-bs-theme="dark"] .tau-template-badge { background: rgba(42,42,62,.86); color: #f29ea8; border-color: #68414a; }
.tau-preset-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-bottom: 14px; }
@media(max-width:760px){ .tau-preset-grid { grid-template-columns: 1fr; } }
.tau-preset-card {
    position: relative; border: 1.5px solid #ead6d8; border-radius: 16px; padding: 16px;
    background: rgba(255,255,255,.82); cursor: pointer; transition: transform .18s, border-color .18s, box-shadow .18s;
}
.tau-preset-card:hover { transform: translateY(-2px); border-color: #c62b3a; box-shadow: 0 10px 24px rgba(198,43,58,.12); }
.tau-preset-card.selected { border-color: #c62b3a; box-shadow: 0 12px 28px rgba(198,43,58,.18); }
[data-bs-theme="dark"] .tau-preset-card { background: rgba(35,35,52,.86); border-color: #3f3340; }
.tau-preset-kicker { font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: #c62b3a; font-weight: 800; margin-bottom: 8px; }
.tau-preset-title { font-size: .96rem; font-weight: 800; color: #1a1a2e; margin-bottom: 6px; }
[data-bs-theme="dark"] .tau-preset-title { color: #f0f0f0; }
.tau-preset-copy { font-size: .82rem; color: #666; min-height: 44px; }
[data-bs-theme="dark"] .tau-preset-copy { color: #aaa; }
.tau-preset-meta { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
.tau-pill {
    display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 5px 9px;
    background: #fff; color: #555; border: 1px solid #eee; font-size: .7rem; font-weight: 700;
}
[data-bs-theme="dark"] .tau-pill { background: #26263a; border-color: #3b3b50; color: #c8c8d2; }
.tau-template-controls { display: grid; grid-template-columns: 1.1fr .9fr; gap: 14px; }
@media(max-width:760px){ .tau-template-controls { grid-template-columns: 1fr; } }
.tau-chip-row { display: flex; gap: 8px; flex-wrap: wrap; }
.tau-chip {
    border: 1px solid #ead6d8; background: rgba(255,255,255,.82); color: #6d4c53; border-radius: 999px;
    padding: 8px 12px; font-size: .77rem; font-weight: 700; cursor: pointer; transition: all .15s;
}
.tau-chip.active, .tau-chip:hover { border-color: #c62b3a; color: #c62b3a; background: #fff2f4; }
[data-bs-theme="dark"] .tau-chip { background: #25253a; border-color: #403548; color: #c8b7bb; }
[data-bs-theme="dark"] .tau-chip.active, [data-bs-theme="dark"] .tau-chip:hover { background: #311d23; color: #f29ea8; }
.tau-outline-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
    border: 1.5px dashed #c62b3a; color: #c62b3a; background: rgba(255,255,255,.75);
    border-radius: 12px; padding: 12px 18px; font-size: .9rem; font-weight: 700; cursor: pointer;
}
.tau-outline-btn:hover { background: #fff1f3; }

/* —— Plan editor —— */
.tau-plan-layout { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(320px, .95fr); gap: 18px; align-items: start; }
@media(max-width:920px){ .tau-plan-layout { grid-template-columns: 1fr; } }
.tau-blueprint-editor {
    display: none; padding: 22px 28px 26px; border-top: 1px solid #f0f0f0;
    background: linear-gradient(180deg, #fff 0%, #fff9fa 100%);
}
[data-bs-theme="dark"] .tau-blueprint-editor { background: linear-gradient(180deg, #1e1e2e 0%, #211922 100%); border-color: #2a2a3e; }
.tau-editor-panel { border: 1px solid #eee2e4; border-radius: 18px; background: rgba(255,255,255,.86); overflow: hidden; }
[data-bs-theme="dark"] .tau-editor-panel { background: rgba(34,34,52,.9); border-color: #3a3140; }
.tau-editor-head {
    padding: 18px 20px; display: flex; justify-content: space-between; gap: 14px; align-items: start;
    border-bottom: 1px solid #f3ecee;
}
[data-bs-theme="dark"] .tau-editor-head { border-color: #343448; }
.tau-editor-head h3 { margin: 0 0 4px; font-size: 1rem; font-weight: 800; color: #1a1a2e; }
[data-bs-theme="dark"] .tau-editor-head h3 { color: #f0f0f0; }
.tau-editor-head p { margin: 0; font-size: .8rem; color: #756b70; }
.tau-editor-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.tau-editor-btn {
    border: 1px solid #ead6d8; background: #fff; color: #a32230; border-radius: 999px;
    padding: 8px 12px; font-size: .77rem; font-weight: 800; cursor: pointer;
}
.tau-editor-btn:hover { border-color: #c62b3a; background: #fff3f5; }
[data-bs-theme="dark"] .tau-editor-btn { background: #28283d; border-color: #433744; color: #f2a7af; }
.tau-editor-stack { padding: 18px 20px 20px; display: flex; flex-direction: column; gap: 16px; }
.tau-course-meta {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
}
@media(max-width:680px){ .tau-course-meta { grid-template-columns: 1fr; } }
.tau-inline-field label { display: block; font-size: .72rem; font-weight: 800; color: #7b6c72; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 6px; }
.tau-inline-field input, .tau-inline-field textarea, .tau-inline-field select {
    width: 100%; border: 1.4px solid #e6dfe1; border-radius: 12px; background: #fff; color: #222;
    padding: 11px 13px; font-size: .88rem; font-family: inherit;
}
[data-bs-theme="dark"] .tau-inline-field input,
[data-bs-theme="dark"] .tau-inline-field textarea,
[data-bs-theme="dark"] .tau-inline-field select { background: #232338; border-color: #3c3c50; color: #eee; }
.tau-inline-field textarea { min-height: 84px; resize: vertical; }
.tau-module-card { border: 1px solid #f0e3e6; border-radius: 18px; overflow: hidden; background: #fff; }
[data-bs-theme="dark"] .tau-module-card { background: #232337; border-color: #3a3343; }
.tau-module-header {
    display: flex; justify-content: space-between; gap: 14px; align-items: center;
    padding: 14px 16px; background: linear-gradient(135deg, rgba(198,43,58,.07), rgba(255,255,255,.95));
    border-bottom: 1px solid #f3ecee;
}
[data-bs-theme="dark"] .tau-module-header { background: linear-gradient(135deg, rgba(198,43,58,.12), rgba(35,35,55,.96)); border-color: #353548; }
.tau-module-titlebar { display: flex; align-items: center; gap: 10px; min-width: 0; }
.tau-module-number {
    width: 34px; height: 34px; border-radius: 12px; background: #c62b3a; color: #fff; font-weight: 800;
    display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.tau-module-titlebar strong { display: block; color: #1a1a2e; font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
[data-bs-theme="dark"] .tau-module-titlebar strong { color: #f0f0f0; }
.tau-module-titlebar small { color: #7f7178; }
.tau-icon-btn {
    width: 34px; height: 34px; border-radius: 10px; border: 1px solid #ead6d8; background: #fff;
    color: #a32230; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
}
.tau-icon-btn:hover { background: #fff2f4; border-color: #c62b3a; }
.tau-module-body { padding: 16px; display: flex; flex-direction: column; gap: 12px; }
.tau-activity-row {
    display: grid; grid-template-columns: 120px 1fr auto; gap: 10px; align-items: start;
    border: 1px solid #f2e7e9; border-radius: 14px; padding: 12px; background: #fffafb;
}
@media(max-width:680px){ .tau-activity-row { grid-template-columns: 1fr; } }
[data-bs-theme="dark"] .tau-activity-row { background: #25253a; border-color: #3a3343; }
.tau-activity-main { display: grid; grid-template-columns: 1fr; gap: 8px; }
.tau-resource-upload {
    grid-column: 1 / -1;
}
.tau-resource-uploadbox {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    padding: 10px 12px; border: 1.4px dashed #d9c7cb; border-radius: 12px; background: #fff;
}
.tau-resource-uploadbox input[type="file"] {
    border: 0 !important; padding: 0 !important; background: transparent !important;
}
.tau-resource-uploadmeta {
    display: block; margin-top: 6px; font-size: .74rem; color: #7b6c72;
}
[data-bs-theme="dark"] .tau-resource-uploadbox { background: #232338; border-color: #4a3f4c; }
[data-bs-theme="dark"] .tau-resource-uploadmeta { color: #b7b1ba; }
.tau-add-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
@media(max-width:680px){ .tau-add-grid { grid-template-columns: 1fr 1fr; } }
.tau-add-card {
    border: 1px dashed #e3c5cb; border-radius: 14px; background: #fff; color: #8f4c58;
    padding: 9px 10px; font-size: .74rem; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 5px; text-align: center;
}
.tau-add-card:hover { border-color: #c62b3a; color: #c62b3a; background: #fff4f6; }
[data-bs-theme="dark"] .tau-add-card { background: #26263a; border-color: #4a3740; color: #d2afb6; }
.tau-mini-stats { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }

/* ── Prompt Templates & Guide ── */
.tau-prompt-templates { margin-top: 15px; background: rgba(0,0,0,0.02); border-radius: 12px; padding: 16px; border: 1px dashed #e0e0e0; }
[data-bs-theme="dark"] .tau-prompt-templates { background: rgba(255,255,255,0.02); border-color: #3a3a4e; }
.tau-templates-title { display: block; font-weight: 700; font-size: .84rem; color: #c62b3a; margin-bottom: 10px; }
.tau-templates-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
@media(max-width:760px){ .tau-templates-grid { grid-template-columns: 1fr; } }
.tau-template-item {
    background: #fff; border: 1.5px solid #e0e0e0; border-radius: 10px; padding: 12px; text-align: left;
    cursor: pointer; transition: all .2s ease; display: flex; flex-direction: column; gap: 4px; width: 100%;
}
[data-bs-theme="dark"] .tau-template-item { background: #2a2a3e; border-color: #3a3a4e; }
.tau-template-item:hover { border-color: #c62b3a; background: #fff8f9; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(198, 43, 58, 0.08); }
[data-bs-theme="dark"] .tau-template-item:hover { background: #2f1b20; }
.tau-template-item strong { font-size: .82rem; color: #1a1a2e; font-weight: 700; }
[data-bs-theme="dark"] .tau-template-item strong { color: #f0f0f0; }
.tau-template-item span { font-size: .72rem; color: #666; }
[data-bs-theme="dark"] .tau-template-item span { color: #aaa; }

.tau-resources-guide { margin-top: 24px; border-top: 1px solid #f0f0f0; padding-top: 20px; }
[data-bs-theme="dark"] .tau-resources-guide { border-color: #2a2a3e; }
.tau-guide-title { display: block; font-weight: 800; font-size: .93rem; color: #1a1a2e; margin-bottom: 6px; }
[data-bs-theme="dark"] .tau-guide-title { color: #f0f0f0; }
.tau-guide-desc { font-size: .81rem; color: #666; margin-bottom: 14px; }
[data-bs-theme="dark"] .tau-guide-desc { color: #aaa; }
.tau-guide-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
@media(max-width:680px){ .tau-guide-grid { grid-template-columns: 1fr; } }
.tau-guide-card {
    background: #fafafa; border: 1px solid #eee; border-radius: 10px; padding: 12px;
    display: flex; flex-direction: column; gap: 6px;
}
[data-bs-theme="dark"] .tau-guide-card { background: #222235; border-color: #2a2a3e; }
.tau-guide-card .tau-type-badge { align-self: flex-start; margin-top: 0; font-size: .7rem; padding: 3px 8px; }
.tau-guide-card p { font-size: .76rem; color: #555; margin: 0; line-height: 1.4; }
[data-bs-theme="dark"] .tau-guide-card p { color: #ccc; }

/* AI checkable activity chips */
.tau-ai-chips-grid { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 6px; }
.tau-ai-activity-chip {
    border: 1.5px solid #dee2e6; background: #fff; color: #495057; border-radius: 999px;
    padding: 8px 16px; font-size: .81rem; font-weight: 700; cursor: pointer; transition: all .15s;
    display: inline-flex; align-items: center; gap: 6px; font-family: inherit;
}
.tau-ai-activity-chip:hover { border-color: #c62b3a; color: #c62b3a; background: #fff8f9; }
.tau-ai-activity-chip.active { border-color: #c62b3a; color: #fff; background: linear-gradient(135deg, #c62b3a 0%, #8d182a 100%); box-shadow: 0 3px 10px rgba(198, 43, 58, 0.18); }
[data-bs-theme="dark"] .tau-ai-activity-chip { background: #25253a; border-color: #3a3a4e; color: #ccc; }
[data-bs-theme="dark"] .tau-ai-activity-chip.active { color: #fff; }

/* Nested Modules/Weeks Hierarchical Layout */
.tau-general-editor-card {
    border-left: 5px solid #6c757d;
}
.tau-module-block-preview {
    background: #fafafa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
}
[data-bs-theme="dark"] .tau-module-block-preview {
    background: #1a1a2a;
    border-color: #2a2a3e;
}
.tau-module-preview-header h3 {
    margin-top: 0;
    margin-bottom: 8px;
    color: #c62b3a;
    font-weight: 800;
}
.tau-weeks-container-preview {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-top: 15px;
}
.tau-week-block-preview {
    background: #fff;
    border: 1.5px solid #f1f3f5;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.01);
}
[data-bs-theme="dark"] .tau-week-block-preview {
    background: #222235;
    border-color: #2d2d42;
}
.tau-week-block-preview h4 {
    margin-top: 0;
    margin-bottom: 8px;
    font-size: 0.95rem;
    font-weight: 700;
    color: #495057;
}
[data-bs-theme="dark"] .tau-week-block-preview h4 {
    color: #dee2e6;
}
.tau-nested-weeks-editor {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 18px;
    padding-top: 15px;
    border-top: 1px dashed #dee2e6;
}
[data-bs-theme="dark"] .tau-nested-weeks-editor {
    border-color: #3a3a4e;
}
.tau-nested-week-card {
    background: #fafafa;
    border: 1.5px solid #e9ecef;
    border-radius: 12px;
    padding: 18px;
}
[data-bs-theme="dark"] .tau-nested-week-card {
    background: #1d1d2b;
    border-color: #2b2b3b;
}
.tau-week-header-editor {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1.5px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 12px;
}
[data-bs-theme="dark"] .tau-week-header-editor {
    border-color: #2b2b3b;
}
.tau-week-header-editor h5 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 800;
    color: #343a40;
}
[data-bs-theme="dark"] .tau-week-header-editor h5 {
    color: #e9ecef;
}
.tau-week-remove-btn {
    background: transparent;
    border: none;
    color: #dc3545;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background 0.15s;
}
.tau-week-remove-btn:hover {
    background: rgba(220, 53, 69, 0.08);
}
.nested-activity {
    border-left: 3.5px solid #c62b3a !important;
    background: #fff !important;
    padding: 12px 14px !important;
    margin-bottom: 8px !important;
    border-radius: 8px !important;
}
[data-bs-theme="dark"] .nested-activity {
    background: #252538 !important;
}
.tau-week-activities-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin: 12px 0;
}
.secondary-btn {
    background: #e9ecef !important;
    color: #495057 !important;
    border-color: #ced4da !important;
}
[data-bs-theme="dark"] .secondary-btn {
    background: #2c2c3e !important;
    color: #ccc !important;
    border-color: #3c3c50 !important;
}
.secondary-btn:hover {
    background: #dee2e6 !important;
}
[data-bs-theme="dark"] .secondary-btn:hover {
    background: #34344a !important;
}
</style>

<?php if (!$api_key_set): ?>
<div class="tau-creator">
    <div class="tau-card">
        <div class="tau-card-header">
            <h2><i class="fa fa-magic me-2"></i>AI Course Creator</h2>
        </div>
        <div class="tau-card-body">
            <div class="tau-alert tau-alert-danger">
                <strong><i class="fa fa-exclamation-triangle me-1"></i> Clave de API no configurada.</strong><br>
                Ve a <a href="<?php echo $CFG->wwwroot; ?>/admin/settings.php?section=local_tau_course_creator_ai">Administración del sitio → TAU Course Creator AI → Configuración</a> e ingresa tu clave de OpenAI.
            </div>
        </div>
    </div>
</div>
<?php else: ?>

<div class="tau-creator">

    <!-- ══ STEP 1: Configurar ══ -->
    <div class="tau-step active" id="step-config">
        <div class="tau-card">
            <div class="tau-card-header">
                <h2><i class="fa fa-magic me-2"></i>AI Course Creator</h2>
                <p>Pega tu sílabo o describe el curso y la IA generará la estructura completa con actividades reales en Moodle.</p>
            </div>

            <!-- Wizard nav -->
            <div class="tau-wizard-nav">
                <div class="tau-wstep active" id="wstep-1">
                    <div class="tau-wstep-dot">1</div>
                    <div class="tau-wstep-label">Configurar</div>
                </div>
                <div class="tau-wstep-line" id="wline-1"></div>
                <div class="tau-wstep" id="wstep-2">
                    <div class="tau-wstep-dot">2</div>
                    <div class="tau-wstep-label">Revisar plan</div>
                </div>
                <div class="tau-wstep-line" id="wline-2"></div>
                <div class="tau-wstep" id="wstep-3">
                    <div class="tau-wstep-dot">3</div>
                    <div class="tau-wstep-label">Crear curso</div>
                </div>
            </div>

            <div class="tau-card-body">
                <div id="step-config-alert"></div>

                <form id="creator-form" autocomplete="off">

                    <!-- Hidden provider grid (keeps JS working, OpenAI auto-selected) -->
                    <div style="display:none;" id="provider-grid">
                        <button type="button" class="tau-provider-btn selected" data-provider="openai"></button>
                    </div>
                    <!-- Hidden fields for JS compatibility -->
                    <input type="hidden" id="f-language" name="language" value="es">
                    <input type="hidden" id="f-welcome-text" name="welcometext" value="">
                    <input type="hidden" id="f-system" name="systemPrompt" value="">
                    <input type="hidden" id="f-manual-modules-count" name="manualmodulescount" value="3">
                    <!-- Dummy dropzone elements needed by the PDF JS -->
                    <div style="display:none;">
                        <div id="tau-syllabus-dropzone">
                            <input type="file" id="f-syllabus-file" accept=".pdf,.txt">
                            <div id="dropzone-default"></div>
                            <div id="dropzone-loading"></div>
                            <div id="dropzone-success"><span id="dropzone-success-msg"></span></div>
                        </div>
                        <div id="welcome-spinner"></div>
                        <button type="button" id="btn-generate-welcome-ai"></button>
                        <button type="button" id="advanced-toggle-btn"></button>
                        <div id="advanced-panel"></div>
                    </div>
                    <!-- Tab content wrapper (JS expects tab-ai active) -->
                    <div class="tau-tab-content active" id="tab-ai">
                    <div class="tau-tab-content" id="tab-manual"></div>

                    <!-- ── CAMPO PRINCIPAL: tema del curso ── -->
                    <div class="tau-field">
                        <label for="f-prompt" style="font-size:1rem;font-weight:800;color:#1a1a2e;">
                            ¿Sobre qué trata el curso? <span style="color:#c62b3a">*</span>
                        </label>
                        <textarea id="f-prompt" name="prompt" rows="4"
                            style="font-size:1rem;min-height:120px;"
                            placeholder="Ej: Ciberseguridad para universitarios&#10;Ej: Marketing Digital para emprendedores&#10;Ej: Programación básica en Python para ingenieros&#10;&#10;También puedes pegar aquí el sílabo completo del curso."
                            required></textarea>
                    </div>

                    <!-- Shared Settings (Category + Language) -->
                    <div class="tau-row">
                        <div class="tau-field">
                            <label for="f-category">Categoría del curso</label>
                            <select id="f-category" name="category">
                                <?php foreach ($categories as $id => $name): ?>
                                <option value="<?php echo $id; ?>"><?php echo s($name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="tau-field">
                            <label for="f-language">Idioma del contenido</label>
                            <select id="f-language" name="language">
                                <option value="es">Español</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                    </div>

                    <!-- Publish to Academic Support Course setting -->
                    <div class="tau-field" style="margin-top: 6px; margin-bottom: 22px;">
                        <label class="tau-toggle-label" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; font-size: .87rem; color: #444;">
                            <input type="checkbox" id="f-publish-apoyo" name="publish_apoyo" value="1" checked style="width: 18px; height: 18px; margin: 0; cursor: pointer;">
                            <span>Publicar en Cursos de Apoyo Académico</span>
                        </label>
                        <p class="tau-hint" style="margin: 4px 0 0 26px;">Si se desmarca, el curso se configurará como privado y no se mostrará en la página principal.</p>
                    </div>

                    <div class="tau-field">
                        <label for="f-teacher-name">Nombre del docente <span style="color:#c62b3a">*</span></label>
                        <input type="text" id="f-teacher-name" name="teachername"
                            placeholder="Ej: Dr. Carlos Pérez" required>
                    </div>

                    <!-- ── OPCIONES DE ESTRUCTURA (colapsable) ── -->
                    <div style="border:1.5px solid #e8e8e8;border-radius:12px;overflow:hidden;margin-bottom:20px;">
                        <button type="button" id="tau-opts-toggle"
                            style="width:100%;background:#fafafa;border:none;padding:14px 18px;text-align:left;font-weight:700;font-size:.9rem;color:#555;cursor:pointer;display:flex;justify-content:space-between;align-items:center;">
                            <span><i class="fa fa-sliders me-2"></i>Opciones de estructura del curso</span>
                            <i class="fa fa-chevron-down" id="tau-opts-icon" style="transition:transform .2s;"></i>
                        </button>
                        <div id="tau-opts-panel" style="display:none;padding:18px;border-top:1px solid #f0f0f0;">

                            <div class="tau-row">
                                <div class="tau-field">
                                    <label for="f-ai-modules">Número de módulos</label>
                                    <input type="number" id="f-ai-modules" name="aimodules" value="3" min="1" max="15">
                                </div>
                                <div class="tau-field">
                                    <label for="f-ai-weeks">Secciones por módulo</label>
                                    <select id="f-ai-weeks" name="aiweeks">
                                        <option value="4" selected>4 secciones</option>
                                        <option value="3">3 secciones</option>
                                        <option value="2">2 secciones</option>
                                        <option value="1">1 sección</option>
                                    </select>
                                </div>
                            </div>

                            <div class="tau-field" style="margin-bottom:0;">
                                <label style="margin-bottom:8px;display:block;">¿Qué incluir en cada sección?</label>
                                <div class="tau-ai-chips-grid">
                                    <button type="button" class="tau-ai-activity-chip active" data-type="page">
                                        <i class="fa fa-file-text-o"></i> Página de contenido
                                    </button>
                                    <button type="button" class="tau-ai-activity-chip active" data-type="resource">
                                        <i class="fa fa-television"></i> Presentación interactiva
                                    </button>
                                    <button type="button" class="tau-ai-activity-chip active" data-type="url">
                                        <i class="fa fa-link"></i> Enlace/Video
                                    </button>
                                    <button type="button" class="tau-ai-activity-chip active" data-type="forum">
                                        <i class="fa fa-comments-o"></i> Foro de debate
                                    </button>
                                    <button type="button" class="tau-ai-activity-chip active" data-type="assign">
                                        <i class="fa fa-upload"></i> Taller entregable
                                    </button>
                                    <button type="button" class="tau-ai-activity-chip" data-type="quiz">
                                        <i class="fa fa-question-circle-o"></i> Quiz
                                    </button>
                                    <button type="button" class="tau-ai-activity-chip" data-type="glossary">
                                        <i class="fa fa-book"></i> Glosario
                                    </button>
                                    <button type="button" class="tau-ai-activity-chip" data-type="feedback">
                                        <i class="fa fa-star-o"></i> Encuesta
                                    </button>
                                </div>
                            </div>

                            <!-- ── Material fuente para presentaciones ── -->
                            <div class="tau-field" style="margin-top:18px;padding-top:16px;border-top:1px solid #f0f0f0;">
                                <label style="margin-bottom:6px;display:block;font-weight:700;font-size:.9rem;">
                                    <i class="fa fa-paperclip me-1"></i> Material fuente para las presentaciones
                                    <span style="font-weight:400;color:#999;font-size:.82rem;margin-left:6px;">(opcional)</span>
                                </label>
                                <p style="font-size:.82rem;color:#999;margin:0 0 10px;">
                                    La IA usará este material para construir el contenido de cada diapositiva.
                                    Sube un archivo <strong>.txt</strong> o pega el texto directamente.
                                </p>
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                                    <label style="display:inline-flex;align-items:center;gap:6px;background:#f4f4f4;border:1px solid #ddd;border-radius:8px;padding:7px 14px;cursor:pointer;font-size:.85rem;font-weight:600;color:#555;margin:0;">
                                        <i class="fa fa-folder-open-o"></i> Cargar .txt
                                        <input type="file" id="f-material-file" accept=".txt" style="display:none;">
                                    </label>
                                    <span id="f-material-file-name" style="font-size:.82rem;color:#888;"></span>
                                </div>
                                <textarea id="f-material-text" rows="4"
                                    placeholder="O pega aquí tu sílabo, guía o resumen del tema (máx. 8000 caracteres)..."
                                    style="width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:8px;font-size:.85rem;resize:vertical;box-sizing:border-box;"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── BOTÓN PRINCIPAL ── -->
                    <button type="submit" class="tau-btn" id="btn-generate" style="font-size:1.05rem;padding:16px;">
                        <i class="fa fa-magic"></i> Crear curso con IA
                    </button>

                    </div><!-- /tab-ai -->

                </form>
            </div>
        </div>
    </div>

    <!-- ══ STEP 2: Plan preview + chat ══ -->
    <div class="tau-step" id="step-plan">
        <div class="tau-card">
            <div class="tau-wizard-nav">
                <div class="tau-wstep done" id="wstep-1b">
                    <div class="tau-wstep-dot"><i class="fa fa-check" style="font-size:.7rem"></i></div>
                    <div class="tau-wstep-label">Configurar</div>
                </div>
                <div class="tau-wstep-line done"></div>
                <div class="tau-wstep active" id="wstep-2b">
                    <div class="tau-wstep-dot">2</div>
                    <div class="tau-wstep-label">Revisar plan</div>
                </div>
                <div class="tau-wstep-line"></div>
                <div class="tau-wstep" id="wstep-3b">
                    <div class="tau-wstep-dot">3</div>
                    <div class="tau-wstep-label">Crear curso</div>
                </div>
            </div>

            <div class="tau-plan-status" id="plan-status">
                <div class="tau-spinner"></div>
                <div class="tau-plan-status-text">
                    <strong>Generando plan del curso</strong>
                    <small>La IA está analizando tu sílabo y diseñando la estructura...</small>
                </div>
            </div>

            <div class="tau-plan-preview" id="plan-preview" style="display:none;"></div>
            <div class="tau-blueprint-editor" id="blueprint-editor" style="display:none;"></div>

            <div class="tau-chat-section" id="chat-section" style="display:none;">
                <p style="font-size:.82rem;color:#888;margin:0 0 10px;"><i class="fa fa-comments-o me-1"></i> Ajusta el plan antes de crear el curso:</p>
                <div class="tau-chat-messages" id="chat-messages"></div>
                <div class="tau-chat-input-row">
                    <input type="text" id="chat-input"
                        placeholder="Ej: Agrega más actividades prácticas / Reduce a 4 módulos / Añade un glosario..." />
                    <button class="tau-chat-send" id="chat-send" onclick="sendChat()">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="plan-actions" style="display:none;">
            <div class="tau-plan-actions">
                <button class="tau-btn-secondary" onclick="backToConfig()">
                    <i class="fa fa-arrow-left"></i> Volver
                </button>
                <button class="tau-btn" onclick="startBuild()">
                    <i class="fa fa-rocket"></i> Crear curso en Moodle
                </button>
            </div>
        </div>
    </div>

    <!-- ══ STEP 3: Building ══ -->
    <div class="tau-step" id="step-building">
        <div class="tau-card">
            <div class="tau-wizard-nav">
                <div class="tau-wstep done">
                    <div class="tau-wstep-dot"><i class="fa fa-check" style="font-size:.7rem"></i></div>
                    <div class="tau-wstep-label">Configurar</div>
                </div>
                <div class="tau-wstep-line done"></div>
                <div class="tau-wstep done">
                    <div class="tau-wstep-dot"><i class="fa fa-check" style="font-size:.7rem"></i></div>
                    <div class="tau-wstep-label">Revisar plan</div>
                </div>
                <div class="tau-wstep-line done"></div>
                <div class="tau-wstep active">
                    <div class="tau-wstep-dot">3</div>
                    <div class="tau-wstep-label">Crear curso</div>
                </div>
            </div>
            <div class="tau-plan-status">
                <div class="tau-spinner"></div>
                <div class="tau-plan-status-text">
                    <strong>Creando el curso en Moodle</strong>
                    <small>Generando secciones, actividades y cuestionarios...</small>
                </div>
            </div>
            <div class="tau-build-log" id="build-log"></div>
        </div>
    </div>

    <!-- ══ STEP 4: Done ══ -->
    <div class="tau-step" id="step-done">
        <div class="tau-card">
            <div class="tau-wizard-nav">
                <div class="tau-wstep done">
                    <div class="tau-wstep-dot"><i class="fa fa-check" style="font-size:.7rem"></i></div>
                    <div class="tau-wstep-label">Configurar</div>
                </div>
                <div class="tau-wstep-line done"></div>
                <div class="tau-wstep done">
                    <div class="tau-wstep-dot"><i class="fa fa-check" style="font-size:.7rem"></i></div>
                    <div class="tau-wstep-label">Revisar plan</div>
                </div>
                <div class="tau-wstep-line done"></div>
                <div class="tau-wstep done">
                    <div class="tau-wstep-dot"><i class="fa fa-check" style="font-size:.7rem"></i></div>
                    <div class="tau-wstep-label">Crear curso</div>
                </div>
            </div>
            <div class="tau-done-center">
                <div class="done-icon"><i class="fa fa-check-circle"></i></div>
                <h2>¡Curso creado exitosamente!</h2>
                <p>Tu curso fue generado con todas las secciones, actividades y cuestionarios.<br>Puedes personalizarlo directamente en Moodle.</p>
                <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                    <a id="link-course" href="#" class="tau-btn" style="text-decoration:none;max-width:240px;">
                        <i class="fa fa-graduation-cap"></i> Ver el curso
                    </a>
                    <a id="link-improve" href="<?php echo (new moodle_url('/local/tau_course_creator_ai/improve.php'))->out(); ?>" class="tau-btn-secondary" style="text-decoration:none;">
                        <i class="fa fa-magic"></i> Mejorar presentaciones con IA
                    </a>
                    <button class="tau-btn-secondary" onclick="backToConfig()">
                        <i class="fa fa-plus"></i> Crear otro curso
                    </button>
                </div>
            </div>
        </div>
    </div>

</div><!-- .tau-creator -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
// ── Material file → textarea ───────────────────────────────────────────────
document.getElementById('f-material-file').addEventListener('change', function () {
    var file = this.files[0];
    if (!file) return;
    document.getElementById('f-material-file-name').textContent = file.name;
    var reader = new FileReader();
    reader.onload = function (e) {
        var text = e.target.result;
        if (text.length > 8000) text = text.slice(0, 8000);
        document.getElementById('f-material-text').value = text;
    };
    reader.readAsText(file, 'UTF-8');
});

// Toggle "Opciones de estructura"
(function() {
    var btn = document.getElementById('tau-opts-toggle');
    var panel = document.getElementById('tau-opts-panel');
    var icon = document.getElementById('tau-opts-icon');
    if (btn && panel) {
        btn.addEventListener('click', function() {
            var open = panel.style.display !== 'none';
            panel.style.display = open ? 'none' : 'block';
            if (icon) icon.style.transform = open ? '' : 'rotate(180deg)';
        });
    }
})();
</script>
<script>
(function () {
    const AJAX           = <?php echo json_encode($ajax_url); ?>;
    const STREAM         = <?php echo json_encode($stream_url); ?>;
    const SESSKEY        = <?php echo json_encode($sesskey); ?>;
    const PREFILL_PROMPT = <?php echo json_encode($prefill_prompt); ?>;

    let currentBlueprint = null;
    let currentLanguage  = 'es';
    let currentCategory  = '1';
    let currentProvider  = '<?php echo s($active_provider); ?>';
    let currentPreset    = 'impacto';

    // ── PDF/TXT Syllabus Drag and Drop Parser ──────────────────────────────────
    var dropzone = document.getElementById('tau-syllabus-dropzone');
    var fileInput = document.getElementById('f-syllabus-file');
    var defaultView = document.getElementById('dropzone-default');
    var loadingView = document.getElementById('dropzone-loading');
    var successView = document.getElementById('dropzone-success');
    var successMsg = document.getElementById('dropzone-success-msg');

    if (dropzone && fileInput) {
        // Prevent default drag behaviors
        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.background = 'rgba(95,63,159,0.08)';
                dropzone.style.borderColor = '#c62b3a';
            }, false);
        });

        ['dragleave', 'drop'].forEach(function(eventName) {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.background = 'rgba(95,63,159,0.03)';
                dropzone.style.borderColor = '#5f3f9f';
            }, false);
        });

        dropzone.addEventListener('drop', function(e) {
            var dt = e.dataTransfer;
            var files = dt.files;
            if (files.length > 0) {
                handleSyllabusFile(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                handleSyllabusFile(fileInput.files[0]);
            }
        });

        function handleSyllabusFile(file) {
            if (!file) return;

            // Show loading
            defaultView.style.display = 'none';
            successView.style.display = 'none';
            loadingView.style.display = 'block';

            var fileType = file.name.split('.').pop().toLowerCase();

            if (fileType === 'pdf') {
                if (window.pdfjsLib) {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
                }
                var reader = new FileReader();
                reader.onload = function() {
                    var typedarray = new Uint8Array(this.result);
                    pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
                        var maxPages = pdf.numPages;
                        var countPromises = [];
                        for (var j = 1; j <= maxPages; j++) {
                            countPromises.push(pdf.getPage(j).then(function(page) {
                                return page.getTextContent().then(function(textContent) {
                                    return textContent.items.map(function(item) {
                                        return item.str;
                                    }).join(' ');
                                });
                            }));
                        }
                        Promise.all(countPromises).then(function(texts) {
                            var fullText = texts.join('\n');
                            processSyllabusText(fullText, file.name);
                        }).catch(function(err) {
                            showError('Error al leer el PDF: ' + err.message);
                        });
                    }).catch(function(err) {
                        showError('Error al cargar el PDF: ' + err.message);
                    });
                };
                reader.readAsArrayBuffer(file);
            } else if (fileType === 'txt') {
                var reader = new FileReader();
                reader.onload = function(e) {
                    processSyllabusText(e.target.result, file.name);
                };
                reader.readAsText(file);
            } else {
                showError('Formato no soportado. Por favor, sube un archivo PDF o TXT.');
            }
        }

        function processSyllabusText(text, filename) {
            var promptInput = document.getElementById('f-prompt');
            if (promptInput) {
                promptInput.value = text;
            }
            
            // Auto-detect modules count
            var matches = text.match(/(módulo|modulo|unidad|sección|unidad\s+de\s+aprendizaje|tema)\s*\d+/gi);
            var detectedModules = 3; // default fallback

            if (matches) {
                var numbers = [];
                matches.forEach(function(m) {
                    var numMatch = m.match(/\d+/);
                    if (numMatch) {
                        var n = parseInt(numMatch[0]);
                        if (numbers.indexOf(n) === -1) {
                            numbers.push(n);
                        }
                    }
                });
                if (numbers.length > 0) {
                    detectedModules = Math.max.apply(null, numbers);
                }
            } else {
                // Try to find list numbers like "Unidad I, Unidad II" etc
                var romanMatches = text.match(/(módulo|modulo|unidad|sección|tema)\s*(I|II|III|IV|V|VI|VII|VIII|IX|X)/gi);
                if (romanMatches) {
                    var map = { 'I':1, 'II':2, 'III':3, 'IV':4, 'V':5, 'VI':6, 'VII':7, 'VIII':8, 'IX':9, 'X':10 };
                    var numbers = [];
                    romanMatches.forEach(function(m) {
                        var rom = m.split(/\s+/).pop().toUpperCase();
                        if (map[rom]) {
                            numbers.push(map[rom]);
                        }
                    });
                    if (numbers.length > 0) {
                        detectedModules = Math.max.apply(null, numbers);
                    }
                }
            }

            if (detectedModules > 24) detectedModules = 24;
            if (detectedModules < 1) detectedModules = 3;

            // Auto-set the inputs in Step 1
            var aiModulesInput = document.getElementById('f-ai-modules');
            if (aiModulesInput) {
                aiModulesInput.value = detectedModules;
            }
            var manualModulesInput = document.getElementById('f-manual-modules-count');
            if (manualModulesInput) {
                manualModulesInput.value = detectedModules;
            }

            // Show success
            loadingView.style.display = 'none';
            successMsg.textContent = '¡Sílabo procesado! Detectamos ' + detectedModules + ' módulos.';
            successView.style.display = 'block';
        }

        function showError(msg) {
            loadingView.style.display = 'none';
            defaultView.style.display = 'block';
            alert(msg);
        }
    }

    // ── Tab switching ─────────────────────────────────────────────────────────
    document.querySelectorAll('.tau-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tau-tab-btn').forEach(function (b) { b.classList.remove('active'); });
            document.querySelectorAll('.tau-tab-content').forEach(function (c) { c.classList.remove('active'); });
            this.classList.add('active');
            var targetTab = this.dataset.tab;
            var targetEl = document.getElementById(targetTab);
            if (targetEl) {
                targetEl.classList.add('active');
            }
        });
    });

    // ── AI activity chips selector ─────────────────────────────────────────────
    document.querySelectorAll('.tau-ai-activity-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            this.classList.toggle('active');
        });
    });

    // ── Prompt template loaders ────────────────────────────────────────────────
    document.querySelectorAll('.tau-template-item').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var prompt = this.dataset.prompt || '';
            var modules = this.dataset.modules || '3';
            var weeks = this.dataset.weeks || '4';
            var teacher = this.dataset.teacher || '';
            var welcome = this.dataset.welcome || '';
            var activities = (this.dataset.activities || '').split(',').filter(Boolean);

            var txt = document.getElementById('f-prompt');
            if (txt) {
                txt.value = prompt;
                txt.focus();
                txt.style.transition = 'none';
                txt.style.borderColor = '#c62b3a';
                txt.style.boxShadow = '0 0 0 4px rgba(198, 43, 58, 0.15)';
                setTimeout(function () {
                    txt.style.transition = 'all .4s ease';
                    txt.style.borderColor = '';
                    txt.style.boxShadow = '';
                }, 180);
            }

            var teacherInput = document.getElementById('f-teacher-name');
            if (teacherInput) {
                teacherInput.value = teacher;
            }

            var welcomeInput = document.getElementById('f-welcome-text');
            if (welcomeInput) {
                welcomeInput.value = welcome;
            }

            var modInput = document.getElementById('f-ai-modules');
            if (modInput) {
                modInput.value = modules;
            }

            var weekSelect = document.getElementById('f-ai-weeks');
            if (weekSelect) {
                weekSelect.value = weeks;
            }

            // Set active activity chips
            document.querySelectorAll('.tau-ai-activity-chip').forEach(function (chip) {
                var type = chip.dataset.type;
                if (activities.indexOf(type) !== -1) {
                    chip.classList.add('active');
                } else {
                    chip.classList.remove('active');
                }
            });
        });
    });

    // ── Generate general welcome via AI dynamically ────────────────────────────
    var welcomeBtn = document.getElementById('btn-generate-welcome-ai');
    if (welcomeBtn) {
        welcomeBtn.addEventListener('click', async function () {
            var prompt = document.getElementById('f-prompt').value.trim();
            var teacher = document.getElementById('f-teacher-name').value.trim() || 'Docente de la Institución';
            var welcomeInput = document.getElementById('f-welcome-text');
            var spinner = document.getElementById('welcome-spinner');

            if (!prompt) {
                alert('Por favor, escribe primero la temática o nombre del curso para poder generar la bienvenida.');
                document.getElementById('f-prompt').focus();
                return;
            }

            welcomeBtn.disabled = true;
            if (spinner) spinner.style.display = 'block';
            if (welcomeInput) {
                welcomeInput.disabled = true;
                welcomeInput.value = 'Generando bienvenida con IA...';
            }

            try {
                var res = await ajax('generatewelcome', {
                    prompt: prompt,
                    teacher: teacher,
                    language: document.getElementById('f-language').value,
                    provider: currentProvider
                });

                if (welcomeInput && res.welcome) {
                    welcomeInput.value = '';
                    var welcomeText = res.welcome;
                    var charIdx = 0;
                    // Cute typewriter animation
                    (function typeChar() {
                        if (charIdx < welcomeText.length) {
                            welcomeInput.value += welcomeText.charAt(charIdx);
                            charIdx++;
                            setTimeout(typeChar, 12);
                        } else {
                            welcomeInput.disabled = false;
                            welcomeBtn.disabled = false;
                        }
                    })();
                } else {
                    if (welcomeInput) {
                        welcomeInput.value = '';
                        welcomeInput.disabled = false;
                    }
                    welcomeBtn.disabled = false;
                }
            } catch (err) {
                alert('No se pudo generar la bienvenida: ' + err.message);
                if (welcomeInput) {
                    welcomeInput.value = '';
                    welcomeInput.disabled = false;
                }
                welcomeBtn.disabled = false;
            } finally {
                if (spinner) spinner.style.display = 'none';
            }
        });
    }

    // ── Provider selector ─────────────────────────────────────────────────────
    document.querySelectorAll('.tau-provider-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tau-provider-btn').forEach(function (b) {
                b.classList.remove('selected');
            });
            this.classList.add('selected');
            currentProvider = this.dataset.provider;
        });
    });

    // ── Activity toggles ──────────────────────────────────────────────────────
    document.querySelectorAll('.tau-toggle:not(.locked)').forEach(function (btn) {
        btn.addEventListener('click', function () {
            this.classList.toggle('selected');
        });
    });

    document.querySelectorAll('.tau-preset-card').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tau-preset-card').forEach(function (card) {
                card.classList.remove('selected');
            });
            this.classList.add('selected');
            currentPreset = this.dataset.preset || 'impacto';
        });
    });

    document.querySelectorAll('.tau-chip').forEach(function (btn) {
        btn.addEventListener('click', function () {
            this.classList.toggle('active');
        });
    });

    function getOptions() {
        var opts = { page: true };
        document.querySelectorAll('.tau-toggle.selected:not(.locked)').forEach(function (btn) {
            opts[btn.dataset.type] = true;
        });
        return opts;
    }

    function getTemplateResources() {
        return Array.from(document.querySelectorAll('.tau-chip.active')).map(function (btn) {
            return btn.dataset.resource;
        }).filter(Boolean);
    }

    function getTemplateConfig() {
        var modCountEl = document.getElementById('f-module-count');
        return {
            preset: currentPreset,
            moduleCount: Number(modCountEl ? modCountEl.value : 6),
            resources: getTemplateResources(),
            language: document.getElementById('f-language').value || 'es'
        };
    }

    function cloneData(data) {
        return JSON.parse(JSON.stringify(data));
    }

    function normalizeTextKey(value) {
        return String(value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }

    function buildPdfActivity(title, description) {
        return {
            type: 'resource',
            title: title,
            description: description,
            uploadedfile: null
        };
    }

    function ensureGeneralSectionDocuments(source) {
        if (!source || !Array.isArray(source.sections) || !source.sections.length) {
            return source;
        }

        var section = source.sections[0];
        if (!section) {
            return source;
        }
        if (!Array.isArray(section.activities)) {
            section.activities = [];
        }

        var definitions = [
            buildPdfActivity('Microcurrículo', 'Adjunta el microcurrículo oficial en formato PDF.'),
            buildPdfActivity('Ficha de desarrollo temático', 'Adjunta la ficha de desarrollo temático en formato PDF.')
        ];

        function matchesTitle(activity, expected) {
            return normalizeTextKey(activity && activity.title).indexOf(normalizeTextKey(expected)) !== -1;
        }

        var selected = definitions.map(function(definition) {
            return section.activities.find(function(activity) {
                return matchesTitle(activity, definition.title);
            }) || definition;
        });

        var remaining = section.activities.filter(function(activity) {
            return !definitions.some(function(definition) {
                return matchesTitle(activity, definition.title);
            });
        });

        var insertAt = remaining.findIndex(function(activity) {
            return normalizeTextKey(activity && activity.title).indexOf('reglamento estudiantil') !== -1;
        });
        if (insertAt === -1) {
            insertAt = remaining.findIndex(function(activity) {
                return normalizeTextKey(activity && activity.title).indexOf('foro de inquietudes') !== -1;
            });
        }
        if (insertAt === -1) {
            insertAt = remaining.length;
        }

        remaining.splice(insertAt, 0, selected[0], selected[1]);
        section.activities = remaining;
        return source;
    }

    function buildInstitutionalFrontActivity(config) {
        return {
            key: config.key,
            type: config.type || 'page',
            title: config.title || 'Nuevo recurso',
            description: config.description || '',
            content: config.content || '',
            externalurl: config.externalurl || '',
            forumtype: config.forumtype || '',
            uploadedfile: null
        };
    }

    function buildInstitutionalWelcomeHtml() {
        return [
            '<div style="font-family:Manrope,Inter,sans-serif;max-width:900px;margin:0 auto;padding:18px 8px;">',
            '<div style="border-radius:24px;overflow:hidden;background:linear-gradient(135deg,#6e1224 0%,#9d1f33 52%,#d43b4d 100%);box-shadow:0 18px 44px rgba(118,15,34,.18);">',
            '<div style="padding:34px 34px 28px;color:#fff;">',
            '<div style="display:inline-flex;align-items:center;gap:8px;padding:7px 14px;border-radius:999px;background:rgba(255,255,255,.12);font-size:.76rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">General</div>',
            '<h2 style="margin:18px 0 12px;font-size:2rem;line-height:1.15;font-weight:900;">Paz y Bien, apreciado estudiante. Bienvenido(a) al curso.</h2>',
            '<p style="margin:0;font-size:1rem;line-height:1.75;color:rgba(255,255,255,.9);max-width:720px;">Este espacio ha sido organizado para acompaÃ±arte con claridad, cercanÃ­a y sentido institucional durante todo tu proceso de aprendizaje.</p>',
            '</div></div></div>'
        ].join('');
    }

    function buildInstitutionalSupportHtml() {
        return [
            '<div style="font-family:Manrope,Inter,sans-serif;max-width:900px;margin:0 auto;padding:12px 8px;">',
            '<div style="border:1px solid rgba(198,43,58,.14);border-radius:22px;background:linear-gradient(180deg,#fff 0%,#fff8f9 100%);padding:24px 26px;box-shadow:0 12px 30px rgba(88,16,28,.06);">',
            '<div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;">',
            '<div style="width:56px;height:56px;border-radius:18px;background:#fff0f2;color:#b02230;font-size:1.45rem;display:flex;align-items:center;justify-content:center;">&#129309;</div>',
            '<div style="flex:1;min-width:240px;">',
            '<h3 style="margin:0 0 8px;font-size:1.2rem;font-weight:800;color:#172033;">Estamos Contigo</h3>',
            '<p style="margin:0;color:#5b6472;font-size:.95rem;line-height:1.7;">En este curso encontrarÃ¡s acompaÃ±amiento y orientaciÃ³n permanente. Usa este espacio para recordar que cuentas con apoyo docente, canales institucionales y recursos para avanzar con confianza.</p>',
            '</div></div></div></div>'
        ].join('');
    }

    function buildInstitutionalAiLibraryHtml() {
        var tools = [
            ['Productividad', 'ChatGPT', 'https://chatgpt.com'],
            ['InvestigaciÃ³n', 'Perplexity', 'https://www.perplexity.ai'],
            ['RedacciÃ³n', 'Claude', 'https://claude.ai'],
            ['DiseÃ±o', 'Canva Magic Studio', 'https://www.canva.com'],
            ['Presentaciones', 'Gamma', 'https://gamma.app'],
            ['BÃºsqueda acadÃ©mica', 'Consensus', 'https://consensus.app']
        ];
        var cards = tools.map(function(tool) {
            return '<a href="' + tool[2] + '" target="_blank" rel="noopener" style="text-decoration:none;border:1px solid rgba(15,23,42,.08);border-radius:18px;padding:16px 18px;background:#fff;color:#172033;display:block;box-shadow:0 10px 22px rgba(15,23,42,.04);">' +
                '<div style="font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#b02230;margin-bottom:8px;">' + tool[0] + '</div>' +
                '<div style="font-size:1rem;font-weight:800;margin-bottom:6px;">' + tool[1] + '</div>' +
                '<div style="font-size:.84rem;color:#667085;">Acceso directo a la herramienta</div>' +
            '</a>';
        }).join('');
        return [
            '<div style="font-family:Manrope,Inter,sans-serif;max-width:960px;margin:0 auto;padding:12px 8px;">',
            '<div style="margin-bottom:14px;">',
            '<h3 style="margin:0 0 8px;font-size:1.2rem;font-weight:800;color:#172033;">Biblioteca de Herramientas de Inteligencia Artificial</h3>',
            '<p style="margin:0;color:#5b6472;font-size:.95rem;line-height:1.7;">Explora herramientas organizadas por categorÃ­as para investigar, redactar, diseÃ±ar y fortalecer tu aprendizaje con apoyo de IA.</p>',
            '</div>',
            '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;">' + cards + '</div>',
            '</div>'
        ].join('');
    }

    function buildInstitutionalCommunicationHtml() {
        return [
            '<div style="font-family:Manrope,Inter,sans-serif;max-width:920px;margin:0 auto;padding:12px 8px;">',
            '<div style="border-radius:22px;background:linear-gradient(180deg,#ffffff 0%,#fff8fa 100%);border:1px solid rgba(198,43,58,.12);box-shadow:0 16px 36px rgba(88,16,28,.06);overflow:hidden;">',
            '<div style="padding:22px 26px;border-bottom:1px solid rgba(198,43,58,.08);">',
            '<div style="font-size:.72rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#b02230;margin-bottom:8px;">AcompaÃ±amiento</div>',
            '<h3 style="margin:0;font-size:1.18rem;font-weight:800;color:#172033;">Canales de comunicaciÃ³n o acompaÃ±amiento</h3>',
            '</div>',
            '<div style="padding:24px 26px;">',
            '<p style="margin:0 0 14px;color:#5b6472;font-size:.95rem;line-height:1.75;">Registra aquÃ­ los horarios de atenciÃ³n, medios de contacto, correos institucionales, enlaces a videollamadas o recomendaciones de acompaÃ±amiento para que el estudiante los encuentre en un formato claro y profesional.</p>',
            '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:12px;">',
            '<div style="padding:14px 16px;border-radius:16px;background:#fff;border:1px solid rgba(15,23,42,.08);"><strong style="display:block;color:#172033;margin-bottom:6px;">Canal principal</strong><span style="color:#667085;font-size:.88rem;">Escribe aquÃ­ el medio principal de contacto.</span></div>',
            '<div style="padding:14px 16px;border-radius:16px;background:#fff;border:1px solid rgba(15,23,42,.08);"><strong style="display:block;color:#172033;margin-bottom:6px;">Horario de acompaÃ±amiento</strong><span style="color:#667085;font-size:.88rem;">Indica disponibilidad, franjas y tiempos de respuesta.</span></div>',
            '<div style="padding:14px 16px;border-radius:16px;background:#fff;border:1px solid rgba(15,23,42,.08);"><strong style="display:block;color:#172033;margin-bottom:6px;">Orientaciones</strong><span style="color:#667085;font-size:.88rem;">Comparte recomendaciones de uso y convivencia.</span></div>',
            '</div></div></div></div>'
        ].join('');
    }

    function identifyInstitutionalFrontKey(activity) {
        var titleKey = normalizeTextKey(activity && activity.title);
        if (titleKey.indexOf('bienvenida') !== -1) return 'welcome';
        if (titleKey.indexOf('estamos contigo') !== -1) return 'support';
        if (titleKey.indexOf('noticias') !== -1 || titleKey.indexOf('comunicados') !== -1) return 'news';
        if (titleKey.indexOf('herramientas de inteligencia artificial') !== -1 || titleKey.indexOf('biblioteca de herramientas') !== -1) return 'ai-library';
        if (titleKey.indexOf('biblioteca digital') !== -1) return 'digital-library';
        if (titleKey.indexOf('presentacion del docente') !== -1 || titleKey.indexOf('presentacion profesor') !== -1) return 'teacher-intro';
        if (titleKey.indexOf('reglamento estudiantil') !== -1) return 'reglamento';
        if (titleKey.indexOf('microcurriculo') !== -1) return 'microcurriculo';
        if (titleKey.indexOf('ficha de desarrollo') !== -1) return 'ficha-desarrollo';
        if (titleKey.indexOf('horario') !== -1) return 'horarios';
        if (titleKey.indexOf('canales de comunicacion') !== -1 || titleKey.indexOf('acompanamiento') !== -1) return 'communication';
        return '';
    }

    function isInstitutionalPdfKey(key) {
        return ['microcurriculo', 'ficha-desarrollo', 'horarios'].indexOf(key) !== -1;
    }

    function detectInstitutionalFrontBucket(section) {
        var titleKey = normalizeTextKey(section && section.title);
        if (titleKey === 'general') {
            return 'general';
        }
        if (titleKey.indexOf('informacion general') !== -1) {
            return 'information';
        }

        var hasGeneralMatch = false;
        var hasInformationMatch = false;
        (Array.isArray(section && section.activities) ? section.activities : []).forEach(function(activity) {
            var key = identifyInstitutionalFrontKey(activity);
            if (['welcome', 'support', 'news', 'ai-library', 'digital-library'].indexOf(key) !== -1) {
                hasGeneralMatch = true;
            }
            if (['teacher-intro', 'reglamento', 'microcurriculo', 'ficha-desarrollo', 'horarios', 'communication'].indexOf(key) !== -1) {
                hasInformationMatch = true;
            }
        });

        if (hasGeneralMatch && !hasInformationMatch) {
            return 'general';
        }
        if (hasInformationMatch && !hasGeneralMatch) {
            return 'information';
        }
        if (hasGeneralMatch && hasInformationMatch) {
            return 'information';
        }
        return '';
    }

    function isLegacyInstitutionalActivity(activity) {
        var titleKey = normalizeTextKey(activity && activity.title);
        return titleKey.indexOf('foro de inquietudes') !== -1 ||
            titleKey.indexOf('foro de dudas') !== -1 ||
            titleKey.indexOf('tips de plataforma') !== -1 ||
            titleKey === 'general' ||
            titleKey === 'informacion general';
    }

    function cleanupInstitutionalActivities(activities) {
        var seenKeys = new Set();
        return (Array.isArray(activities) ? activities : []).filter(function(activity) {
            if (!activity || isLegacyInstitutionalActivity(activity)) {
                return false;
            }

            var key = identifyInstitutionalFrontKey(activity);
            if (!key) {
                return true;
            }

            if (seenKeys.has(key)) {
                return false;
            }
            seenKeys.add(key);
            return true;
        });
    }

    function ensureInstitutionalFrontSections(source, teacherName) {
        if (!source) {
            return source;
        }
        var defaults = [
            {
                title: 'General',
                summary: 'Espacio inicial de acogida, orientaciÃ³n, noticias y recursos institucionales del curso.',
                activities: [
                    buildInstitutionalFrontActivity({ key: 'welcome', type: 'page', title: 'Bienvenida', description: 'Mensaje de acogida institucional para el estudiante.', content: buildInstitutionalWelcomeHtml() }),
                    buildInstitutionalFrontActivity({ key: 'support', type: 'page', title: 'Estamos Contigo', description: 'AcompaÃ±amiento y orientaciÃ³n permanente para el estudiante.', content: buildInstitutionalSupportHtml() }),
                    buildInstitutionalFrontActivity({ key: 'news', type: 'forum', title: 'Noticias y Comunicados', description: 'Foro para la publicaciÃ³n de avisos, novedades, fechas importantes y comunicados del curso.', forumtype: 'news' }),
                    buildInstitutionalFrontActivity({ key: 'ai-library', type: 'page', title: 'Biblioteca de Herramientas de Inteligencia Artificial', description: 'Accesos directos a herramientas de IA de apoyo al aprendizaje.', content: buildInstitutionalAiLibraryHtml() }),
                    buildInstitutionalFrontActivity({ key: 'digital-library', type: 'url', title: 'Biblioteca Digital', description: 'Consulta acadÃ©mica, bases de datos, libros digitales y recursos institucionales.', externalurl: 'https://www.unicesmag.edu.co/biblioteca/' })
                ]
            },
            {
                title: 'InformaciÃ³n General',
                summary: 'Documentos, lineamientos y orientaciones clave para comprender el desarrollo del curso.',
                activities: [
                    buildInstitutionalFrontActivity({ key: 'teacher-intro', type: 'page', title: 'PresentaciÃ³n del Docente ' + (teacherName || 'Docente de la InstituciÃ³n'), description: 'Bienvenida profesional y perfil del docente.' }),
                    buildInstitutionalFrontActivity({ key: 'reglamento', type: 'url', title: 'Reglamento Estudiantil', description: 'Consulta las normas y lineamientos institucionales del curso.' }),
                    buildInstitutionalFrontActivity({ key: 'microcurriculo', type: 'resource', title: 'MicrocurrÃ­culo', description: 'Adjunta el microcurrÃ­culo oficial en formato PDF.' }),
                    buildInstitutionalFrontActivity({ key: 'ficha-desarrollo', type: 'resource', title: 'Ficha de desarrollo temÃ¡tico', description: 'Adjunta la ficha de desarrollo temÃ¡tico en formato PDF.' }),
                    buildInstitutionalFrontActivity({ key: 'horarios', type: 'resource', title: 'Horarios', description: 'Adjunta el horario en PDF o cambia este recurso a URL si corresponde.' }),
                    buildInstitutionalFrontActivity({ key: 'communication', type: 'page', title: 'Canales de comunicaciÃ³n o acompaÃ±amiento', description: 'Publica aquÃ­ los medios, horarios y orientaciones de contacto para el estudiante.', content: buildInstitutionalCommunicationHtml() })
                ]
            }
        ];

        var sections = Array.isArray(source.sections) ? source.sections : [];
        var collected = { general: [], information: [] };
        var summaries = { general: '', information: '' };
        var others = [];

        sections.forEach(function(section, index) {
            var bucket = detectInstitutionalFrontBucket(section);
            if (!bucket) {
                others.push(section);
                return;
            }
            if (section && section.summary && !summaries[bucket]) {
                summaries[bucket] = section.summary;
            }
            (Array.isArray(section.activities) ? section.activities : []).forEach(function(activity) {
                var key = identifyInstitutionalFrontKey(activity);
                if (['welcome', 'support', 'news', 'ai-library', 'digital-library'].indexOf(key) !== -1) {
                    collected.general.push(activity);
                } else if (['teacher-intro', 'reglamento', 'microcurriculo', 'ficha-desarrollo', 'horarios', 'communication'].indexOf(key) !== -1) {
                    collected.information.push(activity);
                } else if (bucket === 'general') {
                    collected.general.push(activity);
                } else {
                    collected.information.push(activity);
                }
            });
        });

        function mergeActivities(defaultItems, existingItems) {
            var used = new Set();
            var merged = defaultItems.map(function(definition) {
                var matchIndex = existingItems.findIndex(function(activity, idx) {
                    return !used.has(idx) && identifyInstitutionalFrontKey(activity) === definition.key;
                });
                if (matchIndex === -1) {
                    return definition;
                }
                used.add(matchIndex);
                var existing = existingItems[matchIndex] || {};
                var resolvedType = isInstitutionalPdfKey(definition.key)
                    ? 'resource'
                    : (existing.type || definition.type);
                return Object.assign({}, definition, existing, {
                    type: resolvedType,
                    title: existing.title || definition.title,
                    description: existing.description || definition.description,
                    content: existing.content || definition.content || '',
                    externalurl: existing.externalurl || definition.externalurl || '',
                    forumtype: existing.forumtype || definition.forumtype || '',
                    uploadedfile: existing.uploadedfile || null
                });
            });
            existingItems.forEach(function(activity, idx) {
                if (!used.has(idx)) {
                    merged.push(activity);
                }
            });
            return cleanupInstitutionalActivities(merged);
        }

        source.sections = [
            {
                title: defaults[0].title,
                summary: summaries.general || defaults[0].summary,
                activities: mergeActivities(defaults[0].activities, collected.general)
            },
            {
                title: defaults[1].title,
                summary: summaries.information || defaults[1].summary,
                activities: mergeActivities(defaults[1].activities, collected.information)
            }
        ].concat(others);
        return source;
    }

    function normalizeBlueprint(bp) {
        var source = cloneData(bp || {});
        var teacherEl = document.getElementById('f-teacher-name');
        var teacherName = source.teacherName || (teacherEl ? teacherEl.value.trim() : 'Docente de la InstituciÃ³n');
        ensureInstitutionalFrontSections(source, teacherName);
        var sections = Array.isArray(source.sections) ? source.sections : [];
        var publishCheckbox = document.getElementById('f-publish-apoyo');
        
        var publishApoyo = 1;
        if (publishCheckbox) {
            publishApoyo = publishCheckbox.checked ? 1 : 0;
        } else if (source.publishApoyo !== undefined) {
            publishApoyo = source.publishApoyo ? 1 : 0;
        }
        
        var moduleCount = 0;
        var weekCount = 0;
        
        var normalizedSections = sections.map(function (section, index) {
            var title = section.title || '';
            var titleKey = normalizeTextKey(title);
            var isGeneral = titleKey === 'general' || titleKey.indexOf('informacion general') !== -1;
            
            var newTitle = title;
            if (isGeneral) {
                newTitle = titleKey === 'general' ? 'General' : 'Información General';
            } else if (title.includes(' — Semana ') || title.includes(' - Semana ') || title.includes(' — Sección ') || title.includes(' - Sección ')) {
                weekCount++;
                // Extract original subtheme after the colon or dash
                var parts = title.split(':');
                var subtheme = parts.length > 1 ? parts.slice(1).join(':').trim() : '';
                if (!subtheme) {
                    var dashParts = title.split('—');
                    if (dashParts.length > 1) {
                        subtheme = dashParts.slice(1).join('—').trim();
                    } else {
                        subtheme = title.replace(/^Módulo\s+\d+\s*[\-—]\s*(Semana|Sección)\s+\d+\s*[\-—\:]?\s*/i, '').trim();
                    }
                }
                newTitle = 'Módulo ' + moduleCount + ' — Sección ' + weekCount + ': ' + (subtheme || 'Tema de aprendizaje');
            } else {
                moduleCount++;
                weekCount = 0; // reset week count for new module
                // Extract original module theme
                var parts = title.split(':');
                var theme = parts.length > 1 ? parts.slice(1).join(':').trim() : '';
                if (!theme) {
                    theme = title.replace(/^Módulo\s+\d+\s*[\-—\:]?\s*/i, '').trim();
                }
                newTitle = 'Módulo ' + moduleCount + ': ' + (theme || 'Tema principal');
            }

            return {
                title: newTitle,
                summary: section.summary || '',
                activities: Array.isArray(section.activities) ? section.activities.map(function (activity) {
                    return {
                        type: activity.type || 'page',
                        title: activity.title || 'Nuevo recurso',
                        description: activity.description || '',
                        content: activity.content || '',
                        externalurl: activity.externalurl || '',
                        forumtype: activity.forumtype || '',
                        uploadedfile: activity.uploadedfile || null,
                        questions: Array.isArray(activity.questions) ? activity.questions : undefined,
                        terms: Array.isArray(activity.terms) ? activity.terms : undefined
                    };
                }) : []
            };
        });

        return {
            courseName: source.courseName || 'Nuevo curso',
            courseDescription: source.courseDescription || '',
            teacherName: source.teacherName || (teacherEl ? teacherEl.value.trim() : 'Docente de la Institución'),
            publishApoyo: publishApoyo,
            sections: normalizedSections
        };
    }

    function buildActivity(type, moduleIndex, preset) {
        var moduleNumber = moduleIndex + 1;
        var labels = {
            page: ['Panorama del módulo', 'Explica los conceptos clave, objetivos y ruta de trabajo.'],
            resource: ['Recurso descargable', 'Adjunta o reemplaza por una guía, lectura o formato institucional.'],
            url: ['Enlace curado', 'Agrega aquí un recurso web, video, artículo o repositorio relevante.'],
            forum: ['Foro de reflexión', 'Abre una conversación aplicada sobre el tema del módulo.'],
            assign: ['Evidencia de aprendizaje', 'Solicita una entrega breve o avance aplicando lo visto en clase.'],
            quiz: ['Chequeo de comprensión', 'Incluye preguntas rápidas para validar comprensión del módulo.'],
            glossary: ['Glosario del módulo', 'Recopila conceptos clave y definiciones compartidas.'],
            feedback: ['Pulso de cierre', 'Recoge retroalimentación sobre la experiencia y aprendizajes del módulo.']
        };
        var fallback = labels[type] || ['Actividad', 'Describe aquí la actividad.'];
        var title = fallback[0] + ' ' + moduleNumber;
        if (preset === 'investigacion' && type === 'assign') {
            title = 'Hito de proyecto ' + moduleNumber;
        }
        if (preset === 'taller' && type === 'quiz') {
            title = 'Validación práctica ' + moduleNumber;
        }
        return {
            type: type,
            title: title,
            description: fallback[1],
            uploadedfile: null
        };
    }

    function renderActivityAttachmentField(activity, sectionIndex, activityIndex) {
        var key = identifyInstitutionalFrontKey(activity);
        var shouldRenderUpload = (activity.type || 'page') === 'resource' || isInstitutionalPdfKey(key);
        if (!shouldRenderUpload) {
            return '';
        }
        var uploaded = activity.uploadedfile || null;
        var fileName = uploaded && uploaded.name ? uploaded.name : '';
        var helper = fileName
            ? 'PDF cargado: ' + escHtml(fileName)
            : 'Adjunta aquí el PDF institucional correspondiente.';
        return '<div class="tau-inline-field tau-resource-upload">' +
            '<label>Adjuntar PDF</label>' +
            '<div class="tau-resource-uploadbox">' +
                '<input type="file" accept="application/pdf,.pdf" data-resource-upload="1" data-section-index="' + sectionIndex + '" data-activity-index="' + activityIndex + '">' +
            '</div>' +
            '<small class="tau-resource-uploadmeta">' + helper + '</small>' +
        '</div>';
    }

    function createStarterBlueprint() {
        var prompt = document.getElementById('f-prompt').value.trim();
        var config = getTemplateConfig();
        var sections = [];
        var presetCopy = {
            impacto: {
                opener: 'Aterriza el valor del tema, activa el interés y conduce hacia una evidencia concreta.',
                titles: ['Apertura estratégica', 'Conceptos esenciales', 'Aplicación guiada', 'Profundización', 'Resolución de casos', 'Cierre con evidencia']
            },
            taller: {
                opener: 'Organiza el aprendizaje por práctica, demostración y producción progresiva.',
                titles: ['Preparación del taller', 'Demostración guiada', 'Práctica acompañada', 'Desarrollo autónomo', 'Ajustes y retroalimentación', 'Entrega final']
            },
            investigacion: {
                opener: 'Estructura el curso como una ruta de exploración, análisis y producción académica.',
                titles: ['Problema y contexto', 'Marco conceptual', 'Diseño metodológico', 'Recolección y análisis', 'Discusión de hallazgos', 'Socialización final']
            }
        };
        var presetMeta = presetCopy[config.preset] || presetCopy.impacto;
        var resourceTypes = config.resources.length ? config.resources : ['page', 'resource', 'url'];
        var courseName = prompt ? prompt.slice(0, 90) : 'Curso prediseñado';

        for (var i = 0; i < config.moduleCount; i++) {
            var title = presetMeta.titles[i] || ('Módulo ' + (i + 1));
            var activities = resourceTypes.map(function (type) {
                return buildActivity(type, i, config.preset);
            });
            sections.push({
                title: title,
                summary: presetMeta.opener + ' Módulo ' + (i + 1) + '.',
                activities: activities
            });
        }

        return normalizeBlueprint({
            courseName: courseName,
            courseDescription: 'Plantilla base editable creada para que puedas ajustar módulos, recursos y actividades antes de publicar el curso.',
            sections: sections
        });
    }

    // ── Advanced panel ────────────────────────────────────────────────────────
    document.getElementById('advanced-toggle-btn').addEventListener('click', function () {
        document.getElementById('advanced-panel').classList.toggle('open');
    });

    // ── Step navigation ───────────────────────────────────────────────────────
    function showStep(id) {
        document.querySelectorAll('.tau-step').forEach(function (s) { s.classList.remove('active'); });
        document.getElementById(id).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    window.backToConfig = function () {
        currentBlueprint = null;
        document.getElementById('chat-messages').innerHTML = '';
        document.getElementById('plan-preview').style.display = 'none';
        document.getElementById('blueprint-editor').style.display = 'none';
        document.getElementById('chat-section').style.display = 'none';
        document.getElementById('plan-actions').style.display = 'none';
        showStep('step-config');
    };

    function showEditablePlan(blueprint, title, subtitle) {
        currentBlueprint = normalizeBlueprint(blueprint);
        renderPlan(currentBlueprint);
        renderBlueprintEditor(currentBlueprint);
        setPlanStatus('done', title, subtitle);
        document.getElementById('plan-preview').style.display = 'block';
        document.getElementById('blueprint-editor').style.display = 'block';
        document.getElementById('chat-section').style.display = 'block';
        document.getElementById('plan-actions').style.display = 'block';
    }

    // Manual modules count listener
    var manualCountInput = document.getElementById('f-manual-modules-count');
    if (manualCountInput) {
        function triggerManualGeneration() {
            var count = parseInt(manualCountInput.value);
            if (isNaN(count) || count < 1) {
                return; // Do nothing if it's empty or invalid
            }
            if (count > 24) {
                alert('El número máximo de módulos permitido es 24.');
                manualCountInput.value = 24;
                count = 24;
            }

            var promptValue = document.getElementById('f-prompt').value.trim();
            var welcomeText = document.getElementById('f-welcome-text').value.trim();
            var teacherName = document.getElementById('f-teacher-name').value.trim() || 'Docente de la Institución';

            // Generate clean skeleton
            var sections = [];
            
            // 1. General e Información General
            sections.push({
                title: 'General',
                summary: 'Espacio inicial de acogida, noticias, recursos institucionales y acompañamiento.',
                activities: [
                    {
                        type: 'page',
                        title: 'Bienvenida',
                        description: 'Mensaje de acogida institucional para el estudiante.'
                    },
                    {
                        type: 'page',
                        title: 'Estamos Contigo',
                        description: 'Acompañamiento y orientación permanente para el estudiante.'
                    },
                    {
                        type: 'forum',
                        title: 'Noticias y Comunicados',
                        description: 'Foro para la publicación de avisos, novedades, fechas importantes y comunicados del curso.'
                    },
                    {
                        type: 'page',
                        title: 'Biblioteca de Herramientas de Inteligencia Artificial',
                        description: 'Accesos directos a herramientas de IA de apoyo al aprendizaje.'
                    },
                    {
                        type: 'url',
                        title: 'Biblioteca Digital',
                        description: 'Consulta académica, bases de datos, libros digitales y recursos institucionales.'
                    }
                ]
            });

            sections.push({
                title: 'Información General',
                summary: welcomeText || 'Documentos, lineamientos y orientaciones clave para comprender el desarrollo del curso.',
                activities: [
                    {
                        type: 'page',
                        title: 'Presentación del Docente ' + teacherName,
                        description: 'Bienvenida profesional y perfil del docente.'
                    },
                    {
                        type: 'url',
                        title: 'Reglamento Estudiantil',
                        description: 'Consulta las normas y lineamientos institucionales del curso.'
                    },
                    {
                        type: 'resource',
                        title: 'Microcurrículo',
                        description: 'Adjunta el microcurrículo oficial en formato PDF.',
                        uploadedfile: null
                    },
                    {
                        type: 'resource',
                        title: 'Ficha de desarrollo temático',
                        description: 'Adjunta la ficha de desarrollo temático en formato PDF.',
                        uploadedfile: null
                    },
                    {
                        type: 'resource',
                        title: 'Horarios',
                        description: 'Adjunta el horario en PDF o cambia este recurso a URL si corresponde.',
                        uploadedfile: null
                    },
                    {
                        type: 'page',
                        title: 'Canales de comunicación o acompañamiento',
                        description: 'Publica aquí los medios, horarios y orientaciones de contacto para el estudiante.'
                    }
                ]
            });

            // 2. Add N modules, each with exactly 4 sections
            for (var i = 1; i <= count; i++) {
                sections.push({
                    title: 'Módulo ' + i + ': Tema principal ' + i,
                    summary: 'Describe los resultados de aprendizaje esperados y objetivos de esta unidad modular.',
                    activities: []
                });

                for (var j = 1; j <= 4; j++) {
                    sections.push({
                        title: 'Módulo ' + i + ' — Sección ' + j + ': Subtema de la sección ' + j,
                        summary: 'Describe la temática específica a estudiar durante esta sección académica.',
                        activities: []
                    });
                }
            }

            currentLanguage = document.getElementById('f-language').value;
            currentCategory = document.getElementById('f-category').value;

            var bp = {
                courseName: promptValue || 'Curso Manual',
                courseDescription: welcomeText || 'Estructura base del curso editada manualmente.',
                teacherName: teacherName,
                sections: sections
            };

            showStep('step-plan');
            showEditablePlan(
                bp,
                'Estructura base generada (' + count + ' módulos)',
                'Hemos construido la estructura inicial con 4 secciones por módulo. Ahora puedes colapsar acordeones, agregar, reordenar o eliminar elementos libremente.'
            );
        }

        manualCountInput.addEventListener('blur', triggerManualGeneration);
        manualCountInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerManualGeneration();
            }
        });
    }

    // ── Form submit → generate plan ───────────────────────────────────────────
    document.getElementById('creator-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        var rawPrompt = document.getElementById('f-prompt').value.trim();
        if (!rawPrompt) {
            setAlert('step-config-alert', 'danger', 'Por favor escribe un título, temática o pega tu sílabo.');
            return;
        }

        var teacherName = document.getElementById('f-teacher-name').value.trim() || 'Docente de la Institución';
        var welcomeText = document.getElementById('f-welcome-text').value.trim();
        var numModules = Number(document.getElementById('f-ai-modules').value || 3);
        var weeksPerModule = Number(document.getElementById('f-ai-weeks').value || 4);
        
        // Collect active chips
        var selectedActivities = [];
        document.querySelectorAll('.tau-ai-activity-chip.active').forEach(function(chip) {
            selectedActivities.push(chip.dataset.type);
        });

        // Assemble the system prompt
        var finalPrompt = "Genera la estructura de un curso formal para Moodle bajo el nombre: '" + rawPrompt + "', dictado por el docente '" + teacherName + "'.\n\n";
        
        // 1. General Information Section
        finalPrompt += "ESTRUCTURA DE SECCIONES INICIALES:\n";
        finalPrompt += "- Debes crear obligatoriamente dos secciones iniciales antes de los módulos: 'General' e 'Información General'.\n";
        finalPrompt += "- La sección 'General' debe incluir:\n";
        finalPrompt += "  - Una actividad de tipo 'page' titulada 'Bienvenida' con enfoque institucional y mensaje de acogida al estudiante.\n";
        finalPrompt += "  - Una actividad de tipo 'page' titulada 'Estamos Contigo' para acompañamiento y orientación permanente.\n";
        finalPrompt += "  - Una actividad de tipo 'forum' titulada 'Noticias y Comunicados' para avisos y novedades del curso.\n";
        finalPrompt += "  - Una actividad de tipo 'page' titulada 'Biblioteca de Herramientas de Inteligencia Artificial'.\n";
        finalPrompt += "  - Una actividad de tipo 'url' titulada 'Biblioteca Digital' con acceso a recursos institucionales.\n";
        finalPrompt += "- La sección 'Información General' debe incluir:\n";
        finalPrompt += "  - Una actividad de tipo 'page' titulada 'Presentación del Docente " + teacherName + "'.\n";
        finalPrompt += "  - Una actividad de tipo 'url' titulada 'Reglamento Estudiantil'.\n";
        finalPrompt += "  - Una actividad de tipo 'resource' titulada 'Microcurrículo', preparada para adjuntar PDF.\n";
        finalPrompt += "  - Una actividad de tipo 'resource' titulada 'Ficha de desarrollo temático', preparada para adjuntar PDF.\n";
        finalPrompt += "  - Una actividad de tipo 'resource' titulada 'Horarios', preparada para adjuntar PDF o reemplazarse por URL.\n";
        finalPrompt += "  - Una actividad de tipo 'page' titulada 'Canales de comunicación o acompañamiento'.\n\n";

        // 2. Modules and Sections
        finalPrompt += "ESTRUCTURA DE MÓDULOS Y SECCIONES:\n";
        finalPrompt += "- Debes crear exactamente " + numModules + " módulos de contenido.\n";
        finalPrompt += "- CADA módulo se compondrá de exactamente " + weeksPerModule + " secciones de aprendizaje (máximo 4 secciones por módulo).\n";
        finalPrompt += "- Para cada módulo, incluye primero una sección vacía de cabecera titulada exactamente: 'Módulo X: [Tema del Módulo]'.\n";
        finalPrompt += "- Inmediatamente después de la sección del módulo, crea las " + weeksPerModule + " secciones correspondientes, tituladas exactamente: 'Módulo X — Sección Y: [Subtema de la sección]'.\n";
        
        if (selectedActivities.length > 0) {
            var actLabels = {
                page: 'page (Material de estudio estructurado)',
                resource: 'resource (Presentaciones y PDF descargables de apoyo)',
                url: 'url (Ampliación de conocimientos, videos y lecturas complementarias)',
                forum: 'forum (Foro de discusión de la sección)',
                assign: 'assign (Taller, actividad en clase o entregable práctico)',
                quiz: 'quiz (Cuestionario o Quiz de validación rápida)',
                glossary: 'glossary (Glosario de conceptos clave)',
                feedback: 'feedback (Encuesta de retroalimentación de la sección)'
            };
            var actList = selectedActivities.map(function(type) { return "  - " + (actLabels[type] || type); }).join('\n');
            finalPrompt += "- CADA una de las secciones de aprendizaje DEBE incluir obligatoriamente las siguientes actividades e instrumentos:\n" + actList + "\n";
        }
        
        finalPrompt += "\nAdicionalmente, recuerda inyectar un cuestionario real con 5 preguntas si se solicitó 'quiz' o un glosario con 5 términos si se solicitó 'glossary'.";

        currentLanguage = document.getElementById('f-language').value;
        currentCategory = document.getElementById('f-category').value;

        showStep('step-plan');
        var providerLabel = {'ollama':'Ollama (local)','claude':'Claude','openai':'OpenAI'}[currentProvider] || currentProvider;
        setPlanStatus('spinner', 'Generando plan del curso', 'Usando ' + providerLabel + ' — diseñando la estructura del curso...');
        document.getElementById('plan-preview').style.display  = 'none';
        document.getElementById('blueprint-editor').style.display  = 'none';
        document.getElementById('chat-section').style.display  = 'none';
        document.getElementById('plan-actions').style.display  = 'none';

        try {
            var generatedBlueprint = await streamPlan({
                prompt:       finalPrompt,
                language:     currentLanguage,
                systemPrompt: document.getElementById('f-system').value,
                options:      getOptions(),
                provider:     currentProvider,
            });

            showEditablePlan(
                generatedBlueprint,
                'Plan generado',
                'Revisa la estructura. Puedes ajustar módulos, recursos y actividades antes de crear el curso.'
            );

        } catch (err) {
            showStep('step-config');
            setAlert('step-config-alert', 'danger', '<strong>Error:</strong> ' + escHtml(err.message));
        }
    });

    // ── Chat refinement ────────────────────────────────────────────────────────
    window.sendChat = async function () {
        var input = document.getElementById('chat-input');
        var msg   = input.value.trim();
        if (!msg || !currentBlueprint) return;

        input.value = '';
        document.getElementById('chat-send').disabled = true;

        appendChatMsg('user', msg);

        setPlanStatus('spinner', 'Ajustando el plan', 'La IA está aplicando tus cambios...');
        document.getElementById('plan-preview').style.display = 'none';
        document.getElementById('blueprint-editor').style.display = 'none';
        document.getElementById('plan-actions').style.display = 'none';

        try {
            var refinedBlueprint = await streamPlan({
                action:      'chat',
                instruction: msg,
                blueprint:   currentBlueprint,
                language:    currentLanguage,
            });
            showEditablePlan(
                refinedBlueprint,
                'Plan actualizado',
                'Plan ajustado. Puedes seguir refinando con IA o editar manualmente antes de crear el curso.'
            );
            appendChatMsg('assistant', '✓ Plan actualizado según tus instrucciones.');
        } catch (err) {
            appendChatMsg('assistant', '⚠ ' + err.message);
            setPlanStatus('done', 'Plan anterior', 'No se pudo actualizar. Intenta de nuevo.');
            document.getElementById('plan-preview').style.display = 'block';
            document.getElementById('blueprint-editor').style.display = 'block';
            document.getElementById('plan-actions').style.display = 'block';
        }
        document.getElementById('chat-send').disabled = false;
    };

    document.getElementById('chat-input').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') sendChat();
    });

    // Pre-fill from course description query param
    if (PREFILL_PROMPT) {
        document.getElementById('f-prompt').value = PREFILL_PROMPT;
        setTimeout(function () {
            document.getElementById('creator-form').dispatchEvent(new Event('submit'));
        }, 300);
    }

    // ── Build course ───────────────────────────────────────────────────────────
    window.startBuild = async function () {
        if (!currentBlueprint) return;

        showStep('step-building');
        var log = document.getElementById('build-log');
        log.innerHTML = '';
        addLog('🚀 Iniciando creación del curso en Moodle...');

        try {
            var bp = normalizeBlueprint(currentBlueprint);
            var materialText = (document.getElementById('f-material-text').value || '').trim();
            if (materialText) {
                bp.sourceMaterial = materialText.slice(0, 8000);
            }
            var res = await ajax('build', {
                blueprint: bp,
                category:  currentCategory,
            });

            (res.log || []).forEach(function (m) { addLog('🌿 ' + m); });
            addLog('✅ ¡Curso creado exitosamente!');

            document.getElementById('link-course').href = res.courseUrl;
            var improveBase = '<?php echo (new moodle_url('/local/tau_course_creator_ai/improve.php'))->out(false); ?>';
            document.getElementById('link-improve').href = improveBase + '?courseid=' + res.courseId;
            setTimeout(function () { showStep('step-done'); }, 700);

        } catch (err) {
            addLog('❌ Error: ' + err.message);
        }
    };

    function addLog(text) {
        var log = document.getElementById('build-log');
        var el  = document.createElement('div');
        el.className = 'tau-log-item';
        el.innerHTML = '<span>' + escHtml(text) + '</span>';
        log.appendChild(el);
        log.scrollTop = log.scrollHeight;
    }

    // ── Render plan preview ────────────────────────────────────────────────────
    function renderPlan(bp) {
        var el       = document.getElementById('plan-preview');
        var sections = bp.sections || [];

        var typeIcons = {
            page: 'fa-file-text-o', quiz: 'fa-question-circle-o', forum: 'fa-comments-o',
            assign: 'fa-upload', url: 'fa-link', glossary: 'fa-book',
            feedback: 'fa-star-o', label: 'fa-tag', resource: 'fa-file-pdf-o',
        };

        var html = '<h2>' + escHtml(bp.courseName || '') + '</h2>';
        if (bp.courseDescription) {
            html += '<p class="course-desc">' + escHtml(bp.courseDescription) + '</p>';
        }

        // Grouping algorithm for preview and editor:
        var groups = [];
        var currentModuleGroup = null;
        sections.forEach(function (sec, idx) {
            var title = sec.title || '';
            var titleKey = normalizeTextKey(title);
            var isGeneral = titleKey === 'general' || titleKey.indexOf('informacion general') !== -1;
            
            if (isGeneral) {
                groups.push({
                    type: 'general',
                    section: sec,
                    originalIndex: idx
                });
            } else if (title.includes(' — Semana ') || title.includes(' - Semana ') || title.includes(' — Sección ') || title.includes(' - Sección ')) {
                if (currentModuleGroup) {
                    currentModuleGroup.weeks.push({
                        section: sec,
                        originalIndex: idx
                    });
                } else {
                    groups.push({
                        type: 'standalone-week',
                        section: sec,
                        originalIndex: idx
                    });
                }
            } else {
                currentModuleGroup = {
                    type: 'module',
                    section: sec,
                    originalIndex: idx,
                    weeks: []
                };
                groups.push(currentModuleGroup);
            }
        });

        // Now render grouped items beautifully!
        groups.forEach(function (group) {
            if (group.type === 'general') {
                var acts = group.section.activities || [];
                html += '<div class="tau-section-block tau-general-section">';
                html += '<h3><i class="fa fa-info-circle me-1"></i>' + escHtml(group.section.title || 'Información General') + '</h3>';
                if (group.section.summary) {
                    html += '<p class="section-summary">' + escHtml(group.section.summary) + '</p>';
                }
                html += '<ul class="tau-activity-list">';
                acts.forEach(function (a) {
                    var t = a.type || 'label';
                    var ico = typeIcons[t] || 'fa-circle-o';
                    html += '<li>';
                    html += '<span class="tau-type-badge tau-type-' + escHtml(t) + '"><i class="fa ' + ico + ' me-1"></i>' + escHtml(t) + '</span>';
                    html += '<span><strong>' + escHtml(a.title || '') + '</strong>';
                    if (a.description) { html += ' — <em>' + escHtml(a.description) + '</em>'; }
                    html += '</span></li>';
                });
                html += '</ul></div>';
            } else if (group.type === 'module') {
                html += '<div class="tau-module-block-preview">';
                html += '<div class="tau-module-preview-header">';
                html += '<h3><i class="fa fa-folder-open-o me-1"></i>' + escHtml(group.section.title || 'Módulo') + '</h3>';
                if (group.section.summary) {
                    html += '<p class="section-summary">' + escHtml(group.section.summary) + '</p>';
                }
                html += '</div>';
                
                if (group.weeks.length > 0) {
                    html += '<div class="tau-weeks-container-preview">';
                    group.weeks.forEach(function (wk) {
                        var acts = wk.section.activities || [];
                        html += '<div class="tau-week-block-preview">';
                        html += '<h4><i class="fa fa-calendar-o me-1"></i>' + escHtml(wk.section.title || 'Sección') + '</h4>';
                        if (wk.section.summary) {
                            html += '<p class="week-summary">' + escHtml(wk.section.summary) + '</p>';
                        }
                        html += '<ul class="tau-activity-list">';
                        acts.forEach(function (a) {
                            var t = a.type || 'label';
                            var ico = typeIcons[t] || 'fa-circle-o';
                            var qBadge = (t === 'quiz' && a.questions) ? ' <small style="color:#2e7d32;font-size:.7rem;">(' + a.questions.length + ' preguntas)</small>' : '';
                            var gBadge = (t === 'glossary' && a.terms) ? ' <small style="color:#ad1457;font-size:.7rem;">(' + a.terms.length + ' términos)</small>' : '';
                            html += '<li>';
                            html += '<span class="tau-type-badge tau-type-' + escHtml(t) + '"><i class="fa ' + ico + ' me-1"></i>' + escHtml(t) + '</span>';
                            html += '<span><strong>' + escHtml(a.title || '') + '</strong>' + qBadge + gBadge;
                            if (a.description) { html += ' — <em>' + escHtml(a.description) + '</em>'; }
                            html += '</span></li>';
                        });
                        if (acts.length === 0) {
                            html += '<li style="color:#aaa; font-style:italic;">No hay actividades añadidas para esta sección.</li>';
                        }
                        html += '</ul></div>';
                    });
                    html += '</div>';
                }
                html += '</div>';
            } else if (group.type === 'standalone-week') {
                var acts = group.section.activities || [];
                html += '<div class="tau-section-block">';
                html += '<h3><i class="fa fa-calendar-o me-1"></i>' + escHtml(group.section.title || 'Sección') + '</h3>';
                if (group.section.summary) {
                    html += '<p class="section-summary">' + escHtml(group.section.summary) + '</p>';
                }
                html += '<ul class="tau-activity-list">';
                acts.forEach(function (a) {
                    var t = a.type || 'label';
                    var ico = typeIcons[t] || 'fa-circle-o';
                    html += '<li>';
                    html += '<span class="tau-type-badge tau-type-' + escHtml(t) + '"><i class="fa ' + ico + ' me-1"></i>' + escHtml(t) + '</span>';
                    html += '<span><strong>' + escHtml(a.title || '') + '</strong>';
                    if (a.description) { html += ' — <em>' + escHtml(a.description) + '</em>'; }
                    html += '</span></li>';
                });
                html += '</ul></div>';
            }
        });

        var total = sections.reduce(function (n, s) { return n + (s.activities || []).length; }, 0);
        var quizTotal = sections.reduce(function (n, s) {
            return n + (s.activities || []).filter(function (a) { return a.type === 'quiz' && a.questions; }).length;
        }, 0);
        var qInfo = quizTotal > 0 ? ' · ' + quizTotal + ' cuestionarios con preguntas reales' : '';
        html += '<p style="color:#888;font-size:.81rem;margin-top:16px;"><i class="fa fa-info-circle me-1"></i>' + sections.length + ' secciones · ' + total + ' actividades' + qInfo + '</p>';

        el.innerHTML = html;
    }

    var collapsedModules = {};
    function renderBlueprintEditor(bp) {
        var typeTranslations = {
            page: 'Página',
            resource: 'Archivo',
            url: 'Enlace/Video',
            forum: 'Foro',
            assign: 'Tarea',
            quiz: 'Cuestionario',
            glossary: 'Glosario',
            feedback: 'Encuesta'
        };
        var el = document.getElementById('blueprint-editor');
        var sections = bp.sections || [];
        var html = '<div class="tau-editor-panel">';
        html += '<div class="tau-editor-head">';
        html += '<div><h3>Editor visual del curso</h3><p>Modifica la estructura base sin salir del asistente. Tienes libertad absoluta para agregar, quitar o editar recursos por sección.</p></div>';
        html += '<div class="tau-editor-actions">';
        html += '<button type="button" class="tau-editor-btn" data-editor-action="add-module"><i class="fa fa-plus"></i> Agregar módulo</button>';
        html += '<button type="button" class="tau-editor-btn" data-editor-action="reload-template"><i class="fa fa-refresh"></i> Recargar plantilla</button>';
        html += '</div></div>';
        html += '<div class="tau-editor-stack">';
        html += '<div class="tau-course-meta">';
        html += '<div class="tau-inline-field"><label>Título del curso</label><input data-course-field="courseName" type="text" value="' + escHtml(bp.courseName || '') + '"></div>';
        html += '<div class="tau-inline-field"><label>Descripción general</label><textarea data-course-field="courseDescription">' + escHtml(bp.courseDescription || '') + '</textarea></div>';
        html += '</div>';

        // Grouping algorithm
        var groups = [];
        var currentModuleGroup = null;
        sections.forEach(function (sec, idx) {
            var title = sec.title || '';
            var titleKey = normalizeTextKey(title);
            var isGeneral = titleKey === 'general' || titleKey.indexOf('informacion general') !== -1;
            
            if (isGeneral) {
                groups.push({
                    type: 'general',
                    section: sec,
                    originalIndex: idx
                });
            } else if (title.includes(' — Semana ') || title.includes(' - Semana ') || title.includes(' — Sección ') || title.includes(' - Sección ')) {
                if (currentModuleGroup) {
                    currentModuleGroup.weeks.push({
                        section: sec,
                        originalIndex: idx
                    });
                } else {
                    groups.push({
                        type: 'standalone-week',
                        section: sec,
                        originalIndex: idx
                    });
                }
            } else {
                currentModuleGroup = {
                    type: 'module',
                    section: sec,
                    originalIndex: idx,
                    weeks: []
                };
                groups.push(currentModuleGroup);
            }
        });

        var moduleIdxCounter = 0;

        // Now render the nested blueprint editor cards!
        groups.forEach(function (group) {
            if (group.type === 'general') {
                html += '<div class="tau-module-card tau-general-editor-card">';
                html += '<div class="tau-module-header">';
                html += '<div class="tau-module-titlebar"><span class="tau-module-number"><i class="fa fa-info-circle"></i></span><div><strong>' + escHtml(group.section.title || 'Información General') + '</strong><small>' + ((group.section.activities || []).length) + ' elementos</small></div></div>';
                html += '</div><div class="tau-module-body">';
                html += '<div class="tau-course-meta">';
                html += '<div class="tau-inline-field"><label>Título de la sección</label><input data-section-index="' + group.originalIndex + '" data-section-field="title" type="text" value="' + escHtml(group.section.title || '') + '"></div>';
                html += '<div class="tau-inline-field"><label>Resumen de la sección</label><textarea data-section-index="' + group.originalIndex + '" data-section-field="summary">' + escHtml(group.section.summary || '') + '</textarea></div>';
                html += '</div>';
                
                // Activities list
                (group.section.activities || []).forEach(function (activity, activityIndex) {
                    html += '<div class="tau-activity-row">';
                    html += '<div class="tau-inline-field"><label>Tipo</label><select data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="type">';
                    ['page', 'resource', 'url', 'forum', 'assign', 'quiz', 'glossary', 'feedback'].forEach(function (type) {
                        html += '<option value="' + type + '"' + ((activity.type || 'page') === type ? ' selected' : '') + '>' + (typeTranslations[type] || type) + '</option>';
                    });
                    html += '</select></div>';
                    html += '<div class="tau-activity-main">';
                    html += '<div class="tau-inline-field"><label>Título</label><input type="text" data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="title" value="' + escHtml(activity.title || '') + '"></div>';
                    html += '<div class="tau-inline-field"><label>Descripción</label><textarea data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="description">' + escHtml(activity.description || '') + '</textarea></div>';
                    html += renderActivityAttachmentField(activity, group.originalIndex, activityIndex);
                    html += '</div>';
                    html += '<button type="button" class="tau-icon-btn" data-editor-action="remove-activity" data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" title="Eliminar elemento" style="color: #dc3545; border-color: #ead6d8;"><i class="fa fa-times"></i></button>';
                    html += '</div>';
                });
                var genAddTypes = [
                    {t:'page',     i:'fa-file-text-o', l:'Página'},
                    {t:'url',      i:'fa-link',        l:'Enlace/Video'},
                    {t:'forum',    i:'fa-comments-o',  l:'Foro'},
                    {t:'resource', i:'fa-file-pdf-o',  l:'Archivo'},
                    {t:'assign',   i:'fa-upload',      l:'Tarea'},
                    {t:'feedback', i:'fa-star-o',      l:'Encuesta'}
                ];
                html += '<div class="tau-add-grid">';
                genAddTypes.forEach(function (a) {
                    html += '<button type="button" class="tau-add-card" data-editor-action="add-activity" data-section-index="' + group.originalIndex + '" data-activity-type="' + a.t + '"><i class="fa ' + a.i + '"></i> ' + a.l + '</button>';
                });
                html += '</div></div></div>';
            } else if (group.type === 'module') {
                moduleIdxCounter++;
                if (collapsedModules[group.originalIndex] === undefined) {
                    collapsedModules[group.originalIndex] = (moduleIdxCounter > 1); // Expand first module, collapse others by default
                }
                var isCollapsed = collapsedModules[group.originalIndex] === true;

                html += '<div class="tau-module-card' + (isCollapsed ? ' collapsed' : '') + '">';
                html += '<div class="tau-module-header" style="cursor: pointer;" data-editor-action="toggle-accordion" data-section-index="' + group.originalIndex + '">';
                html += '<div class="tau-module-titlebar">';
                html += '<span class="tau-module-number"><i class="fa fa-folder-open-o"></i></span>';
                html += '<div><strong>' + escHtml(group.section.title || 'Módulo') + '</strong><small>' + group.weeks.length + ' secciones de aprendizaje</small></div>';
                html += '</div>';
                html += '<div style="display:flex; gap:8px; align-items:center;">';
                html += '<i class="fa ' + (isCollapsed ? 'fa-chevron-down' : 'fa-chevron-up') + ' accordion-caret" style="font-size:0.9rem; color:#888; margin-right:8px;"></i>';
                
                var disableAddWeek = (group.weeks.length >= 4) ? ' disabled style="opacity:0.5; cursor:not-allowed;"' : '';
                html += '<button type="button" class="tau-editor-btn secondary-btn" data-editor-action="add-week-to-module" data-section-index="' + group.originalIndex + '"' + disableAddWeek + ' onclick="event.stopPropagation();"><i class="fa fa-plus"></i> Añadir Sección</button>';
                html += '<button type="button" class="tau-icon-btn" data-editor-action="remove-module" data-section-index="' + group.originalIndex + '" title="Eliminar módulo completo" onclick="event.stopPropagation();" style="color: #dc3545; border-color: #ead6d8;"><i class="fa fa-trash"></i></button>';
                html += '</div>';
                html += '</div>';
                
                html += '<div class="tau-module-body" style="' + (isCollapsed ? 'display:none;' : '') + '">';
                html += '<div class="tau-course-meta">';
                html += '<div class="tau-inline-field"><label>Título del módulo</label><input data-section-index="' + group.originalIndex + '" data-section-field="title" type="text" value="' + escHtml(group.section.title || '') + '"></div>';
                html += '<div class="tau-inline-field"><label>Resumen del módulo</label><textarea data-section-index="' + group.originalIndex + '" data-section-field="summary">' + escHtml(group.section.summary || '') + '</textarea></div>';
                html += '</div>';

                // Render nested Sections
                if (group.weeks.length > 0) {
                    html += '<div class="tau-nested-weeks-editor">';
                    group.weeks.forEach(function (wk) {
                        html += '<div class="tau-nested-week-card">';
                        html += '<div class="tau-week-header-editor">';
                        html += '<h5><i class="fa fa-calendar-o me-1"></i>' + escHtml(wk.section.title || 'Sección') + '</h5>';
                        html += '<button type="button" class="tau-week-remove-btn" data-editor-action="remove-module" data-section-index="' + wk.originalIndex + '" title="Eliminar sección" style="color: #dc3545;"><i class="fa fa-trash-o me-1"></i>Eliminar sección</button>';
                        html += '</div>';
                        html += '<div class="tau-course-meta" style="margin-top: 10px;">';
                        html += '<div class="tau-inline-field"><label>Título de la sección</label><input data-section-index="' + wk.originalIndex + '" data-section-field="title" type="text" value="' + escHtml(wk.section.title || '') + '"></div>';
                        html += '<div class="tau-inline-field"><label>Subtema/Resumen</label><textarea data-section-index="' + wk.originalIndex + '" data-section-field="summary">' + escHtml(wk.section.summary || '') + '</textarea></div>';
                        html += '</div>';

                        // Activities inside this week
                        var wkActs = wk.section.activities || [];
                        if (wkActs.length > 0) {
                            html += '<div class="tau-week-activities-list">';
                            wkActs.forEach(function (activity, activityIndex) {
                                html += '<div class="tau-activity-row nested-activity">';
                                html += '<div class="tau-inline-field"><label>Tipo</label><select data-section-index="' + wk.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="type">';
                                ['page', 'resource', 'url', 'forum', 'assign', 'quiz', 'glossary', 'feedback'].forEach(function (type) {
                                    html += '<option value="' + type + '"' + ((activity.type || 'page') === type ? ' selected' : '') + '>' + (typeTranslations[type] || type) + '</option>';
                                });
                                html += '</select></div>';
                                html += '<div class="tau-activity-main">';
                                html += '<div class="tau-inline-field"><label>Título</label><input type="text" data-section-index="' + wk.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="title" value="' + escHtml(activity.title || '') + '"></div>';
                                html += '<div class="tau-inline-field"><label>Descripción</label><textarea data-section-index="' + wk.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="description">' + escHtml(activity.description || '') + '</textarea></div>';
                                html += renderActivityAttachmentField(activity, wk.originalIndex, activityIndex);
                                html += '</div>';
                                
                                html += '<div style="display:flex; flex-direction:column; gap:4px; margin-left: 8px;">';
                                if (activityIndex > 0) {
                                    html += '<button type="button" class="tau-icon-btn" data-editor-action="move-activity-up" data-section-index="' + wk.originalIndex + '" data-activity-index="' + activityIndex + '" title="Subir"><i class="fa fa-chevron-up"></i></button>';
                                }
                                if (activityIndex < wkActs.length - 1) {
                                    html += '<button type="button" class="tau-icon-btn" data-editor-action="move-activity-down" data-section-index="' + wk.originalIndex + '" data-activity-index="' + activityIndex + '" title="Bajar"><i class="fa fa-chevron-down"></i></button>';
                                }
                                html += '<button type="button" class="tau-icon-btn" data-editor-action="remove-activity" data-section-index="' + wk.originalIndex + '" data-activity-index="' + activityIndex + '" title="Eliminar elemento" style="color: #dc3545; border-color: #ead6d8;"><i class="fa fa-times"></i></button>';
                                html += '</div>';
                                
                                html += '</div>';
                            });
                            html += '</div>';
                        } else {
                            html += '<p style="color:#aaa; font-style:italic; font-size:.8rem; margin:10px 0;">No hay recursos agregados a esta sección.</p>';
                        }

                        // Add activity buttons specific to this week
                        html += '<div class="tau-add-grid" style="margin-top: 10px; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px;">';
                        var actConfigs = [
                            { type: 'page', label: 'Página guía', icon: 'fa-file-text-o' },
                            { type: 'resource', label: 'Presentación / PDF', icon: 'fa-file-pdf-o' },
                            { type: 'url', label: 'Enlace externo / URL', icon: 'fa-link' },
                            { type: 'forum', label: 'Foro', icon: 'fa-comments-o' },
                            { type: 'assign', label: 'Tarea (Entregar act.)', icon: 'fa-upload' },
                            { type: 'quiz', label: 'Quiz / Evaluación', icon: 'fa-question-circle-o' },
                            { type: 'glossary', label: 'Glosario', icon: 'fa-book' },
                            { type: 'feedback', label: 'Encuesta / Feedback', icon: 'fa-star-o' }
                        ];
                        actConfigs.forEach(function (cfg) {
                            html += '<button type="button" class="tau-add-card" data-editor-action="add-activity" data-section-index="' + wk.originalIndex + '" data-activity-type="' + cfg.type + '" style="font-size: 0.72rem; padding: 8px 10px;"><i class="fa ' + cfg.icon + ' me-1"></i> ' + cfg.label + '</button>';
                        });
                        html += '</div>';
                        html += '</div>'; // End tau-nested-week-card
                    });
                    html += '</div>'; // End tau-nested-weeks-editor
                }
                html += '</div></div>';
            } else if (group.type === 'standalone-week') {
                html += '<div class="tau-module-card">';
                html += '<div class="tau-module-header">';
                html += '<div class="tau-module-titlebar"><span class="tau-module-number"><i class="fa fa-calendar-o"></i></span><div><strong>' + escHtml(group.section.title || 'Sección') + '</strong><small>' + ((group.section.activities || []).length) + ' elementos</small></div></div>';
                html += '<button type="button" class="tau-icon-btn" data-editor-action="remove-module" data-section-index="' + group.originalIndex + '" title="Eliminar" style="color: #dc3545; border-color: #ead6d8;"><i class="fa fa-trash"></i></button>';
                html += '</div><div class="tau-module-body">';
                html += '<div class="tau-course-meta">';
                html += '<div class="tau-inline-field"><label>Título</label><input data-section-index="' + group.originalIndex + '" data-section-field="title" type="text" value="' + escHtml(group.section.title || '') + '"></div>';
                html += '<div class="tau-inline-field"><label>Resumen</label><textarea data-section-index="' + group.originalIndex + '" data-section-field="summary">' + escHtml(group.section.summary || '') + '</textarea></div>';
                html += '</div>';
                (group.section.activities || []).forEach(function (activity, activityIndex) {
                    html += '<div class="tau-activity-row">';
                    html += '<div class="tau-inline-field"><label>Tipo</label><select data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="type">';
                    ['page', 'resource', 'url', 'forum', 'assign', 'quiz', 'glossary', 'feedback'].forEach(function (type) {
                        html += '<option value="' + type + '"' + ((activity.type || 'page') === type ? ' selected' : '') + '>' + (typeTranslations[type] || type) + '</option>';
                    });
                    html += '</select></div>';
                    html += '<div class="tau-activity-main">';
                    html += '<div class="tau-inline-field"><label>Título</label><input type="text" data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="title" value="' + escHtml(activity.title || '') + '"></div>';
                    html += '<div class="tau-inline-field"><label>Descripción</label><textarea data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" data-activity-field="description">' + escHtml(activity.description || '') + '</textarea></div>';
                    html += renderActivityAttachmentField(activity, group.originalIndex, activityIndex);
                    html += '</div>';
                    
                    html += '<div style="display:flex; flex-direction:column; gap:4px; margin-left: 8px;">';
                    if (activityIndex > 0) {
                        html += '<button type="button" class="tau-icon-btn" data-editor-action="move-activity-up" data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" title="Subir"><i class="fa fa-chevron-up"></i></button>';
                    }
                    if (activityIndex < (group.section.activities || []).length - 1) {
                        html += '<button type="button" class="tau-icon-btn" data-editor-action="move-activity-down" data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" title="Bajar"><i class="fa fa-chevron-down"></i></button>';
                    }
                    html += '<button type="button" class="tau-icon-btn" data-editor-action="remove-activity" data-section-index="' + group.originalIndex + '" data-activity-index="' + activityIndex + '" title="Eliminar elemento" style="color: #dc3545; border-color: #ead6d8;"><i class="fa fa-times"></i></button>';
                    html += '</div>';
                    
                    html += '</div>';
                });
                var swAddTypes = [
                    {t:'page',     i:'fa-file-text-o',       l:'Página'},
                    {t:'resource', i:'fa-file-pdf-o',        l:'Archivo'},
                    {t:'url',      i:'fa-link',              l:'Enlace/Video'},
                    {t:'forum',    i:'fa-comments-o',        l:'Foro'},
                    {t:'assign',   i:'fa-upload',            l:'Tarea'},
                    {t:'quiz',     i:'fa-question-circle-o', l:'Quiz'},
                    {t:'glossary', i:'fa-book',              l:'Glosario'},
                    {t:'feedback', i:'fa-star-o',            l:'Encuesta'}
                ];
                html += '<div class="tau-add-grid">';
                swAddTypes.forEach(function (a) {
                    html += '<button type="button" class="tau-add-card" data-editor-action="add-activity" data-section-index="' + group.originalIndex + '" data-activity-type="' + a.t + '"><i class="fa ' + a.i + '"></i> ' + a.l + '</button>';
                });
                html += '</div></div></div>';
            }
        });

        html += '</div></div>';
        el.innerHTML = html;
    }

    function syncBlueprintField(target) {
        if (!currentBlueprint || !target) return false;
        if (target.dataset.courseField) {
            currentBlueprint[target.dataset.courseField] = target.value;
            renderPlan(currentBlueprint);
            return true;
        }
        if (target.dataset.sectionField) {
            currentBlueprint.sections[Number(target.dataset.sectionIndex)][target.dataset.sectionField] = target.value;
            renderPlan(currentBlueprint);
            return true;
        }
        if (target.dataset.activityField) {
            currentBlueprint.sections[Number(target.dataset.sectionIndex)].activities[Number(target.dataset.activityIndex)][target.dataset.activityField] = target.value;
            renderPlan(currentBlueprint);
            return true;
        }
        return false;
    }

    document.getElementById('blueprint-editor').addEventListener('input', function (e) {
        syncBlueprintField(e.target);
    });

    document.getElementById('blueprint-editor').addEventListener('change', function (e) {
        var target = e.target;
        if (target.dataset && target.dataset.resourceUpload) {
            if (!currentBlueprint) return;
            var sectionIndex = Number(target.dataset.sectionIndex);
            var activityIndex = Number(target.dataset.activityIndex);
            var file = target.files && target.files[0] ? target.files[0] : null;
            if (!file) {
                return;
            }
            if (!/\.pdf$/i.test(file.name) && file.type !== 'application/pdf') {
                alert('Solo se permiten archivos PDF para este recurso.');
                target.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function(evt) {
                var result = String(evt.target.result || '');
                var base64 = result.indexOf('base64,') !== -1 ? result.split('base64,')[1] : '';
                currentBlueprint.sections[sectionIndex].activities[activityIndex].uploadedfile = {
                    name: file.name,
                    type: file.type || 'application/pdf',
                    size: file.size || 0,
                    content: base64
                };
                renderPlan(currentBlueprint);
                renderBlueprintEditor(currentBlueprint);
            };
            reader.readAsDataURL(file);
            return;
        }

        if (syncBlueprintField(target)) {
            currentBlueprint = normalizeBlueprint(currentBlueprint);
            renderPlan(currentBlueprint);
            renderBlueprintEditor(currentBlueprint);
        }
    });

    document.getElementById('blueprint-editor').addEventListener('click', function (e) {
        var button = e.target.closest('[data-editor-action]');
        if (!button || !currentBlueprint) return;
        var action = button.dataset.editorAction;
        var sectionIndex = Number(button.dataset.sectionIndex);
        var activityIndex = Number(button.dataset.activityIndex);
        var type = button.dataset.activityType || 'page';

        if (action === 'toggle-accordion') {
            collapsedModules[sectionIndex] = !collapsedModules[sectionIndex];
            var card = button.closest('.tau-module-card');
            if (card) {
                card.classList.toggle('collapsed');
                var body = card.querySelector('.tau-module-body');
                var caret = card.querySelector('.accordion-caret');
                if (body) {
                    if (card.classList.contains('collapsed')) {
                        body.style.display = 'none';
                        if (caret) {
                            caret.classList.remove('fa-chevron-up');
                            caret.classList.add('fa-chevron-down');
                        }
                    } else {
                        body.style.display = '';
                        if (caret) {
                            caret.classList.remove('fa-chevron-down');
                            caret.classList.add('fa-chevron-up');
                        }
                    }
                }
            }
            return; // Buttery-smooth local DOM transition, no full render needed
        }

        if (action === 'add-module') {
            currentBlueprint.sections.push({
                title: 'Nuevo módulo ' + (currentBlueprint.sections.length + 1),
                summary: 'Describe aquí el enfoque y resultados esperados del módulo.',
                activities: [buildActivity('page', currentBlueprint.sections.length, currentPreset)]
            });
        } else if (action === 'add-week-to-module') {
            var existingWeeksCount = 0;
            for (var k = sectionIndex + 1; k < currentBlueprint.sections.length; k++) {
                var nextTitle = currentBlueprint.sections[k].title || '';
                if (nextTitle.includes(' — Semana ') || nextTitle.includes(' - Semana ') || nextTitle.includes(' — Sección ') || nextTitle.includes(' - Sección ')) {
                    existingWeeksCount++;
                } else {
                    break;
                }
            }
            
            if (existingWeeksCount >= 4) {
                alert('Un módulo no puede tener más de 4 secciones de aprendizaje.');
                return;
            }

            var insertIndex = sectionIndex + 1;
            var modTitle = currentBlueprint.sections[sectionIndex].title || '';
            var modMatch = modTitle.match(/Módulo\s+(\d+)/i);
            var modNum = modMatch ? modMatch[1] : (sectionIndex + 1);
            
            // Loop forward to find the last section of this module
            for (var k = sectionIndex + 1; k < currentBlueprint.sections.length; k++) {
                var nextTitle = currentBlueprint.sections[k].title || '';
                if (nextTitle.includes(' — Semana ') || nextTitle.includes(' - Semana ') || nextTitle.includes(' — Sección ') || nextTitle.includes(' - Sección ')) {
                    insertIndex = k + 1;
                } else {
                    break;
                }
            }
            
            // Determine section number
            var weekCount = insertIndex - sectionIndex;
            var newWeekSection = {
                title: 'Módulo ' + modNum + ' — Sección ' + weekCount + ': Nuevo subtema',
                summary: 'Describe los temas específicos de la sección.',
                activities: [
                    buildActivity('page', sectionIndex, currentPreset)
                ]
            };
            
            // Insert in array
            currentBlueprint.sections.splice(insertIndex, 0, newWeekSection);
        } else if (action === 'reload-template') {
            currentBlueprint = createStarterBlueprint();
        } else if (action === 'remove-module') {
            var title = currentBlueprint.sections[sectionIndex].title || '';
            var isWeek = title.includes(' — Semana ') || title.includes(' - Semana ') || title.includes(' — Sección ') || title.includes(' - Sección ');
            
            if (isWeek) {
                // It's a section, just remove this single section!
                currentBlueprint.sections.splice(sectionIndex, 1);
            } else {
                // It's a module, let's find how many subsequent sections belong to it
                var countToRemove = 1; // start with the module header itself
                for (var k = sectionIndex + 1; k < currentBlueprint.sections.length; k++) {
                    var nextTitle = currentBlueprint.sections[k].title || '';
                    if (nextTitle.includes(' — Semana ') || nextTitle.includes(' - Semana ') || nextTitle.includes(' — Sección ') || nextTitle.includes(' - Sección ')) {
                        countToRemove++;
                    } else {
                        break;
                    }
                }
                currentBlueprint.sections.splice(sectionIndex, countToRemove);
            }
        } else if (action === 'add-activity') {
            currentBlueprint.sections[sectionIndex].activities.push(buildActivity(type, sectionIndex, currentPreset));
        } else if (action === 'remove-activity') {
            currentBlueprint.sections[sectionIndex].activities.splice(activityIndex, 1);
        } else if (action === 'move-activity-up') {
            if (activityIndex > 0) {
                var acts = currentBlueprint.sections[sectionIndex].activities;
                var temp = acts[activityIndex];
                acts[activityIndex] = acts[activityIndex - 1];
                acts[activityIndex - 1] = temp;
            }
        } else if (action === 'move-activity-down') {
            var acts = currentBlueprint.sections[sectionIndex].activities;
            if (activityIndex < acts.length - 1) {
                var temp = acts[activityIndex];
                acts[activityIndex] = acts[activityIndex + 1];
                acts[activityIndex + 1] = temp;
            }
        }

        currentBlueprint = normalizeBlueprint(currentBlueprint);
        renderPlan(currentBlueprint);
        renderBlueprintEditor(currentBlueprint);
    });

    // ── Chat helpers ───────────────────────────────────────────────────────────
    function appendChatMsg(role, text) {
        var box = document.getElementById('chat-messages');
        var el  = document.createElement('div');
        el.className = 'tau-chat-msg ' + role;
        el.textContent = text;
        box.appendChild(el);
        box.scrollTop = box.scrollHeight;
    }

    // ── Status helpers ─────────────────────────────────────────────────────────
    function setPlanStatus(type, title, sub) {
        var el   = document.getElementById('plan-status');
        var icon = type === 'spinner'
            ? '<div class="tau-spinner"></div>'
            : '<i class="fa fa-check-circle tau-done-icon"></i>';
        el.innerHTML = icon + '<div class="tau-plan-status-text"><strong>' + escHtml(title) + '</strong><small>' + escHtml(sub) + '</small></div>';
    }

    function setAlert(containerId, type, html) {
        document.getElementById(containerId).innerHTML =
            '<div class="tau-alert tau-alert-' + type + '">' + html + '</div>';
    }

    // ── Streaming plan generation ─────────────────────────────────────────────
    async function streamPlan(params) {
        var response = await fetch(STREAM, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(Object.assign({ sesskey: SESSKEY }, params)),
        });

        if (!response.ok) {
            var text = await response.text();
            throw new Error('HTTP ' + response.status + ': ' + text.slice(0, 300));
        }

        var reader  = response.body.getReader();
        var decoder = new TextDecoder();
        var buf     = '';
        var tokens  = 0;

        while (true) {
            var chunk = await reader.read();
            if (chunk.done) break;

            buf += decoder.decode(chunk.value, { stream: true });

            var lines = buf.split('\n');
            buf = lines.pop(); // keep incomplete last line

            for (var i = 0; i < lines.length; i++) {
                var line = lines[i];
                if (line.indexOf('data: ') !== 0) continue;
                var data;
                try { data = JSON.parse(line.slice(6)); } catch (e) { continue; }

                if (data.token !== undefined) {
                    tokens++;
                    if (tokens % 15 === 0) {
                        setPlanStatus('spinner', 'Generando plan del curso',
                            'La IA está diseñando la estructura... (' + tokens + ' tokens generados)');
                    }
                } else if (data.done) {
                    return data.blueprint;
                } else if (data.error) {
                    throw new Error(data.error);
                }
            }
        }

        throw new Error('El stream terminó sin devolver un plan. Intenta de nuevo.');
    }

    // ── AJAX ───────────────────────────────────────────────────────────────────
    async function ajax(action, payload) {
        var res = await fetch(AJAX, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(Object.assign({ action: action, sesskey: SESSKEY }, payload)),
        });
        var data = await res.json();
        if (!res.ok || data.error) throw new Error(data.error || 'Error ' + res.status);
        return data;
    }

    function escHtml(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
})();
</script>

<?php endif; ?>
<?php echo $OUTPUT->footer(); ?>
