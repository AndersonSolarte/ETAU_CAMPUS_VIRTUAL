<?php
defined('MOODLE_INTERNAL') || die();

$string['pluginname']      = 'TAU Creador de Cursos con IA';
$string['pagetitle']       = 'Crear curso con IA';
$string['privacy:metadata']= 'TAU Course Creator AI envía prompts a OpenAI para generar estructuras de cursos.';
$string['capabilityuse']   = 'Usar TAU Creador de Cursos con IA';
$string['nokey']           = 'Clave de OpenAI no configurada.';

// Settings
$string['settings_openai_heading']      = 'Configuración de OpenAI';
$string['settings_openai_heading_desc'] = 'Ingresa tu clave API de OpenAI para habilitar la generación de cursos con IA. Obtén tu clave en platform.openai.com.';
$string['settings_openai_key']          = 'Clave API de OpenAI';
$string['settings_openai_key_desc']     = 'Tu clave secreta de platform.openai.com/api-keys. Comienza con sk-.';
$string['settings_openai_model']        = 'Modelo de OpenAI';
$string['settings_openai_model_desc']   = 'GPT-4o mini es rápido y económico. GPT-4o genera contenido más completo pero tiene mayor costo.';
