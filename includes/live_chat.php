<?php
function ensure_live_chat_tables() {
    static $ready = false;
    if ($ready) return;
    q("CREATE TABLE IF NOT EXISTS live_chat_conversations (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        visitor_key CHAR(64) NOT NULL,
        user_id INT DEFAULT NULL,
        visitor_name VARCHAR(120) NOT NULL DEFAULT 'Website visitor',
        visitor_email VARCHAR(160) DEFAULT NULL,
        staff_read_at DATETIME DEFAULT NULL,
        last_message_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY visitor_key (visitor_key),
        KEY last_message_at (last_message_at),
        KEY user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    q("CREATE TABLE IF NOT EXISTS live_chat_messages (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        conversation_id INT UNSIGNED NOT NULL,
        sender ENUM('visitor','staff','auto') NOT NULL,
        sender_id INT DEFAULT NULL,
        sender_name VARCHAR(120) NOT NULL,
        body TEXT NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY conversation_created (conversation_id, created_at, id),
        KEY sender_created (sender, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ready = true;
}

function live_chat_visitor_conversation() {
    if (empty($_SESSION['live_chat_key'])) $_SESSION['live_chat_key'] = bin2hex(random_bytes(32));
    $key = $_SESSION['live_chat_key'];
    $conversation = row('SELECT * FROM live_chat_conversations WHERE visitor_key=?', [$key]);
    if (!$conversation) {
        $u = user();
        q('INSERT INTO live_chat_conversations(visitor_key,user_id,visitor_name,visitor_email) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)', [
            $key, $u ? (int)$u['id'] : null, $u ? $u['name'] : 'Website visitor', $u ? $u['email'] : null
        ]);
        $conversation = row('SELECT * FROM live_chat_conversations WHERE visitor_key=?', [$key]);
        if (!$conversation) throw new RuntimeException('Could not create the chat conversation.');
    } elseif (user() && empty($conversation['user_id'])) {
        $u = user();
        q('UPDATE live_chat_conversations SET user_id=?, visitor_name=?, visitor_email=? WHERE id=?', [(int)$u['id'], $u['name'], $u['email'], (int)$conversation['id']]);
        $conversation['user_id'] = (int)$u['id'];
    }
    db()->beginTransaction();
    try {
        q('SELECT id FROM live_chat_conversations WHERE id=? FOR UPDATE', [(int)$conversation['id']]);
        if (!val('SELECT id FROM live_chat_messages WHERE conversation_id=? LIMIT 1', [(int)$conversation['id']])) {
            q("INSERT INTO live_chat_messages(conversation_id,sender,sender_name,body) VALUES(?,'auto','Support','Hi! Thanks for contacting us. Send us your question and our team will reply here as soon as possible.')", [(int)$conversation['id']]);
        }
        db()->commit();
    } catch (Throwable $e) {
        db()->rollBack();
        throw $e;
    }
    return $conversation;
}

function live_chat_messages($conversationId) {
    $messages = rows("SELECT * FROM (
        SELECT id,sender,sender_name,body,image,created_at
        FROM live_chat_messages WHERE conversation_id=? ORDER BY id DESC LIMIT 100
    ) recent ORDER BY id ASC", [$conversationId]);
    foreach ($messages as &$message) {
        $message['id'] = (int)$message['id'];
        $message['image_url'] = has_img($message['image']) ? img($message['image']) : '';
        $message['time'] = date('d M, h:i A', strtotime($message['created_at']));
        unset($message['image'], $message['created_at']);
    }
    unset($message);
    return $messages;
}
