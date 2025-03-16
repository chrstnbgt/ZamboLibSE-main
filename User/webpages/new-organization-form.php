<?php

require_once '../classes/userfull.class.php';
require_once '../classes/organizationclub.class.php';
require_once '../tools/functions.php';

//resume session here to fetch session values
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
    // $user->userPassword = $record['userPassword'];
    $user->userSchoolOffice = $record['userSchoolOffice'];
    // $user->userIDCard = $record['userIDCard'];
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
    exit();
}


if (isset($_POST['save'])) {



    $orgClub = new OrganizationClub();
    //sanitize
    $orgClub->orgClubImage = htmlentities($_POST['orgClubImage']);
    $orgClub->ocName = htmlentities($_POST['ocName']);
    $orgClub->ocEmail = htmlentities($_POST['ocEmail']);
    $orgClub->ocContactNumber = htmlentities($_POST['ocContactNumber']);
    // $orgClub->organizationClubType = htmlentities($_POST['organizationClubType']);

    // Set the userID
    $orgClub->userID = $userID;

        // Handle image upload
        if ($_FILES['orgClubImage']['name']) {
            $target_dir = "../images/orgClub_pic/";
    
            // Get the organization club name
            $ocEmail = htmlentities($_POST['ocEmail']);
    
            // Get the file extension
            $imageFileType = strtolower(pathinfo($_FILES["orgClubImage"]["name"], PATHINFO_EXTENSION));
    
            // Construct the target file name using the organization club name and file extension
            $target_file = $target_dir . $ocEmail . "." . $imageFileType;
    
            $uploadOk = 1;
    
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["orgClubImage"]["tmp_name"]);
            if ($check === false) {
                $uploadOk = 0;
            }
    
            // Check file size
            if ($_FILES["orgClubImage"]["size"] > 5000000000) {
                $uploadOk = 0;
            }
    
            // Allow certain file formats
            $allowed_extensions = array("jpg", "png", "jpeg", "gif");
            if (!in_array($imageFileType, $allowed_extensions)) {
                $uploadOk = 0;
            }
    
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["orgClubImage"]["tmp_name"], $target_file)) {
                    $orgClub->orgClubImage = $target_file;
                }
            }
        }

    //validate
    if (validate_field3($orgClub->orgClubImage) &&
        validate_field2($orgClub->ocName) &&
        validate_field3($orgClub->ocEmail) &&
        validate_field5($orgClub->ocContactNumber)
        /* validate_field2($orgClub->organizationClubType) */) {
        if ($orgClub->addOrgClub()) {
            header('location: organization-list.php');
            exit();
        } else {
            echo 'An error occurred while adding to the database.';
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
    <!-- Your custome css goes here -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../images/zc_lib_seal.png" type="image/x-icon">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet"/>

    <title>New Organization
        
    </title>
    
</head>

<body>

    <div class="main">
        <div class="row d-flex flex-row-reverse">

            <div class="col-12 col-md-7 create_account_div d-flex justify-content-center align-items-center">

                <div class="col-12 col-md-6 form_div">
                    <div class="mb-3">
                        <a href="./clubs.php" class="back_btn">Back</a>
                    </div>
                    <h3 class="header mb-4">New Organization/Club</h3>
                    <form method="post" enctype="multipart/form-data">
                        <!-- Form Fields -->

                        <div class="input-group flex-column mb-3">
                            <label for="orgClubImage" class="label">Logo/Image</label>
                            <?php if (!empty($orgClub->orgClubImage)): ?>
                                <p><?php echo basename($orgClub->orgClubImage); ?></p>
                                <input type="hidden" name="existingImage" value="<?php echo $orgClub->orgClubImage; ?>">
                            <?php endif; ?>
                            <input type="file" name="orgClubImage" id="orgClubImage" accept="image/*">
                            <div></div>
                        </div>

                        <div class="input-group flex-column mb-3">
                            <label for="ocName" class="label">Organization/Club's Name</label>
                            <input type="text" name="ocName" id="ocName" class="input" placeholder="Enter Org" required value="<?php if(isset($_POST['ocName'])) { echo $_POST['ocName']; }else if(isset($orgClub->ocName)) { echo $orgClub->ocName; } ?>">
                            <div></div>
                        </div>

                        <div class="input-group flex-column mb-3">
                            <label for="ocEmail" class="label">Email</label>
                            <input type="email" name="ocEmail" id="ocEmail" class="input" placeholder="Null" required value="<?php if(isset($_POST['ocEmail'])) { echo $_POST['ocEmail']; } else if(isset($orgClub->ocEmail)) { echo $orgClub->ocEmail; } ?>">
                            <div></div>
                        </div>

                        <div class="input-group flex-column mb-3">
                            <label for="ocContactNumber" class="label">Contact Number</label>
                            <input type="text" name="ocContactNumber" id="ocContactNumber" class="input" placeholder="Enter 11 Digits" value="<?php if(isset($_POST['ocContactNumber'])) { echo $_POST['ocContactNumber']; } else if(isset($orgClub->ocContactNumber)) { echo $orgClub->ocContactNumber; } ?>">
                            <div></div>
                        </div>

                        <!-- <div class="input-group flex-column mb-3">
                            <label for="organizationClubType" class="label">Group Type</label>
                                <select name="organizationClubType" id="organizationClubType" class="input me-lg-3">
                                    <option value="">Select</option>
                                    <option value="Club" <?php if(isset($_POST['organizationClubType']) && $_POST['organizationClubType'] == 'Club') { echo 'selected'; } else if(isset($orgClub->organizationClubType) && $orgClub->organizationClubType == 'Club') { echo 'selected'; } ?>>Club</option>
                                    <option value="Organization" <?php if(isset($_POST['organizationClubType']) && $_POST['organizationClubType'] == 'Organization') { echo 'selected'; } else if(isset($orgClub->organizationClubType) && $orgClub->organizationClubType == 'Organization') { echo 'selected'; } ?>>Organization</option>
                                </select>
                                <div></div>
                        </div> -->


                        <!-- Add more form fields here -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" name="save" class="btn btn-primary px-3 submit_btn">Submit</button>
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

    
    <?php
        require_once('../include/footer.php');
    ?>
      
    <?php
        require_once('../include/js.php');
    ?>


</body>
</html>