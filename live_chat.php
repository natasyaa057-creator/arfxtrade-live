<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/koneksi.php';
require_once __DIR__ . '/includes/keamanan.php';
require_once __DIR__ . '/includes/fungsi.php';
require_once __DIR__ . '/includes/premium_guard.php';

$is_admin = isset($_SESSION['admin_id']);
$is_member_premium = isset($_SESSION['member_id']) && isPremiumMember();

if (!$is_admin && !$is_member_premium) {
    header('Location: ' . basis_url('login.php'));
    exit;
}

$judul_halaman = 'Live Chat - ARFXTRADE';
$deskripsi_meta = 'Live chat online antara member premium dan admin.';
require_once __DIR__ . '/includes/kepala.php';
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Live <span class="teks-emas">Chat</span></h1>
                <p class="text-secondary mb-0">Semua member dan admin akan chat di satu ruang online.</p>
            </div>
            <a href="<?= aman_html(basis_url($is_admin ? 'dashboard.php' : 'member_dashboard.php')) ?>" class="btn btn-outline-light">
                <i class="fa-solid fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="kartu-gelap p-4">
            <div id="nameSetup" class="mb-3">
                <label for="displayName" class="form-label fw-semibold">Masukkan Nama Sebelum Chat</label>
                <div class="d-flex gap-2">
                    <input type="text" id="displayName" class="form-control" placeholder="Contoh: Abiyan / Admin ARFXTRADE" maxlength="80">
                    <button type="button" id="saveNameBtn" class="btn btn-emas">Simpan Nama</button>
                </div>
                <small class="text-muted">Nama ini akan dipakai sebagai pengirim setiap pesan Anda.</small>
            </div>

            <div id="chatStatus" class="alert alert-info py-2 mb-3">
                Menyambungkan ke live chat...
            </div>

            <div id="chatBox" class="live-chat-box mb-3"></div>

            <form id="chatForm" class="d-flex gap-2">
                <input type="text" id="chatInput" class="form-control" placeholder="Tulis pesan..." maxlength="1000" disabled>
                <button type="submit" class="btn btn-emas" disabled id="sendBtn">
                    <i class="fa-solid fa-paper-plane me-1"></i>Kirim
                </button>
            </form>
        </div>
    </div>
</section>

<style>
.live-chat-box {
    min-height: 360px;
    max-height: 520px;
    overflow-y: auto;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 16px;
    background: rgba(0, 0, 0, 0.2);
}
.live-chat-item {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 10px;
}
.live-chat-meta {
    font-size: 0.85rem;
    color: #bdbdbd;
}
.live-chat-role {
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 999px;
    margin-left: 8px;
}
.role-admin {
    background: rgba(212, 175, 55, 0.18);
    color: #ffd86a;
}
.role-member {
    background: rgba(13, 110, 253, 0.18);
    color: #9ec5fe;
}
</style>

<script>
(() => {
    const API_URL = <?= json_encode(basis_url('api/live_chat.php')) ?>;
    const CSRF_TOKEN = <?= json_encode(token_csrf()) ?>;
    const STORAGE_KEY = 'arfxtrade_live_chat_name';

    const nameInput = document.getElementById('displayName');
    const saveNameBtn = document.getElementById('saveNameBtn');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatBox = document.getElementById('chatBox');
    const chatStatus = document.getElementById('chatStatus');

    let currentName = localStorage.getItem(STORAGE_KEY) || '';
    let lastId = 0;
    let loading = false;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function setStatus(text, type = 'info') {
        chatStatus.className = 'alert py-2 mb-3 alert-' + type;
        chatStatus.textContent = text;
    }

    function applyNameState() {
        const canChat = currentName.length >= 2;
        chatInput.disabled = !canChat;
        sendBtn.disabled = !canChat;
        if (canChat) {
            nameInput.value = currentName;
            setStatus('Tersambung. Chat berjalan realtime (auto refresh).', 'success');
        } else {
            setStatus('Isi nama Anda dulu untuk mulai chat.', 'warning');
        }
    }

    function appendMessage(item) {
        const wrapper = document.createElement('div');
        wrapper.className = 'live-chat-item';

        const roleClass = item.pengirim_role === 'admin' ? 'role-admin' : 'role-member';
        const roleText = item.pengirim_role === 'admin' ? 'Admin' : 'Member';
        const waktu = new Date(item.dibuat_pada.replace(' ', 'T'));
        const waktuTampil = isNaN(waktu.getTime()) ? item.dibuat_pada : waktu.toLocaleString('id-ID');

        wrapper.innerHTML = `
            <div class="live-chat-meta mb-1">
                <strong>${escapeHtml(item.pengirim_nama)}</strong>
                <span class="live-chat-role ${roleClass}">${roleText}</span>
                <span class="ms-2">${escapeHtml(waktuTampil)}</span>
            </div>
            <div>${escapeHtml(item.pesan)}</div>
        `;
        chatBox.appendChild(wrapper);
    }

    async function loadMessages() {
        if (loading) {
            return;
        }
        loading = true;
        try {
            const url = new URL(API_URL, window.location.origin);
            if (lastId > 0) {
                url.searchParams.set('after_id', String(lastId));
            }
            const res = await fetch(url.toString(), { credentials: 'same-origin' });
            const data = await res.json();
            if (!data.ok) {
                throw new Error(data.error || 'Gagal memuat chat');
            }

            const wasNearBottom = chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - 80;
            for (const item of (data.messages || [])) {
                appendMessage(item);
            }
            lastId = Number(data.last_id || lastId);

            if (wasNearBottom || lastId === Number(data.last_id || 0)) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        } catch (err) {
            setStatus((err && err.message) ? err.message : 'Koneksi live chat bermasalah.', 'danger');
        } finally {
            loading = false;
        }
    }

    async function sendMessage(message) {
        const body = new URLSearchParams();
        body.set('token_csrf', CSRF_TOKEN);
        body.set('nama', currentName);
        body.set('pesan', message);

        const res = await fetch(API_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: body.toString()
        });
        const data = await res.json();
        if (!data.ok) {
            throw new Error(data.error || 'Gagal mengirim pesan');
        }
    }

    saveNameBtn.addEventListener('click', () => {
        const value = nameInput.value.trim();
        if (value.length < 2) {
            setStatus('Nama minimal 2 karakter.', 'warning');
            return;
        }
        currentName = value;
        localStorage.setItem(STORAGE_KEY, currentName);
        applyNameState();
    });

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!currentName || currentName.length < 2) {
            setStatus('Masukkan nama sebelum kirim pesan.', 'warning');
            return;
        }
        if (!message) {
            return;
        }
        sendBtn.disabled = true;
        try {
            await sendMessage(message);
            chatInput.value = '';
            await loadMessages();
        } catch (err) {
            setStatus((err && err.message) ? err.message : 'Gagal mengirim pesan.', 'danger');
        } finally {
            sendBtn.disabled = false;
        }
    });

    applyNameState();
    loadMessages();
    setInterval(loadMessages, 2000);
})();
</script>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>

