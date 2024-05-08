<?php 

class User {
    private $db;

public function __construct($db){
    $this->db = $db;    
}

public function UserVerify($password){
    $conn = $this->db->GetConnection();
    $password = $conn->real_escape_string($password);

    $hPass = password_hash($password, PASSWORD_DEFAULT);

    if(password_verify($password, $hPass)){  
            return true;
        }
        else{
            return false; 
        }
    }
}
?>