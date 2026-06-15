<?php
namespace local_tau_course_creator_ai\h5p;

defined('MOODLE_INTERNAL') || die();

/**
 * Imports a .h5p file into the Moodle Content Bank and optionally links it
 * to an existing mod_h5pactivity course module.
 */
class h5p_publisher {

    /**
     * Import a .h5p file into the Content Bank.
     *
     * @param  string   $h5p_path  Absolute path to the .h5p ZIP file.
     * @param  string   $name      Human-readable name for the Content Bank entry.
     * @param  \context $context   Moodle context for the Content Bank (usually course context).
     * @return int      contentbank_content.id
     */
    public function publish(string $h5p_path, string $name, \context $context): int {
        global $DB, $USER;

        if (!file_exists($h5p_path)) {
            throw new \InvalidArgumentException("H5P file not found: {$h5p_path}");
        }

        $fs  = get_file_storage();
        $now = time();

        // Insert contentbank_content record first to get the auto-assigned ID.
        $cb              = new \stdClass();
        $cb->name        = $name;
        $cb->contextid   = $context->id;
        $cb->contenttype = 'contenttype_h5p';
        $cb->instanceid  = 0;         // updated after H5P deploy (or stays 0 — lazy deploy on first view)
        $cb->configdata  = null;
        $cb->visibility  = 1;         // 1 = public
        $cb->usercreated = $USER->id;
        $cb->usermodified= $USER->id;
        $cb->timecreated = $now;
        $cb->timemodified= $now;
        $cb->timeimported= $now;

        $cb_id = $DB->insert_record('contentbank_content', $cb);

        // Store the .h5p file in the Content Bank filearea.
        $fs->delete_area_files($context->id, 'contentbank', 'public', $cb_id);
        $stored = $fs->create_file_from_pathname([
            'contextid' => $context->id,
            'component' => 'contentbank',
            'filearea'  => 'public',
            'itemid'    => $cb_id,
            'filepath'  => '/',
            'filename'  => self::safe_filename($name),
        ], $h5p_path);

        // Attempt eager H5P deployment so the content is ready immediately.
        // Fails gracefully — lazy deployment happens on first activity view.
        $this->try_eager_deploy($stored, $cb_id, $context);

        return $cb_id;
    }

    /**
     * Link a Content Bank H5P item to an h5pactivity module using the native
     * contentbankentryid field (Moodle 4.3+, available in Moodle 5.0).
     *
     * Falls back to file-copy if the column doesn't exist.
     *
     * @param  int      $cb_id           contentbank_content.id
     * @param  int      $h5pactivity_id  h5pactivity.id (instance, not CM id)
     * @param  \context $cb_context      Context where the Content Bank item lives.
     * @param  \context $cm_context      Course-module context of the h5pactivity.
     * @return bool
     */
    public function link_to_activity(
        int $cb_id,
        int $h5pactivity_id,
        \context $cb_context,
        \context $cm_context
    ): bool {
        global $DB;

        // Prefer native contentbankentryid link (no file duplication).
        if ($DB->get_manager()->column_exists('h5pactivity', 'contentbankentryid')) {
            $DB->set_field('h5pactivity', 'contentbankentryid', $cb_id, ['id' => $h5pactivity_id]);
            return true;
        }

        // Fall back: copy .h5p from Content Bank into mod_h5pactivity filearea.
        return $this->copy_file_to_activity($cb_id, $cb_context, $cm_context);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function try_eager_deploy(\stored_file $file, int $cb_id, \context $context): void {
        global $DB;

        try {
            if (!class_exists('\\core_h5p\\factory') || !class_exists('\\core_h5p\\api')) {
                return;
            }
            if (!method_exists('\\core_h5p\\api', 'create_content_from_pluginfile_url')) {
                return;
            }

            $factory = new \core_h5p\factory();
            $core    = new \core_h5p\core($factory);

            $file_url = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                'contentbank', 'public',
                $cb_id, '/',
                $file->get_filename(),
                false, true
            )->out(false);

            $h5p_id = \core_h5p\api::create_content_from_pluginfile_url(
                $file_url, $file, $factory, $core
            );
            if ($h5p_id) {
                $DB->set_field('contentbank_content', 'instanceid', $h5p_id, ['id' => $cb_id]);
            }
        } catch (\Throwable $e) {
            // Deployment deferred to first access — non-fatal.
            debugging('H5P eager deploy deferred: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    private function copy_file_to_activity(int $cb_id, \context $cb_context, \context $cm_context): bool {
        $fs       = get_file_storage();
        $cb_files = $fs->get_area_files($cb_context->id, 'contentbank', 'public', $cb_id, '', false);
        if (empty($cb_files)) {
            return false;
        }
        $source = reset($cb_files);

        $fs->delete_area_files($cm_context->id, 'mod_h5pactivity', 'package');
        $fs->create_file_from_storedfile([
            'contextid' => $cm_context->id,
            'component' => 'mod_h5pactivity',
            'filearea'  => 'package',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $source->get_filename(),
        ], $source);

        return true;
    }

    private static function safe_filename(string $name): string {
        $slug = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
        $slug = trim($slug, '_') ?: 'h5p_content';
        return mb_substr($slug, 0, 80) . '.h5p';
    }
}
