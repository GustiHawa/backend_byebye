<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT 
            u.name as nama_customer, 
            b.booking_date as tanggal, 
            b.number_of_people as jumlah_kursi, 
            b.total_price as harga 
        FROM bookings b
        INNER JOIN users u ON b.user_id = u.id
        ORDER BY b.booking_date DESC;";

$stmt = $db->prepare($query);
if (!$stmt) {
    echo json_encode(["error" => $db->errorInfo()]);
}
$stmt->execute();

$stmt = $db->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();

try {
    $stmt = $db->prepare($query);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $history_arr = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $history_item = array(
                "nama_customer" => $row['nama_customer'],
                "tanggal" => $row['tanggal'],
                "jumlah_kursi" => $row['jumlah_kursi'],
                "harga" => $row['harga']
            );
            array_push($history_arr, $history_item);
        }
        http_response_code(200);
        echo json_encode($history_arr);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "No bookings found."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>