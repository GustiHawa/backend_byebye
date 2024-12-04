<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    // Properti Booking
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

    // Konstruktor
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Fungsi untuk membuat booking baru
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id = :user_id, place_id = :place_id, booking_date = :booking_date, 
                      number_of_people = :number_of_people, status_id = :status_id, 
                      total_price = :total_price, payment_proof = :payment_proof, 
                      created_at = :created_at, updated_at = :updated_at";

        $stmt = $this->conn->prepare($query);

        // Sanitasi data
        $this->sanitizeProperties();

        // Bind parameter
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":place_id", $this->place_id);
        $stmt->bindParam(":booking_date", $this->booking_date);
        $stmt->bindParam(":number_of_people", $this->number_of_people);
        $stmt->bindParam(":status_id", $this->status_id);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":payment_proof", $this->payment_proof);
        $stmt->bindParam(":created_at", $this->created_at);
        $stmt->bindParam(":updated_at", $this->updated_at);

        // Eksekusi query
        if ($stmt->execute()) {
            return true;
        }

        error_log("Error executing query: " . $stmt->errorInfo()[2]); // Log error
        return false;
    }

    /**
     * Fungsi untuk membaca booking berdasarkan user
     */
    public function readByUser($limit, $offset) {
        $query = "SELECT b.id, p.name as place_name, b.booking_date, p.address, b.number_of_people, 
                         b.total_price, s.name as status, p.photo
                  FROM " . $this->table_name . " b
                  JOIN places p ON b.place_id = p.id
                  JOIN statuses s ON b.status_id = s.id
                  WHERE b.user_id = :user_id
                  ORDER BY b.booking_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    /**
     * Fungsi untuk membaca booking berdasarkan tempat
     */
    public function readByPlace($limit, $offset) {
        $query = "SELECT b.id, u.name as user_name, b.booking_date, b.number_of_people, 
                         b.total_price, s.name as status, b.payment_proof
                  FROM " . $this->table_name . " b
                  JOIN users u ON b.user_id = u.id
                  JOIN statuses s ON b.status_id = s.id
                  WHERE b.place_id = :place_id
                  ORDER BY b.booking_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":place_id", $this->place_id, PDO::PARAM_INT);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    /**
     * Fungsi untuk membaca semua booking
     */
    public function readAll($limit, $offset) {
        $query = "SELECT b.id, b.user_id, b.place_id, b.booking_date, b.number_of_people, 
                         b.total_price, s.name as status, b.payment_proof, b.created_at, b.updated_at
                  FROM " . $this->table_name . " b
                  LEFT JOIN statuses s ON b.status_id = s.id
                  ORDER BY b.booking_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    /**
     * Fungsi untuk memperbarui status booking
     */
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status_id = :status_id WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitasi data
        $this->sanitizeProperties(['status_id', 'id']);

        $stmt->bindParam(":status_id", $this->status_id);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        error_log("Error updating status: " . $stmt->errorInfo()[2]); // Log error
        return false;
    }

    /**
     * Sanitasi properti untuk menghindari data tidak aman
     */
    private function sanitizeProperties($fields = null) {
        $properties = $fields ? $fields : get_object_vars($this);
        foreach ($properties as $field) {
            if (property_exists($this, $field)) {
                $this->$field = htmlspecialchars(strip_tags($this->$field));
            }
        }
    }
}
?>
