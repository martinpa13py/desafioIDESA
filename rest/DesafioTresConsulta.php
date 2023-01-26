<?php
require_once 'base/DesafioTres.php';

$loteID = trim(stripslashes(htmlspecialchars($_GET['id_lote']))); // clean param
$response = array("status" => "success", "message" => "");
if (!is_numeric($loteID)) {
    header("HTTP/1.1 400 Bad Request");
    header('Content-Type: application/json');

    echo json_encode(array("message" => "Debe especificar in ID de lote"));
    exit;
}

try {
    $loteInfo = DesafioTres::getLoteInfo($loteID);
    $response['data'] = $loteInfo;
} catch (\Throwable $th) {
    $response['status'] = "failed";
    $response['message'] = $th->getMessage();
}

if ($response['status'] === "failed") {
    header("HTTP/1.1 500 Server Error");
    header('Content-Type: application/json');

    echo json_encode(array("message" => $response['message']));
    exit;
}

header('Content-Type: application/json');
echo json_encode(array("data" => $response['data']));
