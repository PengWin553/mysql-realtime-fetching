<?php
// Turn off error reporting for production
error_reporting(0);

// Set appropriate headers for SSE
header("Cache-Control: no-store");
header("Content-Type: text/event-stream");
header("Connection: keep-alive");

// Include database connection
include("db_connection.php");

// Initialize previous data state
$previousData = '';

// Continuous loop for SSE
while (true) {
    // Query database for current data
    $result = $con->query("SELECT * FROM data");
    
    if ($result && $result->num_rows > 0) {
        // Fetch and prepare data array
        $dataArray = [];
        while ($row = $result->fetch_assoc()) {
            $dataArray[] = $row;
        }

        // Convert data array to JSON
        $currentData = json_encode($dataArray);

        // Compare current data with previous data
        if ($currentData !== $previousData) {
            // Send SSE message if data has changed
            echo "data: $currentData\n\n";
            $previousData = $currentData;
            ob_flush();
            flush();
        }
    }

    // Sleep for 1 second before checking again
    sleep(1);
}
?>
