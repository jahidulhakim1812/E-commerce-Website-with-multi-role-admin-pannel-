<?php
require_once __DIR__ . '/includes/core.php';
require_once __DIR__ . '/includes/live_chat.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'Unsupported request method.'], 405);
csrf_check();
ensure_live_chat_tables();
$action = $_POST['action'] ?? '';

function chat_error($message, $status = 400) { json_out(['ok' => false, 'error' => $message], $status); }
function chat_staff() {
    if (!is_staff() || !can('customers')) chat_error('You do not have permission to access live chat.', 403);
    return user();
}
function chat_post_body() {
    $body = trim((string)($_POST['body'] ?? ''));
    if (mb_strlen($body) > 2000) chat_error('Messages must be 2,000 characters or fewer.');
    return $body;
}
function chat_image_upload() {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) chat_error('The image could not be uploaded. Please try again.');
    if ($file['size'] > 5 * 1024 * 1024) chat_error('Images must be 5 MB or smaller.');
    $info = @getimagesize($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!$info || empty($extensions[$info['mime'] ?? ''])) chat_error('Choose a valid JPG, PNG, GIF, or WebP image.');
    $saved = save_upload($file['tmp_name'], 'chat.' . $extensions[$info['mime']], (int)$file['size']);
    if (!$saved) chat_error('The image could not be saved. Please try again.', 500);
    return $saved;
}
function chat_store_message($conversationId, $sender, $senderId, $senderName, $body, $image = null) {
    q('INSERT INTO live_chat_messages(conversation_id,sender,sender_id,sender_name,body,image) VALUES(?,?,?,?,?,?)', [
        $conversationId, $sender, $senderId, mb_substr($senderName, 0, 120), $body, $image
    ]);
    q('UPDATE live_chat_conversations SET last_message_at=NOW() WHERE id=?', [$conversationId]);
}

switch ($action) {
    case 'visitor_load':
        $conversation = live_chat_visitor_conversation();
        json_out(['ok' => true, 'messages' => live_chat_messages((int)$conversation['id'])]);

    case 'visitor_send':
        $conversation = live_chat_visitor_conversation();
        $body = chat_post_body();
        if ((int)val("SELECT COUNT(*) FROM live_chat_messages WHERE conversation_id=? AND sender='visitor' AND created_at>=DATE_SUB(NOW(), INTERVAL 1 MINUTE)", [(int)$conversation['id']]) >= 8) {
            chat_error('Please wait a moment before sending another message.', 429);
        }
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) chat_error('Enter a valid email address.');
        if (mb_strlen($name) > 120 || mb_strlen($email) > 160) chat_error('Your name or email is too long.');
        $u = user();
        if ($u) { $name = $u['name']; $email = $u['email']; }
        if ($name === '') $name = $conversation['visitor_name'] !== 'Website visitor' ? $conversation['visitor_name'] : 'Website visitor';
        if ($email === '') $email = $conversation['visitor_email'] ?? '';
        $image = chat_image_upload();
        if ($body === '' && !$image) chat_error('Write a message or attach an image.');
        q('UPDATE live_chat_conversations SET user_id=?,visitor_name=?,visitor_email=? WHERE id=?', [
            $u ? (int)$u['id'] : null, $name, $email !== '' ? $email : null, (int)$conversation['id']
        ]);
        chat_store_message((int)$conversation['id'], 'visitor', $u ? (int)$u['id'] : null, $name, $body, $image);
        json_out(['ok' => true, 'messages' => live_chat_messages((int)$conversation['id'])]);

    case 'staff_list':
        chat_staff();
        $conversations = rows("SELECT c.id,c.visitor_name,c.visitor_email,c.last_message_at,
            (SELECT m.body FROM live_chat_messages m WHERE m.conversation_id=c.id ORDER BY m.id DESC LIMIT 1) last_body,
            (SELECT m.image FROM live_chat_messages m WHERE m.conversation_id=c.id ORDER BY m.id DESC LIMIT 1) last_image,
            (SELECT m.sender FROM live_chat_messages m WHERE m.conversation_id=c.id ORDER BY m.id DESC LIMIT 1) last_sender,
            (SELECT COUNT(*) FROM live_chat_messages m WHERE m.conversation_id=c.id AND m.sender='visitor' AND (c.staff_read_at IS NULL OR m.created_at>c.staff_read_at)) unread
            FROM live_chat_conversations c ORDER BY c.last_message_at DESC LIMIT 100");
        foreach ($conversations as &$conversation) {
            $conversation['id'] = (int)$conversation['id'];
            $conversation['unread'] = (int)$conversation['unread'];
            $conversation['last_image_url'] = has_img($conversation['last_image']) ? img($conversation['last_image']) : '';
            $conversation['last_message_at'] = date('d M, h:i A', strtotime($conversation['last_message_at']));
            unset($conversation['last_image']);
        }
        unset($conversation);
        json_out(['ok' => true, 'conversations' => $conversations]);

    case 'staff_messages':
        chat_staff();
        $id = (int)($_POST['conversation_id'] ?? 0);
        if ($id < 1 || !val('SELECT id FROM live_chat_conversations WHERE id=?', [$id])) chat_error('This conversation is no longer available.', 404);
        q('UPDATE live_chat_conversations SET staff_read_at=NOW() WHERE id=?', [$id]);
        json_out(['ok' => true, 'messages' => live_chat_messages($id)]);

    case 'staff_send':
        $staff = chat_staff();
        $id = (int)($_POST['conversation_id'] ?? 0);
        if ($id < 1 || !val('SELECT id FROM live_chat_conversations WHERE id=?', [$id])) chat_error('This conversation is no longer available.', 404);
        $body = chat_post_body();
        if ($body === '') chat_error('Write a reply before sending.');
        chat_store_message($id, 'staff', (int)$staff['id'], $staff['name'], $body);
        audit_activity('live_chat_reply', 'Replied to a live chat conversation', 'live_chat_conversation', $id, $staff);
        json_out(['ok' => true, 'messages' => live_chat_messages($id)]);
}

chat_error('Unknown live chat action.', 400);
