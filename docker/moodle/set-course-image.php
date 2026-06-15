<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->libdir . '/filelib.php');

$courseid = 9;
$imagepath = $CFG->dirroot . '/theme/tau_branding/assets/course-banners/banner-ciberseguridad.svg';

if (!is_readable($imagepath)) {
    echo "Imagen no encontrada: {$imagepath}\n";
    exit(1);
}

$context  = context_course::instance($courseid);
$fs       = get_file_storage();

$fs->delete_area_files($context->id, 'course', 'overviewfiles', 0);

$fs->create_file_from_pathname([
    'contextid' => $context->id,
    'component' => 'course',
    'filearea'  => 'overviewfiles',
    'itemid'    => 0,
    'filepath'  => '/',
    'filename'  => 'banner-ciberseguridad.svg',
], $imagepath);

rebuild_course_cache($courseid, true);
echo "Imagen del curso actualizada.\n";
