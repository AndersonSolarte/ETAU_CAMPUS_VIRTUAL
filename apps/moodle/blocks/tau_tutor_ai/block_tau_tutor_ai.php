<?php
defined('MOODLE_INTERNAL') || die();

class block_tau_tutor_ai extends block_base {

    public function init(): void {
        $this->title = get_string('pluginname', 'block_tau_tutor_ai');
    }

    public function applicable_formats(): array {
        return ['course-view' => true, 'mod' => true, 'site' => true];
    }

    public function has_config(): bool {
        return false;
    }

    public function get_content(): stdClass {
        global $CFG, $COURSE, $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $courseid  = $COURSE->id ?? 0;
        $coursename = $COURSE->fullname ?? '';
        $sesskey   = sesskey();
        $ajax_url  = $CFG->wwwroot . '/blocks/tau_tutor_ai/chat.php';
        $welcome   = get_string('chat_welcome', 'block_tau_tutor_ai');
        $placeholder = get_string('chat_placeholder', 'block_tau_tutor_ai');
        $send_label  = get_string('send_btn', 'block_tau_tutor_ai');
        $typing_label = get_string('typing', 'block_tau_tutor_ai');

        ob_start();
        ?>
        <div class="tau-tutor-ai-widget" data-courseid="<?php echo $courseid; ?>">
          <div class="tau-chat-messages" id="tau-chat-msgs-<?php echo $courseid; ?>">
            <div class="tau-msg tau-msg-bot">
              <div class="tau-msg-bubble"><?php echo s($welcome); ?></div>
            </div>
          </div>
          <div class="tau-chat-input-row">
            <input type="text" class="tau-chat-input"
              id="tau-chat-input-<?php echo $courseid; ?>"
              placeholder="<?php echo s($placeholder); ?>"
              autocomplete="off">
            <button class="tau-chat-send-btn" id="tau-chat-send-<?php echo $courseid; ?>"
              title="<?php echo s($send_label); ?>">
              <i class="fa fa-paper-plane"></i>
            </button>
          </div>
        </div>

        <style>
        .tau-tutor-ai-widget{display:flex;flex-direction:column;gap:8px}
        .tau-chat-messages{min-height:180px;max-height:280px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;padding:4px 2px}
        .tau-msg{display:flex;align-items:flex-end}
        .tau-msg-bot .tau-msg-bubble{background:#f0f0f0;border-radius:12px 12px 12px 2px;padding:8px 12px;max-width:90%;font-size:.88rem}
        .tau-msg-user{justify-content:flex-end}
        .tau-msg-user .tau-msg-bubble{background:#c62b3a;color:#fff;border-radius:12px 12px 2px 12px;padding:8px 12px;max-width:90%;font-size:.88rem}
        .tau-msg-typing .tau-msg-bubble{font-style:italic;color:#888}
        .tau-chat-input-row{display:flex;gap:6px}
        .tau-chat-input{flex:1;border-radius:20px;border:1px solid #ddd;padding:6px 14px;font-size:.88rem}
        .tau-chat-send-btn{background:#c62b3a;border:none;border-radius:50%;width:36px;height:36px;color:#fff;cursor:pointer;flex-shrink:0}
        .tau-chat-send-btn:hover{background:#a32230}
        </style>

        <script>
        (function(){
          const cid     = <?php echo $courseid; ?>;
          const msgs    = document.getElementById('tau-chat-msgs-' + cid);
          const input   = document.getElementById('tau-chat-input-' + cid);
          const sendBtn = document.getElementById('tau-chat-send-' + cid);
          const AJAX    = '<?php echo $ajax_url; ?>';
          const SK      = '<?php echo $sesskey; ?>';
          const history = [];

          function scrollBottom(){ msgs.scrollTop = msgs.scrollHeight; }

          function addMsg(text, role){
            const div  = document.createElement('div');
            div.className = 'tau-msg tau-msg-' + role;
            const bub  = document.createElement('div');
            bub.className = 'tau-msg-bubble';
            bub.textContent = text;
            div.appendChild(bub);
            msgs.appendChild(div);
            scrollBottom();
            return div;
          }

          async function send(){
            const text = input.value.trim();
            if (!text) return;
            input.value = '';
            addMsg(text, 'user');
            history.push({ role: 'user', content: text });

            const typing = addMsg('<?php echo $typing_label; ?>', 'bot tau-msg-typing');
            sendBtn.disabled = true;

            try {
              const res = await fetch(AJAX, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sesskey: SK, courseid: cid, message: text, history }),
              });
              const data = await res.json();
              msgs.removeChild(typing);
              const reply = data.reply || data.message || 'Sin respuesta';
              addMsg(reply, 'bot');
              history.push({ role: 'assistant', content: reply });
            } catch(e) {
              msgs.removeChild(typing);
              addMsg('Error de conexión con el Tutor AI.', 'bot');
            } finally {
              sendBtn.disabled = false;
              input.focus();
            }
          }

          sendBtn.addEventListener('click', send);
          input.addEventListener('keydown', e => { if(e.key === 'Enter') send(); });
        })();
        </script>
        <?php
        $this->content->text = ob_get_clean();
        $this->content->footer = '';
        return $this->content;
    }
}
