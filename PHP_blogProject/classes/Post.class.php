<?php

require_once 'classes/Database.class.php';
require_once 'classes/Login.class.php';  


class Post {

    private $db;

    public function __construct($db){ // Constructor för databasen
        $this->db = $db;    
    }

    /*
    CheckPorm tar emot inmatade titeln och brödtexten som inparameter, 
    för att sedan kolla ifall variablarna i tomma eller ej. 
    */
    public function CheckPost($title, $text){

        if(!empty($title) && !empty($text)){  
            return true;
        }
        else{
            return false;  
        }
    }

    /*
    SetPost tar emot 3 inparametrar som används för inmatningen i databasen. 
    Lägger in datan i posts tabellen i databasen och därmed skapar inläggen. 
    */
    public function SetPost($title, $text, $author){
        $conn = $this->db->GetConnection();
        $author = $conn->real_escape_string($author);  
        $title = $conn->real_escape_string($title);
        $text = $conn->real_escape_string($text);

        $query = "INSERT INTO posts (titel, bodytext, user) VALUES (?, ?, ?)"; 
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $title, $text, $author); 

        try{
            $stmt->execute(); 
        }
        catch(mysqli_sql_exception $e){
            die("COULD NOT POST! " . $e->getMessage());
        } 
        $conn->close();
        $stmt->close();      
    }

    /*
    GetPost hämtar datan från posts tabellen för att sedan skriva ut
    informationen via html element. Metoden ansvarar för att inläggen
    skrivs ut dynamiskt. Respektive inlägg får även en länk med sitt
    respektive id nummer.
    Ifall inga inlägg finns ännu genereras en "platshållare" (nothing has been posted yet!).
    */
    public function GetPost(){ 
        $conn = $this->db->GetConnection();
        $query = "SELECT * FROM posts ORDER BY post_date DESC"; 
        $stmt = $conn->prepare($query);
        
        if($stmt->execute()) {
            $result = $stmt->get_result();
            $allposts = $result->fetch_all(MYSQLI_ASSOC); 
            $conn->close();
            $stmt->close(); 
        }
        else{
            echo $stmt->error;
            die();
        }

        if(!empty($allposts)){  

            foreach($allposts as $post) {
                $aID = $post["id"];
                $aTitle = $post["titel"];
                $aBodytext = $post["bodytext"];
                $aUser = $post["user"]; 
                $aDate = $post["post_date"]; 

            ?> 
            <div class="blog-post"> 
                <section> 
                    <h3 id="postTitle"> <?php echo htmlspecialchars($aTitle); ?></h3> 
                <article>
                    <p id="postText"><?php echo htmlspecialchars($aBodytext); ?></p>
                </article>
                </section>
                <section>
                    <a href=<?php echo 'selectedPage.php?id='.$aID; ?>>
                        <button id="button"> View Page </button>
                    </a>
                </section>
                <section class="sign-date-time">
                    <p id="sign"> <?php echo htmlspecialchars($aUser); ?></p> 
                    <p id="date"> <?php echo htmlspecialchars($aDate); ?></p>
                </section> 
            </div>
            <?php
            } 
        }
        else{
            ?> 
            <div class="blog-post"> 
                <section> 
                <h3 id="postTitle"> Nothing has been posted yet!</h3> 
                <article>
                    <p id="postText"></p>
                </article>
                </section>
                <section class="sign-date-time">
                    <p id="sign"></p> 
                    <p id="date"></p>
                </section>
            </div>
            <?php
        }
    }

    /*
    GetSelectedPost tar emot en inparameter vilket är det id nummer 
    som det valda inlägget har (hämtas via $_GET['id']). 
    Metoden väljer ut från tabellen "posts" utifrån id nummer och
    hämtar det som en associativ array, informationen skriv ut med
    echo via html element. Alltså metoden generar det inlägg användaren 
    har valt från ingångssidan (index.php).  
    */
    public function GetSelectedPost($id) {
            $postID = $id;
            $conn = $this->db->GetConnection();
            $query = "SELECT * FROM posts WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $postID);
            
            if($stmt->execute()) {
                $result = $stmt->get_result();
                $allposts = $result->fetch_all(MYSQLI_ASSOC); 
                $conn = null;
                $stmt = null;
            }
            else{
                echo $stmt->error;
                die();
            }

            foreach($allposts as $post) { 
                $aTitle = $post["titel"];
                $aBodytext = $post["bodytext"];
                $aUser = $post["user"];
                $aDate = $post["post_date"];
            ?> 
            <div class="blog-post"> 
                <section> 
                <h3 id="postTitle"> <?php echo htmlspecialchars($aTitle); ?></h3> 
                <article>
                    <p id="postText"><?php echo htmlspecialchars($aBodytext); ?></p>
                </article>
                </section>
                <section class="sign-date-time">
                    <p id="sign"> <?php echo htmlspecialchars($aUser); ?></p> 
                    <p id="date"> <?php echo htmlspecialchars($aDate); ?></p>
                </section> 
            </div>
            <?php
            }  
        }

    /*
    GetUserPosts är en metod för VG delen som ej är färdig.
    Vad metoden gör är att den tar den inloggade användarens 
    användarnamn (via $_SESSION) som inparameter. Hämtar de 
    inlägg som är associerade med användarnamnet och skriver ut
    dem samt lägger på två knappar för redigering och bortagning 
    av inläggen. Självaste metoden är fullt fungerande.
    OBS! Är en metod för VG delen som ej är färdig. 
    Dock är självaste metoden fullt fungerande. 
    */
    public function GetUserPosts($author){
            $conn = $this->db->GetConnection();
            $author = $conn->real_escape_string($author);  
            $query = "SELECT * FROM posts WHERE user=? ORDER BY post_date DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $author);
            
            if($stmt->execute()) {
                $result = $stmt->get_result();
                $allposts = $result->fetch_all(MYSQLI_ASSOC); 
                $conn = null;
                $stmt = null; 
            }
            else{
                echo $stmt->error;
                die();
            }

            if(!empty($allposts)){  
            foreach($allposts as $post) {
                $aID = $post["id"];
                $aTitle = $post["titel"];
                $aBodytext = $post["bodytext"];
                $aUser = $post["user"];
                $aDate = $post["post_date"];

            ?> 
            <div class="blog-post"> 
                <section> 
                <h3 id="postTitle"> <?php echo htmlspecialchars($aTitle); ?></h3> 
                <article>
                    <p id="postText"><?php echo htmlspecialchars($aBodytext); ?></p>
                </article>
                <section>
                    <form class="mainPanelButtons" action="postPage.php" method="post">
                        <input type="hidden" name="postID" value="<?php echo htmlspecialchars($aID) ?>">
                        <input id="button" type="submit" name="editButton" value="Edit &#x270E;" > </input>
                        <input id="logout-button" type="submit" name="deleteButton" value="Delete &#x2716;" > </input> 
                    </form>
                </section>
                </section>
                <section class="sign-date-time">
                    <p id="sign"> <?php echo htmlspecialchars($aUser); ?></p> 
                    <p id="date"> <?php echo htmlspecialchars($aDate); ?></p> 
                </section> 
            </div>
            <?php
            }
        }
        else if(empty($allposts)){
            echo "You have not made any posts yet!";
        }
    }

    /*
    SelectedEditPost är lik GetSelectedPost. Det som skiljer 
    sig är att den generar innehållet för editPage.php 
    där användaren kan redigera sitt valda inlägg.
    OBS! Är en metod för VG delen som ej är färdig. 
    Dock är självaste metoden fullt fungerande.
    */
    public function SelectedEditPost($editID){
        $conn = $this->db->GetConnection();
        $query = "SELECT * FROM posts WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $editID);
        
        if($stmt->execute()) {
            $result = $stmt->get_result();
            $allposts = $result->fetch_all(MYSQLI_ASSOC); 
            $conn->close();
            $stmt->close(); 
        }
        else{
            echo $stmt->error;
            die();
        }

        foreach($allposts as $post) { 
            $aTitle = $post["titel"];
            $aBodytext = $post["bodytext"];
        ?> 
        <h2>Edit your blog post!</h2>
        <div class="postForm">
            <form action="editPage.php?id=<?php echo htmlspecialchars($editID)?>" method="post">
                <h4>Title:</h4>
                    <input id="title-area"type="text" name="newtitle" placeholder="Write your title here" value="<?php echo $aTitle ?>"  maxlength="40"> 
                    <br>
                <h4>Text:</h4>
                    <textarea id="text-area" type="text" name="newtext" placeholder="Write your text here" rows="10" cols="60" maxlength="1000"> <?php echo $aBodytext ?> </textarea> <br> 
                    <br>
                    <input id="button" type="submit" name="editedpost" value="Confirm Edited Post &#x270E;"> 
            </form> 
        </div>
        <?php
        }  
    }

    /*
    UpdatePost tar emot de inparametrar som skapas på editPage.php 
    använder UPDATE i SQL frågan för att updatera tabellen
    i databasen. Vilken som ska uppdateras bestäms via id nummer.
    OBS! Är en metod för VG delen som ej är färdig. 
    Dock är självaste metoden fullt fungerande.
    */
    public function UpdatePost($title, $text, $postID){
        $conn = $this->db->GetConnection();
        $postID = $conn->real_escape_string($postID);  
        $title = $conn->real_escape_string($title);
        $text = $conn->real_escape_string($text);

        $query = "UPDATE posts SET titel=?, bodytext=? WHERE id=?"; 
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $title, $text, $postID); 

        try{
            $stmt->execute(); 
        }
        catch(mysqli_sql_exception $e){
            die("Error could not update post" . $e->getMessage()); 
        } 
        $conn->close();
        $stmt->close();  
    }
    
    /*
    DeletePost radera det inlägg användaren väljer att 
    radera via det grafiska gränssnittet. Metoden tar
    emot inläggets id nummer som inparameter via (<input type="hidden" name="postID" value="<?php echo htmlspecialchars($aID) ?>">)
    från metoden GetUserPosts. 
    DELETE används i SQL frågan för att radera data beroende på id nummer.     
    OBS! Är en metod för VG delen som ej är färdig. 
    Dock är självaste metoden fullt fungerande.
    */
    public function DeletePost($delID){
        $conn = $this->db->GetConnection();
        $delID = $conn->real_escape_string($delID); 
        $query = "DELETE FROM posts WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $delID); 

        try {
            $stmt->execute(); 
        } catch (mysqli_sql_exception $e) {
            die("Could not delete: " . $e->getMessage()); 
        }
        $conn->close();
        $stmt->close(); 
    }
}
?>