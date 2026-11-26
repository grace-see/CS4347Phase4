<?php

    require_once 'exit_with_error_func.php';

    $transaction = $_POST['TransactionID']; //note: optional, transactionID should be auto_increment
    $customer = is_null($_POST['CustomerID']) ? null : $_POST['CustomerID']; //can be null
    $date = $_POST['Date'];
    $total_cost = 0;

    $all_item_no = $_POST['Item_No'];
    $all_qty = $_POST['Sale_Quantity'];
    $size = sizeof($all_item_no);

    $item_exists = 1;

    $message = "ok";

    $conn = new mysqli("localhost", "root", "pass", "comet_apparel");

    if (!$conn) {
        die("Connection failed: "
            . mysqli_connect_error());
    }

    $duplicate_transaction = "SELECT * FROM sale WHERE TransactionID=? LIMIT 1";

    if ($stmt = $conn->prepare($duplicate_transaction)) {
        if ($stmt->bind_param("i", $transaction)) {
            $stmt->execute();
            $stmt->store_result();
            $transaction_exists = $stmt->num_rows;
            if ($transaction_exists > 0) {
                $conn->close();
                exit_with_error("A sale with this transaction ID already exists.");
            }
        } else {
            $conn->close();
            exit_with_error("Issue with adding clothing item.");
        }
        $stmt->close();
    }

    $check_item_exists = "SELECT * FROM Clothing_Item WHERE Item_No=? LIMIT 1";

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

    for ($i = 0; $i < $size; $i++) {
        if ($all_item_no[$i] == "")
            break;
        if ($stmt = $conn->prepare($check_item_exists)) {
            if ($stmt->bind_param("i", $all_item_no[$i])) {
                $stmt->execute();
                $stmt->store_result();
                $item_exists = $stmt->num_rows;
                if (!($item_exists > 0)) {
                    $conn->close();
                    exit_with_error("One or more items listed do not exist.");
                }
            }
        }
    }

    /***** CALCULATE TOTAL COST OF SALE *****/
    if ($item_exists > 0) {
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
            if ($stmt->bind_param("iisi", $transaction, $customer, $date, $total_cost)) {
                $stmt->execute();
            } else {
                $message = "Issue processing order.";
            }
            $stmt->close();
        }

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