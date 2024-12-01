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
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, address=:address, facilities=:facilities, capacity=:capacity, price=:price, photo=:photo, user_id=:user_id, created_at=:created_at, updated_at=:updated_at";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->facilities = htmlspecialchars(strip_tags($this->facilities));
        $this->capacity = htmlspecialchars(strip_tags($this->capacity));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->photo = htmlspecialchars(strip_tags($this->photo));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":facilities", $this->facilities);
        $stmt->bindParam(":capacity", $this->capacity);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":photo", $this->photo);
        $stmt->bindParam(":user_id", $this->user_id);
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
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        } else {
            $this->name = null;
        }
    }
}
?>