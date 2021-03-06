<?php
        // Include config file
    require_once 'config.php';
    
    //Just start a goddamn session already
    session_start();
     
    // Define variables and initialize with empty values
    $username = $password = $email = $cmdr = "";
    $username_err = $password_err = $email_err = $cmdr_err = "";
    $popoff = false;
     
    //Check get data for a logout request
    if($_GET['logout']) {
        $_SESSION = array();
        session_destroy();
    }
    // Processing form data when form is submitted
     else if($_SERVER["REQUEST_METHOD"] == "POST"){
     
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = 'Please enter username.';
            $popoff = true;
        } else{
            $username = trim($_POST["username"]);
        }
        
        // Check if password is empty
        if(empty(trim($_POST['password']))){
            $password_err = 'Please enter your password.';
            $popoff = true;
        } else{
            $password = trim($_POST['password']);
        }
        
        // Validate credentials
        if(empty($username_err) && empty($password_err)){
            // Prepare a select statement
            $sql = "SELECT username, password FROM user WHERE username = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);
                
                // Set parameters
                $param_username = $username;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Store result
                    mysqli_stmt_store_result($stmt);
                    
                    // Check if username exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1){                    
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                /* Password is correct, so start a new session and
                                save the username to the session */
                                if(session_start()) {
                                    $_SESSION['username'] = $username;      
                                    header("location: messages.php");
                                } else {
                                    $username_err = 'Something unexpected went wrong';
                                    $popoff = true;
                                }
                            } else{
                                // Display an error message if password is not valid
                                $password_err = 'The password you entered was not valid.';
                                $popoff = true;
                            }
                        }
                    } else{
                        // Display an error message if username doesn't exist
                        $username_err = 'No account found with that username.';
                        $popoff = true;
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                    $popoff = true;
                }
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
        
        // Close connection
        mysqli_close($link);
    }
?>
<!DOCTYPE html>
<html lang='en'>
    <head>
        <?php require('references.php'); ?>
    </head>
    <body>
        <button type="button" id="mobile-nav-toggle"></button> 
		
        <?php require('header.php'); ?>
        
        <main id='main'>
            <section id='portfolio'>
                <div class='container wow fadeInUp' style="visibility: visible; animation-name: fadeInUp;">
                    <div class='section-header'>
                        <h3 class='section-title'>Messages</h3>
                        <p class='section-description'><a href='compose_message.php'><button type='button' class='btn btn-default'>Compose New</button></a></p>
                    </div>
                    <div class='row'>
                        <div class='col-lg-l2' style="margin:auto;">
                            <ul id='portfolio-flters'>
                                <li data-filter='.filter-all' class='filter-active'>All</li>
                                <li data-filter='.filter-new'>New</li>
                                <li data-filter='.filter-group'>Group</li>
                                <li data-filter='.filter-personal'>Personal</li>
                                <li data-filter='.filter-announcement'>Announcement</li>
                            </ul>
                        </div>
                    </div>
                    <div class='row' id='messages-wrapper'>
                        <div class='col-lg-3 col-md-6 portfolio-item filter-new'>
                            <a href=''>
                                <img src='/img/qstn.png'>
                                <div class='details'>
                                    <h4>Test User</h4>
                                    <span>Message preview</span>
                                </div>
                            </a>
                        </div>
                        <?php
                            //Get the connection ready
                            require_once 'config.php';
                            
                            //Pull and echo every message
                            $sql_a = "CALL getUserMessages(?);";
                                        
                            if($stmt_a = mysqli_prepare($link, $sql_a)) {
                                mysqli_stmt_bind_param($stmt_a, 's', $_SESSION['username']);
                                
                                if(mysqli_stmt_execute($stmt_a)) {
                                    mysqli_stmt_store_result($stmt_a);
                                    mysqli_stmt_bind_result($stmt_a, $sender_id, $subject, $content, $create_date);
                                    
                                    while(mysqli_stmt_fetch($stmt_a)) {
                                        echo "<div class='col-lg-3 col-md-6 portfolio-item filter-all'>
                                           <a href=''>
                                           <img src=''>
                                           <div class='details'>
                                           <h4>" . $subject . "</h4>
                                           <span>" . $content . "</span>
                                           </div>
                                           </div>";
                                    }
                                } 
                                mysqli_stmt_close($stmt_a);
                            }
                            
                            //Pull and echo all the personal messages
                            $sql_p = "CALL getUserPersonalMessages(?);";
                                        
                            if($stmt_p = mysqli_prepare($link, $sql_p)) {
                                mysqli_stmt_bind_param($stmt_p, 's', $_SESSION['username']);
                                
                                if(mysqli_stmt_execute($stmt_p)) {
                                    mysqli_stmt_store_result($stmt_p);
                                    mysqli_stmt_bind_result($stmt_p, $sender_id, $subject, $content, $create_date);
                                    
                                    while(mysqli_stmt_fetch($stmt_p)) {
                                        echo "<div class='col-lg-3 col-md-6 portfolio-item filter-personal'>
                                           <a href=''>
                                           <img src=''>
                                           <div class='details'>
                                           <h4>" . $subject . "</h4>
                                           <span>" . $content . "</span>
                                           </div>
                                           </div>";
                                    }
                                } 
                                mysqli_stmt_close($stmt_p);
                            }
                            
                            //Pull and echo all the group messages
                            $sql_g = "CALL getUserGroupMessages(?);";
                                        
                            if($stmt_g = mysqli_prepare($link, $sql_g)) {
                                mysqli_stmt_bind_param($stmt_g, 's', $_SESSION['username']);
                                
                                if(mysqli_stmt_execute($stmt_g)) {
                                    mysqli_stmt_store_result($stmt_g);
                                    mysqli_stmt_bind_result($stmt_g, $sender_id, $subject, $content, $create_date);
                                    
                                    while(mysqli_stmt_fetch($stmt_g)) {
                                        echo "<div class='col-lg-3 col-md-6 portfolio-item filter-group'>
                                           <a href=''>
                                           <img src=''>
                                           <div class='details'>
                                           <h4>" . $subject . "</h4>
                                           <span>" . $content . "</span>
                                           </div>
                                           </div>";
                                    }
                                } 
                                mysqli_stmt_close($stmt_g);
                            }
                            
                            //Pull and echo all the new messages
                            $sql_u = "CALL getUserUnreadMessages(?);";
                                        
                            if($stmt_u = mysqli_prepare($link, $sql_u)) {
                                mysqli_stmt_bind_param($stmt_u, 's', $_SESSION['username']);
                                
                                if(mysqli_stmt_execute($stmt_u)) {
                                    mysqli_stmt_store_result($stmt_u);
                                    mysqli_stmt_bind_result($stmt_u, $sender_id, $subject, $content, $create_date);
                                    
                                    while(mysqli_stmt_fetch($stmt_u)) {
                                        echo "<div class='col-lg-3 col-md-6 portfolio-item filter-new'>
                                           <a href=''>
                                           <img src=''>
                                           <div class='details'>
                                           <h4>" . $subject . "</h4>
                                           <span>" . $content . "</span>
                                           </div>
                                           </div>";
                                    }
                                } 
                                mysqli_stmt_close($stmt_u);
                            }
                            
                            //Close outta here
                            mysqli_close($link);

                        ?>
                    </div>
                </div>
            </section>
        </main>
        <div class='modal fade' id='loginModal'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title'>Login</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <?php                                
                                if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
                                    echo "<div class=\"wrapper\">
                                    <p>Please fill in your credentials to login.</p>
                                    <form action=" .  htmlspecialchars($_SERVER["PHP_SELF"]) . " method=\"post\">
                                        <div class=\"form-group " . ((!empty($username_err)) ? 'has-error alert alert-warning' : '') . "\">
                                            <label>Username</label>
                                            <input type=\"text\" name=\"username\"class=\"form-control\" value=" . $username . ">
                                            <span class=\"help-block\">" . $username_err . "</span>
                                        </div>    
                                        <div class=\"form-group " . ((!empty($password_err)) ? 'has-error alert alert-warning' : '') . "\">
                                            <label>Password</label>
                                            <input type=\"password\" name=\"password\" class=\"form-control\">
                                            <span class=\"help-block\">" . $password_err . "</span>
                                        </div>
                                        <div class=\"form-group\">
                                            <input type=\"submit\" class=\"btn btn-primary\" value=\"Login\">
                                        </div>
                                        <p>Don't have an account? <a href=\"register.php\">Sign up now</a>.</p>
                                    </form>
                                </div>";
                                } else echo "<h1>Already logged in!</h1>";
                            ?>
                    </div>
                </div>
            </div>
        </div>
         <?php require('scripts.php'); ?>
          
          <?php 
            if($popoff) echo "<script>\$(document).ready(function(){\$('#loginModal').modal('show');});</script>";
          ?>

	</body>
</html>