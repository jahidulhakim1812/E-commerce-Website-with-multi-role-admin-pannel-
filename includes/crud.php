<?php
/* Generic admin CRUD engine: one config array -> list, search, add, edit, delete, image upload. */
function crud_opts($f) { $o = $f['opts'] ?? []; return is_callable($o) ? $o() : $o; }
function crud($c) {
    $t = $c['table']; $F = $c['fields']; $self = basename($_SERVER['SCRIPT_NAME']);
    $where = $c['where'] ?? '1=1'; $wargs = $c['wargs'] ?? []; $fixed = $c['fixed'] ?? [];
    $keep = $c['keep'] ?? []; $kq = $keep ? '&' . http_build_query($keep) : '';
    $back = $self . ($keep ? '?' . http_build_query($keep) : '');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();
        if (isset($_POST['_delete'])) {
            $r = row("SELECT * FROM $t WHERE id=? AND ($where)", array_merge([(int)$_POST['_delete']], $wargs));
            if ($r && (!isset($c['can_delete']) || $c['can_delete']($r))) { q("DELETE FROM $t WHERE id=?", [$r['id']]); if (isset($c['on_delete'])) $c['on_delete']($r); flash('Deleted.'); } else flash('Cannot delete this item.', 'err');
            redirect($back);
        }
        if (isset($_POST['_save'])) {
            $id = (int)($_POST['id'] ?? 0); $old = $id ? row("SELECT * FROM $t WHERE id=? AND ($where)", array_merge([$id], $wargs)) : null;
            if ($id && !$old) { flash('Not found', 'err'); redirect($back); }
            $d = []; $err = '';
            foreach ($F as $f) {
                $n = $f['name']; $ty = $f['type'] ?? 'text'; if (!empty($f['noform']) || $ty === 'custom') continue;
                switch ($ty) {
                    case 'image': $v = $old[$n] ?? ''; if (!empty($_POST['rm_' . $n])) $v = ''; $d[$n] = upload_image('f_' . $n, $v); break;
                    case 'images':
                        $cur = array_filter(explode(',', $old[$n] ?? '')); $cur = array_values(array_diff($cur, (array)($_POST['rmimg_' . $n] ?? [])));
                        if (!empty($_FILES['f_' . $n]['name'][0])) foreach ($_FILES['f_' . $n]['name'] as $i => $nm) { if ($_FILES['f_' . $n]['error'][$i] === UPLOAD_ERR_OK && ($sv = save_upload($_FILES['f_' . $n]['tmp_name'][$i], $nm, $_FILES['f_' . $n]['size'][$i]))) $cur[] = $sv; }
                        $d[$n] = implode(',', $cur); break;
                    case 'checkbox': $d[$n] = isset($_POST[$n]) ? 1 : 0; break;
                    case 'checks': $d[$n] = implode(',', array_intersect((array)($_POST[$n] ?? []), array_keys(crud_opts($f)))); break;
                    case 'password': if (($_POST[$n] ?? '') !== '') $d[$n] = password_hash($_POST[$n], PASSWORD_DEFAULT); elseif (!$id) $err = 'Password is required.'; break;
                    case 'number': $d[$n] = ($_POST[$n] ?? '') === '' ? ($f['default'] ?? 0) : (float)$_POST[$n]; break;
                    case 'date': $d[$n] = ($_POST[$n] ?? '') ?: null; break;
                    case 'select': $v = $_POST[$n] ?? ''; $d[$n] = array_key_exists($v, crud_opts($f)) ? $v : ($old[$n] ?? array_key_first(crud_opts($f))); break;
                    default: $d[$n] = trim($_POST[$n] ?? '');
                }
                if (!empty($f['req']) && in_array($ty, ['text', 'textarea']) && $d[$n] === '') $err = ($f['label'] ?? $n) . ' is required.';
                if (($f['type'] ?? '') === 'slug') {}
            }
            foreach ($F as $f) if (($f['slug_from'] ?? '') && isset($d[$f['name']])) { $s = $d[$f['name']] ?: slugify($d[$f['slug_from']] ?? ''); $s = slugify($s); if (val("SELECT id FROM $t WHERE {$f['name']}=? AND id<>?", [$s, $id])) $s .= '-' . rand(100, 999); $d[$f['name']] = $s; }
            foreach ($fixed as $k => $v) $d[$k] = $v;
            if ($err) { flash($err, 'err'); $_SESSION['old'] = $_POST; redirect($self . '?a=' . ($id ? 'edit&id=' . $id : 'add') . $kq); }
            try {
                if ($id) { $set = implode(',', array_map(fn($k) => "$k=?", array_keys($d))); q("UPDATE $t SET $set WHERE id=?", array_merge(array_values($d), [$id])); }
                else { $cols = implode(',', array_keys($d)); $ph = implode(',', array_fill(0, count($d), '?')); q("INSERT INTO $t ($cols) VALUES ($ph)", array_values($d)); $id = (int)db()->lastInsertId(); }
                if (isset($c['after'])) $c['after']($id, !$old, $d);
                flash('Saved successfully.');
            } catch (PDOException $ex) { flash('Could not save: ' . (strpos($ex->getMessage(), 'Duplicate') !== false ? 'duplicate value (email/slug/code must be unique).' : $ex->getMessage()), 'err'); }
            redirect($back);
        }
    }
    $a = $_GET['a'] ?? 'list'; $title = $c['title'];
    if ($a === 'add' || $a === 'edit') {
        $id = (int)($_GET['id'] ?? 0); $r = $id ? row("SELECT * FROM $t WHERE id=? AND ($where)", array_merge([$id], $wargs)) : [];
        if ($id && !$r) { echo '<p>Not found</p>'; return; }
        $ov = $_SESSION['old'] ?? null; unset($_SESSION['old']); $r = $r ?: ($c['defaults'] ?? []); if ($ov) $r = array_merge($r, $ov);
        echo '<div class="panel"><div class="panel-h"><h3>' . ($id ? 'Edit' : 'Add') . ' ' . e($c['single'] ?? $title) . '</h3><a class="abtn ghost" href="' . e($back) . '">← Back</a></div><form method="post" enctype="multipart/form-data" class="aform">' . csrf_field() . '<input type="hidden" name="_save" value="1"><input type="hidden" name="id" value="' . $id . '"><div class="fgrid2">';
        foreach ($F as $f) {
            if (!empty($f['noform'])) continue; $n = $f['name']; $ty = $f['type'] ?? 'text'; $v = $r[$n] ?? ($f['default'] ?? ''); $lab = e($f['label'] ?? ucfirst($n)); $w = !empty($f['half']) ? 'half' : 'full';
            if ($ty === 'custom') { echo '<div class="fld full">' . $f['render']($r, $id) . '</div>'; continue; }
            echo '<div class="fld ' . $w . '"><label>' . $lab . (!empty($f['req']) ? ' *' : '') . '</label>';
            if ($ty === 'textarea') echo '<textarea name="' . $n . '" rows="' . ($f['rows'] ?? 4) . '">' . e($v) . '</textarea>';
            elseif ($ty === 'select') { echo '<select name="' . $n . '">'; foreach (crud_opts($f) as $k => $l) echo '<option value="' . e($k) . '" ' . ((string)$v === (string)$k ? 'selected' : '') . '>' . e($l) . '</option>'; echo '</select>'; }
            elseif ($ty === 'checkbox') echo '<label class="sw"><input type="checkbox" name="' . $n . '" value="1" ' . ($v ? 'checked' : '') . '><span></span> ' . e($f['hint'] ?? 'Enabled') . '</label>';
            elseif ($ty === 'checks') { $sel = explode(',', (string)$v); echo '<div class="checks">'; foreach (crud_opts($f) as $k => $l) echo '<label><input type="checkbox" name="' . $n . '[]" value="' . e($k) . '" ' . (in_array($k, $sel) ? 'checked' : '') . '> ' . e($l) . '</label>'; echo '</div>'; }
            elseif ($ty === 'image') { if ($v) echo '<div class="prev"><img src="' . img($v) . '" alt=""><label><input type="checkbox" name="rm_' . $n . '" value="1"> Remove</label></div>'; echo '<input type="file" name="f_' . $n . '" accept="image/*">'; }
            elseif ($ty === 'images') { echo '<div class="prev">'; foreach (array_filter(explode(',', (string)$v)) as $im) echo '<span><img src="' . img($im) . '" alt=""><label><input type="checkbox" name="rmimg_' . $n . '[]" value="' . e($im) . '"> ✕</label></span>'; echo '</div><input type="file" name="f_' . $n . '[]" accept="image/*" multiple>'; }
            elseif ($ty === 'password') echo '<input type="password" name="' . $n . '" autocomplete="new-password" placeholder="' . ($id ? 'Leave blank to keep current' : '') . '">';
            else echo '<input type="' . ($ty === 'number' ? 'number" step="any' : ($ty === 'date' ? 'date' : ($ty === 'color' ? 'color' : 'text'))) . '" name="' . $n . '" value="' . e($v) . '">';
            if (!empty($f['help'])) echo '<small>' . e($f['help']) . '</small>';
            echo '</div>';
        }
        echo '</div><div class="form-actions"><button class="abtn">💾 Save</button> <a class="abtn ghost" href="' . e($back) . '">Cancel</a></div></form></div>'; return;
    }
    /* list */
    $qv = trim($_GET['q'] ?? ''); $w = $where; $args = $wargs;
    if ($qv !== '' && !empty($c['search'])) { $w .= ' AND (' . implode(' OR ', array_map(fn($s) => "$s LIKE ?", $c['search'])) . ')'; foreach ($c['search'] as $s) $args[] = "%$qv%"; }
    $per = 15; $pg = max(1, (int)($_GET['page'] ?? 1)); $tot = (int)val("SELECT COUNT(*) FROM $t WHERE $w", $args); $pages = max(1, ceil($tot / $per));
    $rs = rows("SELECT * FROM $t WHERE $w ORDER BY " . ($c['order'] ?? 'id DESC') . " LIMIT $per OFFSET " . (($pg - 1) * $per), $args);
    echo '<div class="panel"><div class="panel-h"><h3>' . e($title) . ' <small>(' . $tot . ')</small></h3><div class="ph-r"><form class="mini-s"><input name="q" value="' . e($qv) . '" placeholder="Search...">' . implode('', array_map(fn($k, $v) => '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">', array_keys($keep), $keep)) . '</form>' . (($c['can_add'] ?? true) ? '<a class="abtn" href="?a=add' . $kq . '">＋ Add New</a>' : '') . '</div></div>';
    echo '<div class="table-wrap"><table class="atbl"><thead><tr><th>#</th>'; foreach ($F as $f) if (!empty($f['list'])) echo '<th>' . e($f['label'] ?? $f['name']) . '</th>'; echo '<th>Actions</th></tr></thead><tbody>';
    foreach ($rs as $r) {
        echo '<tr><td>' . $r['id'] . '</td>';
        foreach ($F as $f) { if (empty($f['list'])) continue; $n = $f['name']; $ty = $f['type'] ?? 'text'; $v = $r[$n] ?? ''; echo '<td>';
            if (isset($f['fmt'])) echo $f['fmt']($r); elseif ($ty === 'image') echo '<img class="thumb" src="' . img($v) . '" alt="">'; elseif ($ty === 'checkbox') echo '<span class="pill ' . ($v ? 's-delivered' : 's-cancelled') . '">' . ($v ? 'Active' : 'Off') . '</span>';
            elseif ($ty === 'select') echo e(crud_opts($f)[$v] ?? $v); elseif ($ty === 'number' && !empty($f['money'])) echo money($v); else echo e(mb_strimwidth((string)$v, 0, 60, '…')); echo '</td>'; }
        echo '<td class="acts">' . (isset($c['actions']) ? $c['actions']($r) : '') . '<a class="abtn sm" href="?a=edit&id=' . $r['id'] . $kq . '">✎ Edit</a>';
        if (!isset($c['can_delete']) || $c['can_delete']($r)) echo '<form method="post" onsubmit="return confirm(\'Delete this item?\')" style="display:inline">' . csrf_field() . '<input type="hidden" name="_delete" value="' . $r['id'] . '"><button class="abtn sm danger">🗑</button></form>';
        echo '</td></tr>';
    }
    if (!$rs) echo '<tr><td colspan="20" class="empty">No records yet.</td></tr>';
    echo '</tbody></table></div>';
    if ($pages > 1) { echo '<div class="pager">'; for ($i = 1; $i <= $pages; $i++) echo '<a class="' . ($i == $pg ? 'on' : '') . '" href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '">' . $i . '</a>'; echo '</div>'; }
    echo '</div>';
}
