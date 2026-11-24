<?php

    $item_no = $_POST['Item_No'];
    $qty = $_POST['Quantity'];

    $item_exists = 1;

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $check_item_exists = "SELECT * FROM Clothing_Item WHERE Item_No=? LIMIT 1";

    $sql = "UPDATE Clothing_Item
            SET Current_Stock = Current_Stock + ?
            WHERE Item_No=?";

    for ($i = 0; $i < $size; $i++) {
        if ($all_item_no[$i] == "")
            break;
        if ($stmt = $conn->prepare($check_item_exists)) {
            if ($stmt->bind_param("i", $all_item_no[$i])) {
                $stmt->execute();
                $stmt->store_result();
                $item_exists = $stmt->num_rows;
                if (!($item_exists > 0))
                    break;
            }
        }
    }

    if ($item_exists > 0) {
        if ($stmt = $conn->prepare($sql)) {
            if ($stmt->bind_param("ii", $qty, $item_no)) {
                $stmt->execute();
            } else {
                $message = "Issue updating item quantity.";
            }
            $stmt->close();
        }
    } else {
        $message = "Item does not exist";
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);

?>