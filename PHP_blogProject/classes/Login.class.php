<?php

require_once 'classes/Database.class.php';

class Login {

    private $db;

    public function __construct($db){ // Constructor för databasen
        $this->db = $db;
    }
    /*
    CheckForm tar emot inmatade användarnamnet och lösenordet som inparameter, 
    för att sedan kolla ifall variablarna i tomma eller ej. 
    */
    public function CheckForm($usrn, $pwd){

        if(!empty($usrn) && !empty($pwd)){ 
            return true;
        }
        else{
            return false; 
        }
    }

    /*
    CheckCred Tar emot inmatade användarnamnet och lösenordet som inparameter, krypterar lösenordet så att det överensstämmer med 
    det krypterade lösenordet i databasen. Sedan skapas en query och hämtar resultatet från den. 
    */
    public function CheckCred($usrn, $pwd){
        $conn = $this->db->GetConnection(); 
        $usrn = $conn->real_escape_string($usrn);
        $pwd = $conn->real_escape_string($pwd);
        $hPass = md5($pwd);

        $query = "SELECT * FROM users WHERE username=? AND password=?"; 
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $usrn, $hPass); 

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true; // Kontot finns
            } else {
                return false; // Kontot finns inte 
            }
        } else {
            return false; // ifall execute() (boolean) är false, alltså fel i queryn. Då returneras false. 
        }
    }
}
?>