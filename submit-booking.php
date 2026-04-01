<?php
header('Content-Type: application/json');
require_once __DIR__ . '/admin/config/db.php';

$pdo  = getPDO();
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$full_name      = trim($data['full_name']       ?? '');
$email          = trim($data['email']           ?? '');
$phone          = trim($data['phone']           ?? '');
$nationality    = trim($data['nationality']     ?? '');
$adults         = max(1, (int)($data['adults']  ?? 1));
$children       = max(0, (int)($data['children']?? 0));
$travel_date    = trim($data['travel_date']     ?? '');
$special_request= trim($data['special_request'] ?? '');
$package_id     = !empty($data['package_id']) ? (int)$data['package_id'] : null;

if ($full_name === '' || $email === '') {
    echo json_encode(['success' => false, 'error' => 'Name and email are required.']);
    exit;
}

// Validate travel date
$travelDate = null;
if ($travel_date !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $travel_date);
    if ($d) $travelDate = $d->format('Y-m-d');
}

try {
    $pdo->prepare('
        INSERT INTO bookings
          (package_id, full_name, email, phone, nationality, adults, children, travel_date, special_request, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, \'new\', NOW())
    ')->execute([$package_id, $full_name, $email, $phone ?: null, $nationality ?: null,
                 $adults, $children, $travelDate, $special_request ?: null]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Could not save booking. Please try again.']);
}
