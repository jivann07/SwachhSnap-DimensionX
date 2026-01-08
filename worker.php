<?php 
include 'db.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Worker Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3 class="text-success text-center">👷‍♂️ Worker Tasks</h3>
    <p class="text-center text-muted">Locations assigned to you</p>

    <div class="row">
        <?php
        // Show only PENDING tasks
        $sql = "SELECT * FROM reports WHERE status='Pending' ORDER BY created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
        ?>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <img src="<?php echo $row['image_path']; ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px; margin-right: 15px;">
                        <div>
                            <h5 class="card-title text-danger"><?php echo $row['waste_type']; ?></h5>
                            <p class="small text-muted mb-1">📍 <?php echo number_format($row['latitude'], 4) . ", " . number_format($row['longitude'], 4); ?></p>
                            
                            <form action="update_status.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-success btn-sm">✅ Mark Cleaned</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo "<div class='alert alert-info text-center'>No trash to collect! 🎉</div>";
        }
        ?>
    </div>
</div>

</body>
</html>