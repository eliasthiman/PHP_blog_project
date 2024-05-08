<?php 
session_start();


require_once 'classes/Database.class.php';
require_once 'classes/Post.class.php';

$db = new Database(); 

// Kollar ifall en användare är inloggad eller ej. Om inte skickas användaren till inloggningsidan. 
if ($_SESSION['loggedin'] == false && !isset($_SESSION["username"])){
    header("location: loginPage.php");
    die("Unotherized user, please sign in to gain access");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
        processPost($db); 
}

/*
Funktionen använder samma design och metod som i postPage, 
men istället körs UpdatePost för att uppdatera inlägg. 
*/
function processPost($db) {
    $checkinput = null; 
    $updatedPost = new Post($db);

    $title = htmlspecialchars($_POST["newtitle"]); 
    $text = htmlspecialchars($_POST["newtext"]);
    $postID = htmlspecialchars($_GET['id']);
   
    $checkinput = $updatedPost->CheckPost($title, $text);

    if($checkinput){
        $updatedPost->UpdatePost($title, $text, $postID);
    }   
    else{
        $message = "Please provide a title and some body text"; 
        echo $message;
    }
    header("location: postPage.php"); // Efter uppdateringen skickas användaren till postPage.php
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
            <div class="myHeader">
                <header>
                    <ul class="nav-bar">
                        <li> <a href="index.php"> Home </a> </li>
                    </ul>
                </header>
            </div>
            <main>
                <div class="main-content"> 
                    <?php
                        /* 
                        Kör SelectedEditPost som associerar $id variabeln med $_GET['id']
                        SelectedEditPost tar variabeln som inparameter. Metoden generarar
                        innehållet för sidan beroende på valt inlägg.  
                        */   
                        if(isset($_GET['id'])){ 
                        $editPost = new Post($db);
                        $id = $_GET['id'];
                        $editPost->SelectedEditPost($id);
                        }
                        else{
                            die("Could not load selected post!"); //ifall id i url inte existerar. 
                        }
                    ?>
                </div>
            </main> 
        </div>
    </body>
</html>