<?php

    $cl_name = $_POST['CL_Name'];
    $type = $_POST['Type'];
    $first_available = $_POST['First_Available'];
    $last_available = $_POST['Last_Available'];
    $sale = $_POST['On_Sale'];
    $price_tier = $_POST['Price_Tier'];
    $description = $_POST['Description'];

    $message = "ok";

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
            $message = "Issue with adding clothing line.";
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);
?>