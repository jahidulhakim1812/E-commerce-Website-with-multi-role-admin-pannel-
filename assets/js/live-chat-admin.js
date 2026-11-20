(function () {
  var root = document.querySelector('[data-live-chat-admin]');
  if (!root) return;
  var csrf = root.dataset.csrf;
  var conversations = root.querySelector('[data-chat-conversations]');
  var messages = root.querySelector('[data-chat-messages]');
  var header = root.querySelector('[data-chat-thread-head]');
  var empty = root.querySelector('[data-chat-empty]');
  var active = root.querySelector('[data-chat-active]');
  var reply = root.querySelector('[data-chat-reply]');
  var error = root.querySelector('[data-chat-error]');
  var selected = 0;
  var conversationData = {};
  var busy = false;

  function escapeHtml(value) {
    return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char];
    });
  }
  function request(action, data) {
    var body = new FormData();
    body.append('action', action);
    body.append('_csrf', csrf);
    Object.keys(data || {}).forEach(function (key) { body.append(key, data[key]); });
    return fetch('../chat_api.php', {method:'POST', body:body, credentials:'same-origin'})
      .then(function (response) {
        return response.json().then(function (result) {
          if (!response.ok || !result.ok) throw new Error(result.error || 'Could not load live chat.');
          return result;
        });
      });
  }
  function renderMessages(items) {
    var atBottom = messages.scrollHeight - messages.scrollTop - messages.clientHeight < 80;
    var oldScroll = messages.scrollTop;
    messages.innerHTML = items.map(function (message) {
      var image = message.image_url ? '<a href="' + escapeHtml(message.image_url) + '" target="_blank" rel="noopener"><img src="' + escapeHtml(message.image_url) + '" alt="Image attachment"></a>' : '';
      var text = message.body ? '<p>' + escapeHtml(message.body).replace(/\n/g, '<br>') + '</p>' : '';
      return '<article class="lc-message ' + escapeHtml(message.sender) + '"><small>' + escapeHtml(message.sender_name) + '</small>' + text + image + '<time>' + escapeHtml(message.time) + '</time></article>';
    }).join('');
    messages.scrollTop = atBottom ? messages.scrollHeight : oldScroll;
  }
  function loadThread(id) {
    selected = id;
    empty.hidden = true;
    active.hidden = false;
    var conversation = conversationData[id];
    header.innerHTML = conversation ? '<b>' + escapeHtml(conversation.visitor_name) + '</b><small>' + escapeHtml(conversation.visitor_email || 'Website visitor') + '</small>' : '';
    request('staff_messages', {conversation_id:id}).then(function (result) {
      if (selected !== id) return;
      renderMessages(result.messages);
      refreshList();
    }).catch(function (err) { error.textContent = err.message; });
  }
  function refreshList() {
    return request('staff_list').then(function (result) {
      conversationData = {};
      result.conversations.forEach(function (item) { conversationData[item.id] = item; });
      conversations.innerHTML = result.conversations.length ? result.conversations.map(function (item) {
        var preview = item.last_body || (item.last_image_url ? 'Image attachment' : 'New conversation');
        return '<button type="button" class="lc-conversation ' + (item.id === selected ? 'selected' : '') + '" data-conversation="' + item.id + '">' +
          '<span class="lc-conversation-top"><b>' + escapeHtml(item.visitor_name) + '</b><time>' + escapeHtml(item.last_message_at) + '</time></span>' +
          '<small>' + escapeHtml(item.visitor_email || 'Visitor') + '</small><span class="lc-preview">' + (item.last_sender === 'staff' ? 'You: ' : '') + escapeHtml(preview) + '</span>' +
          (item.unread ? '<i class="lc-unread">' + item.unread + '</i>' : '') + '</button>';
      }).join('') : '<p class="lc-empty">No conversations yet. New visitor chats will appear here.</p>';
    }).catch(function (err) {
      conversations.innerHTML = '<p class="lc-error">' + escapeHtml(err.message) + '</p>';
    });
  }
  conversations.addEventListener('click', function (event) {
    var button = event.target.closest('[data-conversation]');
    if (button) loadThread(+button.dataset.conversation);
  });
  root.querySelector('[data-chat-refresh]').addEventListener('click', function () {
    refreshList().then(function () { if (selected) loadThread(selected); });
  });
  reply.addEventListener('submit', function (event) {
    event.preventDefault();
    if (busy || !selected) return;
    busy = true;
    error.textContent = '';
    request('staff_send', {conversation_id:selected, body:reply.elements.body.value}).then(function (result) {
      renderMessages(result.messages);
      reply.elements.body.value = '';
      return refreshList();
    }).catch(function (err) { error.textContent = err.message; })
      .finally(function () { busy = false; });
  });
  refreshList().then(function () {
    var first = conversations.querySelector('[data-conversation]');
    if (first) loadThread(+first.dataset.conversation);
  });
  setInterval(function () {
    refreshList();
    var activeId = selected;
    if (activeId && !busy) request('staff_messages', {conversation_id:activeId}).then(function (result) {
      if (selected !== activeId) return;
      renderMessages(result.messages);
    }).catch(function (err) { error.textContent = err.message; });
  }, 3500);
})();
