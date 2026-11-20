(function () {
  var root = document.querySelector('[data-chat-panel]');
  if (!root) return;
  var toggle = document.querySelector('[data-chat-toggle]');
  var close = document.querySelector('[data-chat-close]');
  var list = root.querySelector('[data-chat-messages]');
  var form = root.querySelector('[data-chat-form]');
  var error = root.querySelector('[data-chat-error]');
  var fileInput = root.querySelector('[data-chat-image]');
  var fileLabel = root.querySelector('[data-chat-file]');
  var csrf = form.elements._csrf.value;
  var opened = false;
  var timer = null;
  var busy = false;

  function escapeHtml(value) {
    return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char];
    });
  }
  function request(action, data) {
    var body = data instanceof FormData ? data : new FormData();
    if (!(data instanceof FormData)) Object.keys(data || {}).forEach(function (key) { body.append(key, data[key]); });
    body.append('action', action);
    if (!body.has('_csrf')) body.append('_csrf', csrf);
    return fetch((window.BASE || '') + '/chat_api.php', {method:'POST', body:body, credentials:'same-origin'})
      .then(function (response) {
        return response.json().then(function (result) {
          if (!response.ok || !result.ok) throw new Error(result.error || 'Could not load live chat.');
          return result;
        });
      });
  }
  function render(messages) {
    var atBottom = list.scrollHeight - list.scrollTop - list.clientHeight < 80;
    var oldScroll = list.scrollTop;
    list.innerHTML = messages.map(function (message) {
      var mine = message.sender === 'visitor';
      var image = message.image_url ? '<a href="' + escapeHtml(message.image_url) + '" target="_blank" rel="noopener"><img src="' + escapeHtml(message.image_url) + '" alt="Image attachment"></a>' : '';
      var text = message.body ? '<p>' + escapeHtml(message.body).replace(/\n/g, '<br>') + '</p>' : '';
      return '<article class="live-chat-message ' + (mine ? 'mine' : '') + '"><small>' + escapeHtml(message.sender_name) + '</small>' + text + image + '<time>' + escapeHtml(message.time) + '</time></article>';
    }).join('') || '<p class="live-chat-loading">Send us a message to get started.</p>';
    list.scrollTop = atBottom ? list.scrollHeight : oldScroll;
  }
  function load() {
    if (!opened) return Promise.resolve();
    return request('visitor_load').then(function (result) {
      render(result.messages);
      error.textContent = '';
    }).catch(function (err) { error.textContent = err.message; });
  }
  function startPolling() {
    clearInterval(timer);
    timer = setInterval(load, 3500);
  }
  function show() {
    root.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    opened = true;
    load();
    startPolling();
    form.elements.body.focus();
  }
  function hide() {
    root.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
    opened = false;
    clearInterval(timer);
  }
  toggle.addEventListener('click', function () { if (opened) hide(); else show(); });
  close.addEventListener('click', hide);
  fileInput.addEventListener('change', function () {
    fileLabel.textContent = fileInput.files.length ? fileInput.files[0].name : '';
  });
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    if (busy) return;
    busy = true;
    error.textContent = '';
    var data = new FormData(form);
    request('visitor_send', data).then(function (result) {
      render(result.messages);
      form.elements.body.value = '';
      fileInput.value = '';
      fileLabel.textContent = '';
    }).catch(function (err) {
      error.textContent = err.message;
    }).finally(function () { busy = false; });
  });
  document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && opened) hide(); });
})();
