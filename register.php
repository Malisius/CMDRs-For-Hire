<?php
// Include config file
require_once 'config.php';
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = $cmdr = "";
$username_err = $password_err = $confirm_password_err = $email_err = $cmdr_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } 
    //Check that the username doesn't contain illegal characters
    elseif(trim($_POST["username"]) != filter_var(trim($_POST["username"]), FILTER_SANITIZE_STRING)) {
        $username_err = "Invalid username. Only alphanumeric characters accepted.";
    }
    //Check for existing username
    else{
        // Prepare a select statement
        $sql = "SELECT id FROM user WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    //Validate email
    if(empty(trim($_POST['email'])) or !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email_err = 'Please enter a valid email.';
    } else $email = $_POST['email'];
    //Validate cmdr name
    if(empty(trim($_POST['cmdr']))) {
        $cmdr_err = 'Please enter your Commander\'s name.';
    } else $cmdr = $_POST['cmdr'];
    
    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST['password'])) < 8){
        $password_err = "Password must have atleast 8 characters.";
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';     
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($cmdr_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO user (username, password, email, cmdr) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $param_email, $param_cmdr);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_email = $email;
            $param_cmdr = $cmdr;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: index.php");
            } else{
                echo "Something went wrong. Please try again later.";
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
        <section id='hero'>
            <div class="hero-container">
                <div style='background-color:rgba(80,80,80,0.4); padding:20px;'>
                    <h2>Sign Up</h2>
                    <p>Please fill this form to create an account.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error alert alert-warning' : ''; ?>">
                            <label>Username</label>
                            <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                            <span class="help-block"><?php echo $username_err; ?></span>
                        </div>    
                        <div class="form-group <?php echo (!empty($email_err)) ? 'has-error alert alert-warning' : ''; ?>">
                            <label>Email</label>
                            <input type="text" name="email"class="form-control" value="<?php echo $email; ?>">
                            <span class="help-block"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($cmdr_err)) ? 'has-error alert alert-warning' : ''; ?>">
                            <label>Commander Name</label>
                            <input type="text" name="cmdr"class="form-control" value="<?php echo $cmdr; ?>">
                            <span class="help-block"><?php echo $cmdr_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error alert alert-warning' : ''; ?>">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                            <span class="help-block"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error alert alert-warning' : ''; ?>">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                            <span class="help-block"><?php echo $confirm_password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn btn-default" value="Reset">
                        </div>
                        <p>Already have an account? <a href="index.php">Login here</a>.</p>
                    </form>
                </div>
            </div>    
        </section>
        <?php require('scripts.php'); ?>
    </body>
</html>