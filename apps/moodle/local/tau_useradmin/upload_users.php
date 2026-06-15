<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

require_login();
admin_externalpage_setup('local_tau_useradmin_upload');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_useradmin/upload_users.php'));
$PAGE->set_title(get_string('pagetitleupload', 'local_tau_useradmin'));
$PAGE->set_heading(get_string('pagetitleupload', 'local_tau_useradmin'));
$PAGE->set_pagelayout('admin');

$expectedheaders = ['idnumber', 'status', 'firstname', 'lastname', 'email', 'country', 'department', 'city', 'phone1'];
$summary = null;
$messages = [];

if (!empty($_FILES['csvfile']['tmp_name'])) {
    require_sesskey();
    $rows = file($_FILES['csvfile']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (!$rows) {
        $messages[] = get_string('csvempty', 'local_tau_useradmin');
    } else {
        $delimiter = strpos($rows[0], ';') !== false ? ';' : ',';
        $headers = array_map('trim', str_getcsv(array_shift($rows), $delimiter));

        if ($headers !== $expectedheaders) {
            $messages[] = get_string('csvinvalidheader', 'local_tau_useradmin');
        } else {
            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($rows as $index => $line) {
                $rownumber = $index + 2;
                $row = array_map('trim', str_getcsv($line, $delimiter));
                if (count($row) !== count($expectedheaders)) {
                    $messages[] = get_string('csvrowerror', 'local_tau_useradmin', (object)[
                        'row' => $rownumber,
                        'message' => 'Numero de columnas invalido',
                    ]);
                    $skipped++;
                    continue;
                }

                $data = array_combine($expectedheaders, $row);
                $payload = local_tau_useradmin_extract_form_data([
                    'idnumber' => $data['idnumber'],
                    'suspended' => $data['status'],
                    'firstname' => $data['firstname'],
                    'lastname' => $data['lastname'],
                    'email' => $data['email'],
                    'country' => $data['country'],
                    'department' => $data['department'],
                    'city' => $data['city'],
                    'phone1' => $data['phone1'],
                ]);

                $existing = local_tau_useradmin_find_user_by_idnumber($payload['idnumber']);
                if (!$existing && $payload['email'] !== '') {
                    $existing = $DB->get_record('user', ['email' => $payload['email'], 'deleted' => 0]);
                }
                $payload['id'] = $existing ? (int)$existing->id : 0;

                $errors = local_tau_useradmin_validate_data($payload, (int)$payload['id']);
                if ($errors) {
                    $messages[] = get_string('csvrowerror', 'local_tau_useradmin', (object)[
                        'row' => $rownumber,
                        'message' => implode(' | ', $errors),
                    ]);
                    $skipped++;
                    continue;
                }

                local_tau_useradmin_upsert_user($payload);
                if ($existing) {
                    $updated++;
                } else {
                    $created++;
                }
            }

            $summary = get_string('csvsummary', 'local_tau_useradmin', (object)[
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);
        }
    }
} else if (optional_param('processcsv', '', PARAM_TEXT) !== '') {
    $messages[] = get_string('csvempty', 'local_tau_useradmin');
}

echo $OUTPUT->header();
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <h1 class="h2 mb-2"><?php echo s(get_string('pagetitleupload', 'local_tau_useradmin')); ?></h1>
                            <p class="text-muted mb-0"><?php echo s(get_string('descriptionupload', 'local_tau_useradmin')); ?></p>
                        </div>
                        <a class="btn btn-outline-primary" href="<?php echo new moodle_url('/local/tau_useradmin/example.csv'); ?>">
                            <?php echo s(get_string('csvtemplate', 'local_tau_useradmin')); ?>
                        </a>
                    </div>

                    <?php if ($summary): ?>
                        <div class="alert alert-success"><?php echo s($summary); ?></div>
                    <?php endif; ?>

                    <?php foreach ($messages as $item): ?>
                        <div class="alert alert-warning"><?php echo s($item); ?></div>
                    <?php endforeach; ?>

                    <form method="post" enctype="multipart/form-data" class="row g-4">
                        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
                        <div class="col-12">
                            <label class="form-label fw-semibold"><?php echo s(get_string('csvfile', 'local_tau_useradmin')); ?></label>
                            <input type="file" name="csvfile" class="form-control" accept=".csv,text/csv" required>
                            <div class="form-text"><?php echo s(get_string('csvhelp', 'local_tau_useradmin')); ?></div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <?php foreach ($expectedheaders as $header): ?>
                                                <th><?php echo s($header); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1085327166</td>
                                            <td>activo</td>
                                            <td>Andrea</td>
                                            <td>Lopez</td>
                                            <td>andrea.lopez@unicesmag.edu.co</td>
                                            <td>CO</td>
                                            <td>Narino</td>
                                            <td>Pasto</td>
                                            <td>3001234567</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" name="processcsv" value="1" class="btn btn-primary px-4">
                                <?php echo s(get_string('processcsv', 'local_tau_useradmin')); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo $OUTPUT->footer();
