<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/keamanan.php';
require_once __DIR__ . '/../includes/premium_guard.php';

header('Content-Type: application/json; charset=UTF-8');

function kirim_json(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function pastikan_tabel_live_chat_ada(mysqli $koneksi): void {
    $sql = "CREATE TABLE IF NOT EXISTS live_chat_messages (
        id_chat BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        pengirim_nama VARCHAR(80) NOT NULL,
        pengirim_role ENUM('member','admin') NOT NULL DEFAULT 'member',
        pesan TEXT NOT NULL,
        dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id_chat),
        INDEX idx_dibuat_pada (dibuat_pada)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$koneksi->query($sql)) {
        throw new Exception('Gagal membuat tabel live chat.');
    }
}

try {
    $is_admin = isset($_SESSION['admin_id']);
    $is_member_premium = isset($_SESSION['member_id']) && isPremiumMember();

    if (!$is_admin && !$is_member_premium) {
        kirim_json(['ok' => false, 'error' => 'Akses ditolak.'], 401);
    }

    pastikan_tabel_live_chat_ada($koneksi);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = (string)($_POST['token_csrf'] ?? '');
        if (!verifikasi_csrf($token)) {
            kirim_json(['ok' => false, 'error' => 'Token CSRF tidak valid.'], 419);
        }

        $nama = trim((string)($_POST['nama'] ?? ''));
        $pesan = trim((string)($_POST['pesan'] ?? ''));

        if ($nama === '' || mb_strlen($nama) < 2 || mb_strlen($nama) > 80) {
            kirim_json(['ok' => false, 'error' => 'Nama wajib diisi (2-80 karakter).'], 422);
        }
        if ($pesan === '' || mb_strlen($pesan) > 1000) {
            kirim_json(['ok' => false, 'error' => 'Pesan wajib diisi (maks 1000 karakter).'], 422);
        }

        $role = $is_admin ? 'admin' : 'member';
        $sql = "INSERT INTO live_chat_messages (pengirim_nama, pengirim_role, pesan) VALUES (?, ?, ?)";
        $stmt = jalankan_query_siap($sql, 'sss', [$nama, $role, $pesan]);
        $id_baru = $koneksi->insert_id;
        $stmt->close();

        kirim_json([
            'ok' => true,
            'message' => [
                'id_chat' => (int)$id_baru,
                'pengirim_nama' => $nama,
                'pengirim_role' => $role,
                'pesan' => $pesan,
                'dibuat_pada' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $after_id = max(0, (int)($_GET['after_id'] ?? 0));
        $batas = 80;

        if ($after_id > 0) {
            $sql = "SELECT id_chat, pengirim_nama, pengirim_role, pesan, dibuat_pada
                    FROM live_chat_messages
                    WHERE id_chat > ?
                    ORDER BY id_chat ASC
                    LIMIT ?";
            $stmt = jalankan_query_siap($sql, 'ii', [$after_id, $batas]);
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $sql = "SELECT id_chat, pengirim_nama, pengirim_role, pesan, dibuat_pada
                    FROM (
                        SELECT id_chat, pengirim_nama, pengirim_role, pesan, dibuat_pada
                        FROM live_chat_messages
                        ORDER BY id_chat DESC
                        LIMIT ?
                    ) AS latest
                    ORDER BY id_chat ASC";
            $stmt = jalankan_query_siap($sql, 'i', [$batas]);
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }

        $last_id = $after_id;
        if (!empty($rows)) {
            $last = end($rows);
            $last_id = (int)$last['id_chat'];
        }

        kirim_json([
            'ok' => true,
            'messages' => $rows,
            'last_id' => $last_id
        ]);
    }

    kirim_json(['ok' => false, 'error' => 'Method tidak didukung.'], 405);
} catch (Throwable $e) {
    kirim_json([
        'ok' => false,
        'error' => 'Terjadi kesalahan server saat memproses live chat.'
    ], 500);
}

