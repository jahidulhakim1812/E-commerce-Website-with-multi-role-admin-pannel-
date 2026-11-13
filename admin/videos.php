<?php
require '_layout.php';
admin_start('Product Stories & Videos', 'content');
ensure_product_videos();

function upload_product_video($file) {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return [null, ''];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return [null, 'The video upload failed. Check the server upload size limit and try again.'];
    if (($file['size'] ?? 0) > 50 * 1024 * 1024) return [null, 'Videos must be 50 MB or smaller.'];
    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $types = ['mp4' => 'video/mp4', 'webm' => 'video/webm'];
    if (!isset($types[$ext]) || $mime !== $types[$ext]) return [null, 'Upload a valid MP4 or WebM video.'];
    if (!is_uploaded_file($file['tmp_name'])) return [null, 'The uploaded file could not be verified.'];
    if (!is_dir(UPLOAD_DIR) && !@mkdir(UPLOAD_DIR, 0755, true)) return [null, 'Could not prepare the uploads folder.'];
    $name = date('ymd') . '-' . bin2hex(random_bytes(5)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) return [null, 'Could not save the uploaded video.'];
    return [$name, ''];
}

$categories = rows('SELECT id,name_en,status FROM categories ORDER BY parent_id,sort_order,id');
$id = (int)($_GET['id'] ?? 0);
$editing = $id ? row('SELECT * FROM product_videos WHERE id=?', [$id]) : null;
$action = $_GET['a'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['delete'])) {
        $video = row('SELECT * FROM product_videos WHERE id=?', [(int)$_POST['delete']]);
        if ($video) {
            q('DELETE FROM product_videos WHERE id=?', [$video['id']]);
            $path = UPLOAD_DIR . basename($video['video_file']);
            if (is_file($path) && !unlink($path)) flash('Video entry deleted, but its file could not be removed.', 'err');
            else flash('Video deleted.');
        }
        redirect('videos.php');
    }

    $id = (int)($_POST['id'] ?? 0);
    $old = $id ? row('SELECT * FROM product_videos WHERE id=?', [$id]) : null;
    $titleEn = trim($_POST['title_en'] ?? '');
    $titleBn = trim($_POST['title_bn'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $categoryExists = val('SELECT id FROM categories WHERE id=? AND status=1', [$categoryId]);
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;
    [$videoFile, $uploadError] = upload_product_video($_FILES['video'] ?? []);
    $error = $uploadError;
    if (!$old && $id) $error = 'Video not found.';
    elseif (!$categoryExists) $error = 'Choose an active category.';
    elseif ($titleEn === '') $error = 'English video title is required.';
    elseif (!$old && !$videoFile && !$error) $error = 'Choose an MP4 or WebM video to upload.';

    if ($error) {
        if ($videoFile) unlink(UPLOAD_DIR . $videoFile);
        flash($error, 'err');
        redirect($id ? 'videos.php?a=edit&id=' . $id : 'videos.php?a=add');
    }

    $storedFile = $videoFile ?: $old['video_file'];
    try {
        if ($old) {
            q('UPDATE product_videos SET category_id=?,title_en=?,title_bn=?,video_file=?,sort_order=?,status=? WHERE id=?', [$categoryId, $titleEn, $titleBn ?: null, $storedFile, $sortOrder, $status, $id]);
        } else {
            q('INSERT INTO product_videos (category_id,title_en,title_bn,video_file,sort_order,status) VALUES (?,?,?,?,?,?)', [$categoryId, $titleEn, $titleBn ?: null, $storedFile, $sortOrder, $status]);
        }
        if ($videoFile && $old && $old['video_file'] !== $videoFile) {
            $oldPath = UPLOAD_DIR . basename($old['video_file']);
            if (is_file($oldPath) && !unlink($oldPath)) flash('Saved, but the previous video file could not be removed.', 'err');
            else flash('Video saved.');
        } else flash('Video saved.');
    } catch (PDOException $ex) {
        if ($videoFile && is_file(UPLOAD_DIR . $videoFile)) unlink(UPLOAD_DIR . $videoFile);
        flash('Could not save the video: ' . $ex->getMessage(), 'err');
        redirect($id ? 'videos.php?a=edit&id=' . $id : 'videos.php?a=add');
    }
    redirect('videos.php');
}

if ($action === 'add' || $action === 'edit') {
    if ($action === 'edit' && !$editing) { echo '<div class="alert err">Video not found.</div>'; admin_end(); exit; }
    $r = $editing ?: ['category_id' => '', 'title_en' => '', 'title_bn' => '', 'sort_order' => 0, 'status' => 1, 'video_file' => ''];
    ?>
    <div class="alert ok">Upload portrait product clips (MP4 or WebM, up to 50 MB), choose their category, and they will appear in the homepage story carousel. Your server must allow uploads of at least 50 MB.</div>
    <div class="panel">
      <div class="panel-h"><h3><?= $editing ? 'Edit' : 'Add' ?> Video Story</h3><a class="abtn ghost" href="videos.php">← Back</a></div>
      <form method="post" enctype="multipart/form-data" class="aform">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
        <div class="fgrid2">
          <div class="fld half"><label>Category *</label><select name="category_id" required><option value="">Choose category</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$r['category_id'] === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['name_en']) ?><?= !$c['status'] ? ' (inactive)' : '' ?></option><?php endforeach; ?></select></div>
          <div class="fld half"><label>Position (lower appears first)</label><input type="number" name="sort_order" value="<?= (int)$r['sort_order'] ?>"></div>
          <div class="fld half"><label>Title (English) *</label><input name="title_en" maxlength="200" required value="<?= e($r['title_en']) ?>"></div>
          <div class="fld half"><label>Title (বাংলা)</label><input name="title_bn" maxlength="200" value="<?= e($r['title_bn']) ?>"></div>
          <div class="fld full"><label>Video <?= $editing ? '(optional; leave empty to keep current)' : '*' ?></label>
            <?php if ($r['video_file']): ?><video class="admin-video-preview" controls playsinline preload="metadata"><source src="<?= e(img($r['video_file'])) ?>" type="<?= strtolower(pathinfo($r['video_file'], PATHINFO_EXTENSION)) === 'webm' ? 'video/webm' : 'video/mp4' ?>"></video><?php endif; ?>
            <input type="file" name="video" accept="video/mp4,video/webm" <?= $editing ? '' : 'required' ?>>
            <small>MP4 or WebM only. Maximum 50 MB.</small>
          </div>
          <div class="fld full"><label class="sw"><input type="checkbox" name="status" value="1" <?= $r['status'] ? 'checked' : '' ?>><span></span> Visible on the homepage</label></div>
        </div>
        <div class="form-actions"><button class="abtn">💾 Save Video</button> <a class="abtn ghost" href="videos.php">Cancel</a></div>
      </form>
    </div>
    <?php
    admin_end();
    exit;
}

$videos = rows('SELECT v.*,c.name_en AS category_name FROM product_videos v LEFT JOIN categories c ON c.id=v.category_id ORDER BY v.sort_order,v.id DESC');
?>
<div class="alert ok">Add short product videos here. Assign each one to a category; visible videos appear in the Product Stories & Videos section on your homepage.</div>
<div class="panel">
  <div class="panel-h"><h3>Product Stories & Videos <small>(<?= count($videos) ?>)</small></h3><a class="abtn" href="videos.php?a=add">＋ Add Video</a></div>
  <div class="table-wrap"><table class="atbl video-table"><thead><tr><th>Video</th><th>Title</th><th>Category</th><th>Position</th><th>Status</th><th>Actions</th></tr></thead><tbody>
    <?php foreach ($videos as $video): ?><tr>
      <td><video class="admin-video-thumb" muted playsinline preload="metadata" aria-label="Preview: <?= e($video['title_en']) ?>"><source src="<?= e(img($video['video_file'])) ?>" type="<?= strtolower(pathinfo($video['video_file'], PATHINFO_EXTENSION)) === 'webm' ? 'video/webm' : 'video/mp4' ?>"></video></td>
      <td class="video-title"><?= e($video['title_en']) ?></td><td><?= e($video['category_name'] ?: 'Category removed') ?></td><td class="video-position"><?= (int)$video['sort_order'] ?></td>
      <td class="video-status"><span class="pill <?= $video['status'] ? 's-delivered' : 's-cancelled' ?>"><?= $video['status'] ? 'Active' : 'Off' ?></span></td>
      <td><div class="video-actions"><a class="abtn sm" href="videos.php?a=edit&id=<?= (int)$video['id'] ?>">✎ Edit</a><form method="post" onsubmit="return confirm('Delete this video?')"><?= csrf_field() ?><button class="abtn sm danger" name="delete" value="<?= (int)$video['id'] ?>">🗑</button></form></div></td>
    </tr><?php endforeach; ?>
    <?php if (!$videos): ?><tr><td colspan="6" class="empty">No video stories yet.</td></tr><?php endif; ?>
  </tbody></table></div>
</div>
<?php admin_end(); ?>
