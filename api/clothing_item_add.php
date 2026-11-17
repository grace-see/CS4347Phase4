<?php

    $item_no = $_POST['Item_No'];
    $cl_name = $_POST['CL_Name'];
    $gender = $_POST['Gender'];
    $size = $_POST['Size'];
    $material = $_POST['Material'];
    $price = $_POST['Price'];
    $curr_stock = $_POST['Current_Stock'];

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $add_item = "INSERT INTO Clothing_Item
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($add_item)) {
        if ($stmt->bind_param("isssdi", $item_no, $gender, $material, $price, $curr_stock)) {
            $stmt->execute();
        } else {
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    $add_relation = "INSERT INTO C_Line_Contains
            VALUES (?, ?)";

    if ($stmt = $conn->prepare($add_relation)) {
        if ($stmt->bind_param("si", $cl_name, $item_no)) {
            $stmt->execute();
        } else {
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    $conn->close();
?>