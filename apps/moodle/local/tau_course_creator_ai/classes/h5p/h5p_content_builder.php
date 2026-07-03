<?php
namespace local_tau_course_creator_ai\h5p;

defined('MOODLE_INTERNAL') || die();

/**
 * Packages H5P metadata + content JSON into a .h5p ZIP file.
 * PHP's ZipArchive extension is standard on moodlehq/moodle-php-apache (PHP 8.3).
 */
class h5p_content_builder {

    /**
     * Build a .h5p archive and return the path to the temporary file.
     * Caller is responsible for deleting the file when done.
     */
    public static function build(array $h5p_meta, array $content_json): string {
        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException('PHP ZipArchive extension is required to generate H5P files.');
        }

        // H5P core validates authors/changes items strictly when the keys exist.
        // - authors[].name: /^.{1,255}$/, authors[].role: /^\w+$/
        // - changes[].date: /^[0-9]{2}-[0-9]{2}-[0-9]{2} [0-9]{1,2}:[0-9]{2}:[0-9]{2}$/ (DD-MM-YY H:MM:SS)
        // - changes[].author: /^.{1,255}$/, changes[].log: /^.{1,5000}$/s (non-empty)
        if (empty($h5p_meta['authors'])) {
            $h5p_meta['authors'] = [
                ['name' => 'E-TAU Campus Virtual', 'role' => 'Author'],
            ];
        }
        if (empty($h5p_meta['changes'])) {
            $h5p_meta['changes'] = [
                [
                    'date'   => date('d-m-y G:i:s'),
                    'author' => 'E-TAU Campus Virtual',
                    'log'    => 'Generado automaticamente',
                ],
            ];
        }

        global $CFG;
        $tmpdir  = isset($CFG->tempdir) ? $CFG->tempdir : sys_get_temp_dir();
        $tmpfile = $tmpdir . DIRECTORY_SEPARATOR . 'tau_h5p_' . uniqid() . '.h5p';

        $zip = new \ZipArchive();
        $result = $zip->open($tmpfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($result !== true) {
            throw new \RuntimeException("Cannot create H5P ZIP at {$tmpfile} (ZipArchive error {$result}).");
        }

        $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $zip->addFromString('h5p.json',              json_encode($h5p_meta,     $flags));
        $zip->addFromString('content/content.json',  json_encode($content_json, $flags));
        $zip->close();

        return $tmpfile;
    }

    /**
     * Returns the highest installed version of an H5P library as
     * ['machineName'=>…, 'majorVersion'=>int, 'minorVersion'=>int].
     * Returns null if the library is not installed.
     */
    public static function lib(string $machine_name): ?array {
        global $DB;
        $row = $DB->get_record_sql(
            'SELECT majorversion, minorversion FROM {h5p_libraries}
              WHERE machinename = :mn
              ORDER BY majorversion DESC, minorversion DESC',
            ['mn' => $machine_name],
            IGNORE_MULTIPLE
        );
        if (!$row) {
            return null;
        }
        return [
            'machineName'  => $machine_name,
            'majorVersion' => (int) $row->majorversion,
            'minorVersion' => (int) $row->minorversion,
        ];
    }

    /**
     * Build a preloadedDependencies array using installed library versions.
     * Skips any library that is not installed (avoids validation failures).
     */
    public static function deps(array $machine_names): array {
        $result = [];
        foreach ($machine_names as $name) {
            $lib = self::lib($name);
            if ($lib !== null) {
                $result[] = $lib;
            }
        }
        return $result;
    }

    /** UUID v4 – shared by all type generators. */
    public static function uuid(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
