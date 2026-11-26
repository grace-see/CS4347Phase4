<?php

    require_once 'exit_with_error_func.php';

    $item_no = $_POST['Item_No'];

    $message = "ok";

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
            $conn->close();
            exit_with_error("Issue with deleting clothing item.");
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);
?>