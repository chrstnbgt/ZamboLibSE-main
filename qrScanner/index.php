<?php
  // Start session
  session_start();

  // Check if user is already logged in
  if (isset($_SESSION['librarianID']) && isset($_SESSION['librarianEmail'])) {
    header('location: ./webpages/homepage.php');
    exit();
  }

  // Require the account class
  require_once('./classes/account.class.php');

  // Check if login form is submitted
  if (isset($_POST['login'])) {
    $account = new Account();
    $account->librarianEmail = htmlentities($_POST['librarianEmail']);
    $account->librarianPassword = htmlentities($_POST['librarianPassword']);
    if ($account->sign_in_users()){
      $librarianID =  $account->librarianID; 
      $librarianEmail = $account->librarianEmail; 
      $_SESSION['librarian'] = 'librarian'; 
      $_SESSION['librarianID'] = $librarianID;
      $_SESSION['librarianEmail'] = $librarianEmail;
      header('location: ./webpages/homepage.php');
      exit(); // Always exit after a header redirect
    } else {
      // $error = 'Invalid username/password. Try again.';
      echo "<script>alert('Invalid email/password. Try again.');</script>"; // JavaScript alert
    }
  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../vendor/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custome css goes here -->
    <link rel="stylesheet" href="./css/stylesheet.css">
    <link rel="icon" href="./images/zc_lib_seal.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet"/>

    <title>Attendance Checker</title>
    
</head>

<body>
    <?php 
        require_once('./tools/functions.php');
    ?>

    <div class="main d-flex">
        <div class="row">
            <div class="col-12 container background">
                <img src="./images/wave-bg.png" alt="" class="background-image">
            </div>

            <div class="col-12 container login_div px-5">
                <div class="header d-flex justify-content-center align-items-center mb-5">
                    <img src="./images/zc_lib_seal.png" alt="" class="seal">
                    <p class="ms-3 pt-3">Attendance Checker</p>
                </div>

                <form action="" method="post"> <!-- Added method="post" and removed action="" -->
                    <div class="row d-flex justify-content-center">
                        <div class="input-group flex-column mb-3">
                            <label for="librarianEmail" class="label">Email</label>
                            <input type="email" name="librarianEmail" id="librarianEmail" class="input" placeholder="example@gmail.com" required value="<?php if(isset($_POST['librarianEmail'])){ echo $_POST['librarianEmail']; } ?>">
                            <div></div>
                        </div>
                    </div>

                    <div class="row d-flex justify-content-center">
                        <div class="input-group flex-column mb-3">
                            <label for="librarianPassword" class="label">Password</label>
                            <input type="password" name="librarianPassword" id="librarianPassword" class="input" placeholder="Enter your password" value="<?php if(isset($_POST['librarianPassword'])){ echo $_POST['librarianPassword']; } ?>">
                            <div></div>
                        </div>
                    </div>

                    <button type="submit" name="login" class="sign-in-btn mt-4">SIGN IN</button> <!-- Changed type to submit -->
                    <?php
                        if (isset($_POST['login']) && isset($error)){
                        ?>
                            <p class="text-danger mt-3 text-center"><?= $error ?></p>
                        <?php
                        }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body> 