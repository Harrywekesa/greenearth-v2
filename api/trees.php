<!-- api/trees.php -->
<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

// Get all planted trees for mobile app
$stmt = $connection->prepare("
    SELECT tp.id, tp.tree_type, tp.latitude, tp.longitude, tp.planting_date, tp.status,
           u.name as planter_name, e.title as event_name
    FROM tree_plantings tp
    LEFT JOIN users u ON tp.user_id = u.id
    LEFT JOIN events e ON tp.event_id = e.id
    WHERE tp.status = 'planted'
    ORDER BY tp.planting_date DESC
    LIMIT 100
");
$stmt->execute();
$result = $stmt->get_result();
$trees = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'status' => 'success',
    'data' => $trees,
    'count' => count($trees)
]);
?>