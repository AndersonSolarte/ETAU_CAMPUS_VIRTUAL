<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => '\local_tau_smart_rules_ai\task\evaluate_rules',
        'blocking'  => 0,
        'minute'    => '*/30',
        'hour'      => '*',
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*',
        'disabled'  => 0,
    ],
];
