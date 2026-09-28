<?php

require_once __DIR__ . '/../model/class_model.php';

header('Content-Type: application/json; charset=utf-8');

$studentId = require_authenticated_session('student_id');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$photo = $_FILES['profile_photo'] ?? null;
if (!is_array($photo) || ($photo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please choose a photo first.']);
    exit;
}

if (($photo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'The photo could not be uploaded. Please try again.']);
    exit;
}

$maximumSize = 5 * 1024 * 1024;
$fileSize = (int) ($photo['size'] ?? 0);
if ($fileSize <= 0 || $fileSize > $maximumSize) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Photo must be 5 MB or smaller.']);
    exit;
}

$temporaryPath = (string) ($photo['tmp_name'] ?? '');
$imageDetails = $temporaryPath !== '' ? @getimagesize($temporaryPath) : false;
$contentType = is_array($imageDetails) ? (string) ($imageDetails['mime'] ?? '') : '';
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($contentType, $allowedTypes, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Use a JPG, PNG, or WebP image.']);
    exit;
}

$contents = @file_get_contents($temporaryPath);
if ($contents === false) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'The selected photo could not be read.']);
    exit;
}

$model = new class_model();
$photoUrl = $model->upload_student_profile_photo($studentId, $contents, $contentType);
if ($photoUrl === '') {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Upload failed. Please try again shortly.']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Profile photo updated.',
    'photo_url' => $photoUrl . '?v=' . time(),
]);
