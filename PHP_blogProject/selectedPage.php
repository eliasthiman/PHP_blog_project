<?php 
session_start();
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
                    <?php
                        // Kör GetSelectedPost, alltså generar den sida som användaren har klickat på (inläggssida).
                        require_once 'classes/Database.class.php';
                        require_once 'classes/Post.class.php';
                        if(isset($_GET['id'])){
                        $db = new Database(); 
                        $posts = new Post($db);
                        $id = $_GET['id'];
                        ob_start();
                        $posts->GetSelectedPost($id); 
                        ob_end_flush();
                        }
                        else{
                            die("Could not load selected post!");
                        }
                    ?>
                </div>
            </main> 
        </div>
    </body>
</html>