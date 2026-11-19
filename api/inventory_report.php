<?php

    $status = $_POST['status'];
    $clothing_line = $_POST['CL_Name'];
    $gender = $_POST['Gender'];
    $sortBy = $_POST['sort'];

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    #$sql = "INSERT INTO Customer (Phone_No, F_Name, L_Name, DOB, Email)
    #        VALUES (?, ?, ?, ?, ?)";
    if ($status = "all") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$clothing_line."' OR Cl_Name IS NULL)";
    }


    $result = $conn->query($sql);

    #if ($stmt = $conn->prepare($sql)) {
    #    if ($stmt->bind_param("ssss", $status, $clothing_line, $gender, $sortBy)) {
    #        $stmt->execute();
    #    } else {
    #        echo "Parameter binding failed.";
    #    }
    #    $stmt->close();
    #}

    $conn->close();
?>
