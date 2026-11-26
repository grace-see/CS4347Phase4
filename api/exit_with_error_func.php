<?php

    function exit_with_error($message) {
        $res = array("message" => $message);
        header('Content-Type: application/json; charset=utf-8');
        http_response_code('400');
        echo json_encode($res);
        exit();
    }

?>