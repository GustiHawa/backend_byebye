<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $place->read();
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
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->name) &&
        !empty($data->address) &&
        !empty($data->user_id)
    ) {
        $place->name = $data->name;
        $place->address = $data->address;
        $place->facilities = $data->facilities;
        $place->capacity = $data->capacity;
        $place->price = $data->price;
        $place->photo = $data->photo;
        $place->user_id = $data->user_id;
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
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create place. Data is incomplete."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>