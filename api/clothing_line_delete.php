<?php

    $cl_name = $_POST['CL_Name'];

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
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    $conn->close();

?>