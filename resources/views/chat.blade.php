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
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">Chatbot AI DASHBOARD PRINGSEWU</h5>
                            <small class="text-muted">Analisis data IKM (tabel: ikm_koperindag)</small>
                        </div>
                        <span class="badge text-bg-success">Online</span>
                    </div>
                </div>
                <div class="card-body" style="height: 60vh; overflow-y: auto;" id="chatBox">
                    <div class="alert alert-primary mb-3">
                        Silakan tanya, contoh:
                        <ul class="mb-0">
                            <li>Berapa jumlah IKM per kecamatan?</li>
                            <li>Kecamatan mana tenaga kerjanya paling banyak?</li>
                            <li>Bandingkan jumlah IKM di Kecamatan A vs Kecamatan B</li>
                            <li>Top 10 jenis produk terbanyak</li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <form id="chatForm" class="d-flex gap-2">
                        <input type="text" class="form-control" id="message" placeholder="Ketik pertanyaan..." autocomplete="off" maxlength="1000">
                        <button class="btn btn-primary" type="submit" id="sendBtn">Kirim</button>
                    </form>
                    <small class="text-muted d-block mt-2">
                        Catatan: chatbot hanya membaca data tabel <b>ikm_koperindag</b>.
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
