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
include_once '../models/Report.php';

$database = new Database();
$db = $database->getConnection();

$report = new Report($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (
        !empty($data->user_id) &&
        !empty($data->place_id) &&
        !empty($data->total_report) &&
        !empty($data->report_date)
    ) {
        $report->user_id = $data->user_id;
        $report->place_id = $data->place_id;
        $report->total_report = $data->total_report;
        $report->report_date = $data->report_date;
        $report->created_at = date('Y-m-d H:i:s');
        $report->updated_at = date('Y-m-d H:i:s');

        if ($report->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Report was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create report."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Unable to create report. Data is incomplete."));
    }
} elseif ($method == 'GET') {
    $stmt = $report->read();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $reports_arr = array();
        $reports_arr["records"] = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $report_item = array(
                "id" => $id,
                "user_id" => $user_id,
                "place_id" => $place_id,
                "total_report" => $total_report,
                "report_date" => $report_date,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            );
            array_push($reports_arr["records"], $report_item);
        }
        http_response_code(200);
        echo json_encode($reports_arr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No reports found."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?>