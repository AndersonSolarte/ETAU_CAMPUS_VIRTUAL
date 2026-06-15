<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/tau_course_creator_ai:use' => [
        'riskbitmask'  => RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => [
            'manager'          => CAP_ALLOW,
        ],
    ],
];
