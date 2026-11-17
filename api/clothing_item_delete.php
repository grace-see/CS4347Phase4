<?php

    $item_no = $_POST['Item_No'];

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $sql = "DELETE FROM Clothing_Item
            WHERE Item_No=?";


    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("si", $cl_name, $item_no)) {
            $stmt->execute();
        } else {
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    $conn->close();
?>