<?php
$url = "http://rwandavehiclesplates.atwebpages.com/api.php";

// Use file_get_contents instead of CURL
$response = file_get_contents($url);

if (!$response) {
    echo "<p class='text-danger'>❌ Unable to reach API. Try again.</p>";
    exit();
}

$data = json_decode($response, true);

if (isset($_POST['btn'])) {
    $get_input = strtoupper(trim($_POST['btn']));
    $found = false;

    foreach ($data as $row) {
        if (strtoupper($row['plate_no']) === $get_input) {
            echo "<div class='alert alert-success p-2'>
                    <strong>✅ Match found:</strong><br>
                    <ul class='mb-0'>
                        <li><strong>Plate:</strong> {$row['plate_no']}</li>
                        <li><strong>Owner:</strong> {$row['owner']}</li>
                        <li><strong>Phone:</strong> {$row['phone']}</li>
                        <li><strong>Registered:</strong> {$row['registered_on']}</li>
                    </ul>
                  </div>";
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo "<div class='alert alert-warning'>⚠️ No match found for <strong>$get_input</strong>.</div>";
    }
}
?>
