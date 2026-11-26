<?php

    require_once 'exit_with_error_func.php';

    $item_no = $_POST['Item_No'];
    $cl_name = $_POST['CL_Name'];
    $gender = $_POST['Gender'];
    $size = $_POST['Size'];
    $material = $_POST['Material'];
    $price = $_POST['Price'];
    $curr_stock = $_POST['Current_Stock'];

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $duplicate_item = "SELECT * FROM clothing_item WHERE Item_No=? LIMIT 1";

    if ($stmt = $conn->prepare($duplicate_item)) {
        if ($stmt->bind_param("i", $item_no)) {
            $stmt->execute();
            $stmt->store_result();
            $item_exists = $stmt->num_rows;
            if ($item_exists > 0) {
                $conn->close();
                exit_with_error("Clothing item with the same item number already exists.");
            }
        } else {
            $conn->close();
            exit_with_error("Issue with adding clothing item.");
        }
        $stmt->close();
    }

    $check_cl = "SELECT * FROM clothing_line WHERE CL_Name LIKE ? LIMIT 1";
    
    if ($stmt = $conn->prepare($check_cl)) {
        if ($stmt->bind_param("s", $cl_name)) {
            $stmt->execute();
            $stmt->store_result();
            $cl_exists = $stmt->num_rows;
            if ($cl_exists == 0) {
                $conn->close();
                exit_with_error("Clothing Line does not exist.");
            }
        } else {
            $conn->close();
            exit_with_error("Issue with adding clothing item.");
        }
        $stmt->close();
    }

    $add_item = "INSERT INTO Clothing_Item
                 VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($add_item)) {
        if ($stmt->bind_param("isssdi", $item_no, $gender, $size, $material, $price, $curr_stock)) {
            $stmt->execute();
        } else {
            $conn->close();
            exit_with_error("Issue with adding clothing item.");
        }
        $stmt->close();
    }
    $add_relation = "INSERT INTO C_Line_Contains
                     VALUES (?, ?)";

    if ($stmt = $conn->prepare($add_relation)) {
        if ($stmt->bind_param("si", $cl_name, $item_no)) {
            $stmt->execute();
        } else {
            $conn->close();
            exit_with_error("Issue with adding clothing item.");
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);
?>