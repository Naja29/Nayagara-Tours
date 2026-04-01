<?php
header('Content-Type: application/json');
require_once __DIR__ . '/admin/config/db.php';

$pdo  = getPDO();
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$name    = trim($data['name']    ?? '');
$email   = trim($data['email']   ?? '');
$phone   = trim($data['phone']   ?? '');
$subject = trim($data['subject'] ?? 'General Inquiry');
$message = trim($data['message'] ?? '');

if ($name === '' || $message === '') {
    echo json_encode(['success' => false, 'error' => 'Name and message are required.']);
    exit;
}

try {
    $pdo->prepare('
        INSERT INTO inquiries (full_name, email, phone, subject, message, is_read, created_at)
        VALUES (?, ?, ?, ?, ?, 0, NOW())
    ')->execute([$name, $email ?: '', $phone ?: null, $subject, $message]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Could not save. Please try again.']);
}
