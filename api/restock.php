<?php

    $item_no = $_POST['Item_No'];
    $qty = $_POST['Quantity'];

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $sql = "UPDATE Clothing_Item
            SET Current_Stock = Current_Stock + ?
            WHERE Item_No=?";

    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("ii", $qty, $item_no)) {
            $stmt->execute();
        } else {
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    $conn->close();

?>