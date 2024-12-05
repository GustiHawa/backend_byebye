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

    // Validasi input
    if (
        isset($data->user_id) &&
        isset($data->place_id) &&
        isset($data->booking_date) &&
        isset($data->number_of_people) &&
        isset($data->status_id) &&
        isset($data->total_price) &&
        isset($data->payment_proof)
    ) {
        $booking->user_id = intval($data->user_id);
        $booking->place_id = intval($data->place_id);
        $booking->booking_date = $data->booking_date;
        $booking->number_of_people = intval($data->number_of_people);
        $booking->status_id = intval($data->status_id);
        $booking->total_price = floatval($data->total_price);
        $booking->payment_proof = $data->payment_proof;
        $booking->created_at = date('Y-m-d H:i:s');
        $booking->updated_at = date('Y-m-d H:i:s');

        if ($booking->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Booking was created."));
        } else {
            error_log("Failed to create booking: " . $stmt->errorInfo()[2]); // Debugging
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create booking."));
        }
    } else {
        // Tambahkan logging untuk parameter yang hilang
        error_log("Missing parameters: " . 
            (!isset($data->user_id) ? "user_id " : "") .
            (!isset($data->place_id) ? "place_id " : "") .
            (!isset($data->booking_date) ? "booking_date " : "") .
            (!isset($data->number_of_people) ? "number_of_people " : "") .
            (!isset($data->status_id) ? "status_id " : "") .
            (!isset($data->total_price) ? "total_price " : "") .
            (!isset($data->payment_proof) ? "payment_proof " : "")
        );
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create booking. Data is incomplete."));
    }
} elseif ($method == 'GET') {
    // Mendukung pagination
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = ($page - 1) * $limit;

    if (isset($_GET['user_id'])) {
        $booking->user_id = intval($_GET['user_id']);
        $stmt = $booking->readByUser($limit, $offset);
    } elseif (isset($_GET['place_id'])) {
        $booking->place_id = intval($_GET['place_id']);
        $stmt = $booking->readByPlace($limit, $offset);
    } else {
        $stmt = $booking->readAll($limit, $offset);
    }

    $num = $stmt->rowCount();

    if ($num > 0) {
        $bookings_arr = array();
        $bookings_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user_id = isset($row['user_id']) ? $row['user_id'] : null;
            $place_id = isset($row['place_id']) ? $row['place_id'] : null;
            extract($row);

            $booking_item = array(
                "id" => $id,
                "user_id" => $user_id,
                "place_id" => $place_id,
                "booking_date" => $booking_date,
                "number_of_people" => $number_of_people,
                "total_price" => $total_price,
                "status_id" => $status_id ?? '', // Pastikan tipe data sesuai
                "payment_proof" => $payment_proof ?? '', // Pastikan tipe data sesuai
                "created_at" => $created_at ?? '',
                "updated_at" => $updated_at ?? ''
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