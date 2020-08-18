<?php

if (!isset($payload_data['category_name']) || empty($payload_data['category_name'])) {
    http_response_code(400);
    $result->message = 'category name is mandatory';
    goto output;
}
try {
    include_once 'datab_management.php';
    $filter = [];

    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->insert(['name' => $payload_data['category_name']]);
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
    $result = $mng->executeBulkWrite('assignment.Category', $bulk, $writeConcern);
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
} catch (\Exception $e) {
    http_response_code(500);
    $result->message = "Exception:" . $e->getMessage();
    goto output;
}

output:'';
