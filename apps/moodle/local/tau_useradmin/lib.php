<?php
defined('MOODLE_INTERNAL') || die();

function local_tau_useradmin_allowed_email_domain(): string {
    $domain = trim((string)get_config('local_tau_useradmin', 'institutionaldomain'));
    return $domain !== '' ? strtolower($domain) : 'unicesmag.edu.co';
}

function local_tau_useradmin_validate_email(string $email): bool {
    $email = core_text::strtolower(trim($email));
    $domain = local_tau_useradmin_allowed_email_domain();
    return (bool)preg_match('/@' . preg_quote($domain, '/') . '$/', $email);
}

function local_tau_useradmin_normalize_status($value): int {
    if (is_numeric($value)) {
        return ((int)$value === 1) ? 1 : 0;
    }
    $value = core_text::strtolower(trim((string)$value));
    return in_array($value, ['0', 'activo', 'active', 'si', 'sí', 'yes'], true) ? 0 : 1;
}

function local_tau_useradmin_extract_form_data(array $source): array {
    return [
        'id' => isset($source['userid']) ? (int)$source['userid'] : 0,
        'idnumber' => trim((string)($source['idnumber'] ?? '')),
        'suspended' => local_tau_useradmin_normalize_status($source['suspended'] ?? 0),
        'firstname' => trim((string)($source['firstname'] ?? '')),
        'lastname' => trim((string)($source['lastname'] ?? '')),
        'email' => core_text::strtolower(trim((string)($source['email'] ?? ''))),
        'country' => trim((string)($source['country'] ?? '')),
        'department' => trim((string)($source['department'] ?? '')),
        'city' => trim((string)($source['city'] ?? '')),
        'phone1' => trim((string)($source['phone1'] ?? '')),
    ];
}

function local_tau_useradmin_validate_data(array $data, int $currentuserid = 0): array {
    global $DB;

    $errors = [];

    if ($data['idnumber'] === '') {
        $errors[] = get_string('idrequired', 'local_tau_useradmin');
    }
    if ($data['firstname'] === '') {
        $errors[] = get_string('firstnamerequired', 'local_tau_useradmin');
    }
    if ($data['lastname'] === '') {
        $errors[] = get_string('lastnamerequired', 'local_tau_useradmin');
    }
    if ($data['email'] === '') {
        $errors[] = get_string('emailrequired', 'local_tau_useradmin');
    } else if (!validate_email($data['email']) || !local_tau_useradmin_validate_email($data['email'])) {
        $errors[] = get_string('emaildomainerror', 'local_tau_useradmin');
    }

    if ($data['idnumber'] !== '') {
        $existing = $DB->get_record('user', ['idnumber' => $data['idnumber'], 'deleted' => 0], 'id');
        if ($existing && (int)$existing->id !== $currentuserid) {
            $errors[] = get_string('duplicatedidnumber', 'local_tau_useradmin');
        }
    }

    if ($data['email'] !== '') {
        $existing = $DB->get_record('user', ['email' => $data['email'], 'deleted' => 0], 'id');
        if ($existing && (int)$existing->id !== $currentuserid) {
            $errors[] = get_string('duplicatedemail', 'local_tau_useradmin');
        }
    }

    return $errors;
}

function local_tau_useradmin_upsert_user(array $data): int {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/user/lib.php');

    $user = (object)$data;
    $user->username = $user->email;
    $user->auth = 'oauth2';
    $user->confirmed = 1;
    $user->mnethostid = $CFG->mnet_localhost_id ?? 1;
    $user->maildisplay = 2;
    $user->timecreated = time();
    $user->timemodified = time();

    if (!empty($user->id)) {
        $existing = $DB->get_record('user', ['id' => $user->id], '*', MUST_EXIST);
        foreach ([
            'username', 'email', 'firstname', 'lastname', 'country', 'department',
            'city', 'phone1', 'idnumber', 'suspended', 'auth', 'confirmed'
        ] as $field) {
            $existing->{$field} = $user->{$field} ?? $existing->{$field};
        }
        $existing->timemodified = time();
        user_update_user($existing, false, false);
        return (int)$existing->id;
    }

    unset($user->id);
    return (int)user_create_user($user, false, false);
}

function local_tau_useradmin_find_user_by_idnumber(string $idnumber) {
    global $DB;
    if ($idnumber === '') {
        return false;
    }
    return $DB->get_record('user', ['idnumber' => $idnumber, 'deleted' => 0]);
}
