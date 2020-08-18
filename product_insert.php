<?php

if (!isset($payload_data['sub_category_id']) || empty($payload_data['sub_category_id'])) {
    http_response_code(400);
    $result->message = 'Sub Category Id is mandatory';
    goto output;
}
if (!isset($payload_data['product_name']) || empty($payload_data['product_name'])) {
    http_response_code(400);
    $result->message = 'Product name is mandatory';
    goto output;
}

use MongoDB\Driver\Query;
use MongoDB\BSON\ObjectId;

try {
    include_once 'datab_management.php';
    $filter = [];
    $id = new ObjectId($payload_data['sub_category_id']);
    $filter = ['_id' => $id];
    $query = new Query($filter, ['sort' => ['name' => 1], 'limit' => 5]);
    $rows = $mng->executeQuery("assignment.SubCategory", $query);
    $i = 0;
    foreach ($rows as $row) {
        $i++;
        break;
    }
    if ($i < 1) {
        http_response_code(404);
        $result->message = 'No Sub Category were found';
        goto output;
    }

    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->insert(['name' => $payload_data['product_name'],'SubCategoryId'=>$payload_data['sub_category_id']]);
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $result = $mng->executeBulkWrite('assignment.Product', $bulk, $writeConcern);
    if ($result->getInsertedCount() > 0) {
        http_response_code(200);
        $result->message = "success";
        goto output;
    } else {
        http_response_code(500);
        $result->message = "failed";
        goto output;
    }
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    http_response_code(500);
    $result->message = "Exception:" . $e->getMessage();
    goto output;
} catch (MongoDB\Driver\Exception\ConnectionException $e) {
    http_response_code(500);
    $result->message = "Exception:" . $e->getMessage();
    goto output;
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    http_response_code(500);
    $result->message = "Exception:" . $e->getMessage();
    goto output;
} catch (MongoDB\Driver\Exception\InvalidArgumentException $e) {
    http_response_code(500);
    $result->message = "Invalid Sub Category Id Provided";
    goto output;
} catch (\Exception $e) {
    http_response_code(500);
    var_dump($e);
    $result->message = "Exception:" . $e->getMessage();
    goto output;
}

output:'';
