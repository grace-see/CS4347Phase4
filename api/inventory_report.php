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

    $name_empty = false;
    if (empty($cl_name)) {
        $name_empty = true;
    }

    $gender_empty = false;
    if ($gender = "") {
        $gender_empty = true;
    }   

    if ($status = "all") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains AS t1) NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR 1) AND (Cl_Name = '".$cl_name."' OR ($name_empty))
                ORDER BY ".$sortBy."";
    }
    else if ($status = "in") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$cl_name."' OR (empty($cl_name)) AND Current_Stock > 0
                ORDER BY ".$sortBy."";
    }
    else if ($status = "out") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE gender = '".$gender."' AND (Cl_Name = '".$cl_name."' OR (empty($cl_name)) AND Current_Stock = 0
                ORDER BY ".$sortBy."";
    }
    else if ($status = "discontinued") {
        $sql = "SELECT Item_No, Size, Material, Gender, Current_Stock, Price, CL_Name, Last_Available
                FROM (Clothing_Line NATURAL JOIN C_Line_Contains) AS t1 NATURAL JOIN Clothing_Item
                WHERE (gender = '".$gender."' OR 1) AND (Cl_Name = '".$cl_name."' OR ($name_empty)) AND Last_Available IS NOT NULL
                ORDER BY ".$sortBy. "";
    }


    $result = $conn->query($sql);

    //echo "status " .$status. "name " .$cl_name. "gender " .$gender. "sort " .$sort. " ";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // need to compute based on Clothing_Line.LastAvailable and Current_Stock
            $current_status = "In Stock"; 
            if (!is_null($row['Last_Available'])) {
                //update logic to also include specify that date should be earlier than today
                $current_status = "Discontinued";
            } else if ($row['Current_Stock'] == 0) {
                $current_status = "Out of Stock";
            }
            array_push($data, array(
                "Item_No" => $row['Item_No'],
                "CL_Name" => $row['CL_Name'],
                "Gender" => $row['Gender'],
                "Size" => $row['Size'],
                "Material" => $row['Material'],
                "Price" => $row['Price'],
                "Current_Stock" => $row['Current_Stock'],
                "Current_Status" => $current_status
            ));
        }
        //echo $domDocument->saveHTML();
        //header("location: inventory_report.html");
        $message = "ok";
    }
    else {
        //echo "No results found.";
        $message = "No results found.";
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message, "data" => $data);
    echo json_encode($res);
?>
