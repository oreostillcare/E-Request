<?php

require_once '../model/class_model.php';
require_authenticated_session('user_id');

if (!isset($_POST['view'])) {
    exit;
}

$conn = new class_model();
if ($_POST['view'] !== '') {
    $conn->mark_notifications_seen();
}

$rows = $conn->latest_notifications();
$output = '';
foreach ($rows as $row) {
    $name = htmlspecialchars((string) ($row['document_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $date = !empty($row['date_releasing']) ? date('M d, Y', strtotime($row['date_releasing'])) : 'To be announced';
    $output .= '<li style="background-color:#23b294;width:100%">'
        . '<a class="nav-item" href="request.php" style="margin-left:10px">'
        . '<b><i class="fa fa-fw fa-file"></i>Document Name: ' . $name . '</b>'
        . '<p style="margin-left:14px;font-size:11px"><i class="fa fa-calendar"></i> Date Releasing: <i>' . $date . '</i></p>'
        . '<p style="border-bottom:1px dotted green;width:100%"></p></a></li>';
}

if ($output === '') {
    $output = '<li><a href="#" class="text-bold text-italic">No Notification Found</a></li>';
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'notification' => $output,
    'unseen_notification' => $conn->unseen_notification_count(),
]);

