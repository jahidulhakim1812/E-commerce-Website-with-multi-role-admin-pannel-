<?php
require '_layout.php';
require_once __DIR__ . '/../includes/live_chat.php';
ensure_live_chat_tables();
admin_start('Live Chat', 'customers');
?>
<link rel="stylesheet" href="../assets/css/live-chat-admin.css">
<div class="live-chat-admin" data-live-chat-admin data-csrf="<?= e(csrf()) ?>">
  <aside class="live-chat-inbox">
    <div class="live-chat-inbox-head"><b>Conversations</b><button type="button" data-chat-refresh title="Refresh">↻</button></div>
    <div class="live-chat-conversations" data-chat-conversations><p class="lc-empty">Loading conversations…</p></div>
  </aside>
  <section class="live-chat-thread">
    <div class="live-chat-thread-empty" data-chat-empty>Select a conversation to view messages.</div>
    <div class="live-chat-thread-active" data-chat-active hidden>
      <header class="live-chat-thread-head" data-chat-thread-head></header>
      <div class="live-chat-thread-messages" data-chat-messages aria-live="polite"></div>
      <form class="live-chat-reply" data-chat-reply>
        <textarea name="body" rows="2" maxlength="2000" placeholder="Write a reply…" required></textarea>
        <button class="abtn" type="submit">Send reply</button>
        <p data-chat-error role="alert"></p>
      </form>
    </div>
  </section>
</div>
<script src="../assets/js/live-chat-admin.js"></script>
<?php admin_end(); ?>
