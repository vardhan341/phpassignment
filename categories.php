<?php

include_once 'common.php';

$payload = file_get_contents('php://input');

$payload_data = [];
if(!empty($payload)){
    $payload_data = json_decode($payload, true);
}

switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
        require_once 'categories_get.php';
    break;
    case 'POST':
        require_once 'categories_insert.php';
    break;
    case 'PUT':
        require_once 'categories_update.php';
    break;
    case 'DELETE':
        require_once 'categories_delete.php';
    break;
    default:
}

output:
echo json_encode($result,JSON_NUMERIC_CHECK);
?>