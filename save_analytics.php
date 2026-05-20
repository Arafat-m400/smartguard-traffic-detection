<?php
session_start();
include('connection.php');

$input = json_decode(file_get_contents('php://input'), true);
$user_email = $input['user_email'] ?? null;
$analytics = $input['analytics'] ?? null;

if (!$user_email || !$analytics) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Extract location info (for simplicity, use the first location point if exists)
$latitude = null;
$longitude = null;
if (isset($analytics['locations']) && is_array($analytics['locations']) && count($analytics['locations']) > 0) {
    // Assuming locations array format: [ [lat, lng, intensity], ... ]
    $latitude = $analytics['locations'][0][0];
    $longitude = $analytics['locations'][0][1];
}

$analytics_json = json_encode($analytics);

$query = "INSERT INTO history (user_email, analytics_data, latitude, longitude, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $con->prepare($query);
$stmt->bind_param("ssdd", $user_email, $analytics_json, $latitude, $longitude);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Analytics saved successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}
?>
