<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\mod_forum\event\post_created',
        'callback'    => '\local_tau_forum_ai\observer::post_created',
        'priority'    => 200,
        'internal'    => false,
    ],
];
