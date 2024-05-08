<?php
session_start();

    require_once 'classes/Database.class.php'; 
    require_once 'classes/Login.class.php';  

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        processLogin();
    }

    /*
    Funktionen associerar variablerna $username och $password med respektive 
    data via $_POST. Sedan körs metoden ChekForm för att säkerhetsställa 
    att användaren har matat in uppgifterna korrekt. Ifall användaren har
    gjort rätt körs CheckCred som behandlar inloggningsuppifterna och kollar ifall
    användaren finns i databasen eller ej. Om användaren finns blir användaren
    inloggad. I annat fall skrivs ett passande felmeddelande ut. 
    */
    function processLogin() {
        $db = new Database();
        $login = new Login($db);
        $checkForm = null;
        $checkCred = null;
        global $message;
        $username = htmlspecialchars($_POST["username"]); 
        $password = htmlspecialchars($_POST["password"]);
        $checkForm = $login->CheckForm($username, $password);
    
        if ($checkForm) {
            $checkCred = $login->CheckCred($username, $password);
    
            if ($checkCred) {
                $_SESSION["username"] = $username; 
                $_SESSION['loggedin'] = true; 
                header("location: index.php"); 
                exit();
            } else {
                $message = "Unauthorized user, please sign in with a valid account. Password or Username is incorrect"; // Uppgifterna matchar inte i databasen.
                
            }
        } else {
            $message = "Username or Password missing"; // Användaren har glömt skriva in lösen eller användarnamn. 
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE-edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Blogs.com</title>
        <link rel="stylesheet" type="text/css" href="src/css/style.css">
    </head>
    <body>
        <div class="page-wrapper">
                <header class="myHeader">
                    <ul class="nav-bar">
                        <li> <a href="index.php"> Home </a> </li> 
                    </ul>
                </header>
            <main>
                <div class="main-content">
                    <h2>Please login to gain access</h2>
                    <div class="loginForm">
                        <form action="loginPage.php" method="post"> 
                            <br>
                            <input type="text" name="username" placeholder="Username"><br>
                            <br>
                            <input type="password" name="password" placeholder="Password"><br>
                            <input id="login-button" type="submit" name="login" value="Log In">
                        </form> 
                    </div>
                    <div class="message">
                        <p><?php global $message; if(!empty($message)) {echo $message;} // meddelande till användaren ?></p>
                    </div>
                </div>
            </main> 
        </div>
    </body>
</html>