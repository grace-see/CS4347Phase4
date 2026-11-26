<?php

    require_once 'exit_with_error_func.php';

    $f_name = $_POST['F_Name'];
    $l_name = $_POST['L_Name'];
    $phone_no = $_POST['Phone_No'];
    $dob = $_POST['DOB'];
    $email = $_POST['Email'];

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    // assuming duplicate custoemr can be checked by name? name is required for signup
    $duplicate_customer = "SELECT * 
                           FROM Customer 
                           WHERE F_Name LIKE ? AND L_Name LIKE ?
                           LIMIT 1";

    $sql = "INSERT INTO Customer (Phone_No, F_Name, L_Name, DOB, Email)
            VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($duplicate_customer)) {
        if ($stmt->bind_param("ss", $f_name, $l_name)) {
            $stmt->execute();
            $stmt->store_result();
            $customer_exists = $stmt->num_rows;
            if ($customer_exists > 0) {
                $conn->close();
                exit_with_error("Customer with same name already exists.");
            }
        }
    }

    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("sssss", $phone_no, $f_name, $l_name, $dob, $email)) {
            $stmt->execute();
        } else {
            $conn->close();
            exit_with_error("Issue with customer signup.");
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    http_response_code('200');
    $res = array("message" => $message);
    echo json_encode($res);

?>