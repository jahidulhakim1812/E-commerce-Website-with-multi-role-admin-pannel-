<div class="live-chat" id="liveChat">
  <button class="live-chat-launch" type="button" data-chat-toggle aria-label="Open live chat" aria-expanded="false">
    <span aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-3 2 1-4.1A7.5 7.5 0 1 1 20 11.5Z"/></svg></span><span class="live-chat-launch-label">Chat with us</span>
  </button>
  <section class="live-chat-panel" data-chat-panel aria-label="Live chat" hidden>
    <header class="live-chat-head">
      <div><b>We're here to help</b><small><i></i> Usually replies shortly</small></div>
      <button type="button" data-chat-close aria-label="Close chat">×</button>
    </header>
    <div class="live-chat-messages" data-chat-messages aria-live="polite">
      <p class="live-chat-loading">Connecting to support…</p>
    </div>
    <form class="live-chat-form" data-chat-form enctype="multipart/form-data">
      <?php if (!$u): ?>
      <div class="live-chat-identity">
        <input name="name" maxlength="120" placeholder="Your name" aria-label="Your name">
        <input name="email" type="email" maxlength="160" placeholder="Email (optional)" aria-label="Email (optional)">
      </div>
      <?php endif; ?>
      <div class="live-chat-compose">
        <label class="live-chat-attach" title="Attach an image">＋<input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" data-chat-image></label>
        <textarea name="body" rows="1" maxlength="2000" placeholder="Type your message…" aria-label="Type your message"></textarea>
        <button type="submit" aria-label="Send message">➤</button>
      </div>
      <small class="live-chat-file" data-chat-file></small>
      <p class="live-chat-error" data-chat-error role="alert"></p>
      <input type="hidden" name="_csrf" value="<?= e(csrf()) ?>">
    </form>
  </section>
</div>
