<?php

use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Query;

if (!isset($payload_data['category_name']) || empty($payload_data['category_name'])) {
    http_response_code(400);
    $result->message = 'category name is mandatory';
    goto output;
}

if (!isset($payload_data['category_id']) || empty($payload_data['category_id'])) {
    http_response_code(400);
    $result->message = 'category id is mandatory';
    goto output;
}

try {
    include_once 'datab_management.php';
    $filter = [];
    $id = new ObjectId($payload_data['category_id']);
    $filter = ['_id' => $id];
    $query = new Query($filter, ['sort' => ['name' => 1], 'limit' => 5]);
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
    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update($filter, ['name' => $payload_data['category_name']]);
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $result = $mng->executeBulkWrite('assignment.Category', $bulk, $writeConcern);
    if ($result->getModifiedCount() > 0) {
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
