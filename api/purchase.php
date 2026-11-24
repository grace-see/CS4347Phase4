<?php

    $customer = $_POST['CustomerID'];
    $all_item_no = $_POST['Item_No'];
    $all_qty = $_POST['Sale_Quantity'];

    $size = sizeof($all_item_no);
    $cost = 0;
    date_default_timezone_set('America/Los_Angeles');
    $date = date('Y-m-d', time());

    $item_exists = 1;

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $check_item_exists = "SELECT * FROM Clothing_Item WHERE Item_No=? LIMIT 1";

    $most_recent_entry = "SELECT TransactionID FROM Sale ORDER BY TransactionID DESC LIMIT 1";

    $get_item_cost = "SELECT Price
                      FROM Clothing_Item
                      WHERE Item_No=?";

    $insert_sale = "INSERT INTO Sale (CustomerID, Date, Cost)
                    VALUES (?, ?, ?)";
    
    $insert_sales_line = "INSERT INTO Sales_Line (TransactionID, Item_No, Sale_Quantity)
                          VALUES (?, ?, ?)";
    
    $deduct_item_qty = "UPDATE Clothing_Item
                        SET Current_Stock = Current_Stock - ?
                        WHERE Item_No=?";

    for ($i = 0; $i < $size; $i++) {
        if ($all_item_no[$i] == "")
            break;
        if ($stmt = $conn->prepare($check_item_exists)) {
            if ($stmt->bind_param("i", $all_item_no[$i])) {
                $stmt->execute();
                $stmt->store_result();
                $item_exists = $stmt->num_rows;
                if (!($item_exists > 0))
                    break;
            }
        }
    }

    if ($item_exists > 0) {
        /***** CALCULATE TOTAL COST OF SALE *****/
        $total_cost = 0;

        for ($i = 0; $i < $size; $i++) {
            if ($stmt = $conn->prepare($get_item_cost)) {
                if ($stmt->bind_param("i", $all_item_no[$i])) {
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($item_price);
                    $stmt->fetch();
                    $total_cost += ($item_price * floatval($all_qty[$i]));
                } else {
                    $message = "Issue processing order.";
                }
                $stmt->close();
            }
        }
        
        if ($stmt = $conn->prepare($insert_sale)) {
            if ($stmt->bind_param("isi", $customer, $date, $total_cost)) {
                $stmt->execute();
            } else {
                $message = "Issue processing order.";
            }
            $stmt->close();
        }

        //get id to insert in sales_line
        $result = $conn->query($most_recent_entry);
        $row = $result->fetch_assoc();
        $transaction = $row['TransactionID'];

        if ($stmt = $conn->prepare($insert_sales_line)) {
            for ($i = 0; $i < $size; $i++) {
                if ($stmt->bind_param("iii", $transaction, $all_item_no[$i], $all_qty[$i])) {
                    $stmt->execute();
                } else {
                    $message = "Issue processing order.";
                }
            }
            $stmt->close();
        }

        if ($stmt = $conn->prepare($deduct_item_qty)) {
            for ($i = 0; $i < $size; $i++) {
                if ($stmt->bind_param("ii", $all_qty[$i], $all_item_no[$i])) {
                    $stmt->execute();
                } else {
                    $message = "Issue processing order.";
                }
            }
            $stmt->close();
        }
    } else {
        $message = "One or more items listed do not exist.";
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);

?>