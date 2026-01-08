<?php
include 'db.php';
header('Content-Type: application/json');

if (isset($_FILES['image'])) {
    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];
    
    // --- SIMULATION MODE START ---
    // Since we are on a laptop, let's generate fake coordinates in KOLHAPUR
    // Base location: Near Dr. Bapuji Salunkhe Institute
    $base_lat = 16.7050; 
    $base_lng = 74.2433;
    
    // Add a random offset so pins appear in different spots
    $lat_offset = (rand(-50, 50) / 10000); 
    $lng_offset = (rand(-50, 50) / 10000);

    $latitude = $base_lat + $lat_offset;
    $longitude = $base_lng + $lng_offset;
    // --- SIMULATION MODE END ---

    $unique_name = time() . "_" . $file_name;
    $target_dir = "uploads/";
    $target_file = $target_dir . $unique_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        
        $waste_types = ['Plastic Waste', 'Organic Waste', 'Metal Waste'];
        $detected_type = $waste_types[array_rand($waste_types)];

        $sql = "INSERT INTO reports (image_path, latitude, longitude, waste_type, status) 
                VALUES ('$target_file', '$latitude', '$longitude', '$detected_type', 'Pending')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("status" => "success", "message" => "Report Saved with GPS!"));
        } else {
            echo json_encode(array("status" => "error", "message" => "DB Error: " . $conn->error));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Upload Failed"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "No image."));
}
$conn->close();
?>