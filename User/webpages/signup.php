<?php

require_once '../classes/user.class.php';
require_once '../tools/functions.php';
require_once '../classes/database.php'; // Include your Database class

// Create a new instance of the Database class
$database = new Database();

// Call the connect method to establish a connection to the database
$conn = $database->connect();

if (!$conn) {
    // If the connection fails, you can handle the error here
    die("Failed to connect to the database!");
}

if(isset($_POST['save'])){

    $user = new User();
    // Sanitize
    $user->userFirstName = htmlentities($_POST['userFirstName']);
    $user->userMiddleName = htmlentities($_POST['userMiddleName']);
    $user->userLastName = htmlentities($_POST['userLastName']);
    $user->userEmail = htmlentities($_POST['userEmail']);
    $user->userPassword = htmlentities($_POST['userPassword']);

    // Validate
    if (validate_field($user->userFirstName) &&
        validate_field2($user->userMiddleName) &&
        validate_field($user->userLastName) &&
        validate_field3($user->userEmail) &&
        validate_password($user->userPassword) &&
        validate_username($user->userEmail) && !$user->isUserNameExist()) {
        
        // Attempt to sign up the user
        $activation_token = $user->signup($conn);
        
        if($activation_token !== false) {

            // Update activation hash in the database
            if ($user->setAccountActivationHash($activation_token, $conn)) {
                // Send activation email
                try {
                    $mail = require_once __DIR__ . "/mailer.php";
                    $mail->setFrom("zambolib123@gmail.com");
                    $mail->addAddress($user->userEmail);
                    $mail->Subject = "Account Activation";
                    $mail->Body = <<<END
                    Click <a href="localhost/ZamboLib/User/webpages/activate-account.php?token=$activation_token">here</a> to activate your  account.
                    END;                

                    /*
                    $mail->Body = <<<END
                    Click <a href="http://zambocitylibrary.com/final/ZamboLib/User/webpages/activate-account.php?token=$activation_token">here</a> to activate your  account.
                    END; */
                    // Enable debugging
                    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

                    $mail->send();
                    header('Location: signup-success.php');
                    exit;
                } catch (Exception $e) {
                    echo "An error occurred while sending the activation email: {$mail->ErrorInfo}";
                }
            } else {
                echo 'Failed to update activation hash in the database.';
            }

        } else {
            echo 'An error occurred while adding to the database.qqq';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../vendor/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <!-- Your custom css goes here -->
    <link rel="icon" href="../images/zc_lib_seal.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet"/>
    <title>Sign up</title>
</head>
<body>
    <div class="main">
        <div class="row d-flex flex-row-reverse">
            <div class="col-12 col-md-7 create_account_div d-flex justify-content-center align-items-center">
                <div class="col-12 col-md-6 form_div">
                    <div class="mb-3">
                        <a href="../index.php" class="back_btn"><i class='bx bx-chevron-left icon-back'></i><span class="icon-label">Back</span></a>
                    </div>
                    <h3 class="header mb-4 ms-4">Create Your Account</h3>
                    
                    <form action="" method="post">
                        <div class="row d-flex justify-content-center">
                            <div class="input-group flex-column mb-3">
                                <label for="userFirstName" class="label">First Name</label>
                                <input type="text" name="userFirstName" id="userFirstName" class="input" placeholder="Juan" required value="<?php if(isset($_POST['userFirstName'])) { echo $_POST['userFirstName']; } ?>">
                                <div></div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="input-group flex-column mb-3 field_size">
                                <label for="userMiddleName" class="label">Middle Name</label>
                                <input type="text" name="userMiddleName" id="userMiddleName" class="input" placeholder="Gonzalez" value="<?php if(isset($_POST['userMiddleName'])) { echo $_POST['userMiddleName']; } ?>">
                                <div></div>
                            </div>
                            <div class="input-group flex-column mb-3 field_size">
                                <label for="userLastName" class="label">Last Name</label>
                                <input type="text" name="userLastName" id="userLastName" class="input" placeholder="Dela Cruz" required value="<?php if(isset($_POST['userLastName'])) { echo $_POST['userLastName']; } ?>">
                                <div></div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="input-group flex-column mb-3">
                                <label for="userEmail" class="label">Email</label>
                                <input type="email" name="userEmail" id="userEmail" class="input" placeholder="juan_delacruz@gmail.com" required value="<?php if(isset($_POST['userEmail'])) { echo $_POST['userEmail']; } ?>">
                                <?php
                                    $new_user = new User();
                                    if(isset($_POST['userEmail'])){
                                        $new_user->userEmail = htmlentities($_POST['userEmail']);
                                    }else{
                                        $new_user->userEmail = '';
                                    }

                                        if(isset($_POST['userEmail']) && strcmp(validate_username($_POST['userEmail']), 'success') != 0){
                                    ?>
                                            <p class="text-danger my-1"><?php echo validate_username($_POST['userEmail']) ?></p>
                                    <?php
                                        }else if ($new_user->isUserNameExist() && $_POST['userEmail']){
                                    ?>
                                            <p class="text-danger my-1">Email already exist</p>
                                    <?php      
                                        }
                                    ?>
                                <div></div>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center">
                            <div class="input-group flex-column mb-3">
                                <label for="userPassword" class="label">Password</label>
                                <input name="userPassword" id="userPassword" class="input" type="password" placeholder="The password must have at least 8 characters" minlength="8" required value="<?php if(isset($_POST['userPassword'])) { echo $_POST['userPassword']; } ?>">
                                <?php
                                    if(isset($_POST['userPassword'])) {
                                        $passwordValidationResult = validate_password($_POST['userPassword']);
                                        if ($passwordValidationResult !== 'success') {
                                            echo "<div class='error'>$passwordValidationResult</div>";
                                        }
                                    }
                                ?>
                                <div></div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 px-3 d-flex justify-content-center px-5 px-md-2">
                                <button type="submit" name="save" class="btn btn-primary px-3 submit_btn">Create Account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-5 banner_div d-flex flex-column justify-content-center">
                <div class="logo text-center">
                    <img src="../images/zc_lib_seal.png" alt="Logo Text" id="seal">
                    <span class="logo-name mt-3" id="logo-seal">Zamboanga City Library</span>
                </div>
                <h3 class="header mt-3 ms-3 d-flex justify-content-center align-items-center mb-5">Normal Rd, Zamboanga, Zamboanga del Sur</h3>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../js/custom.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
