<?php

try {
    include_once 'datab_management.php';
    $id           = new \MongoDB\BSON\ObjectId("5f3ad4199a2bf8307a778686");
    $filter      = ['_id' => $id];
    $query = new MongoDB\Driver\Query([], ['sort' => [ 'name' => 1], 'limit' => 5]);     

    $rows = $mng->executeQuery("assignment.SubCategory", $query);
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
} catch (MongoDB\Driver\Exception\AuthenticationException $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
} catch (MongoDB\Driver\Exception\ConnectionException $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
} catch (\Exception $e) {
    http_response_code(500);
    $result->message="Exception:". $e->getMessage();
}

output:'';
?>