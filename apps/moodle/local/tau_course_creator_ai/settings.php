<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_tau_course_creator_ai',
        get_string('pluginname', 'local_tau_course_creator_ai')
    );
    $ADMIN->add('localplugins', $settings);

    // ── OpenAI ────────────────────────────────────────────────────────────────
    $settings->add(new admin_setting_heading(
        'local_tau_course_creator_ai/openai_heading',
        'Configuración OpenAI',
        'Usa tu API key de OpenAI para generar cursos con GPT-4o.'
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_tau_course_creator_ai/openai_api_key',
        'API Key de OpenAI',
        'Clave de API de OpenAI. Empieza con <code>sk-proj-...</code> o <code>sk-...</code>',
        ''
    ));

    $settings->add(new admin_setting_configselect(
        'local_tau_course_creator_ai/openai_model',
        'Modelo de OpenAI',
        'Modelo de OpenAI a usar.',
        'gpt-4o',
        [
            'gpt-4o'          => 'GPT-4o — Recomendado',
            'gpt-4o-mini'     => 'GPT-4o Mini — Más rápido y económico',
            'gpt-4-turbo'     => 'GPT-4 Turbo',
        ]
    ));
}
