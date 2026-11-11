<?php
/* DryNutri web installer. Delete this file after installation. */
session_start();
$locked = file_exists(__DIR__ . '/config.local.php');
$msg = ''; $ok = false;
function split_sql($sql) { $out = []; $cur = ''; $inq = false; $n = strlen($sql);
    for ($i = 0; $i < $n; $i++) { $c = $sql[$i];
        if ($c === "'") { if ($inq && ($sql[$i + 1] ?? '') === "'") { $cur .= "''"; $i++; continue; } $inq = !$inq; }
        if ($c === ';' && !$inq) { if (trim($cur) !== '') $out[] = $cur; $cur = ''; continue; }
        $cur .= $c; }
    if (trim($cur) !== '') $out[] = $cur; return $out; }
if (!$locked && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $h = trim($_POST['host']); $d = preg_replace('/[^A-Za-z0-9_]/', '', $_POST['db']); $u = $_POST['user']; $p = $_POST['pass']; $ae = trim($_POST['aemail']); $ap = $_POST['apass'];
    try {
        if (!filter_var($ae, FILTER_VALIDATE_EMAIL) || strlen($ap) < 6) throw new Exception('Enter a valid admin email and a password of at least 6 characters.');
        $pdo = new PDO("mysql:host=$h;charset=utf8mb4", $u, $p, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$d` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"); $pdo->exec("USE `$d`");
        if ($pdo->query("SHOW TABLES LIKE 'settings'")->fetch()) throw new Exception("Database '$d' already contains DryNutri tables. Use an empty database.");
        $sql = preg_replace('/^--.*$/m', '', file_get_contents(__DIR__ . '/database.sql'));
        foreach (split_sql($sql) as $st) $pdo->exec($st);
        $pdo->prepare("UPDATE users SET email=?, password=? WHERE id=1")->execute([$ae, password_hash($ap, PASSWORD_DEFAULT)]);
        $pdo->prepare("REPLACE INTO settings(skey,sval) VALUES('admin_email',?)")->execute([$ae]);
        $cfg = "<?php\ndefine('DB_HOST'," . var_export($h, true) . ");\ndefine('DB_NAME'," . var_export($d, true) . ");\ndefine('DB_USER'," . var_export($u, true) . ");\ndefine('DB_PASS'," . var_export($p, true) . ");\n";
        if (file_put_contents(__DIR__ . '/config.local.php', $cfg) === false) throw new Exception('Could not write config.local.php – make the folder writable or create it manually.');
        $ok = true;
    } catch (Exception $e) { $msg = $e->getMessage(); }
}
?><!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>DryNutri Installer</title>
<style>body{font-family:'Segoe UI',system-ui,sans-serif;background:linear-gradient(135deg,#e7f3e1,#fdf6e6);min-height:100vh;margin:0;display:grid;place-items:center;padding:20px}.box{background:#fff;max-width:520px;width:100%;border-radius:22px;padding:34px;box-shadow:0 20px 50px rgba(31,92,58,.18)}h1{color:#2f7d32;margin:0 0 6px}label{display:block;font-weight:600;font-size:.85rem;margin:12px 0 4px}input{width:100%;padding:11px 14px;border:1.5px solid #dfe6da;border-radius:12px;font-size:.95rem;box-sizing:border-box}button,.btn{display:block;text-align:center;width:100%;margin-top:20px;background:#2f7d32;color:#fff;border:0;padding:13px;border-radius:999px;font-size:1rem;font-weight:700;cursor:pointer;text-decoration:none;box-sizing:border-box}.err{background:#fde4e4;color:#b02a2a;padding:12px;border-radius:10px;margin:12px 0}.okb{background:#e1f6e8;color:#1c6b3a;padding:14px;border-radius:10px;margin:12px 0}h3{margin:22px 0 0;color:#e8923a}</style></head><body><div class="box">
<p style="text-align:center"><img src="assets/img/logo.png" alt="DryNutri" style="height:90px"></p><h1 style="text-align:center">Installer</h1>
<?php if ($locked && !$ok): ?><div class="okb">DryNutri is already installed. For security, delete <b>install.php</b> from your server.</div><a class="btn" href="index.php">Open Website</a>
<?php elseif ($ok): ?><div class="okb">✅ Installation complete!<br>Admin login: <b><?= htmlspecialchars($ae) ?></b></div><p>Please <b>delete install.php</b> now.</p><a class="btn" href="login.php">Go to Login</a>
<?php else: ?><p style="color:#6b786f">Fill in your MySQL details. The database and all tables (with demo products) will be created automatically.</p><?php if ($msg) echo '<div class="err">' . htmlspecialchars($msg) . '</div>'; ?>
<form method="post"><h3>Database</h3><label>Host</label><input name="host" value="localhost" required><label>Database name</label><input name="db" value="drynutri" required><label>Username</label><input name="user" value="root" required><label>Password</label><input name="pass" type="password">
<h3>Admin account</h3><label>Admin email</label><input name="aemail" type="email" value="admin@drynutri.com" required><label>Admin password</label><input name="apass" type="password" required minlength="6"><button>Install DryNutri</button></form><?php endif; ?></div></body></html>
