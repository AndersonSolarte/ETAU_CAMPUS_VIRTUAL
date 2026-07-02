<?php
namespace local_tau_course_creator_ai;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');

class recordings_manager {
    public const SECTION_NAME = 'Grabaciones de clase';
    private const SECTION_MARKER = 'data-tau-recordings-section="1"';

    public static function ensure_recordings_section(int $courseid): int {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $existing = self::get_recordings_section($courseid);
        if ($existing) {
            if (empty($existing->visible)) {
                $existing->visible = 1;
                $DB->update_record('course_sections', $existing);
                rebuild_course_cache($courseid, true);
            }
            return (int)$existing->id;
        }

        $maxsection = (int)$DB->get_field_sql(
            'SELECT COALESCE(MAX(section), 0) FROM {course_sections} WHERE course = ?',
            [$courseid]
        );
        $targetsection = $maxsection + 1;

        course_create_sections_if_missing($course, $targetsection);
        $section = $DB->get_record('course_sections', ['course' => $courseid, 'section' => $targetsection], '*', MUST_EXIST);
        $section->name = self::SECTION_NAME;
        $section->summary = '<div ' . self::SECTION_MARKER . '>Espacio para grabaciones organizadas por modulo y seccion.</div>';
        $section->summaryformat = FORMAT_HTML;
        $section->visible = 1;
        $DB->update_record('course_sections', $section);

        rebuild_course_cache($courseid, true);
        return (int)$section->id;
    }

    public static function get_recordings_section(int $courseid): ?\stdClass {
        global $DB;

        $sections = $DB->get_records('course_sections', ['course' => $courseid], 'section ASC');
        foreach ($sections as $section) {
            $summary = (string)($section->summary ?? '');
            if (strpos($summary, self::SECTION_MARKER) !== false || trim((string)$section->name) === self::SECTION_NAME) {
                return $section;
            }
        }

        return null;
    }

    public static function get_section_picker_data(int $courseid): array {
        global $DB;

        $recordingssection = self::get_recordings_section($courseid);
        $sections = $DB->get_records('course_sections', ['course' => $courseid], 'section ASC', 'id,section,name,summary');
        $currentmodule = 'Contenido general';
        $items = [];

        foreach ($sections as $section) {
            if ((int)$section->section === 0) {
                continue;
            }
            if ($recordingssection && (int)$section->id === (int)$recordingssection->id) {
                continue;
            }

            $name = trim((string)$section->name);
            if ($name === '') {
                $name = 'Seccion ' . (int)$section->section;
            }

            $items[] = [
                'id' => (int)$section->id,
                'sectionnum' => (int)$section->section,
                'modulelabel' => $name,
                'sectionlabel' => '',
            ];
        }

        return $items;
    }

    public static function create_recording(int $courseid, int $sectionid, string $title, string $videourl, int $userid, string $customgroup = ''): void {
        global $DB;

        $sectionnum = 0;
        if ($sectionid > 0) {
            $section = $DB->get_record('course_sections', ['id' => $sectionid, 'course' => $courseid], 'id,section');
            if ($section) {
                $sectionnum = (int)$section->section;
            } else {
                $sectionid = 0;
            }
        }

        $providerinfo = self::detect_provider($videourl);
        $sortorder = (int)$DB->get_field_sql(
            'SELECT COALESCE(MAX(sortorder), 0) FROM {local_tau_ccai_recordings} WHERE courseid = ? AND sectionid = ? AND customgroup = ?',
            [$courseid, $sectionid, trim($customgroup)]
        ) + 1;

        $record = (object)[
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'sectionnum' => $sectionnum,
            'customgroup' => trim($customgroup),
            'title' => trim($title),
            'videourl' => trim($videourl),
            'provider' => $providerinfo['provider'],
            'sortorder' => $sortorder,
            'userid' => $userid,
            'timecreated' => time(),
            'timemodified' => time(),
        ];

        $DB->insert_record('local_tau_ccai_recordings', $record);
    }

    public static function delete_recording(int $recordingid, int $courseid): void {
        global $DB;
        $DB->delete_records('local_tau_ccai_recordings', ['id' => $recordingid, 'courseid' => $courseid]);
    }

    public static function get_recordings_for_course(int $courseid): array {
        global $DB;

        $pickermap = [];
        foreach (self::get_section_picker_data($courseid) as $item) {
            $pickermap[(int)$item['id']] = $item;
        }

        $records = $DB->get_records('local_tau_ccai_recordings', ['courseid' => $courseid], 'sectionnum ASC, sortorder ASC, timemodified DESC');
        $result = [];

        foreach ($records as $record) {
            $provider = self::detect_provider((string)$record->videourl);
            $customgroup = property_exists($record, 'customgroup') ? trim((string)$record->customgroup) : '';
            
            if ($customgroup !== '') {
                $modulelabel = $customgroup;
            } else {
                $sectionmeta = $pickermap[(int)$record->sectionid] ?? [
                    'modulelabel' => 'Contenido general',
                    'sectionlabel' => 'Seccion',
                    'sectionnum' => (int)$record->sectionnum,
                ];
                $modulelabel = (string)$sectionmeta['modulelabel'];
            }

            $result[] = [
                'id' => (int)$record->id,
                'title' => (string)$record->title,
                'videourl' => (string)$record->videourl,
                'embedurl' => $provider['embedurl'],
                'provider' => $provider['provider'],
                'modulelabel' => $modulelabel,
                'sectionlabel' => (string)$sectionmeta['sectionlabel'],
                'sectionnum' => (int)$sectionmeta['sectionnum'],
            ];
        }

        return $result;
    }

    public static function detect_provider(string $url): array {
        $url = trim($url);
        $provider = 'generic';
        $embedurl = $url;

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~i', $url, $matches)) {
            $provider = 'youtube';
            $embedurl = 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0&modestbranding=1';
        } else if (preg_match('~drive\.google\.com/file/d/([^/]+)~i', $url, $matches)) {
            $provider = 'drive';
            $embedurl = 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
        } else if (preg_match('~drive\.google\.com/open\?id=([^&]+)~i', $url, $matches)) {
            $provider = 'drive';
            $embedurl = 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
        } else if (preg_match('~\.mp4(?:\?|$)~i', $url)) {
            $provider = 'direct';
            $embedurl = $url;
        }

        return [
            'provider' => $provider,
            'embedurl' => $embedurl,
        ];
    }
}
