<?php

    $status = $_GET['status'];
    $cl_name = $_GET['CL_Name'];
    $gender = $_GET['Gender'];
    $sort = $_GET['sort'];

    $conn = new mysqli("localhost", "root", "codeTimSeQuElAB10101011!)!)!)!!", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    if ($sort = "stock_asc") {
        $sortBy = "Current_Stock ASC";
    }
    else if ($sort = "stock_desc") {
        $sortBy = "Current_Stock DESC";
    }
    else if ($sort = "price_asc") {
        $sortBy = "Current_Stock ASC";
    }
    else if ($sort = "price_desc") {
        $sortBy = "Current_Stock DESC";
    }

    $name_empty = false;
    if (empty($cl_name)) {
        $name_empty = true;
    }

    $gender_empty = false;
    if ($gender = "") {
        $gender_empty = true;
    }   

    if ($status = "all") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains AS t1) NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR 1) AND (Cl_Name = '".$cl_name."' OR ($name_empty))
                ORDER BY ".$sortBy."";
    }
    else if ($status = "in") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$cl_name."' OR (empty($cl_name)) AND Current_Stock > 0
                ORDER BY ".$sortBy."";
    }
    else if ($status = "out") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$cl_name."' OR (empty($cl_name)) AND Current_Stock = 0
                ORDER BY ".$sortBy."";
    }
    else if ($status = "discontinued") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR 1) AND (Cl_Name = '".$cl_name."' OR ($name_empty)) AND Last_Available IS NOT NULL
                ORDER BY ".$sortBy. "";
    }


    $result = $conn->query($sql);

    echo "status " .$status. "name " .$cl_name. "gender " .$gender. "sort " .$sort. " ";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Item No: " .$row["Item_No"]. " - Size: " .$row["Size"]. " - Material: " .$row["Material"]. " - Gender: " .$row["Gender"]. " - Current Stock: " .$row["Current_Stock"]. " - Price: " .$row["Price"]."<br>";
        }
    }
    else {
        echo "Zero results";
    }

    $conn->close();
?>
