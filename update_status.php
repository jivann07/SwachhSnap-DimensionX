<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    // Update status to 'Cleaned'
    $sql = "UPDATE reports SET status='Cleaned' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: worker.php"); // Go back to dashboard
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>