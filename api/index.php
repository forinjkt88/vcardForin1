<?php
$path = __DIR__."/api/data.json";
$data = json_decode(file_get_contents(__DIR__ . '/data.json'), true);
$slug = $_GET['id'] ?? 'willy';

if (!isset($data[$slug])) {
    die("Kontak tidak ditemukan.");
}

$user = $data[$slug];

// Format nomor WhatsApp (menghapus karakter non-angka dan mengubah awal 08 menjadi 628)
$wa_number = preg_replace('/[^0-9]/', '', $user['mobile']);
if (substr($wa_number, 0, 1) === '0') {
    $wa_number = '62' . substr($wa_number, 1);
}

// Cek apakah ada banner custom, jika tidak ada pakai warna gradasi default
$banner_path = $user['banner'] ?? '';
$banner_style = !empty($banner_path) 
    ? "background: url('" . htmlspecialchars($banner_path) . "') center/cover no-repeat;" 
    : "background: linear-gradient(135deg, #004488, #d9232a);";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>vCard - <?= htmlspecialchars($user['name']) ?></title>
    <!-- FontAwesome 6 untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background: #f4f4f9; 
            display: flex; 
            justify-content: center; 
            padding: 20px; 
            margin: 0; 
        }
        
        .card { 
            background: #fff; 
            width: 100%; 
            max-width: 380px; 
            border-radius: 20px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            overflow: hidden; 
            position: relative; 
        }
        
        /* Profile & Header Banner */
        .profile { 
            position: relative; 
            padding: 25px 20px 15px 20px; 
            text-align: center; 
        }
        
        .cover-banner {
            height: 120px;
            border-radius: 20px 20px 0 0;
            margin: -25px -20px 0 -20px;
        }

        .logo { 
            width: 90px; 
            height: 90px; 
            border-radius: 50%; 
            object-fit: contain; 
            background: #fff;
            padding: 5px;
            border: 3px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            margin-top: -45px;
            position: relative;
            z-index: 2;
        }

        .name { 
            font-size: 22px; 
            font-weight: bold; 
            color: #000; 
            margin-top: 10px; 
            margin-bottom: 4px; 
        }
        
        .title { 
            color: #666; 
            font-size: 14px; 
            margin-bottom: 25px; 
        }
        
        /* 3 Tombol Aksi Utama (WhatsApp, WeChat, Email) */
        .actions { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 0 15px 15px; 
            border-bottom: 1px solid #eee; 
        }
        
        .action-btn { 
            text-decoration: none; 
            color: #000; 
            font-size: 11px; 
            font-weight: 700; 
            text-align: center; 
            flex: 1; 
            letter-spacing: 0.5px; 
            background: none; 
            border: none; 
            cursor: pointer; 
        }
        
        .action-btn i { 
            font-size: 22px; 
            margin-bottom: 6px; 
            display: block; 
            color: #000; 
        }

        /* Detail Information Section */
        .details { 
            text-align: left; 
            padding: 20px; 
            background: #fafafa; 
        }
        
        .detail-row { 
            display: flex; 
            align-items: flex-start; 
            margin-bottom: 18px; 
        }
        
        .detail-icon { 
            width: 35px; 
            color: #777; 
            font-size: 18px; 
            text-align: center; 
            padding-top: 2px; 
        }
        
        .detail-info { 
            flex: 1; 
            border-bottom: 1px solid #e0e0e0; 
            padding-bottom: 10px; 
        }
        
        .detail-val { 
            font-size: 15px; 
            font-weight: 500; 
            color: #222; 
            word-break: break-all; 
        }
        
        .detail-label { 
            font-size: 12px; 
            color: #888; 
            margin-top: 2px; 
        }

        /* Social Media Icons */
        .social-title { 
            font-size: 13px; 
            color: #888; 
            margin: 20px 0 12px 35px; 
        }
        
        .social-group { 
            display: flex; 
            gap: 12px; 
            margin-left: 35px; 
            margin-bottom: 20px; 
        }
        
        .social-btn { 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: #fff; 
            text-decoration: none; 
            font-size: 18px; 
        }
        
        .bg-youtube { background-color: #FF0000; }
        .bg-instagram { background-color: #E1306C; }
        .bg-website { background-color: #5A6268; }

        /* Floating Action Button (Simpan Kontak) */
        .fab-save { 
            position: fixed; 
            bottom: 30px; 
            right: 30px; 
            background: #000; 
            color: #fff; 
            width: 52px; 
            height: 52px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-decoration: none; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); 
            font-size: 20px; 
            z-index: 99;
        }

        /* Modal Pop-up WeChat */
        .modal-overlay { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(0,0,0,0.6); 
            justify-content: center; 
            align-items: center; 
            z-index: 1000; 
        }
        
        .modal-box { 
            background: #fff; 
            padding: 20px; 
            border-radius: 16px; 
            width: 90%; 
            max-width: 320px; 
            text-align: center; 
            position: relative; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .modal-box img { 
            width: 100%; 
            max-width: 220px; 
            height: auto; 
            border-radius: 8px; 
            margin: 10px 0; 
        }
        
        .close-btn { 
            position: absolute; 
            top: 10px; 
            right: 15px; 
            font-size: 24px; 
            cursor: pointer; 
            color: #888; 
        }
    </style>
</head>
<body>

<div class="card">
    <div class="profile">
        <!-- Banner Header di Belakang Logo -->
        <div class="cover-banner" style="<?= $banner_style ?>"></div>

        <!-- Profile Logo / Photo -->
        <img src="<?= htmlspecialchars($user['logo']) ?>" alt="Logo" class="logo">
        
        <div class="name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="title"><?= htmlspecialchars($user['title']) ?></div>
        
        <!-- 3 Tombol Aksi Utama -->
        <div class="actions">
            <!-- 1. WHATSAPP -->
            <a href="https://wa.me/<?= $wa_number ?>" target="_blank" class="action-btn">
                <i class="fa-brands fa-whatsapp"></i>
                WHATSAPP
            </a>

            <!-- 2. WECHAT (Membuka Pop-up QR Code) -->
            <button onclick="openWeChatModal()" class="action-btn">
                <i class="fa-brands fa-weixin"></i>
                WECHAT
            </button>

            <!-- 3. EMAIL -->
            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="action-btn">
                <i class="fa-solid fa-paper-plane"></i>
                EMAIL
            </a>
        </div>
    </div>

    <div class="details">
        <!-- Mobile -->
        <div class="detail-row">
            <div class="detail-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['mobile']) ?></div>
                <div class="detail-label">Mobile</div>
            </div>
        </div>

        <!-- Telephone -->
        <?php if (!empty($user['phone'])): ?>
        <div class="detail-row">
            <div class="detail-icon"></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['phone']) ?></div>
                <div class="detail-label">Telephone</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- WeChat Detail Row (Bisa diklik untuk salin ID) -->
        <?php if (!empty($user['wechat'])): ?>
        <div class="detail-row" onclick="copyWeChatId('<?= htmlspecialchars($user['wechat']) ?>')" style="cursor: pointer;">
            <div class="detail-icon"><i class="fa-brands fa-weixin"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['wechat']) ?></div>
                <div class="detail-label">WeChat ID (Klik untuk Salin)</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Email -->
        <div class="detail-row">
            <div class="detail-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['email']) ?></div>
                <div class="detail-label">Email</div>
            </div>
        </div>

        <!-- Perusahaan & Jabatan -->
        <div class="detail-row">
            <div class="detail-icon"><i class="fa-solid fa-briefcase"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['company']) ?></div>
                <div class="detail-label"><?= htmlspecialchars($user['title']) ?></div>
            </div>
        </div>

        <!-- Social Media Section -->
        <?php if (!empty($user['social'])): ?>
        <div class="social-title">Social Media</div>
        <div class="social-group">
            <?php if (!empty($user['social']['youtube'])): ?>
                <a href="<?= htmlspecialchars($user['social']['youtube']) ?>" target="_blank" class="social-btn bg-youtube"><i class="fa-brands fa-youtube"></i></a>
            <?php endif; ?>
            
            <?php if (!empty($user['social']['instagram'])): ?>
                <a href="<?= htmlspecialchars($user['social']['instagram']) ?>" target="_blank" class="social-btn bg-instagram"><i class="fa-brands fa-instagram"></i></a>
            <?php endif; ?>

            <?php if (!empty($user['social']['website'])): ?>
                <a href="<?= htmlspecialchars($user['social']['website']) ?>" target="_blank" class="social-btn bg-website"><i class="fa-solid fa-globe"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tombol Simpan Kontak Floating (Kanan Bawah) -->
    <a href="download_vcf.php?id=<?= urlencode($slug) ?>" class="fab-save" title="Simpan Kontak">
        <i class="fa-solid fa-user-plus"></i>
    </a>
</div>

<!-- POPUP MODAL WECHAT -->
<div id="wechatModal" class="modal-overlay" onclick="closeWeChatModal(event)">
    <div class="modal-box">
        <span class="close-btn" onclick="closeWeChatModalDirect()">&times;</span>
        <h4 style="margin: 5px 0 10px; font-size: 16px;">Scan WeChat QR</h4>
        <?php if (!empty($user['wechat'])): ?>
            <p style="font-size: 13px; color: #333; margin-bottom: 5px; font-weight: bold;"><?= htmlspecialchars($user['wechat']) ?></p>
        <?php endif; ?>
        
        <?php if (!empty($user['wechat_qr'])): ?>
            <img src="<?= htmlspecialchars($user['wechat_qr']) ?>" alt="WeChat QR Code">
            <p style="font-size: 11px; color: #888; margin-top: 5px;">Scan QR Code ini menggunakan aplikasi WeChat</p>
        <?php else: ?>
            <p style="font-size: 12px; color: #888; margin: 20px 0;">Salin WeChat ID di atas lalu cari di aplikasi WeChat Anda.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function openWeChatModal() {
    document.getElementById('wechatModal').style.display = 'flex';
}

function closeWeChatModalDirect() {
    document.getElementById('wechatModal').style.display = 'none';
}

function closeWeChatModal(event) {
    if (event.target.id === 'wechatModal') {
        document.getElementById('wechatModal').style.display = 'none';
    }
}

function copyWeChatId(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('WeChat ID berhasil disalin: ' + text);
    });
}
</script>

</body>
</html>
