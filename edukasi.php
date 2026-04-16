<?php
declare(strict_types=1);
$judul_halaman = 'Edukasi Trader';
$deskripsi_meta = 'Modul edukasi trading ARFXTRADE - dasar trading, money management, psikologi trading, dan analisis teknikal untuk trader pemula hingga profesional.';
require_once __DIR__ . '/includes/kepala.php';

// Parameter pencarian dan filter
$cari = trim($_GET['cari'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');
$halaman = max(1, (int)($_GET['halaman'] ?? 1));
$limit = 6;
$offset = ($halaman - 1) * $limit;

// Query edukasi dengan pencarian
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($cari)) {
    $where_conditions[] = "(judul LIKE ? OR isi_materi LIKE ?)";
    $params[] = "%$cari%";
    $params[] = "%$cari%";
    $param_types .= 'ss';
}

if (!empty($kategori)) {
    $where_conditions[] = "kategori = ?";
    $params[] = $kategori;
    $param_types .= 's';
}

$where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Ambil data edukasi
$edukasi_data = [];
$total_data = 0;

try {
    // Hitung total data
    $sql_count = "SELECT COUNT(*) as total FROM edukasi $where_sql";
    if (!empty($params)) {
        $stmt_count = jalankan_query_siap($sql_count, $param_types, $params);
        $total_data = $stmt_count->get_result()->fetch_assoc()['total'];
        $stmt_count->close();
    } else {
        $stmt_count = jalankan_query_siap($sql_count, '', []);
        $total_data = $stmt_count->get_result()->fetch_assoc()['total'];
        $stmt_count->close();
    }
    
    // Ambil data dengan pagination
    $sql = "SELECT * FROM edukasi $where_sql ORDER BY id_materi DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $param_types .= 'ii';
    
    $stmt = jalankan_query_siap($sql, $param_types, $params);
    $hasil = $stmt->get_result();
    $edukasi_data = $hasil->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    // Jika tabel belum ada, gunakan data dummy
    $edukasi_data = [
        [
            'id_materi' => 1,
            'judul' => 'Dasar-Dasar Trading Forex',
            'kategori' => 'Dasar Trading',
            'isi_materi' => 'Panduan lengkap untuk pemula yang ingin memulai trading forex.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ],
        [
            'id_materi' => 2,
            'judul' => 'Money Management dalam Trading',
            'kategori' => 'Money Management',
            'isi_materi' => 'Strategi mengelola modal dan risiko dalam trading.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ],
        [
            'id_materi' => 3,
            'judul' => 'Psikologi Trading',
            'kategori' => 'Psikologi Trading',
            'isi_materi' => 'Mengendalikan emosi dan mindset dalam trading.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ],
        [
            'id_materi' => 4,
            'judul' => 'Analisis Teknikal Dasar',
            'kategori' => 'Analisis Teknikal',
            'isi_materi' => 'Mengenal chart pattern, support resistance, dan indikator teknikal.',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ]
    ];
    $total_data = count($edukasi_data);
}

// Ambil daftar kategori unik
$kategori_unik = [];
try {
    $sql_kategori = "SELECT DISTINCT kategori FROM edukasi ORDER BY kategori";
    $stmt_kategori = jalankan_query_siap($sql_kategori, '', []);
    $hasil_kategori = $stmt_kategori->get_result();
    $kategori_unik = array_column($hasil_kategori->fetch_all(MYSQLI_ASSOC), 'kategori');
    $stmt_kategori->close();
} catch (Exception $e) {
    $kategori_unik = ['Dasar Trading', 'Money Management', 'Psikologi Trading', 'Analisis Teknikal', 'Strategi Trading'];
}

$total_halaman = ceil($total_data / $limit);
$is_admin = isset($_SESSION['admin_id']);
$is_member_premium = isset($_SESSION['member_id']) && isPremiumMember();
$boleh_live_chat = $is_admin || $is_member_premium;
?>

<section class="py-5 mt-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h1 class="display-5 fw-bold mb-3">Edukasi <span class="teks-emas">Trader</span></h1>
            <p class="lead text-secondary">Tingkatkan kemampuan trading Anda dengan modul pembelajaran yang komprehensif</p>
        </div>
        
        <!-- Form Pencarian dan Filter -->
        <div class="row mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="col-12">
                <div class="kartu-gelap p-4">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-4">
                            <label for="cari" class="form-label">Cari Materi</label>
                            <input type="text" class="form-control" id="cari" name="cari" 
                                   value="<?= aman_html($cari) ?>" placeholder="Kata kunci...">
                        </div>
                        <div class="col-md-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($kategori_unik as $k): ?>
                                    <option value="<?= aman_html($k) ?>" <?= $kategori === $k ? 'selected' : '' ?>>
                                        <?= aman_html($k) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-emas me-2">
                                <i class="fa-solid fa-search me-1"></i>Cari
                            </button>
                            <a href="<?= aman_html(basis_url('edukasi.php')) ?>" class="btn btn-outline-light">
                                <i class="fa-solid fa-refresh me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Special Section: Analisis Trading -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-dark text-white p-4 rounded" style="border: 2px solid #d4af37;">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-3" style="color: #d4af37;">
                            📊 Analisis Trading Lengkap
                        </h2>
                        <p class="text-light">Modul pembelajaran komprehensif untuk analisis fundamental dan teknikal</p>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-3 border rounded bg-dark" style="border-color: #d4af37 !important;">
                                <div class="mb-3" style="color: #d4af37; font-size: 2rem;">📈</div>
                                <h6 class="fw-bold mb-2 text-white">Analisis Fundamental</h6>
                                <p class="text-light small mb-3">Indikator ekonomi, bank sentral, dan faktor fundamental</p>
                                <a href="<?= aman_html(basis_url('edukasi_analisis.php#fundamental')) ?>" class="btn btn-warning btn-sm">
                                    Pelajari
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-3 border rounded bg-dark" style="border-color: #d4af37 !important;">
                                <div class="mb-3" style="color: #d4af37; font-size: 2rem;">📊</div>
                                <h6 class="fw-bold mb-2 text-white">Analisis Teknikal</h6>
                                <p class="text-light small mb-3">Indikator teknikal, chart patterns, support/resistance</p>
                                <a href="<?= aman_html(basis_url('edukasi_analisis.php#teknikal')) ?>" class="btn btn-warning btn-sm">
                                    Pelajari
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-3 border rounded bg-dark" style="border-color: #d4af37 !important;">
                                <div class="mb-3" style="color: #d4af37; font-size: 2rem;">🧮</div>
                                <h6 class="fw-bold mb-2 text-white">Tools & Kalkulator</h6>
                                <p class="text-light small mb-3">Position size, risk/reward, pivot points, Fibonacci</p>
                                <a href="<?= aman_html(basis_url('edukasi_analisis.php#tools')) ?>" class="btn btn-warning btn-sm">
                                    Gunakan
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6">
                            <div class="text-center p-3 border rounded bg-dark" style="border-color: #d4af37 !important;">
                                <div class="mb-3" style="color: #d4af37; font-size: 2rem;">❓</div>
                                <h6 class="fw-bold mb-2 text-white">Quiz & Assessment</h6>
                                <p class="text-light small mb-3">Test pengetahuan dengan quiz interaktif</p>
                                <a href="<?= aman_html(basis_url('edukasi_analisis.php#quiz')) ?>" class="btn btn-warning btn-sm">
                                    Mulai Quiz
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?= aman_html(basis_url('edukasi_analisis.php')) ?>" class="btn btn-warning btn-lg px-5">
                            🎓 Akses Modul Analisis Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Chat Member -->
        <div class="row mb-5" data-aos="fade-up">
            <div class="col-12">
                <div class="kartu-gelap p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold mb-1">
                                <i class="fa-solid fa-comments teks-emas me-2"></i>Live Chat Antar Member
                            </h4>
                            <p class="text-secondary mb-0">Chat online realtime di ruang bersama member premium dan admin.</p>
                        </div>
                        <span class="badge bg-emas text-dark">Realtime Online</span>
                    </div>

                    <?php if ($boleh_live_chat): ?>
                        <div id="eduNameSetup" class="mb-3">
                            <label for="eduDisplayName" class="form-label fw-semibold">Masukkan Nama Sebelum Chat</label>
                            <div class="d-flex gap-2">
                                <input type="text" id="eduDisplayName" class="form-control" placeholder="Contoh: Nama Anda" maxlength="80">
                                <button type="button" id="eduSaveNameBtn" class="btn btn-emas">Simpan</button>
                            </div>
                            <small class="text-muted">Nama wajib diisi agar bisa kirim pesan.</small>
                        </div>

                        <div id="eduChatStatus" class="alert alert-info py-2 mb-3">Menyambungkan live chat...</div>
                        <div id="eduChatBox" class="edukasi-live-chat-box mb-3"></div>

                        <form id="eduChatForm" class="d-flex gap-2">
                            <input type="text" id="eduChatInput" class="form-control" placeholder="Tulis pesan..." maxlength="1000" disabled>
                            <button type="submit" class="btn btn-emas" id="eduSendBtn" disabled>
                                <i class="fa-solid fa-paper-plane me-1"></i>Kirim
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            Fitur live chat antar member tersedia untuk member premium/admin yang sudah login.
                            <a class="alert-link" href="<?= aman_html(basis_url('login.php')) ?>">Login di sini</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Grid Edukasi -->
        <div class="row g-4" id="edukasiGrid">
            <?php foreach ($edukasi_data as $item): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                    <div class="kartu-gelap h-100">
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <?php if (!empty($item['video_url'])): ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-dark">
                                    <i class="fa-brands fa-youtube teks-emas fa-3x"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-dark">
                                    <i class="fa-solid fa-book teks-emas fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-emas text-dark"><?= aman_html($item['kategori']) ?></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-semibold mb-2"><?= aman_html($item['judul']) ?></h5>
                            <p class="text-secondary small mb-3">
                                <?= aman_html(substr(strip_tags($item['isi_materi']), 0, 100)) ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fa-solid fa-tag me-1"></i>
                                    <?= aman_html($item['kategori']) ?>
                                </small>
                                <button class="btn btn-emas btn-sm" data-bs-toggle="modal" 
                                        data-bs-target="#modalEdukasi" 
                                        data-judul="<?= aman_html($item['judul']) ?>"
                                        data-kategori="<?= aman_html($item['kategori']) ?>"
                                        data-isi="<?= aman_html($item['isi_materi']) ?>"
                                        data-video="<?= aman_html($item['video_url']) ?>">
                                    Pelajari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($edukasi_data)): ?>
            <div class="text-center py-5" data-aos="fade-up">
                <div class="kartu-gelap p-5">
                    <i class="fa-solid fa-graduation-cap teks-emas fa-3x mb-3"></i>
                    <h4 class="fw-semibold mb-2">Materi Edukasi Belum Tersedia</h4>
                    <p class="text-secondary">Materi pembelajaran akan segera ditambahkan.</p>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Pagination -->
        <?php if ($total_halaman > 1): ?>
            <nav aria-label="Pagination edukasi" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($halaman > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['halaman' => $halaman - 1])) ?>">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $halaman - 2); $i <= min($total_halaman, $halaman + 2); $i++): ?>
                        <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['halaman' => $i])) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($halaman < $total_halaman): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['halaman' => $halaman + 1])) ?>">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>

<!-- Modal Edukasi -->
<div class="modal fade" id="modalEdukasi" tabindex="-1" aria-labelledby="modalEdukasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content kartu-gelap">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="modalEdukasiLabel">Materi Edukasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <span class="badge bg-emas text-dark" id="modalKategoriEdukasi"></span>
                </div>
                <h4 id="modalJudulEdukasi" class="fw-bold mb-3"></h4>
                <div id="modalIsiEdukasi" class="mb-4"></div>
                <div id="modalVideoEdukasi" class="text-center"></div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.edukasi-live-chat-box {
    min-height: 280px;
    max-height: 440px;
    overflow-y: auto;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 14px;
    background: rgba(0, 0, 0, 0.2);
}
.edukasi-live-chat-item {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 10px;
}
.edukasi-live-chat-meta {
    font-size: 0.85rem;
    color: #bdbdbd;
}
.edukasi-live-chat-role {
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 999px;
    margin-left: 8px;
}
.edukasi-role-admin {
    background: rgba(212, 175, 55, 0.18);
    color: #ffd86a;
}
.edukasi-role-member {
    background: rgba(13, 110, 253, 0.18);
    color: #9ec5fe;
}
</style>

<script>
// Modal edukasi
document.addEventListener('DOMContentLoaded', function() {
    const modalEdukasi = document.getElementById('modalEdukasi');
    modalEdukasi.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const judul = button.getAttribute('data-judul');
        const kategori = button.getAttribute('data-kategori');
        const isi = button.getAttribute('data-isi');
        const video = button.getAttribute('data-video');
        
        document.getElementById('modalEdukasiLabel').textContent = judul;
        document.getElementById('modalJudulEdukasi').textContent = judul;
        document.getElementById('modalKategoriEdukasi').textContent = kategori;
        document.getElementById('modalIsiEdukasi').textContent = isi;
        
        const videoContainer = document.getElementById('modalVideoEdukasi');
        if (video) {
            videoContainer.innerHTML = `
                <div class="ratio ratio-16x9">
                    <iframe src="${video.replace('watch?v=', 'embed/')}" 
                            title="${judul}" allowfullscreen></iframe>
                </div>
            `;
        } else {
            videoContainer.innerHTML = '';
        }
    });
});
</script>

<?php if ($boleh_live_chat): ?>
<script>
(() => {
    const API_URL = <?= json_encode(basis_url('api/live_chat.php')) ?>;
    const CSRF_TOKEN = <?= json_encode(token_csrf()) ?>;
    const STORAGE_KEY = 'arfxtrade_live_chat_name';

    const nameInput = document.getElementById('eduDisplayName');
    const saveNameBtn = document.getElementById('eduSaveNameBtn');
    const chatForm = document.getElementById('eduChatForm');
    const chatInput = document.getElementById('eduChatInput');
    const sendBtn = document.getElementById('eduSendBtn');
    const chatBox = document.getElementById('eduChatBox');
    const chatStatus = document.getElementById('eduChatStatus');

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
            setStatus('Tersambung. Chat realtime aktif.', 'success');
        } else {
            setStatus('Isi nama Anda dulu untuk mulai chat.', 'warning');
        }
    }

    function appendMessage(item) {
        const wrapper = document.createElement('div');
        wrapper.className = 'edukasi-live-chat-item';

        const roleClass = item.pengirim_role === 'admin' ? 'edukasi-role-admin' : 'edukasi-role-member';
        const roleText = item.pengirim_role === 'admin' ? 'Admin' : 'Member';
        const waktu = new Date(item.dibuat_pada.replace(' ', 'T'));
        const waktuTampil = isNaN(waktu.getTime()) ? item.dibuat_pada : waktu.toLocaleString('id-ID');

        wrapper.innerHTML = `
            <div class="edukasi-live-chat-meta mb-1">
                <strong>${escapeHtml(item.pengirim_nama)}</strong>
                <span class="edukasi-live-chat-role ${roleClass}">${roleText}</span>
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
<?php endif; ?>

<?php require_once __DIR__ . '/includes/kaki.php'; ?>

