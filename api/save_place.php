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

// Terima data JSON
$data = json_decode(file_get_contents("php://input"));

// Debug log untuk memastikan data diterima
error_log("Data received: " . json_encode($data));

// Validasi data secara mendalam
$requiredFields = ['name', 'address', 'facilities', 'capacity', 'price', 'account_number', 'campus_id', 'photo', 'user_id'];
$missingFields = array_filter($requiredFields, fn($field) => empty($data->$field));

if (!empty($missingFields)) {
    http_response_code(400);
    echo json_encode([
        "message" => "Unable to create place. Missing fields: " . implode(', ', $missingFields),
    ]);
    exit;
}

// Isi properti Place
$place->name = $data->name;
$place->address = $data->address;
$place->facilities = $data->facilities;
$place->capacity = $data->capacity;
$place->price = $data->price;
$place->account_number = $data->account_number;
$place->campus_id = $data->campus_id;
$place->photo = $data->photo;
$place->user_id = $data->user_id;
$place->status = 'Pending';
$place->created_at = date('Y-m-d H:i:s');
$place->updated_at = date('Y-m-d H:i:s');

// Buat data
if ($place->create()) {
    http_response_code(201);
    echo json_encode(["message" => "Place was created."]);
} else {
    http_response_code(503);
    echo json_encode(["message" => "Unable to create place."]);
}
?>
