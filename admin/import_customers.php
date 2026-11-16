<?php
require '_layout.php'; admin_start('Import Customers', 'customers');
if (isset($_GET['template'])) {
    ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="customer-import-template.csv"');
    $template = fopen('php://output', 'w');
    fputcsv($template, ['name', 'email', 'phone', 'city', 'address', 'password']);
    fclose($template);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_customers'])) {
    csrf_check();
    if (!manager_action_code_valid($_POST['_manager_action_code'] ?? '')) {
        flash('Enter the action code provided by your administrator.', 'err');
        redirect('import_customers.php');
    }
    $file = $_FILES['customer_file'] ?? null;
    if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK || strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION)) !== 'csv' || (int)($file['size'] ?? 0) > 5 * 1024 * 1024) {
        flash('Upload a CSV file exported from Excel (maximum 5 MB).', 'err');
        redirect('import_customers.php');
    }
    $handle = fopen($file['tmp_name'], 'rb');
    if (!$handle) { flash('Could not read the uploaded customer file.', 'err'); redirect('import_customers.php'); }
    $firstLine = fgets($handle);
    if ($firstLine === false) { fclose($handle); flash('The uploaded customer file is empty.', 'err'); redirect('import_customers.php'); }
    $delimiter = ','; $columnCount = 0;
    foreach ([',', ';', "\t"] as $candidate) {
        $count = count(str_getcsv($firstLine, $candidate));
        if ($count > $columnCount) { $delimiter = $candidate; $columnCount = $count; }
    }
    rewind($handle);
    $headers = fgetcsv($handle, 0, $delimiter);
    $aliases = ['customer name' => 'name', 'full name' => 'name', 'email address' => 'email', 'phone number' => 'phone', 'mobile' => 'phone'];
    $columns = [];
    foreach ((array)$headers as $index => $header) {
        $label = strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string)$header)));
        $key = $aliases[$label] ?? $label;
        if (in_array($key, ['name', 'email', 'phone', 'city', 'address', 'password'], true)) $columns[$key] = $index;
    }
    $missing = array_diff(['name', 'email', 'password'], array_keys($columns));
    if ($missing) { fclose($handle); flash('Customer file must include these columns: name, email, password. Download the template for the correct format.', 'err'); redirect('import_customers.php'); }

    $added = 0; $skipped = 0; $rowNumber = 1; $errors = []; $limitReached = false;
    while (($values = fgetcsv($handle, 0, $delimiter)) !== false) {
        $rowNumber++;
        if (!array_filter($values, function ($value) { return trim((string)$value) !== ''; })) continue;
        if ($added + $skipped >= 1000) { $limitReached = true; break; }
        $get = function ($column) use ($columns, $values) { return isset($columns[$column]) ? trim((string)($values[$columns[$column]] ?? '')) : ''; };
        $name = $get('name'); $email = $get('email'); $password = $get('password');
        $phone = $get('phone'); $city = $get('city'); $address = $get('address');
        $error = '';
        if ($name === '' || mb_strlen($name) > 120) $error = 'name is required (maximum 120 characters)';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) $error = 'email is invalid or too long';
        elseif (strlen($password) < 6) $error = 'password must contain at least 6 characters';
        elseif (mb_strlen($phone) > 30 || mb_strlen($city) > 80) $error = 'phone or city is too long';
        elseif (val('SELECT id FROM users WHERE email=?', [$email])) $error = 'email already exists';
        if ($error !== '') {
            $skipped++;
            if (count($errors) < 5) $errors[] = 'Row ' . $rowNumber . ': ' . $error;
            continue;
        }
        q("INSERT INTO users(name,email,phone,password,role,address,city,is_guest,status) VALUES(?,?,?,?, 'customer',?,?,0,1)", [
            $name, $email, $phone ?: null, password_hash($password, PASSWORD_DEFAULT), $address ?: null, $city ?: null
        ]);
        $added++;
    }
    fclose($handle);
    $message = 'Imported ' . $added . ' customer(s); skipped ' . $skipped . ' row(s).';
    if ($errors) $message .= ' ' . implode('; ', $errors) . ($skipped > count($errors) ? '; additional row errors omitted.' : '');
    if ($limitReached) $message .= ' Import stopped at the 1,000-row limit.';
    if ($added || $skipped) audit_activity('customer_import', 'Imported ' . $added . ' customer(s); skipped ' . $skipped . ' row(s)', 'customer');
    flash($message, $skipped || $limitReached ? 'err' : 'ok');
    redirect('import_customers.php');
}
$codeField = user()['role'] === 'manager' ? '<div class="fld"><label>Admin-issued action code</label><input type="password" name="_manager_action_code" required autocomplete="off"></div>' : '';
echo '<div class="panel"><div class="panel-h"><h3>Import Customers</h3><a class="abtn sm ghost" href="?template=1">Download CSV Template</a></div><form method="post" enctype="multipart/form-data" class="aform">' . csrf_field() . '<input type="hidden" name="import_customers" value="1"><div class="fld"><label>Excel CSV file</label><input type="file" name="customer_file" accept=".csv,text/csv" required><small>Save your Excel sheet as CSV UTF-8. Required columns: name, email, password (minimum 6 characters). Optional: phone, city, address. Maximum 1,000 customers per upload.</small></div>' . $codeField . '<button class="abtn">Upload and Add Customers</button></form></div>';
admin_end();
