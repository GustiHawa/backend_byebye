<?php
class Place {
    private $conn;
    private $table_name = "places";

    public $id;
    public $name;
    public $address;
    public $facilities;
    public $capacity;
    public $price;
    public $photo;
    public $user_id;
    public $campus_id;
    public $account_number;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, address=:address, facilities=:facilities, capacity=:capacity, price=:price, photo=:photo, user_id=:user_id, campus_id=:campus_id, account_number=:account_number, status='Pending', created_at=:created_at, updated_at=:updated_at";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->facilities = htmlspecialchars(strip_tags($this->facilities));
        $this->capacity = htmlspecialchars(strip_tags($this->capacity));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->photo = htmlspecialchars(strip_tags($this->photo));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->campus_id = htmlspecialchars(strip_tags($this->campus_id));
        $this->account_number = htmlspecialchars(strip_tags($this->account_number));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":facilities", $this->facilities);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":photo", $this->photo);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":campus_id", $this->campus_id);
        $stmt->bindParam(":account_number", $this->account_number);
        $stmt->bindParam(":created_at", $this->created_at);
        $stmt->bindParam(":updated_at", $this->updated_at);

        if ($stmt->execute()) {
            return true;
        }

        error_log("Error executing query: " . $stmt->errorInfo()[2]); // Debugging
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByCampus() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE campus_id = :campus_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":campus_id", $this->campus_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->address = $row['address'];
            $this->facilities = $row['facilities'];
            $this->capacity = $row['capacity'];
            $this->price = $row['price'];
            $this->photo = $row['photo'];
            $this->user_id = $row['user_id'];
            $this->campus_id = $row['campus_id'];
            $this->account_number = $row['account_number'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
    }

    public function readAll($search = null) {
        $query = "SELECT * FROM " . $this->table_name;
        if ($search) {
            $query .= " WHERE name LIKE :search OR address LIKE :search OR facilities LIKE :search";
        }
        $query .= " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        if ($search) {
            $search = "%{$search}%";
            $stmt->bindParam(":search", $search);
        }
        $stmt->execute();
        return $stmt;
    }

    public function readPending() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'Pending' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readApproved() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'Approved' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>