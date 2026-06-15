<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

require_login();
admin_externalpage_setup('local_tau_useradmin_create');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tau_useradmin/create_user.php'));
$PAGE->set_title(get_string('pagetitlecreate', 'local_tau_useradmin'));
$PAGE->set_heading(get_string('pagetitlecreate', 'local_tau_useradmin'));
$PAGE->set_pagelayout('admin');

$message = null;
$messagetype = 'info';
$searchidnumber = trim(optional_param('search_idnumber', '', PARAM_TEXT));
$countries = get_string_manager()->get_list_of_countries(false);

$formdata = [
    'userid' => 0,
    'idnumber' => $searchidnumber,
    'suspended' => 0,
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'country' => '',
    'department' => '',
    'city' => '',
    'phone1' => '',
];

if ($searchidnumber !== '') {
    $founduser = local_tau_useradmin_find_user_by_idnumber($searchidnumber);
    if ($founduser) {
        $formdata = [
            'userid' => (int)$founduser->id,
            'idnumber' => (string)$founduser->idnumber,
            'suspended' => (int)$founduser->suspended,
            'firstname' => (string)$founduser->firstname,
            'lastname' => (string)$founduser->lastname,
            'email' => (string)$founduser->email,
            'country' => (string)$founduser->country,
            'department' => (string)$founduser->department,
            'city' => (string)$founduser->city,
            'phone1' => (string)$founduser->phone1,
        ];
        $message = get_string('userfound', 'local_tau_useradmin');
        $messagetype = 'success';
    } else {
        $message = get_string('usernotfound', 'local_tau_useradmin');
        $messagetype = 'warning';
    }
}

if (optional_param('saveuser', '', PARAM_TEXT) !== '') {
    require_sesskey();
    $formdata = local_tau_useradmin_extract_form_data($_POST);
    $errors = local_tau_useradmin_validate_data($formdata, (int)$formdata['id']);

    if (empty($errors)) {
        $userid = local_tau_useradmin_upsert_user($formdata);
        redirect(
            new moodle_url('/local/tau_useradmin/create_user.php', ['search_idnumber' => $formdata['idnumber']]),
            empty($formdata['id']) ? get_string('usercreated', 'local_tau_useradmin') : get_string('userupdated', 'local_tau_useradmin'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }

    $message = implode('<br>', array_map('s', $errors));
    $messagetype = 'danger';
}

echo $OUTPUT->header();
?>
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <h1 class="h2 mb-2"><?php echo s(get_string('pagetitlecreate', 'local_tau_useradmin')); ?></h1>
                            <p class="text-muted mb-0"><?php echo s(get_string('descriptioncreate', 'local_tau_useradmin')); ?></p>
                        </div>
                        <a class="btn btn-outline-primary" href="<?php echo new moodle_url('/local/tau_useradmin/upload_users.php'); ?>">
                            <?php echo s(get_string('menuuploadusers', 'local_tau_useradmin')); ?>
                        </a>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messagetype === 'danger' ? 'danger' : $messagetype; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form class="row g-3 align-items-end mb-4" method="get">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold"><?php echo s(get_string('searchlabel', 'local_tau_useradmin')); ?></label>
                            <input type="text" name="search_idnumber" class="form-control" value="<?php echo s($searchidnumber); ?>" placeholder="Ej: 1085327166">
                            <div class="form-text"><?php echo s(get_string('searchhelp', 'local_tau_useradmin')); ?></div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100"><?php echo s(get_string('searchbutton', 'local_tau_useradmin')); ?></button>
                        </div>
                    </form>

                    <form method="post" class="row g-4">
                        <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>">
                        <input type="hidden" name="userid" value="<?php echo (int)$formdata['userid']; ?>">

                        <div class="col-12">
                            <h2 class="h5 mb-3"><?php echo s(get_string('requiredlegend', 'local_tau_useradmin')); ?></h2>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('searchlabel', 'local_tau_useradmin')); ?></label>
                            <input type="text" name="idnumber" class="form-control" value="<?php echo s($formdata['idnumber']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('status', 'local_tau_useradmin')); ?></label>
                            <select name="suspended" class="form-select">
                                <option value="0" <?php echo (int)$formdata['suspended'] === 0 ? 'selected' : ''; ?>><?php echo s(get_string('active', 'local_tau_useradmin')); ?></option>
                                <option value="1" <?php echo (int)$formdata['suspended'] === 1 ? 'selected' : ''; ?>><?php echo s(get_string('inactive', 'local_tau_useradmin')); ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('firstname', 'local_tau_useradmin')); ?></label>
                            <input type="text" name="firstname" class="form-control" value="<?php echo s($formdata['firstname']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('lastname', 'local_tau_useradmin')); ?></label>
                            <input type="text" name="lastname" class="form-control" value="<?php echo s($formdata['lastname']); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold"><?php echo s(get_string('email', 'local_tau_useradmin')); ?></label>
                            <input type="email" name="email" class="form-control" value="<?php echo s($formdata['email']); ?>" placeholder="usuario@unicesmag.edu.co" required>
                        </div>

                        <div class="col-12">
                            <h2 class="h5 mb-3 mt-2"><?php echo s(get_string('optionallegend', 'local_tau_useradmin')); ?></h2>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('country', 'local_tau_useradmin')); ?></label>
                            <select name="country" class="form-select">
                                <option value=""><?php echo s(get_string('choose')); ?></option>
                                <?php foreach ($countries as $code => $name): ?>
                                    <option value="<?php echo s($code); ?>" <?php echo $formdata['country'] === $code ? 'selected' : ''; ?>><?php echo s($name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('department', 'local_tau_useradmin')); ?></label>
                            <input type="text" name="department" class="form-control" value="<?php echo s($formdata['department']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('city', 'local_tau_useradmin')); ?></label>
                            <input type="text" name="city" class="form-control" value="<?php echo s($formdata['city']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?php echo s(get_string('phone', 'local_tau_useradmin')); ?></label>
                            <input type="text" name="phone1" class="form-control" value="<?php echo s($formdata['phone1']); ?>">
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" name="saveuser" value="1" class="btn btn-primary px-4">
                                <?php echo s((int)$formdata['userid'] > 0 ? get_string('saveupdate', 'local_tau_useradmin') : get_string('savecreate', 'local_tau_useradmin')); ?>
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
