<?php
class Report {
    private $conn;
    private $table_name = "reports";

    public $id;
    public $user_id;
    public $place_id;
    public $total_report;
    public $report_date;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, place_id=:place_id, total_report=:total_report, report_date=:report_date, created_at=:created_at, updated_at=:updated_at";
        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->place_id = htmlspecialchars(strip_tags($this->place_id));
        $this->total_report = htmlspecialchars(strip_tags($this->total_report));
        $this->report_date = htmlspecialchars(strip_tags($this->report_date));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":place_id", $this->place_id);
        $stmt->bindParam(":total_report", $this->total_report);
        $stmt->bindParam(":report_date", $this->report_date);
        $stmt->bindParam(":created_at", $this->created_at);
        $stmt->bindParam(":updated_at", $this->updated_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>