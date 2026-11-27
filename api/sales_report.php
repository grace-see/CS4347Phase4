<?php

    $order = $_GET['order'];
    $from = $_GET['from'];
    $to = $_GET['to'];
    $limit = $_GET['limit'];

    $sql = "";
    $message = "";
    $rank = 0;
    $data = [];

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    if ($order == "desc") {
        $sql = "SELECT Item_No, SUM(Sales_Line.Sale_Quantity) AS Units_Sold
                FROM Clothing_Line NATURAL JOIN Sales_Line NATURAL JOIN Sale
                WHERE Date BETWEEN ? AND ?
                GROUP BY Item_No
                ORDER BY Units_Sold DESC
                LIMIT ?";
    } else if ($order == "asc") {
        $sql = "SELECT Item_No, SUM(Sales_Line.Sale_Quantity) AS Units_Sold
                FROM Clothing_Line NATURAL JOIN Sales_Line NATURAL JOIN Sale
                WHERE Date BETWEEN ? AND ?
                GROUP BY Item_No
                ORDER BY Units_Sold ASC
                LIMIT ?";
    }
    
    if ($stmt = $conn->prepare($sql)) {
        if ($stmt->bind_param("ssi", $from, $to, $limit)) {
            $stmt->execute();
            $stmt->store_result();
            $num_of_rows = $stmt->num_rows;
            if ($num_of_rows > 0) {
                $message = "ok";
                $stmt->bind_result($item_no, $units_sold);
                while ($stmt->fetch()) {
                    $item_details = "SELECT clc.CL_Name, Size, Material, Price, Current_Stock, s.Date
                                     FROM Clothing_Line
                                     NATURAL JOIN C_Line_Contains AS clc
                                     NATURAL JOIN Clothing_Item
                                     NATURAL JOIN Sales_Line
                                     NATURAL JOIN Sale AS s
                                     WHERE Item_No=".$item_no."";
                    $result = $conn->query($item_details);
                    while ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
                        if (array_key_exists($item_no, $data)) {
                            $data[$item_no]['CL_Name'] = $data[$item_no]['CL_Name'] . ", " . $row['CL_Name'];
                        } else {
                            $data[$item_no] = array(
                                "Rank" => ++$rank,
                                "CL_Name" => $row['CL_Name'],
                                "Size" => $row['Size'],
                                "Material" => $row['Material'],
                                "Price" => $row['Price'],
                                "Units_Sold" => $units_sold,
                                "Current_Stock" => $row['Current_Stock']
                            );
                        }
                    }
                }
            } else {
                $message = "No results found.";
            }
        } else {
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message, "data" => $data);
    echo json_encode($res);
?>