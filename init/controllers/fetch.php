<?php

require_once '../model/class_model.php';
$student_id = require_authenticated_session('student_id');

if (!isset($_POST['view'])) {
    exit;
}

$conn = new class_model();
$rows = $conn->notification_rows($student_id);
$output = '';

foreach ($rows as $row) {
    $student = rawurlencode((string) $student_id);
    $document = rawurlencode((string) ($row['document_name'] ?? ''));
    $release = rawurlencode((string) ($row['date_releasing'] ?? ''));
    $name = htmlspecialchars((string) ($row['document_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $date = !empty($row['date_releasing']) ? date('M d, Y', strtotime($row['date_releasing'])) : 'To be announced';
    $output .= '<li style="background-color:#23b294;width:100%">'
        . '<a class="nav-item" href="myrequest.php?student=' . $student . '&document-name=' . $document . '&date-release=' . $release . '" style="margin-left:10px">'
        . '<b><i class="fa fa-fw fa-file"></i>Document Name: ' . $name . '</b>'
        . '<p style="margin-left:14px;font-size:11px;color:#d4cac9"><i class="fa fa-calendar"></i> Date Releasing: <i>' . $date . '</i></p>'
        . '<p style="border-bottom:1px dotted green;width:100%"></p></a></li>';
}

if ($output === '') {
    $output = '<li style="color:red"><a href="#" class="text-bold text-italic"><p style="margin-left:10px;color:red">No Notification Found</p></a></li>';
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'notification' => $output,
    'unseen_notification' => $conn->notification_count($student_id),
]);

