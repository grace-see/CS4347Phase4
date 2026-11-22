<?php

    $transaction = $_POST['TransactionID']; //note: optional, transactionID should be auto_increment
    $customer = is_null($_POST['CustomerID']) ? null : $_POST['CustomerID']; //can be null
    $date = $_POST['Date'];
    $total_cost = 0;

    $all_item_no = $_POST['Item_No'];
    $all_qty = $_POST['Sale_Quantity'];
    $size = sizeof($all_item_no);

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
                echo "Parameter binding failed.";
            }
            $stmt->close();
        }
    }
    
    if ($stmt = $conn->prepare($insert_sale)) {
        if ($stmt->bind_param("iisi", $transaction, $customer, $date, $total_cost)) {
            $stmt->execute();
        } else {
            echo "Parameter binding failed.";
        }
        $stmt->close();
    }

    if ($stmt = $conn->prepare($insert_sales_line)) {
        for ($i = 0; $i < $size; $i++) {
            if ($stmt->bind_param("iii", $transaction, $item_no[$i], $qty[$i])) {
                $stmt->execute();
            } else {
                echo "Parameter binding failed.";
            }
        }
        $stmt->close();
    }

    $conn->close();

?>