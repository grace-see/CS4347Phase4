<?php

    require_once 'exit_with_error_func.php';

    $cl_name = $_POST['CL_Name'];

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $sql = "DELETE FROM Clothing_Line
            WHERE CL_Name=?";

    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("s", $cl_name)) {
            $stmt->execute();
        } else {
            conn->close();
            exit_with_error("Issue with deleting clothing line.");
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);

?>