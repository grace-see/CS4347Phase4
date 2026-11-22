<?php

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

    $sql = "INSERT INTO Customer (Phone_No, F_Name, L_Name, DOB, Email)
            VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("sssss", $phone_no, $f_name, $l_name, $dob, $email)) {
            $stmt->execute();
        } else {
            $message = "Issue with customer signup.";
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);

?>