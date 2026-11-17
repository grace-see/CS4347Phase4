<?php

    $cl_name = $_POST['CL_Name'];
    $type = $_POST['Type'];
    $first_available = $_POST['First_Available'];
    $last_available = $_POST['Last_Available'];
    $sale = $_POST['On_Sale'];
    $price_tier = $_POST['Price_Tier'];
    $description = $_POST['Description'];

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $sql = "INSERT INTO Clothing_Lien
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("ssssdss", $cl_name, $type, $first_available, $last_available, $sale, $price_tier, $description)) {
            $stmt->execute();
        } else {
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    $conn->close();
?>