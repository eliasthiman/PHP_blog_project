<?php

class Database {

    private $db_server = "localhost";
    private $db_user = "";
    private $db_pass = "";
    private $db_name = "";
    private $conn;

    /*
    Constructorn skapar kontakt till databasen genom mysqli, 
    ifall kontakten ej kan etableras fångas en exception och sparas i variablen $e som sedan 
    skrivs ut med die genom getMessage() funktionen. 
    */
    public function __construct() {
        try{
            $this->conn = new mysqli($this->db_server, $this->db_user, $this->db_pass, $this->db_name);
        }
        catch(mysqli_sql_exception $e){
            die ("could not connect to database" . $e->getMessage());
        }
    }

    /*
    Returnerar anslutningen till databasen
    */
    public function GetConnection(){
        return $this->conn;
    }
}
?>