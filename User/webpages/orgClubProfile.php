<?php

require_once '../classes/organizationclub.class.php';
require_once '../tools/functions.php';

// Resume session here to fetch session values
session_start();

if (isset($_GET['id'])) {
    $_SESSION['organizationClubID'] = $_GET['id']; // Store in session
}

// Check if organizationClubID is set
if (isset($_SESSION['organizationClubID'])) {
    $organizationClubID = $_SESSION['organizationClubID'];
} else {
    // Redirect or show an error if the ID is missing
    header("Location: organization.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect to the login page
    header("Location: ../index.php");
    exit();
}

// Fetch organization club details
$orgClub = new OrganizationClub(); // Assuming this class exists
$orgRecord = $orgClub->fetchOrganizationDetails($organizationClubID); // Fetch details

if ($orgRecord) {
    // Use array keys correctly
    $organizationClubID = $orgRecord['organizationClubID'];
    $ocName = $orgRecord['ocName'];
    $orgClubImage = $orgRecord['orgClubImage'];
    $ocEmail = $orgRecord['ocEmail'];
    $ocContactNumber = $orgRecord['ocContactNumber'];
    $ocCreatedAt = $orgRecord['ocCreatedAt'];
} else {
    // Redirect if no data is found
    header("Location: organization.php");
    exit();
}

if (isset($_POST['save'])) {
    // Check if the user is logged in
    if (!isset($_SESSION['userID'])) {
        // Redirect to the login page
        header("Location: ../index.php");
        exit();
    }

    // Handle image upload
    if (!empty($_FILES['orgClubImage']['name'])) {
        $target_dir = "../images/profile_pic/";

        // Get the email
        $ocEmail = htmlentities($_POST['ocEmail']);

        // Get the file extension
        $imageFileType = strtolower(pathinfo($_FILES["orgClubImage"]["name"], PATHINFO_EXTENSION));

        // Construct the target file name using the email and file extension
        $target_file = $target_dir . $ocEmail . "." . $imageFileType;

        $uploadOk = 1;

        // Check if image file is an actual image
        $check = getimagesize($_FILES["orgClubImage"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES["orgClubImage"]["size"] > 5000000) {
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowed_extensions = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowed_extensions)) {
            $uploadOk = 0;
        }

        // Check if file upload is valid
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["orgClubImage"]["tmp_name"], $target_file)) {
                $orgClubImage = $target_file;
            }
        }
    }

    // Assign sanitized data to object properties
    $orgClub->organizationClubID = $organizationClubID;
    $orgClub->ocName = htmlentities($_POST['ocName']);
    $orgClub->orgClubImage = $orgClubImage ?? null;
    $orgClub->ocEmail = htmlentities($_POST['ocEmail']);
    $orgClub->ocContactNumber = htmlentities($_POST['ocContactNumber']);

    // Validate and update the record
    if ($orgClub->edit()) {
        header('Location: orgClubProfile.php');
        exit();
    } else {
        echo 'An error occurred while updating the database.';
    }
}

?>




<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Organization Details';
$courses = 'active';
require_once('../include/head2.php');
?>

<body style="height: 100vh; overflow: hidden;">
    <?php
    require_once('../include/nav-panel.php');
    require_once('../tools/functions.php');
    ?>

    <section class="overlay"></section>

    <div class="main min-vh-100 organization-panel">
        <div class="mt-4">
            <div class="row d-flex">
                <div class="OrgProfileNav col-12 col-md-5 d-flex align-items-center flex-column">
                    <img src="<?php echo !empty($orgClubImage) ? $orgClubImage : '../images/profile_pic/default-profile.png'; ?>" 
                         alt="Organization Image" class="orgProfile-image"> 
                    <h4 class="orgLabel pt-2 mb-3"><?php echo htmlspecialchars($ocName, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <h6 class="= pt-2 mb-5">
                        <?php 
                            echo "Created at " . date("F d, Y", strtotime($ocCreatedAt)); 
                        ?>
                    </h6>

                </div>

                <div class="orgProposalList col-12 col-md-7 ps-lg-3">
                    <div class="row d-flex">
                        <div class="col d-flex justify-content-start align-items-center py-4">
                        <div class="profile-field col-12 col-md-12">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-container">
                                <h5 class="subheading-label ms-lg-4 ms-2 my-2 mb-3 ps-2 ">Personal Information</h5>
                                <div class="row d-flex justify-content-center">
                                <div class="input-group flex-column mb-3">
                                    <label for="orgClubImage" class="label">Organization Picture</label>
                                    
                                    <?php if (!empty($orgRecord['orgClubImage'])): ?>
                                        <p><?php echo htmlspecialchars(basename($orgRecord['orgClubImage']), ENT_QUOTES, 'UTF-8'); ?></p>
                                        <input type="hidden" name="existingImage" value="<?php echo htmlspecialchars($orgRecord['orgClubImage'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php endif; ?>
                                    
                                    <input type="file" name="orgClubImage" id="orgClubImage" accept="image/*">
                                    <div></div>
                                </div>

                                    <div class="input-group flex-column mb-3">
                                        <label for="ocName" class="label">Organization Name</label>
                                        <input type="text" name="ocName" id="ocName" class="input" 
                                            placeholder="Juan" required 
                                            value="<?php 
                                                if (isset($_POST['ocName'])) { 
                                                    echo htmlspecialchars($_POST['ocName'], ENT_QUOTES, 'UTF-8'); 
                                                } else if (isset($orgRecord['ocName'])) { 
                                                    echo htmlspecialchars($orgRecord['ocName'], ENT_QUOTES, 'UTF-8'); 
                                                } 
                                            ?>">
                                        <div></div>
                                    </div>

                                </div>


                                <div class="row d-flex justify-content-center">
                                <div class="input-group flex-column mb-3 field_size">
                                    <label for="ocContactNumber" class="label">Contact Number</label>
                                    <input type="text" name="ocContactNumber" id="ocContactNumber" class="input me-lg-3" 
                                        placeholder="Enter Contact Number" 
                                        value="<?php 
                                            if (isset($_POST['ocContactNumber'])) { 
                                                echo htmlspecialchars($_POST['ocContactNumber'], ENT_QUOTES, 'UTF-8'); 
                                            } else if (isset($orgRecord['ocContactNumber'])) { 
                                                echo htmlspecialchars($orgRecord['ocContactNumber'], ENT_QUOTES, 'UTF-8'); 
                                            } 
                                        ?>">
                                    <div></div>
                                </div>

                
                                    <div class="input-group flex-column mb-3 field_size">
                                        <label for="ocEmail" class="label">Email</label>
                                        <input type="email" name="ocEmail" id="ocEmail" class="input" 
                                            placeholder="Organization Email" 
                                            value="<?php 
                                                if (isset($_POST['ocEmail'])) { 
                                                    echo htmlspecialchars($_POST['ocEmail'], ENT_QUOTES, 'UTF-8'); 
                                                } else if (isset($orgRecord['ocEmail'])) { 
                                                    echo htmlspecialchars($orgRecord['ocEmail'], ENT_QUOTES, 'UTF-8'); 
                                                } 
                                            ?>">
                                        <div></div>
                                    </div>
                                </div>


                            </div>

                            <div class="btn-container-org d-flex justify-content-end align-content-end align-items-end  me-lg-5">
                                <button type="submit" name="save" value="save" class="update-btn-org" id="login-btn">Update</button>
                            </div>
                            
                        </form>
                    </div>
                        </div>
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
