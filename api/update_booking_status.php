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
include_once '../models/Booking.php';

$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && !empty($data->status)) {
    $booking->id = $data->id;
    $booking->status = $data->status;

    if ($booking->updateStatus()) {
        http_response_code(200);
        echo json_encode(array("message" => "Booking status updated."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update booking status."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update booking status. Data is incomplete."));
}
?>