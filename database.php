<?php
class Database {
    private $host = "localhost";
    private $db_name = "globalfly";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");

            // simple migration: add status and cancellation_reason to flights if missing
            try {
                $st = $this->conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'flights' AND COLUMN_NAME = 'status'");
                $st->execute([$this->db_name]);
                if ($st->fetchColumn() == 0) {
                    $this->conn->exec("ALTER TABLE flights ADD COLUMN status ENUM('active','cancelled') NOT NULL DEFAULT 'active'");
                }
                $st = $this->conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'flights' AND COLUMN_NAME = 'cancellation_reason'");
                $st->execute([$this->db_name]);
                if ($st->fetchColumn() == 0) {
                    $this->conn->exec("ALTER TABLE flights ADD COLUMN cancellation_reason VARCHAR(255) NOT NULL DEFAULT ''");
                }
            } catch (PDOException $e) {
                // migration failed (likely permissions) - ignore so app keeps working
            }
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
