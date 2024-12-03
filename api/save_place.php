<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

$data = json_decode(file_get_contents("php://input"));

// Tambahkan logging untuk debugging
error_log("Data received: " . print_r($data, true));

if (
    !empty($data->name) &&
    !empty($data->address) &&
    !empty($data->facilities) &&
    !empty($data->capacity) &&
    !empty($data->price) &&
    !empty($data->account_number) &&
    !empty($data->campus) &&
    !empty($data->photo) &&
    !empty($data->user_id)
) {
    $place->name = $data->name;
    $place->address = $data->address;
    $place->facilities = $data->facilities;
    $place->capacity = $data->capacity;
    $place->price = $data->price;
    $place->account_number = $data->account_number;
    $place->campus_id = $data->campus;
    $place->photo = $data->photo;
    $place->user_id = $data->user_id;
    $place->status = 'Pending'; // Set status to Pending
    $place->created_at = date('Y-m-d H:i:s');
    $place->updated_at = date('Y-m-d H:i:s');

    if ($place->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Place was created."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create place."));
    }
} else {
    // Tambahkan logging untuk parameter yang hilang
    error_log("Missing parameters: " . 
        (empty($data->name) ? "name " : "") .
        (empty($data->address) ? "address " : "") .
        (empty($data->facilities) ? "facilities " : "") .
        (empty($data->capacity) ? "capacity " : "") .
        (empty($data->price) ? "price " : "") .
        (empty($data->account_number) ? "account_number " : "") .
        (empty($data->campus) ? "campus " : "") .
        (empty($data->photo) ? "photo " : "") .
        (empty($data->user_id) ? "user_id " : "")
    );
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create place. Data is incomplete."));
}
?>