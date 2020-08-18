<?php

use MongoDB\Driver\Query;
use MongoDB\BSON\ObjectId;

include_once 'common.php';
try {
    include_once 'datab_management.php';
    $filter = [];

    if(isset($payload_data['category_id']) && !empty($payload_data['category_id'])){
        $id           = new ObjectId($payload_data['category_id']);
        $filter      = ['_id' => $id];
    }
    $query = new Query($filter, ['sort' => [ 'name' => 1], 'limit' => 5]);     

    $rows = $mng->executeQuery("assignment.Category", $query);
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
    goto output;
} catch (MongoDB\Driver\Exception\ConnectionException $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
    goto output;
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
    goto output;
} catch (\Exception $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
    goto output;
}

$data = [];
$i=0;
foreach ($rows as $row) {
    $data[$i]['id'] = (string) $row->_id;
    $data[$i]['name'] = $row->name;
    $i++;
}

if(empty($data)){
    http_response_code(204);
    return;
}
$result->data=$data;
$result->message='data found';

output:'';
?>