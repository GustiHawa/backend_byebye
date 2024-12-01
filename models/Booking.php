<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $user_id;
    public $place_id;
    public $booking_date;
    public $number_of_people;
    public $status_id;
    public $total_price;
    public $payment_proof;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, place_id=:place_id, booking_date=:booking_date, number_of_people=:number_of_people, status_id=:status_id, total_price=:total_price, payment_proof=:payment_proof, created_at=:created_at, updated_at=:updated_at";
        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->place_id = htmlspecialchars(strip_tags($this->place_id));
        $this->booking_date = htmlspecialchars(strip_tags($this->booking_date));
        $this->number_of_people = htmlspecialchars(strip_tags($this->number_of_people));
        $this->status_id = htmlspecialchars(strip_tags($this->status_id));
        $this->total_price = htmlspecialchars(strip_tags($this->total_price));
        $this->payment_proof = htmlspecialchars(strip_tags($this->payment_proof));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":place_id", $this->place_id);
        $stmt->bindParam(":booking_date", $this->booking_date);
        $stmt->bindParam(":number_of_people", $this->number_of_people);
        $stmt->bindParam(":status_id", $this->status_id);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":payment_proof", $this->payment_proof);
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