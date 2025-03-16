<?php
require_once '../classes/userfull.class.php';
require_once '../tools/functions.php';

// Resume session here to fetch session values
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect to the login page or another page as needed
    header("Location: ../index.php");
    exit();
}

// Fetch information for the logged-in user
$userID = $_SESSION['userID'];
$user = new User();
$record = $user->fetch($userID);

// Assuming that the fetch method returns user information
if ($record) {
    $user->userID = $record['userID'];
    $user->userLastName = $record['userLastName'];
    $user->userMiddleName = $record['userMiddleName'];
    $user->userFirstName = $record['userFirstName'];
    $user->userEmail = $record['userEmail'];
    $user->userUserName = $record['userUserName'];
    $old_userEmail = $user->userEmail;
    $user->userBirthdate = $record['userBirthdate'];
    $user->userGender = $record['userGender'];
    $user->userCivilStatus = $record['userCivilStatus'];
    $user->userContactNo = $record['userContactNo'];
    $user->userSchoolOffice = $record['userSchoolOffice'];
    $user->userIDCard = $record['userIDCard']; // Fetch the QR code file path from the database
    $user->userRegion = $record['userRegion'];
    $user->userProvince = $record['userProvince'];
    $user->userCity = $record['userCity'];
    $user->userBarangay = $record['userBarangay'];
    $user->userStreetName = $record['userStreetName'];
    $user->userZipCode = $record['userZipCode'];
    $user->userImage = $record['userImage'];
} else {
    // Handle the case where user information couldn't be retrieved
    // You might want to redirect to an error page or handle it in another way
    header("Location: #");
}

if (isset($_POST['save'])) {
    // Check if the user is logged in
    if (!isset($_SESSION['userID'])) {
        // Redirect to the login page or another page as needed
        header("Location: ../index.php");
        exit();
    }


    // Handle image upload
    if ($_FILES['userImage']['name']) {
        $target_dir = "../images/profile_pic/";

        // Get the username
        $username = htmlentities($_POST['userUserName']);

        // Get the file extension
        $imageFileType = strtolower(pathinfo($_FILES["userImage"]["name"], PATHINFO_EXTENSION));

        // Construct the target file name using the username and file extension
        $target_file = $target_dir . $username . "." . $imageFileType;

        $uploadOk = 1;

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["userImage"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["userImage"]["size"] > 500000000000) {
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowed_extensions = array("jpg", "png", "jpeg", "gif");
        if (!in_array($imageFileType, $allowed_extensions)) {
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["userImage"]["tmp_name"], $target_file)) {
                $user->userImage = $target_file;
            }
        }
    }

    // Generate QR code and save it
    $qrFileName = 'user_' . $user->userID . '_qr.png';
    $qrFilePath = generateQRCode($user->userID . '|' . $user->userFirstName . '|' . $user->userMiddleName . '|' . $user->userLastName, $qrFileName);
    $user->userIDCard = $qrFilePath; // Save QR code file path to user's record

    // Sanitize and retrieve data from the form
    $user->userID = $userID;
    $user->userLastName = htmlentities($_POST['userLastName']);
    $user->userMiddleName = htmlentities($_POST['userMiddleName']);
    $user->userFirstName = htmlentities($_POST['userFirstName']);
    $user->userEmail = htmlentities($_POST['userEmail']);
    $user->userUserName = htmlentities($_POST['userUserName']);
    $user->userBirthdate = htmlentities($_POST['userBirthdate']);
    $user->userGender = htmlentities($_POST['userGender']);
    $user->userCivilStatus = htmlentities($_POST['userCivilStatus']);
    $user->userContactNo = htmlentities($_POST['userContactNo']);
    $user->userSchoolOffice = htmlentities($_POST['userSchoolOffice']);
    $user->userRegion = htmlentities($_POST['userRegion']);
    $user->userProvince = htmlentities($_POST['userProvince']);
    $user->userCity = htmlentities($_POST['userCity']);
    $user->userBarangay = htmlentities($_POST['userBarangay']);
    $user->userStreetName = htmlentities($_POST['userStreetName']);
    $user->userZipCode = htmlentities($_POST['userZipCode']);

    // Validate and update the user record
    if ($user->edit()) {
        header('Location: account-settings.php');
    } else {
        echo 'An error occurred while updating the database.';
    }
}

// Function to generate and save QR code
function generateQRCode($data, $fileName) {
    $tempDir = "../qr-code-generator/user-qrcodes/";
    $pngAbsoluteFilePath = $tempDir . $fileName;
    
    include('../qr-code-generator/phpqrcode/qrlib.php');

    // Generate QR code
    QRcode::png($data, $pngAbsoluteFilePath);

    return $pngAbsoluteFilePath;
}
?>



<!DOCTYPE html>
<html lang="en">
<?php
  $title = 'Account Settings';
  $courses = 'active';
  require_once('../include/head2.php');

?>

<body>
<?php
    require_once('../include/nav-panel.php');
?>

      <section class="overlay"></section>

    <div class="main">
        <div class="container-fluid d-flex flex-column justify-content-center">
            <div class="row profile-container2 justify-content-center mt-3">
                <div class="profile-card col-12 col-lg-6">
                    <h4 class="heading-label mt-lg-2 ms-lg-3 mb-4">Account Settings</h4>
                    <div class="row flex-column mt-lg-4 mb-3">
                        <div class="row user-details d-flex">
                            <div class="col-12 col-lg-4 mt-3 d-flex justify-content-center">
                                <!-- Profile Picture -->
                                <img src="<?php echo isset($user->userImage) ? $user->userImage : '../images/profile_pic/default-profile.png'; ?>" alt="Profile Picture" class="img-fluid rounded-circle profile-picture">
                            </div>

                            <div class="col-12 col-lg-8 mt-2 d-flex align-items-center flex-column justify-content-center align-items-lg-start align-items-sm-center ">
                                <h3 class="user-name-heading d-flex align-items-center"><?php echo $user->userFirstName . ' ' . $user->userMiddleName . ' ' . $user->userLastName ?></h3>
                                <h5 class="email-display"><?php echo $user->userEmail;?></h5>
                            </div>

                            <!-- Display QR code with download button -->
                            <div class="row d-flex flex-row-reverse justify-content-center">
                                <div class="input-group flex-column mb-3">
                                    <label for="userQRCode" class="label text-center">QR Code</label>
                                    <div class="text-center">
                                        <a href="<?php echo isset($user->userIDCard) ? $user->userIDCard : '../images/profile_pic/default-profile.png'; ?>" download="user_qr_code.png">
                                            <img src="<?php echo isset($user->userIDCard) ? $user->userIDCard : '../images/profile_pic/default-profile.png'; ?>" alt="QR Code" class="qr-code">
                                        </a>
                                        <div class="d-flex justify-content-center">
                                            <button class="btn btn-primary mt-2 d-block" onclick="downloadQR()">Download</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of QR code display -->

                        </div> 
                        
                        <div class="bg-bottom">
                            <img src="../images/wave-bg.png" alt="">
                        </div>
                    </div>
                </div>
                <div class="account-card col-12 col-lg-6 px-lg-5">
                    <div class="profile-field col-12 col-md-12">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-container">
                                <h5 class="subheading-label ms-lg-4 ms-2 my-2 mb-3 ps-2 ">Personal Information</h5>
                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3">
                                        <label for="userImage" class="label">Profile Picture</label>
                                        <?php if (!empty($user->userImage)): ?>
                                            <p><?php echo basename($user->userImage); ?></p>
                                            <input type="hidden" name="existingImage" value="<?php echo $user->userImage; ?>">
                                        <?php endif; ?>
                                        <input type="file" name="userImage" id="userImage" accept="image/*">
                                        <div></div>
                                    </div>

                                    <div class="input-group flex-column mb-3">
                                        <label for="userFirstName" class="label">First Name</label>
                                        <input type="text" name="userFirstName" id="userFirstName" class="input" placeholder="Juan" required value="<?php if(isset($_POST['userFirstName'])) { echo $_POST['userFirstName']; } else if(isset($user->userFirstName)) { echo $user->userFirstName; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userMiddleName" class="label">Middle Name</label>
                                        <input type="text" name="userMiddleName" id="userMiddleName" class="input me-lg-3" placeholder="Gonzalez" value="<?php if(isset($_POST['userMiddleName'])) { echo $_POST['userMiddleName']; } else if(isset($user->userMiddleName)) { echo $user->userMiddleName; } ?>">
                                        <div></div>
                                    </div>
                
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userLastName" class="label">Last Name</label>
                                        <input type="text" name="userLastName" id="userLastName" class="input" placeholder="Dela Cruz" required value="<?php if(isset($_POST['userLastName'])) { echo $_POST['userLastName']; } else if(isset($user->userLastName)) { echo $user->userLastName; } ?>">
                                        <div></div>
                                    </div>
            
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3">
                                        <label for="userUserName" class="label">Username</label>
                                        <input type="text" name="userUserName" id="userUserName" class="input" placeholder="juan_delacruz69" required value="<?php if(isset($_POST['userUserName'])) { echo $_POST['userUserName']; } else if(isset($user->userUserName)) { echo $user->userUserName; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <!-- <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3">
                                        <label for="userPassword" class="label">Password</label>
                                        <input name="userPassword" id="userPassword" class="input" type="password" placeholder="The password must have at least 8 characters" minlength="8" required value="<?php if(isset($_POST['userPassword'])) { echo $_POST['userPassword']; } ?>">
                                        <?php
                                            // if(isset($_POST['userPassword'])) {
                                            //     $passwordValidationResult = validate_password($_POST['userPassword']);
                                            //     if ($passwordValidationResult !== 'success') {
                                            //         echo "<div class='error'>$passwordValidationResult</div>";
                                            //     }
                                            // }
                                        ?>
                                        <div></div>
                                    </div>
                                </div> -->

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3">
                                        <label for="userBirthdate" class="label">Birthdate</label>
                                        <input type="date" name="userBirthdate" id="userBirthdate" class="input" placeholder="Choose" value="<?php if(isset($_POST['userBirthdate'])) { echo $_POST['userBirthdate']; } else if(isset($user->userBirthdate)) { echo $user->userBirthdate; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3 field_size">
                                    <label for="userGender" class="label">Gender</label>
                                        <select name="userGender" id="userGender" class="input me-lg-3">
                                            <option value="">Select</option>
                                            <option value="Male" <?php if(isset($_POST['userGender']) && $_POST['userGender'] == 'Male') { echo 'selected'; } else if(isset($user->userGender) && $user->userGender == 'Male') { echo 'selected'; } ?>>Male</option>
                                            <option value="Female" <?php if(isset($_POST['userGender']) && $_POST['userGender'] == 'Female') { echo 'selected'; } else if(isset($user->userGender) && $user->userGender == 'Female') { echo 'selected'; } ?>>Female</option>
                                            <option value="LGBTQ+" <?php if(isset($_POST['userGender']) && $_POST['userGender'] == 'LGBTQ+') { echo 'selected'; } else if(isset($user->userGender) && $user->userGender == 'LGBTQ+') { echo 'selected'; } ?>>LGBTQ+</option>
                                        </select>
                                        <div></div>
                                    </div>



                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userCivilStatus" class="label">Civil Status</label>
                                        <select name="userCivilStatus" id="userCivilStatus" class="input">
                                            <option value="">Select</option>
                                            <option value="Single" <?php if(isset($_POST['userCivilStatus']) && $_POST['userCivilStatus'] == 'Single') { echo 'selected'; } else if(isset($user->userCivilStatus) && $user->userCivilStatus == 'Single') { echo 'selected'; } ?>>Single</option>
                                            <option value="Married" <?php if(isset($_POST['userCivilStatus']) && $_POST['userCivilStatus'] == 'Married') { echo 'selected'; } else if(isset($user->userCivilStatus) && $user->userCivilStatus == 'Married') { echo 'selected'; } ?>>Married</option>
                                            <option value="Widowed" <?php if(isset($_POST['userCivilStatus']) && $_POST['userCivilStatus'] == 'Widowed') { echo 'selected'; } else if(isset($user->userCivilStatus) && $user->userCivilStatus == 'Widowed') { echo 'selected'; } ?>>Widowed</option>
                                            <option value="Legally Separated" <?php if(isset($_POST['userCivilStatus']) && $_POST['userCivilStatus'] == 'Legally Separated') { echo 'selected'; } else if(isset($user->userCivilStatus) && $user->userCivilStatus == 'Legally Separated') { echo 'selected'; } ?>>Legally Separated</option>
                                        </select>
                                        <div></div>
                                    </div>

                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userContactNo" class="label">Contact Number</label>
                                        <input type="text" name="userContactNo" id="userContactNo" class="input me-lg-3" placeholder="Gonzalez" value="<?php if(isset($_POST['userContactNo'])) { echo $_POST['userContactNo']; } else if(isset($user->userContactNo)) { echo $user->userContactNo; } ?>">
                                        <div></div>
                                    </div>
                
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userSchoolOffice" class="label">School/Office</label>
                                        <input type="text" name="userSchoolOffice" id="userSchoolOffice" class="input" placeholder="Dela Cruz" value="<?php if(isset($_POST['userSchoolOffice'])) { echo $_POST['userSchoolOffice']; } else if(isset($user->userSchoolOffice)) { echo $user->userSchoolOffice; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3">
                                        <label for="userEmail" class="label">Email</label>
                                        <input type="email" name="userEmail" id="userEmail" class="input" placeholder="Null" value="<?php if(isset($_POST['userEmail'])) { echo $_POST['userEmail']; } else if(isset($user->userEmail)) { echo $user->userEmail; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <h5 class="subheading-label ms-lg-4 ms-2 my-2 mb-3 ps-2 ">Address</h5>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userRegion" class="label">Region</label>
                                        <input type="text" name="userRegion" id="userRegion" class="input me-lg-3" placeholder="Null" value="<?php if(isset($_POST['userRegion'])) { echo $_POST['userRegion']; } else if(isset($user->userRegion)) { echo $user->userRegion; } ?>">
                                        <div></div>
                                    </div>
                
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userProvince" class="label">Province</label>
                                        <input type="text" name="userProvince" id="userProvince" class="input" placeholder="Null"  value="<?php if(isset($_POST['userProvince'])) { echo $_POST['userProvince']; } else if(isset($user->userProvince)) { echo $user->userProvince; } ?>">
                                        <div></div>
                                    </div>
            
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3">
                                        <label for="userCity" class="label">City</label>
                                        <input type="text" name="userCity" id="userCity" class="input" placeholder="Juan" value="<?php if(isset($_POST[''])) { echo $_POST['userCity']; } else if(isset($user->userCity)) { echo $user->userCity; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3">
                                        <label for="userBarangay" class="label">Baranggay</label>
                                        <input type="text" name="userBarangay" id="userBarangay" class="input" placeholder="Null"value="<?php if(isset($_POST['userBarangay'])) { echo $_POST['userBarangay']; } else if(isset($user->userBarangay)) { echo $user->userBarangay; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userStreetName" class="label">Street Name</label>
                                        <input type="text" name="userStreetName" id="userStreetName" class="input me-lg-3" placeholder="Null" value="<?php if(isset($_POST['userStreetName'])) { echo $_POST['userStreetName']; } else if(isset($user->userStreetName)) { echo $user->userStreetName; } ?>">
                                        <div></div>
                                    </div>
                
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="userZipCode" class="label">Zip Code</label>
                                        <input type="number" name="userZipCode" id="userZipCode" class="input" placeholder="Null" value="<?php if(isset($_POST['userZipCode'])) { echo $_POST['userZipCode']; } else if(isset($user->userZipCode)) { echo $user->userZipCode; } ?>">
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="save" value="save" class="btn btn-primary px-3 submit_btn" id="login-btn">Update</button>
                        </form>
                    </div>
                </div>
            </div>

            
            
        </div>
    </div>
    
      
  <?php
    require_once('../include/js.php');
  ?>
</body>
</html>