<?php
require_once '../includes/db.php';

if (isset($_GET['query'])) {
    $query = trim($_GET['query']);
    $stmt = $db->prepare("SELECT id, name, model, serial_number, price FROM gadgets WHERE name LIKE ? OR model LIKE ? OR serial_number LIKE ?");
    $likeQuery = "%$query%";
    $stmt->bind_param("sss", $likeQuery, $likeQuery, $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $gadgets = [];
    while ($row = $result->fetch_assoc()) {
        $gadgets[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($gadgets);
    exit;
}
?>
