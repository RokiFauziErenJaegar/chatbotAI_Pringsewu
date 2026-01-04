<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chatbot AI Dashboard Pringsewu</title>
    @vite(['resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
<div class="container-fluid py-3">
    <div class="row g-3">
        <!-- Sidebar History -->
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">History Chat</h6>
                            <small class="text-muted">Klik untuk membuka</small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" id="newChatBtn">+ New</button>
                    </div>
                </div>

                <div class="card-body p-2" style="max-height: 75vh; overflow-y: auto;">
                    @if(($conversations ?? collect())->count() === 0)
                        <div class="text-muted p-2">Belum ada percakapan.</div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($conversations as $c)
                                <a href="{{ route('chat.open', $c->id) }}"
                                   class="list-group-item list-group-item-action py-2
                                          {{ ($currentConversation->id ?? '') === $c->id ? 'active' : '' }}">
                                    <div class="fw-semibold" style="font-size: 0.95rem;">
                                        {{ $c->title ?: 'Percakapan' }}
                                    </div>
                                    <small class="opacity-75">
                                        {{ $c->updated_at }}
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-white border-0">
                    <div class="d-flex gap-2">
                        <div class="dropdown w-100">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle w-100"
                                    type="button" data-bs-toggle="dropdown">
                                Download Percakapan Ini
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('chat.download', $currentConversation->id) }}?format=txt">
                                        Download TXT
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('chat.download', $currentConversation->id) }}?format=json">
                                        Download JSON
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('chat.download', $currentConversation->id) }}?format=csv">
                                        Download CSV
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Data chatbot hanya dari tabel <b>ikm_koperindag</b>.
                    </small>
                </div>
            </div>
        </div>

        <!-- Main Chat -->
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">Chatbot AI DASHBOARD PRINGSEWU</h5>
                            <small class="text-muted">
                                {{ $currentConversation->title ?: 'Percakapan' }}
                            </small>
                        </div>
                        <span class="badge text-bg-success">Online</span>
                    </div>
                </div>

                <div class="card-body" style="height: 70vh; overflow-y: auto;" id="chatBox">
                    @if(($messages ?? collect())->count() === 0)
                        <div class="alert alert-primary mb-3">
                            Silakan tanya, contoh:
                            <ul class="mb-0">
                                <li>Berapa jumlah IKM per kecamatan?</li>
                                <li>Kecamatan mana tenaga kerjanya paling banyak?</li>
                                <li>Bandingkan jumlah IKM di Kecamatan A vs Kecamatan B</li>
                                <li>Top 10 jenis produk terbanyak</li>
                            </ul>
                        </div>
                    @else
                        @foreach($messages as $m)
                            <div class="d-flex mb-2 {{ $m->role === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="p-2 rounded-3 {{ $m->role === 'user' ? 'bg-primary text-white' : 'bg-white border' }}"
                                     style="max-width: 80%; white-space: pre-wrap;">
                                    {{ $m->content }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="card-footer bg-white border-0">
                    <form id="chatForm" class="d-flex gap-2">
                        <input type="text" class="form-control" id="message" placeholder="Ketik pertanyaan..." autocomplete="off" maxlength="1000">
                        <button class="btn btn-primary" type="submit" id="sendBtn">Kirim</button>
                    </form>
                    <small class="text-muted d-block mt-2">
                        History tersimpan otomatis (seperti ChatGPT). Bisa download di sidebar.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('message');
    const sendBtn = document.getElementById('sendBtn');
    const newChatBtn = document.getElementById('newChatBtn');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function appendBubble(text, who='bot') {
        const wrap = document.createElement('div');
        wrap.className = 'd-flex mb-2 ' + (who === 'user' ? 'justify-content-end' : 'justify-content-start');

        const bubble = document.createElement('div');
        bubble.className = 'p-2 rounded-3 ' + (who === 'user' ? 'bg-primary text-white' : 'bg-white border');
        bubble.style.maxWidth = '80%';
        bubble.style.whiteSpace = 'pre-wrap';
        bubble.textContent = text;

        wrap.appendChild(bubble);
        chatBox.appendChild(wrap);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function setLoading(isLoading) {
        sendBtn.disabled = isLoading;
        messageInput.disabled = isLoading;
        sendBtn.textContent = isLoading ? '...' : 'Kirim';
    }

    // auto scroll to bottom when open conversation
    chatBox.scrollTop = chatBox.scrollHeight;

    newChatBtn?.addEventListener('click', async () => {
        try {
            const res = await fetch('/conversation/new', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (res.ok && data.ok && data.conversation_id) {
                window.location.href = '/conversation/' + data.conversation_id;
            } else {
                alert('Gagal membuat chat baru.');
            }
        } catch (e) {
            alert('Gagal membuat chat baru.');
        }
    });

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = messageInput.value.trim();
        if (!msg) return;

        appendBubble(msg, 'user');
        messageInput.value = '';
        setLoading(true);

        try {
            const res = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: msg })
            });

            const data = await res.json();
            if (!res.ok || !data.ok) {
                appendBubble(data.reply || 'Terjadi error.', 'bot');
            } else {
                appendBubble(data.reply, 'bot');
            }
        } catch (err) {
            appendBubble('Gagal menghubungi server. Coba lagi.', 'bot');
        } finally {
            setLoading(false);
        }
    });
})();
</script>
</body>
</html>
