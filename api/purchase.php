<?php

    $customer = $_POST['CustomerID'];
    $all_item_no = $_POST['Item_No'];
    $all_qty = $_POST['Sale_Quantity'];

    $size = sizeof($all_item_no);
    $cost = 0;
    date_default_timezone_set('America/Los_Angeles');
    $date = date('Y-m-d', time());

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $get_item_cost = "SELECT Price
                      FROM Clothing_Item
                      WHERE Item_No=?";

    $insert_sale = "INSERT INTO Sale
                    VALUES (?, ?, ?, ?)"; //transactionid, customerid, cost
    
    $insert_sales_line = "INSERT INTO Sales_Line
                          VALUES (?, ?, ?)"; //item_no, transactionID, qty
    
    $deduct_item_qty = "UPDATE Clothing_Item
                        SET Current_Stock = Current_Stock - ?
                        WHERE Item_No=?";

    /***** CALCULATE TOTAL COST OF SALE *****/

    for ($i = 0; $i < $size; $i++) {
        if ($stmt = $conn->prepare($get_item_cost)) {
            if ($stmt->bind_param("i", $all_item_no[$i])) {
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($item_price);
                $stmt->fetch();
                $total_cost += ($item_price * $all_qty[$i]);
            } else {
                $message = "Issue processing order.";
            }
            $stmt->close();
        }
    }
    
    if ($stmt = $conn->prepare($insert_sale)) {
        if ($stmt->bind_param("iisi", $transaction, $customer, $date, $total_cost)) {
            $stmt->execute();
        } else {
            $message = "Issue processing order.";
        }
        $stmt->close();
    }

    if ($stmt = $conn->prepare($insert_sales_line)) {
        for ($i = 0; $i < $size; $i++) {
            if ($stmt->bind_param("iii", $transaction, $item_no[$i], $qty[$i])) {
                $stmt->execute();
            } else {
                $message = "Issue processing order.";
            }
        }
        $stmt->close();
    }

    if ($stmt = $conn->prepare($deduct_item_qty)) {
        for ($i = 0; $i < $size; $i++) {
            if ($stmt->bind_param("ii", $qty[$i], $item_no[$i])) {
                $stmt->execute();
            } else {
                $message = "Issue processing order.";
            }
        }
        $stmt->close();
    }

    $conn->close();

    header('Content-Type: application/json; charset=utf-8');
    $res = array("message" => $message);
    echo json_encode($res);

?>