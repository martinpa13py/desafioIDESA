<?php
require_once 'base/DesafioTres.php';

$loteID = trim(stripslashes(htmlspecialchars($_GET['id_lote']))); // limpiar parametro para prevenir injeccion de codigo
$response = array("status" => "success", "message" => ""); // estructura de manejo interno de datos dentro del ciclo

if (!is_numeric($loteID)) {
    // retorna si el parametro requerido no es numerico o no existe
    header("HTTP/1.1 400 Bad Request");
    header('Content-Type: application/json');

    echo json_encode(array("message" => "Debe especificar un ID de lote"));
    exit;
}

try {
    $loteInfo = DesafioTres::getListOfLoteDebtsClassified($loteID);
    $response['data'] = $loteInfo;
} catch (\Throwable $th) {
    $response['status'] = "failed";
    $response['message'] = $th->getMessage();
}
// maneja si hubo error al obtener datos
if ($response['status'] === "failed") {
    header("HTTP/1.1 500 Server Error");
    header('Content-Type: application/json');

    echo json_encode(array("message" => $response['message']));
    exit;
}

header('Content-Type: application/json');
echo json_encode(array("data" => $response['data']));

/* Obs: Se podria estructurar mejor la carpeta rest dependiento de la complejidad de la API */
