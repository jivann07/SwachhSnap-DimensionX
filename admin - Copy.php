<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwachhSnap - Municipal Dashboard</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; }
        .header { background-color: #2c3e50; color: white; padding: 15px; text-align: center; }
        .container { padding: 20px; max-width: 1200px; margin: auto; }
        
        /* Map Style */
        #map { height: 400px; width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #27ae60; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; background-color: #f39c12; color: white; }
    </style>
</head>
<body>

    <div class="header">
        <h1>♻️ SwachhSnap Command Center</h1>
    </div>

    <div class="container">
        <h3>📍 Live Garbage Hotspots</h3>
        <div id="map"></div>

        <h3>📝 Recent Reports</h3>
        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Waste Type</th>
                    <th>Coordinates</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // We create a PHP array to hold data for the map script later
                $mapData = array();

                $sql = "SELECT * FROM reports ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Add to list
                        echo "<tr>";
                        echo "<td><a href='" . $row["image_path"] . "' target='_blank'><img src='" . $row["image_path"] . "' class='img-thumb'></a></td>";
                        echo "<td><b>" . $row["waste_type"] . "</b></td>";
                        echo "<td>" . $row["latitude"] . ", " . $row["longitude"] . "</td>";
                        echo "<td><span class='status-badge'>" . $row["status"] . "</span></td>";
                        echo "<td>" . $row["created_at"] . "</td>";
                        echo "</tr>";

                        // Add to Map Data if coordinates exist
                        if($row["latitude"] != "0.0000" && $row["longitude"] != "0.0000") {
                            $mapData[] = $row;
                        }
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>No reports found yet!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize Map (Centered on Kolhapur by default)
        var map = L.map('map').setView([16.7050, 74.2433], 13);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Get PHP Data into Javascript
        var reports = <?php echo json_encode($mapData); ?>;

        // Loop through reports and add Red Pins
        reports.forEach(function(report) {
            var lat = parseFloat(report.latitude);
            var lng = parseFloat(report.longitude);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                var marker = L.marker([lat, lng]).addTo(map);
                marker.bindPopup("<b>" + report.waste_type + "</b><br><img src='" + report.image_path + "' width='100px'>");
            }
        });
    </script>

</body>
</html>