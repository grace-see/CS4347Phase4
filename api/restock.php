<?php

    require_once 'exit_with_error_func.php';

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


    if ($stmt = $conn->prepare($check_item_exists)) {
        if ($stmt->bind_param("i", $item_no)) {
            $stmt->execute();
            $stmt->store_result();
            $item_exists = $stmt->num_rows;
            if (!($item_exists > 0)) {
                $conn->close();
                exit_with_error("Item does not exist.");
            }
        }
    }

    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("ii", $qty, $item_no)) {
            $stmt->execute();
        } else {
            $conn->close();
            exit_with_error("Issue updating item quantity");
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);

?>