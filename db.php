<?php



class DB
{
    //Set the host in most cases it is the localhost
    private $db_host = "localhost";
    //Set the user
    private $db_user = "";
    //Set the database name of the system
    private $db_db = "";
    //Set the password of the user
    private $db_pws = "";

    public $conn;

    function close()
    {
        $this->conn = null;
    }

    function __destruct()
    {
        $this->conn = null;
    }

    function __construct()
    {
        try {
            $hostname = $this->db_host;
            $DB = $this->db_db;
            $username = $this->db_user;
            $password = $this->db_pws;
            if ($DB == "" || $username == "") {
                echo "Setup error! Set the values";
                exit();
            }
            $this->conn = new PDO("mysql:host=$hostname;dbname=$DB", $username, $password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            //echo "Connected successfully";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    }
}
?>

<p></p>
