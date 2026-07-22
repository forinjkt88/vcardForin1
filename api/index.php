<?php
// Memastikan PHP mencari file data.json tepat di folder yang sama dengan index.php
$file_path = __DIR__ . '/data.json';

// Cek darurat jika file tidak ditemukan
if (!file_exists($file_path)) {
    die("Sistem Vercel belum membaca file data.json. Coba redeploy atau tunggu beberapa saat.");
}

$data = json_decode(file_get_contents($file_path), true);
$slug = $_GET['id'] ?? 'willy';

if (!isset($data[$slug])) {
    die("Kontak tidak ditemukan di database.");
}

$user = $data[$slug];

// Format nomor WhatsApp
$wa_number = preg_replace('/[^0-9]/', '', $user['mobile']);
if (substr($wa_number, 0, 1) === '0') {
    $wa_number = '62' . substr($wa_number, 1);
}

// Cek Banner
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
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f4f4f9; display: flex; justify-content: center; padding: 20px; margin: 0; }
        .card { background: #fff; width: 100%; max-width: 380px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden; position: relative; }
        .profile { position: relative; padding: 25px 20px 15px 20px; text-align: center; }
        .cover-banner { height: 120px; border-radius: 20px 20px 0 0; margin: -25px -20px 0 -20px; }
        .logo { width: 90px; height: 90px; border-radius: 50%; object-fit: contain; background: #fff; padding: 5px; border: 3px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); margin-top: -45px; position: relative; z-index: 2; }
        .name { font-size: 22px; font-weight: bold; color: #000; margin-top: 10px; margin-bottom: 4px; }
        .title { color: #666; font-size: 14px; margin-bottom: 25px; }
        .actions { display: flex; justify-content: space-between; align-items: center; padding: 0 15px 15px; border-bottom: 1px solid #eee; }
        .action-btn { text-decoration: none; color: #000; font-size: 11px; font-weight: 700; text-align: center; flex: 1; letter-spacing: 0.5px; background: none; border: none; cursor: pointer; }
        .action-btn i { font-size: 22px; margin-bottom: 6px; display: block; color: #000; }
        .details { text-align: left; padding: 20px; background: #fafafa; }
        .detail-row { display: flex; align-items: flex-start; margin-bottom: 18px; }
        .detail-icon { width: 35px; color: #777; font-size: 18px; text-align: center; padding-top: 2px; }
        .detail-info { flex: 1; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; }
        .detail-val { font-size: 15px; font-weight: 500; color: #222; word-break: break-all; }
        .detail-label { font-size: 12px; color: #888; margin-top: 2px; }
        .fab-save { position: fixed; bottom: 30px; right: 30px; background: #000; color: #fff; width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.3); font-size: 20px; z-index: 99; }
    </style>
</head>
<body>

<div class="card">
    <div class="profile">
        <div class="cover-banner" style="<?= $banner_style ?>"></div>
        <img src="<?= htmlspecialchars($user['logo']) ?>" alt="Logo" class="logo">
        <div class="name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="title"><?= htmlspecialchars($user['title']) ?></div>
        
        <div class="actions">
            <a href="https://wa.me/<?= $wa_number ?>" target="_blank" class="action-btn">
                <i class="fa-brands fa-whatsapp"></i>WHATSAPP
            </a>
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
        <div class="detail-row">
            <div class="detail-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-info">
                <div class="detail-val"><?= htmlspecialchars($user['email']) ?></div>
                <div class="detail-label">Email</div>
            </div>
        </div>
    </div>

    <a href="download_vcf.php?id=<?= urlencode($slug) ?>" class="fab-save" title="Simpan Kontak">
        <i class="fa-solid fa-user-plus"></i>
    </a>
</div>

</body>
</html>
