<?php
$file_path = __DIR__ . '/data.json';

if (!file_exists($file_path)) {
    die("Sistem sedang memuat data. Silakan refresh.");
}

$data = json_decode(file_get_contents($file_path), true);
$slug = $_GET['id'] ?? 'willy';

if (!isset($data[$slug])) {
    die("Kontak tidak ditemukan.");
}

$user = $data[$slug];

// Format nomor WhatsApp
$wa_number = preg_replace('/[^0-9]/', '', $user['mobile']);
if (substr($wa_number, 0, 1) === '0') {
    $wa_number = '62' . substr($wa_number, 1);
}

// Banner Background
$banner_style = !empty($user['banner']) 
    ? "background: url('" . htmlspecialchars($user['banner']) . "') center/cover no-repeat;" 
    : "background: linear-gradient(135deg, #004488, #d9232a);";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>vCard - <?= htmlspecialchars($user['name']) ?></title>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f4f4f9; display: flex; justify-content: center; padding: 20px; margin: 0; }
        .card { background: #fff; width: 100%; max-width: 380px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden; position: relative; padding-bottom: 20px; }
        
        .profile { position: relative; padding: 25px 20px 15px 20px; text-align: center; }
        .cover-banner { height: 120px; border-radius: 20px 20px 0 0; margin: -25px -20px 0 -20px; }
        .logo { width: 90px; height: 90px; border-radius: 50%; object-fit: contain; background: #fff; padding: 5px; border: 3px solid #eee; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-top: -45px; position: relative; z-index: 2; }
        
        .name { font-size: 21px; font-weight: bold; color: #000; margin-top: 10px; margin-bottom: 4px; }
        .title { color: #666; font-size: 13px; margin-bottom: 25px; }
        
        .actions { display: flex; justify-content: space-between; align-items: center; padding: 0 15px 15px; border-bottom: 1px solid #eee; }
        .action-btn { text-decoration: none; color: #000; font-size: 10px; font-weight: 700; text-align: center; flex: 1; letter-spacing: 0.5px; background: none; border: none; cursor: pointer; }
        .action-btn i { font-size: 22px; margin-bottom: 6px; display: block; color: #000; }
        
        .details { text-align: left; padding: 20px; background: #fafafa; }
        .detail-row { display: flex; align-items: flex-start; margin-bottom: 18px; }
        .detail-icon { width: 35px; color: #777; font-size: 18px; text-align: center; padding-top: 2px; }
        .detail-info { flex: 1; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; }
        .detail-val { font-size: 14px; font-weight: 500; color: #222; word-break: break-all; }
        .detail-label { font-size: 11px; color: #888; margin-top: 4px; }
        
        /* Media Sosial */
        .social-title { font-size: 12px; color: #888; margin: 20px 0 12px 35px; }
        .social-group { display: flex; gap: 12px; margin-left: 35px; margin-bottom: 10px; }
        .social-btn { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; text-decoration: none; font-size: 18px; }
        .bg-youtube { background-color: #FF0000; }
        .bg-instagram { background-color: #E1306C; }
        .bg-website { background-color: #5A6268; }

        .fab-save { position: fixed; bottom: 30px; right: 30px; background: #000; color: #fff; width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.3); font-size: 20px; z-index: 99; }
        
        /* Modal WeChat */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); justify-content: center; align-items: center; z-index: 1000; }
        .modal-box { background: #fff; padding: 20px; border-radius: 16px; width: 90%; max-width: 320px; text-align: center; position: relative; }
        .modal-box img { width: 100%; max-width: 220px; border-radius: 8px; margin: 10px 0; }
        .close-btn { position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; color: #888; }
    </style>
</head>
<body>

<div class="card">
    <div class="profile">
        <div class="cover-banner" style="<?= $banner_style ?>"></div>
        
        <!-- Panggil Logo -->
        <?php if (!empty($user['logo'])): ?>
            <img src="<?= htmlspecialchars($user['logo']) ?>" alt="Logo" class="logo">
        <?php endif; ?>
        
        <div class="name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="title"><?= htmlspecialchars($user['title']) ?></div>
        
        <div class="actions">
            <a href="https://wa.me/<?= $wa_number ?>" target="_blank" class="action-btn">
                <i class="fa-brands fa-whatsapp"></i>WHATSAPP
            </a>
            <button onclick="openWeChatModal()" class="action-btn" style="padding:0; margin:0;">
                <i class="fa-brands fa-weixin"></i>WECHAT
            </button>
            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="action-btn">
                <i class="fa-solid fa-paper-plane"></i>EMAIL
            </a>
        </div>
    </div>

    <div class="details">
        <div class="detail-row">
            <div class="detail-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['mobile']) ?></div>
                <div class="detail-label">Mobile</div>
            </div>
        </div>
        
        <?php if (!empty($user['phone'])): ?>
        <div class="detail-row">
            <div class="detail-icon"></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['phone']) ?></div>
                <div class="detail-label">Telephone</div>
            </div>
        </div>
        <?php endif; ?>

        <div class="detail-row">
            <div class="detail-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['email']) ?></div>
                <div class="detail-label">Email</div>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-icon"><i class="fa-solid fa-briefcase"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['company']) ?></div>
                <div class="detail-label"><?= htmlspecialchars($user['title']) ?></div>
            </div>
        </div>

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
</div>

<div id="wechatModal" class="modal-overlay" onclick="closeWeChatModal(event)">
    <div class="modal-box">
        <span class="close-btn" onclick="closeWeChatModalDirect()">&times;</span>
        <h4 style="margin: 5px 0 10px;">Scan WeChat QR</h4>
        <?php if (!empty($user['wechat_qr'])): ?>
            <img src="<?= htmlspecialchars($user['wechat_qr']) ?>" alt="WeChat QR Code">
        <?php else: ?>
            <p style="color:#888; font-size:12px;">QR Code tidak tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function openWeChatModal() { document.getElementById('wechatModal').style.display = 'flex'; }
function closeWeChatModalDirect() { document.getElementById('wechatModal').style.display = 'none'; }
function closeWeChatModal(event) { if (event.target.id === 'wechatModal') { document.getElementById('wechatModal').style.display = 'none'; } }
</script>

</body>
</html>
