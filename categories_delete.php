<?php

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Query;

if (!isset($payload_data['category_id']) || empty($payload_data['category_id'])) {
    http_response_code(400);
    $result->message = 'Category id is mandatory';
    goto output;
}

try {
    include_once 'datab_management.php';
    $cat_filter = [];
    $id = new ObjectId($payload_data['category_id']);
    $cat_filter = ['_id' => $id];
    $query = new Query($cat_filter, ['sort' => ['name' => 1], 'limit' => 5]);
    $rows = $mng->executeQuery("assignment.Category", $query);
    $i = 0;
    foreach ($rows as $row) {
        $i++;
        break;
    }
    if ($i < 1) {
        http_response_code(404);
        $result->message = 'No category were found';
        goto output;
    }

    $filter = [];
    $filter = ['CategoryId' => $payload_data['category_id']];
    $query = new Query($filter, ['sort' => ['name' => 1], 'limit' => 5]);
    $rows = $mng->executeQuery("assignment.SubCategory", $query);
    $i = 0;
    foreach ($rows as $row) {
        $i++;
        break;
    }
    if ($i > 0) {
        http_response_code(412);
        $result->message = 'Sub Categories were exists with this Category';
        goto output;
    }

    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->delete($cat_filter);
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $result = $mng->executeBulkWrite('assignment.Category', $bulk, $writeConcern);
    if ($result->getDeletedCount() > 0) {
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
    $result->message = "Invalid Category Id Provided";
    goto output;
} catch (\Exception $e) {
    http_response_code(500);
    $result->message = "Exception:" . $e->getMessage();
    goto output;
}

output:'';
