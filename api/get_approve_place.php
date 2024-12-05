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

$stmt = $place->readApproved(); // Memanggil metode readApproved
$num = $stmt->rowCount();

if ($num > 0) {
    $places_arr = array();
    $places_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $place_item = array(
            "id" => $id,
            "name" => $name,
            "address" => $address,
            "facilities" => $facilities,
            "capacity" => $capacity,
            "price" => $price,
            "photo" => $photo,
            "user_id" => $user_id,
            "campus_id" => $campus_id,
            "account_number" => $account_number,
            "status" => $status,
            "created_at" => $created_at,
            "updated_at" => $updated_at
        );

        array_push($places_arr["records"], $place_item);
    }

    http_response_code(200);
    echo json_encode($places_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No places found."));
}
?>