<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/tau_assign_ai:use' => [
        'riskbitmask'  => RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => [
            'manager'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
        ],
    ],
];
