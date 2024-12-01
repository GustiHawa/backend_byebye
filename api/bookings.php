<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
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

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    error_log("Data received: " . print_r($data, true)); // Debugging

    if (
        !empty($data->user_id) &&
        !empty($data->place_id) &&
        !empty($data->booking_date) &&
        !empty($data->number_of_people) &&
        !empty($data->status_id) &&
        !empty($data->total_price) &&
        !empty($data->payment_proof)
    ) {
        $booking->user_id = $data->user_id;
        $booking->place_id = $data->place_id;
        $booking->booking_date = $data->booking_date;
        $booking->number_of_people = $data->number_of_people;
        $booking->status_id = $data->status_id;
        $booking->total_price = $data->total_price;
        $booking->payment_proof = $data->payment_proof;
        $booking->created_at = date('Y-m-d H:i:s');
        $booking->updated_at = date('Y-m-d H:i:s');

        if ($booking->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Booking was created."));
        } else {
            error_log("Failed to create booking"); // Debugging
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create booking."));
        }
    } else {
        error_log("Incomplete data: " . print_r($data, true)); // Debugging
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create booking. Data is incomplete."));
    }
} elseif ($method == 'GET') {
    $stmt = $booking->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $bookings_arr = array();
        $bookings_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $booking_item = array(
                "id" => $id,
                "user_id" => $user_id,
                "place_id" => $place_id,
                "booking_date" => $booking_date,
                "number_of_people" => $number_of_people,
                "status_id" => $status_id,
                "total_price" => $total_price,
                "payment_proof" => $payment_proof,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($bookings_arr["records"], $booking_item);
        }
        http_response_code(200);
        echo json_encode($bookings_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No bookings found."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>