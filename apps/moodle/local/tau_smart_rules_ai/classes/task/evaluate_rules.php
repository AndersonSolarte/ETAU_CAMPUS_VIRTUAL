<?php
namespace local_tau_smart_rules_ai\task;

defined('MOODLE_INTERNAL') || die();

class evaluate_rules extends \core\task\scheduled_task {

    public function get_name(): string {
        return 'TAU Smart Rules AI — Evaluate Rules';
    }

    public function execute(): void {
        global $DB;

        $rules = $DB->get_records('local_tau_smart_rules', ['active' => 1]);
        if (empty($rules)) {
            return;
        }

        $api = new \local_tau_ai_provider\client();

        foreach ($rules as $rule) {
            try {
                $students = enrol_get_course_users($rule->courseid);
                if (empty($students)) {
                    continue;
                }

                $payloads = [];
                foreach ($students as $student) {
                    $payloads[] = [
                        'userId'   => $student->id,
                        'courseId' => $rule->courseid,
                    ];
                }

                $result = $api->post('ai/rules/evaluate', [
                    'ruleId'    => $rule->id,
                    'trigger'   => $rule->trigger,
                    'condition' => $rule->condition_value,
                    'students'  => $payloads,
                ]);

                // Execute triggered actions.
                foreach (($result['triggered'] ?? []) as $triggered) {
                    $this->execute_action($rule, $triggered['userId']);
                }

                $DB->set_field('local_tau_smart_rules', 'lastrun', time(), ['id' => $rule->id]);
            } catch (\Exception $e) {
                debugging("Smart Rules AI error (rule {$rule->id}): " . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
    }

    private function execute_action(\stdClass $rule, int $userid): void {
        global $DB;

        $action_data = json_decode($rule->action_data ?? '{}', true) ?: [];

        switch ($rule->action) {
            case 'send_message':
                $message = new \core\message\message();
                $message->component     = 'local_tau_smart_rules_ai';
                $message->name          = 'smart_rule_notification';
                $message->userfrom      = \core_user::get_noreply_user();
                $message->userto        = $userid;
                $message->subject       = $action_data['subject'] ?? 'Notificación Smart Rules AI';
                $message->fullmessage   = $action_data['message'] ?? '';
                $message->fullmessageformat = FORMAT_PLAIN;
                $message->fullmessagehtml   = '';
                $message->smallmessage  = '';
                $message->notification  = 1;
                message_send($message);
                break;
        }
    }
}
