<?php
    $status = $_GET['status'];
    $cl_name = $_GET['CL_Name'];
    $gender = $_GET['Gender'];
    $sort = $_GET['sort'];

    $data = [];
    $message = "";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

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

    $name_empty = 0;
    if (empty($cl_name)) {
        $name_empty = 1;
    }

    $gender_empty = 0;
    if (empty($gender)) {
        $gender_empty = 1;
    }   

    if ($status == "all") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains AS t1) NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR ($gender_empty)) AND (Cl_Name LIKE '".$cl_name."' OR ($name_empty))
                ORDER BY ".$sortBy."";
    }
    else if ($status == "in") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains AS t1) NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR ($gender_empty)) AND (Cl_Name LIKE '".$cl_name."' OR ($name_empty)) AND (Current_Stock > 0)
                ORDER BY ".$sortBy."";
    }
    else if ($status == "out") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains AS t1) NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR ($gender_empty)) AND (Cl_Name LIKE '".$cl_name."' OR ($name_empty)) AND (Current_Stock = 0)
                ORDER BY ".$sortBy."";
    }
    else if ($status == "discontinued") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains AS t1) NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR ($gender_empty)) AND (Cl_Name LIKE '".$cl_name."' OR ($name_empty)) AND (Last_Available IS NOT NULL)
                ORDER BY ".$sortBy. "";
    }


    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $current_status = "In Stock"; 
            if (!is_null($row['Last_Available'])) {
                $current_status = "Discontinued";
            } else if ($row['Current_Stock'] == 0) {
                $current_status = "Out of Stock";
            }
            if (array_key_exists($row['Item_No'], $data)) {
                $data[$row['Item_No']]['CL_Name'] = $data[$row['Item_No']]['CL_Name'] . ", " . $row['CL_Name'];
            } else {
                $data[$row['Item_No']] = array(
                    "CL_Name" => $row['CL_Name'],
                    "Gender" => $row['Gender'],
                    "Size" => $row['Size'],
                    "Material" => $row['Material'],
                    "Price" => $row['Price'],
                    "Current_Stock" => $row['Current_Stock'],
                    "Current_Status" => $current_status
                );
            }
        }
        $message = "ok";
    }
    else {
        $message = "No results found.";
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message, "data" => $data);
    echo json_encode($res);
?>
