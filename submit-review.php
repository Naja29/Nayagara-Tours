<?php
header('Content-Type: application/json');
require_once __DIR__ . '/admin/config/db.php';

$pdo  = getPDO();
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$name    = trim($data['name']    ?? '');
$country = trim($data['country'] ?? '');
$rating  = (int)($data['rating'] ?? 5);
$text    = trim($data['review_text'] ?? '');

if ($name === '' || $text === '') {
    echo json_encode(['success' => false, 'error' => 'Name and review are required.']);
    exit;
}

if ($rating < 1 || $rating > 5) $rating = 5;

try {
    $pdo->prepare('
        INSERT INTO reviews (name, country, rating, review_text, is_approved, created_at)
        VALUES (?, ?, ?, ?, 0, NOW())
    ')->execute([$name, $country ?: null, $rating, $text]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Could not save. Please try again.']);
}
