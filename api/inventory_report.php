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

    if ($sortBy = "stock_asc") {
        $sort = "Current_Stock ASC"
    }
    if ($sortBy = "stock_desc") {
        $sort = "Current_Stock DESC"
    }
    if ($sortBy = "price_asc") {
        $sort = "Current_Stock ASC"
    }
    if ($sortBy = "price_desc") {
        $sort = "Current_Stock DESC"
    }

    #$sql = "INSERT INTO Customer (Phone_No, F_Name, L_Name, DOB, Email)
    #        VALUES (?, ?, ?, ?, ?)";
    if ($status = "all") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$clothing_line."' OR (empty($clothing_line))
                ORDER BY ".$sort."";
    }
    else if ($status = "in") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$clothing_line."' OR (empty($clothing_line)) AND Current_Stock > 0
                ORDER BY ".$sort."";
    }
    else if ($status = "out") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$clothing_line."' OR (empty($clothing_line)) AND Current_Stock = 0
                ORDER BY ".$sort."";
    }
    else if ($status = "discontinued") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$clothing_line."' OR (empty($clothing_line)) AND Last_Available IS NOT NULL
                ORDER BY ".$sort."";
    }


    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Item No: " .$row["Item_No"]. " - Size: " .$row["Size"]. " - Material: " .$row["Material"]. " - Gender: " .$row["Gender"]. " - Current Stock: " .$row["Current_Stock"]. " - Price: " .$row["Price"].
        }
    }
    else {
        echo "Zero results";
    }

    $conn->close();
?>
