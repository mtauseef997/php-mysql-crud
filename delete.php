<?php
include 'connect.php';

if (isset($_GET['ID']) && is_numeric($_GET['ID'])) {
    $id = (int)$_GET['ID'];

    $stmt = $con->prepare("DELETE FROM `record` WHERE ID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting record: " . $stmt->error . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Invalid ID provided in URL.</div>";
}
?>