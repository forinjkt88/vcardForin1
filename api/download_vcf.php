<?php
$data = json_decode(file_get_contents('data.json'), true);
$slug = $_GET['id'] ?? '';

if (!isset($data[$slug])) {
    die("Kontak tidak ditemukan.");
}

$u = $data[$slug];

header('Content-Type: text/vcard; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $slug . '.vcf"');

echo "BEGIN:VCARD\r\n";
echo "VERSION:3.0\r\n";
echo "FN:" . $u['name'] . "\r\n";
echo "ORG:" . $u['company'] . "\r\n";
echo "TITLE:" . $u['title'] . "\r\n";
echo "TEL;TYPE=CELL:" . $u['mobile'] . "\r\n";
if (!empty($u['phone'])) echo "TEL;TYPE=WORK:" . $u['phone'] . "\r\n";
echo "EMAIL;TYPE=WORK:" . $u['email'] . "\r\n";
if (!empty($u['social']['website'])) echo "URL:" . $u['social']['website'] . "\r\n";
echo "END:VCARD\r\n";