<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../config/database.php';
include_once '../models/Place.php';

$database = new Database();
$db = $database->getConnection();

$place = new Place($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $place->id = $_GET['id'];
    $place->readOne();

    if ($place->name != null) {
        $place_arr = array(
            "id" => $place->id,
            "name" => $place->name,
            "address" => $place->address,
            "facilities" => $place->facilities,
            "capacity" => $place->capacity,
            "price" => $place->price,
            "photo" => $place->photo,
            "user_id" => $place->user_id,
            "created_at" => $place->created_at,
            "updated_at" => $place->updated_at
        );

        http_response_code(200);
        echo json_encode($place_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Place not found."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid request."));
}
?>