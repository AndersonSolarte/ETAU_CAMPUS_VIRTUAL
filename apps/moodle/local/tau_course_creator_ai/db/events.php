<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_created',
        'callback' => '\local_tau_course_creator_ai\native_course_template_manager::handle_course_created',
        'priority' => 9999,
        'internal' => false,
    ],
];
