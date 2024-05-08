<?php
session_start();

if(!isset($_SESSION['loggedin'])){
    $_SESSION['loggedin'] = false;
}

// Kör funktionen för respektive knapp. 
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST["postButton"])){
        check();
    }
    elseif(isset($_POST["logOutButton"])){
        endSession(); 
    }
}

/*
När användaren klickar på postButton kollas det ifall användaren är inloggad eller ej
Ej inloggad --> inloggningssidan | Inloggad --> inläggssidan/författarverktyget.    
*/
function check(){
    if($_SESSION['loggedin'] == false){
        header("location: loginPage.php"); 
    }
    if($_SESSION['loggedin'] == true){ 
        header("location: postPage.php");
    }
}

/*
endSession avslutar session med session_destroy() och refreshar sidan. 
*/
function endSession(){
    if($_SESSION['loggedin'] == true){
        $_SESSION['loggedin'] = false; 
        session_destroy();
        header("Refresh:0");
        exit();
    }
}

/*
funktionen ansvarar endast för att skriva ut den inloggade användarens användarnamn på sidan
beroende på om användaren är inloggad eller ej. 
*/
function message(){
    if(isset($_SESSION["username"]) && $_SESSION['loggedin'] == true){ 
    echo "You are logged in as: " . htmlspecialchars($_SESSION["username"]); 
    }
    else{
        echo "Klick the button below to login"; 
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
                    <section>
                        <h1>Hello and welcome to Blogs.com!</h1>
                        <h3>Here you can find new posts!</h3>
                        <h4 id="loginNotice"> <?php message(); ?> </h4>
                        <form class="mainPanelButtons" action="index.php" method="post">
                            <input id=button type="submit" name="postButton" value="Create & Manage Posts &#43;" > </input>
                                <?php if(isset($_SESSION["username"]) && $_SESSION['loggedin'] == true) {
                                ?> <input id=logout-button type="submit" name="logOutButton" value="Log Out" > </input> <?php
                                }// skapar utloggningsknappen om användaren är inloggad?>
                        </form>
                    </section>
                        <?php
                            // Kör GetPost som generar innehållet på sidan, alltså inläggen.      
                            require_once 'classes/Database.class.php';
                            require_once 'classes/Post.class.php';
                            $db = new Database(); 
                            $posts = new Post($db); 
                            ob_start();
                            $posts->GetPost(); 
                            ob_end_flush();
                        ?>
                    </div>
            </main> 
        </div>
    </body>
</html>