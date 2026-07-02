<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_tau_course_creator_ai_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026061802) {
        $table = new xmldb_table('local_tau_ccai_recordings');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sectionnum', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '');
        $table->add_field('videourl', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('provider', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'generic');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_index('courseid_idx', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        $table->add_index('sectionid_idx', XMLDB_INDEX_NOTUNIQUE, ['sectionid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026061802, 'local', 'tau_course_creator_ai');
    }
    if ($oldversion < 2026061803) {
        $table = new xmldb_table('local_tau_ccai_recordings');
        $field = new xmldb_field('customgroup', XMLDB_TYPE_CHAR, '255', null, null, null, '', 'title');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026061803, 'local', 'tau_course_creator_ai');
    }

    return true;
}
