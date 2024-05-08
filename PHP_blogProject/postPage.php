<?php 
    session_start();
    require_once 'classes/Database.class.php';
    require_once 'classes/Post.class.php'; 

    $db = new Database(); 

    // Kollar ifall en användare är inloggad eller ej. Om inte skickas användaren till inloggningsidan.  
    if ($_SESSION['loggedin'] == false){
        header("location: loginPage.php");
        die("Unotherized user, please sign in to gain access");
    }

    // Kör funktionen för respektive knapp. 
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['loggedin'] == true){

        if(isset($_POST["post"])){
            processPost($db); 
        }
        elseif(isset($_POST["editButton"])) { 
            $postID = $_POST["postID"];
            editPost($postID);
        }
        elseif(isset($_POST["deleteButton"])) {
            $postID = $_POST["postID"];
            deletePost($db, $postID); 
        }
    }

    // Skickar användaren till en dedikerad sida för redigering av valda inlägget. 
    function editPost($editID){
        header("location:editPage.php?id=$editID");
    }

    // Kör metoden DeletePost och hämtar $delID via $_POST. $db behövs för instansen av klassen Post
    function deletePost($db, $delID){
        $deletePost = new Post($db);
        $deletePost->DeletePost($delID); 
    } 

    // Kör metoden GetUserPosts och hämtar $activeUser via $_SESSION. $db behövs för instansen av klassen Post
    function loadUserPosts($db, $activeUser){
        $userPosts = new Post($db);
        $userPosts->GetUserPosts($activeUser);
    }

    /*  
    Skapar variablarna $title, $text och $author och associerar dem med respektive data
    alltså det användaren har skrivit in i title, text och användarens användarnamn via 
    $_SESSION. Kör metoden CheckPost först för att se om den returnerar true, i så fall 
    körs SetPost vilket skapar inläggen. 
    */
    function processPost($db) {
        $checkinput = null; 
        $post = new Post($db);

        $title = htmlspecialchars($_POST["title"]); 
        $text = htmlspecialchars($_POST["text"]);
        $author = htmlspecialchars($_SESSION["username"]); 

        $checkinput = $post->CheckPost($title, $text);

        if($checkinput){
            $post->SetPost($title, $text, $author);
            global $postmessage;
            $postmessage = "Post Created!";
            header("Refresh:2"); 
        }   
        else{
            global $message;
            $message = "Please provide a title and some bodytext!"; // meddelande till användaren ifall CheckPost är false
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
        <script type="text/javascript" src="script.js"> </script>
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
                <h2>Write your blog post!</h2>
                    <div class="postForm">
                        <form action="postPage.php" method="post">
                            <h4>Title:</h4>
                            <input id="title-area"type="text" name="title" placeholder="Write your title here" maxlength="40"><br>
                            <br>
                            <h4>Text:</h4>
                            <textarea id="text-area" type="text" name="text" placeholder="Write your text here" rows="10" cols="60" maxlength="1000"> </textarea> <br> 
                            <br>
                            <input id="button" type="submit" name="post" value="Submit Post &#43;">
                            <div class="message">
                                <p><?php global $message; if(!empty($message)) {echo $message;} // meddelande till användaren ?></p>
                                <h3 id="loginNotice"> <?php global $postmessage; if(!empty($postmessage)) {echo $postmessage;} ?> </h3>
                            </div>
                        </form> 
                    </div>
                    <br>
                    <br>
                    <h2 id="second-title">Edit or Delete your posts!</h2>
                    <?php
                    // Kör loadUserPosts ifall användaren är inloggad.
                    if(isset($_SESSION["username"]) && $_SESSION['loggedin'] == true){ 
                    loadUserPosts($db, htmlspecialchars($_SESSION["username"]));
                    }
                    ?>
                </div>
            </main> 
        </div>
    </body>
</html>