<?php
require '_layout.php';
admin_start('Send Test Email', 'settings');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $to = trim($_POST['test_to'] ?? '');
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        flash('Enter a valid email address.', 'err');
    } elseif (S('mail_enabled') !== '1') {
        flash('Save the Webmail address and password in Settings first.', 'err');
    } else {
        $ok = send_mail($to, S('site_name') . ' - test email', '<h3 style="color:#1f5a24">It works!</h3><p>Your Webmail settings are working. Website emails can now be sent through this account.</p>');
        $error = val('SELECT error FROM mail_log ORDER BY id DESC LIMIT 1');
        flash($ok ? 'Test email sent to ' . $to . '.' : 'Test failed: ' . ($error ?: 'unknown error'), $ok ? 'ok' : 'err');
    }
    redirect('test_email.php');
}
?>
<div class="panel">
    <h3>Send a test email</h3>
    <form method="post" class="aform">
        <?= csrf_field() ?>
        <div class="fld">
            <label for="test_to">Send test to</label>
            <input id="test_to" type="email" name="test_to" value="<?= e(user()['email']) ?>" required>
        </div>
        <button class="abtn" type="submit"><?= ic('send') ?> Send test email</button>
    </form>
</div>
<?php admin_end();
