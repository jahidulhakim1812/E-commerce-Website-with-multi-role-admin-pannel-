<?php
require '_layout.php'; admin_start('Settings', 'settings');
$G = [
 'general' => ['General', 'settings', [['site_name', 'Site Name', 'text', 1], ['site_url', 'Canonical website URL (e.g. https://example.com; required for password reset links)', 'url'], ['tagline_en', 'Tagline (English)'], ['tagline_bn', 'Tagline (বাংলা)'], ['currency', 'Currency Symbol'], ['copyright', 'Footer Copyright Text'], ['visitor_tracking', 'Track visitors (IP address & page views)', 'toggle']]],
 'appearance' => ['Appearance', 'image', [['logo', 'Logo (header, emails, invoice) – transparent PNG recommended', 'image'], ['logo_tagline', 'Show the tagline text under the logo (edit it in General → Tagline)', 'toggle'], ['logo_dark', 'Logo for dark areas (footer, side menu, admin) – optional white version', 'image'], ['favicon', 'Favicon (square PNG)', 'image'], ['primary_color', 'Primary colour', 'color'], ['accent_color', 'Accent colour', 'color'], ['topbar_en', 'Top bar messages (English) – separate with |', 'text'], ['topbar_bn', 'Top bar messages (বাংলা) – separate with |', 'text']]],
 'contact' => ['Contact & Footer', 'phone', [['phone', 'Phone'], ['email', 'Public Email', 'email'], ['address_en', 'Address (English)'], ['address_bn', 'Address (বাংলা)'], ['about_en', 'Footer About Text (English)', 'textarea'], ['about_bn', 'Footer About Text (বাংলা)', 'textarea'], ['facebook', 'Facebook URL', 'url'], ['youtube', 'YouTube URL', 'url'], ['instagram', 'Instagram URL', 'url'], ['linkedin', 'LinkedIn URL', 'url'], ['pinterest', 'Pinterest URL', 'url']]],
 'shop' => ['Shipping & Payment', 'truck', [['shipping_inside', 'Delivery charge – Inside Dhaka', 'number'], ['shipping_outside', 'Delivery charge – Outside Dhaka', 'number'], ['free_shipping_min', 'Free delivery above (amount, 0 = never free)', 'number'], ['low_stock', 'Low-stock alert at (units)', 'number'], ['payment_methods', 'Payment methods offered at checkout', 'checks:cod=Cash on Delivery,bkash=bKash,nagad=Nagad'], ['payment_note_en', 'Payment instruction (English)', 'textarea'], ['payment_note_bn', 'Payment instruction (বাংলা)', 'textarea'], ['whatsapp_order', 'Show "Order on WhatsApp" button on product pages', 'toggle']]],
 'float' => ['Floating Contact', 'message-circle', [['float_enabled', 'Show floating contact icon on every page', 'toggle'], ['float_whatsapp', 'WhatsApp number (with country code, e.g. 8801XXXXXXXXX)'], ['float_phone', 'Call number'], ['float_email', 'Email', 'email'], ['float_messenger', 'Messenger link (https://m.me/yourpage)', 'url']]],
 'notify' => ['Notifications', 'bell', [['notify_products', 'Auto-notify visitors when a NEW PRODUCT is added', 'toggle'], ['notify_offers', 'Auto-notify visitors when a NEW COUPON / OFFER is created', 'toggle'], ['notify_email_auto', 'Also auto-email subscribers & customers for every notification (needs SMTP)', 'toggle']]],
 'mail' => ['Email (SMTP)', 'mail', [['mail_enabled', 'Enable automatic order & notification emails', 'toggle'], ['smtp_host', 'SMTP Host (Gmail: smtp.gmail.com)'], ['smtp_port', 'SMTP Port (587 for TLS, 465 for SSL)', 'number'], ['smtp_secure', 'Encryption', 'select:tls=TLS (587),ssl=SSL (465),none=None'], ['smtp_user', 'SMTP Username / Email'], ['smtp_pass', 'SMTP Password / Gmail App Password', 'password'], ['mail_from', 'From Email', 'email'], ['mail_from_name', 'From Name'], ['admin_email', 'Admin email (receives new-order alerts)', 'email'], ['order_mail_subject', 'Order email subject'], ['order_mail_body', 'Order email body (HTML). Placeholders: {name} {order_no} {status} {total} {items} {track_url} {site}', 'textarea']]],
 'sms' => ['SMS (SSL Wireless)', 'message-square', [['sms_enabled', 'Send order confirmations by SMS', 'toggle'], ['sms_api_url', 'SSL Wireless API endpoint (HTTPS)', 'url'], ['sms_api_user', 'SSL Wireless API username'], ['sms_api_pass', 'SSL Wireless API password', 'password'], ['sms_sender_id', 'SSL Wireless sender ID'], ['order_sms_body', 'Order confirmation SMS. Placeholders: {name} {order_no} {total} {status} {site}', 'textarea']]],
 'push' => ['Browser Push', 'bell', [['push_enabled', 'Send push notifications to opted-in website visitors', 'toggle']]],
];
$tab = isset($G[$_GET['tab'] ?? '']) ? $_GET['tab'] : 'general';
function generate_vapid_keys() {
    if (!class_exists(\Minishlink\WebPush\VAPID::class)) throw new RuntimeException('Web Push dependency is missing. Run composer install first.');
    $options = ['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC];
    $opensslConfig = getenv('OPENSSL_CONF');
    $phpIni = php_ini_loaded_file();
    $configPaths = [];
    if ($phpIni) {
        $phpDir = dirname($phpIni);
        $configPaths[] = $phpDir . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
        $configPaths[] = $phpDir . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'openssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
        $configPaths[] = dirname($phpDir) . DIRECTORY_SEPARATOR . 'apache' . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'openssl.cnf';
    }
    if (!$opensslConfig || !is_file($opensslConfig)) foreach ($configPaths as $path) if (is_file($path)) { $opensslConfig = $path; break; }
    if ($opensslConfig && is_file($opensslConfig)) $options['config'] = $opensslConfig;
    $key = openssl_pkey_new($options);
    if ($key === false) throw new RuntimeException('OpenSSL could not create a VAPID key. Check that a valid OpenSSL configuration file is installed.');
    $details = openssl_pkey_get_details($key);
    if (!$details || empty($details['ec']['x']) || empty($details['ec']['y']) || empty($details['ec']['d'])) throw new RuntimeException('OpenSSL did not return valid VAPID key details.');
    $encode = fn($value) => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    $keys = [
        'publicKey' => $encode("\x04" . $details['ec']['x'] . $details['ec']['y']),
        'privateKey' => $encode(str_pad($details['ec']['d'], 32, "\0", STR_PAD_LEFT)),
    ];
    \Minishlink\WebPush\VAPID::validate(['subject' => 'mailto:admin@localhost', 'publicKey' => $keys['publicKey'], 'privateKey' => $keys['privateKey']]);
    set_setting('push_public_key', $keys['publicKey']);
    set_setting('push_private_key', $keys['privateKey']);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['test_mail'])) {
        $to = trim($_POST['test_to'] ?? ''); if (!filter_var($to, FILTER_VALIDATE_EMAIL)) { flash('Enter a valid email address.', 'err'); redirect('settings.php?tab=mail'); }
        if (S('mail_enabled') !== '1') { flash('Turn on "Enable automatic emails", save, then send the test.', 'err'); redirect('settings.php?tab=mail'); }
        $ok = send_mail($to, S('site_name') . ' – test email', '<h3 style="color:#1f5a24">It works!</h3><p>Your SMTP settings are correct. Order and notification emails will now be delivered.</p>');
        flash($ok ? 'Test email sent to ' . $to . '.' : 'Test failed: ' . (val('SELECT error FROM mail_log ORDER BY id DESC LIMIT 1') ?: 'unknown error'), $ok ? 'ok' : 'err'); redirect('settings.php?tab=mail');
    }
    if (isset($_POST['test_sms'])) {
        $to = preg_replace('/\D+/', '', trim($_POST['test_phone'] ?? ''));
        if (!preg_match('/^8801[3-9][0-9]{8}$/', sms_normalize_phone($to))) { flash('Enter a valid Bangladesh mobile number.', 'err'); redirect('settings.php?tab=sms'); }
        if (S('sms_enabled') !== '1') { flash('Enable SMS notifications and save the settings before sending a test.', 'err'); redirect('settings.php?tab=sms'); }
        $smsError = '';
        $ok = send_sms($to, 'DryNutri SMS test. Your SSL Wireless settings are working.', $smsError);
        flash($ok ? 'Test SMS sent to ' . $to . '.' : 'Test SMS failed: ' . $smsError, $ok ? 'ok' : 'err'); redirect('settings.php?tab=sms');
    }
    if (isset($_POST['generate_push_keys'])) {
        try {
            generate_vapid_keys();
            flash('VAPID keys generated. Keep them unchanged for existing subscriptions; regenerate only if they are compromised.', 'ok');
        } catch (Throwable $error) { error_log('VAPID key generation failed: ' . $error->getMessage()); flash('Could not generate VAPID keys: ' . $error->getMessage(), 'err'); }
        redirect('settings.php?tab=push');
    }
    if ($tab === 'push' && isset($_POST['push_enabled']) && (!S('push_public_key') || !S('push_private_key'))) {
        try { generate_vapid_keys(); }
        catch (Throwable $error) { error_log('VAPID key generation failed: ' . $error->getMessage()); flash('Browser push was not enabled. Could not generate VAPID keys: ' . $error->getMessage(), 'err'); redirect('settings.php?tab=push'); }
    }
    $errs = [];
    foreach ($G[$tab][2] as $f) { $k = $f[0]; $ty = $f[2] ?? 'text'; $label = $f[1]; $raw = $_POST[$k] ?? ''; $v = is_array($raw) ? '' : trim($raw);
        if ($ty === 'image') { set_setting($k, !empty($_POST['rm_' . $k]) ? '' : upload_image('f_' . $k, S($k))); continue; }
        if ($ty === 'toggle') { set_setting($k, isset($_POST[$k]) ? '1' : '0'); continue; }
        if ($ty === 'password') { if ($v !== '') set_setting($k, $_POST[$k]); continue; }
        if (strpos($ty, 'checks:') === 0) { $allowed = array_map(fn($o) => explode('=', $o)[0], explode(',', substr($ty, 7))); set_setting($k, implode(',', array_intersect((array)($_POST[$k] ?? []), $allowed))); continue; }
        if (strpos($ty, 'select:') === 0) { $allowed = array_map(fn($o) => explode('=', $o)[0], explode(',', substr($ty, 7))); if (in_array($v, $allowed)) set_setting($k, $v); continue; }
        if ($ty === 'color' && !preg_match('/^#[0-9a-fA-F]{6}$/', $v)) { $errs[] = "$label must be a valid colour."; continue; }
        if ($ty === 'number' && ($v === '' || !is_numeric($v) || $v < 0)) { $errs[] = "$label must be a positive number."; continue; }
        if ($ty === 'email' && $v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) { $errs[] = "$label is not a valid email."; continue; }
        if ($ty === 'url' && $v !== '' && !preg_match('#^(https?://|\#)#i', $v)) { $errs[] = "$label must start with https://"; continue; }
        if ($k === 'sms_api_url' && $v !== '') { $smsHost = strtolower((string)parse_url($v, PHP_URL_HOST)); if (stripos($v, 'https://') !== 0 || !preg_match('/(^|\\.)sslwireless\\.com$/', $smsHost)) { $errs[] = "$label must be an HTTPS URL hosted by SSL Wireless."; continue; } }
        if (!empty($f[3]) && $v === '') { $errs[] = "$label is required."; continue; }
        set_setting($k, $v);
    }
    foreach ($errs as $e1) flash($e1, 'err'); if (!$errs) flash('Settings saved successfully.');
    redirect('settings.php?tab=' . $tab);
}
echo '<div class="tabs-a">'; foreach ($G as $k => $g) echo '<a class="' . ($tab === $k ? 'on' : '') . '" href="?tab=' . $k . '">' . ic($g[1], 16) . ' ' . $g[0] . '</a>'; echo '</div>';
echo '<div class="panel"><form method="post" enctype="multipart/form-data" class="aform">' . csrf_field() . '<div class="fgrid2">';
if ($tab === 'appearance') {
    echo '<div class="fld full"><label>Colour presets</label><div class="presets">';
    foreach ([['Natural Green', '#2f7d32', '#7ed957'], ['Forest', '#1f5a24', '#a3d977'], ['Olive Grove', '#5b7a2b', '#c6dc6b'], ['Mint Fresh', '#1f8a6a', '#7be0b8'], ['Earth & Leaf', '#4b6b3a', '#e0b45c']] as $p) echo '<button type="button" class="preset" data-p="' . $p[1] . '" data-a="' . $p[2] . '"><i style="background:' . $p[1] . '"></i><i style="background:' . $p[2] . '"></i>' . $p[0] . '</button>';
    echo '</div></div>';
}
foreach ($G[$tab][2] as $f) { $k = $f[0]; $ty = $f[2] ?? 'text'; $v = S($k); $full = in_array($ty, ['textarea']) || strpos($ty, 'checks:') === 0;
    echo '<div class="fld ' . ($full ? 'full' : 'half') . '"><label>' . e($f[1]) . (!empty($f[3]) ? ' *' : '') . '</label>';
    if ($ty === 'textarea') echo '<textarea name="' . $k . '" rows="4">' . e($v) . '</textarea>';
    elseif ($ty === 'toggle') echo '<label class="sw"><input type="checkbox" name="' . $k . '" value="1" ' . ($v === '1' ? 'checked' : '') . '><span></span> Enabled</label>';
    elseif ($ty === 'color') echo '<div class="colorrow"><input type="color" name="' . $k . '" id="c_' . $k . '" value="' . e(preg_match('/^#[0-9a-f]{6}$/i', $v) ? $v : '#2f7d32') . '"><code id="h_' . $k . '">' . e($v) . '</code></div>';
    elseif ($ty === 'image') { $show = has_img($v) ? img($v) : ($k === 'favicon' ? asset('img/favicon.png') : ($k === 'logo' ? asset('img/logo-mark.png') : '')); if ($show) echo '<div class="prev' . ($k === 'logo_dark' ? ' darkbg' : '') . '"><img src="' . $show . '" alt="" style="width:auto;max-width:220px;height:' . ($k === 'favicon' ? '48' : '56') . 'px;object-fit:contain">' . (has_img($v) ? '<label><input type="checkbox" name="rm_' . $k . '" value="1"> Remove (use default)</label>' : '<small class="muted">Default DryNutri logo</small>') . '</div>'; echo '<input type="file" name="f_' . $k . '" accept="image/*">'; }
    elseif ($ty === 'password') echo '<input type="password" name="' . $k . '" placeholder="' . ($v ? 'Saved – leave blank to keep' : '') . '" autocomplete="new-password">';
    elseif (strpos($ty, 'checks:') === 0) { $sel = array_filter(explode(',', $v)); echo '<div class="checks">'; foreach (explode(',', substr($ty, 7)) as $o) { [$ov, $ol] = explode('=', $o); echo '<label><input type="checkbox" name="' . $k . '[]" value="' . $ov . '" ' . (in_array($ov, $sel) ? 'checked' : '') . '> ' . e($ol) . '</label>'; } echo '</div>'; }
    elseif (strpos($ty, 'select:') === 0) { echo '<select name="' . $k . '">'; foreach (explode(',', substr($ty, 7)) as $o) { [$ov, $ol] = explode('=', $o); echo '<option value="' . $ov . '" ' . ($v === $ov ? 'selected' : '') . '>' . e($ol) . '</option>'; } echo '</select>'; }
    else echo '<input type="' . ($ty === 'number' ? 'number" step="any" min="0' : ($ty === 'email' ? 'email' : 'text')) . '" name="' . $k . '" value="' . e($v) . '"' . (!empty($f[3]) ? ' required' : '') . '>';
    echo '</div>'; }
echo '</div><div class="form-actions"><button class="abtn">' . ic('save') . ' Save ' . e($G[$tab][0]) . '</button><a class="abtn ghost" href="../" target="_blank">' . ic('external') . ' Preview site</a></div></form></div>';
if ($tab === 'appearance') echo '<script>(function(){var P=document.getElementById("c_primary_color"),A=document.getElementById("c_accent_color");function sync(){document.getElementById("h_primary_color").textContent=P.value;document.getElementById("h_accent_color").textContent=A.value}P.oninput=A.oninput=sync;document.querySelectorAll(".preset").forEach(function(b){b.onclick=function(){P.value=b.dataset.p;A.value=b.dataset.a;sync()}})})()</script>';
if ($tab === 'mail') {
    echo '<div class="panel"><h3>' . ic('send') . ' Send a test email</h3><form method="post" class="filt">' . csrf_field() . '<input type="hidden" name="test_mail" value="1"><input type="email" name="test_to" required value="' . e(user()['email']) . '"><button class="abtn sm">Send test</button></form><p class="muted">Gmail: turn on 2-step verification, create an <b>App Password</b> and paste it above (host smtp.gmail.com, port 587, TLS). Save first, then test.</p>';
    echo '<h3>Recent email log</h3><div class="table-wrap"><table class="atbl"><tr><th>Time</th><th>To</th><th>Subject</th><th>Status</th><th>Error</th></tr>'; foreach (rows('SELECT * FROM mail_log ORDER BY id DESC LIMIT 10') as $m) echo '<tr><td>' . date('d M h:i A', strtotime($m['created_at'])) . '</td><td>' . e($m['to_email']) . '</td><td>' . e($m['subject']) . '</td><td><span class="pill ' . ($m['status'] === 'sent' ? 's-delivered' : 's-cancelled') . '">' . $m['status'] . '</span></td><td>' . e($m['error']) . '</td></tr>'; echo '</table></div></div>';
}
if ($tab === 'sms') {
    echo '<div class="panel"><h3>' . ic('send') . ' Send a test SMS</h3><form method="post" class="filt">' . csrf_field() . '<input type="hidden" name="test_sms" value="1"><input type="tel" name="test_phone" required placeholder="01XXXXXXXXX"><button class="abtn sm">Send test SMS</button></form><p class="muted">Save your SSL Wireless API URL, username, password and sender ID first. Test messages may incur charges. Use the endpoint and credentials provided for your SSL Wireless account.</p></div>';
}
if ($tab === 'push') {
    $publicKey = S('push_public_key');
    echo '<div class="panel"><h3>Anonymous visitor subscriptions</h3><p>Visitors do not need an account or contact details. Each visitor must explicitly allow push notifications in their browser. The browser subscription is stored only to deliver updates.</p><p><b>VAPID keys:</b> ' . ($publicKey ? 'Generated' : 'Not generated') . '</p>';
    if ($publicKey) echo '<div class="fld"><label>Public key</label><textarea rows="2" readonly>' . e($publicKey) . '</textarea><small>The private key is stored in settings and is never shown here.</small></div>';
    echo '<form method="post">' . csrf_field() . '<button class="abtn sm" name="generate_push_keys" value="1">' . ($publicKey ? 'Regenerate VAPID keys' : 'Generate VAPID keys') . '</button></form>';
    echo '<p class="muted">Keys are generated automatically when you enable push if they do not already exist. Regenerating keys invalidates existing browser subscriptions; only do so if the private key is compromised. Push requires HTTPS on the live site (localhost works for testing). After saving, ask visitors to click “Enable push notifications” in the site bell. Publishing a new product, offer, or admin notification will send push notifications.</p></div>';
}
admin_end();
