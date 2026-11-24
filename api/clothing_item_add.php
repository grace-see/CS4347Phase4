<?php

    $item_no = $_POST['Item_No'];
    $cl_name = $_POST['CL_Name'];
    $gender = $_POST['Gender'];
    $size = $_POST['Size'];
    $material = $_POST['Material'];
    $price = $_POST['Price'];
    $curr_stock = $_POST['Current_Stock'];

    $cl_exists = 1;

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $check_cl = "SELECT * FROM clothing_line WHERE CL_Name LIKE ? LIMIT 1";
    
    if ($cl_name != "") {
        if ($stmt = $conn->prepare($check_cl)) {
            if ($stmt->bind_param("s", $cl_name)) {
                $stmt->execute();
                $stmt->store_result();
                $cl_exists = $stmt->num_rows;
            } else {
                $message = "Issue with adding clothing item.";
            }
            $stmt->close();
        }
    }

    if ($cl_exists > 0) {
        $add_item = "INSERT INTO Clothing_Item
                VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($add_item)) {
            if ($stmt->bind_param("isssdi", $item_no, $gender, $size, $material, $price, $curr_stock)) {
                $stmt->execute();
            } else {
                $message = "Issue with adding clothing item.";
            }
            $stmt->close();
        }
        if ($cl_name != "") {
            $add_relation = "INSERT INTO C_Line_Contains
                    VALUES (?, ?)";

            if ($stmt = $conn->prepare($add_relation)) {
                if ($stmt->bind_param("si", $cl_name, $item_no)) {
                    $stmt->execute();
                } else {
                    $message = "Issue with adding clothing item.";
                }
                $stmt->close();
            }
        }
    } else {
        $message = "Clothing Line does not exist";
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);
?>