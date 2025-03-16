
<?php

require_once '../classes/organizationproposal.class.php';
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

// Check if the organizationClubID is provided via GET and set it in the session
if(isset($_GET['id'])){
    $_SESSION['organizationClubID'] = $_GET['id'];
} else {
    // Handle case where organizationClubID is not provided in the URL
    // echo 'Organization Club ID is missingfdsds.';
    // exit();
}


// Fetch organizationClubID from session
$organizationClubID = $_SESSION['organizationClubID'];

require_once('../classes/organizationclub.class.php');
$orgDetails = new OrganizationClub();
$orgRecord = $orgDetails->fetchOrganizationDetails($organizationClubID);

// Assuming that the fetch method returns user information
if ($orgRecord) {
    $orgDetails->organizationClubID = $orgRecord['organizationClubID'];
    $orgDetails->userID = $orgRecord['userID'];
    $orgDetails->ocName = $orgRecord['ocName'];
    $orgDetails->orgClubImage = $orgRecord['orgClubImage'];
    // $orgDetails->organizationClubType = $orgRecord['organizationClubType'];
    $orgDetails->ocEmail = $orgRecord['ocEmail'];
    $orgDetails->ocContactNumber = $orgRecord['ocContactNumber'];
} else {
    // Handle the case where user information couldn't be retrieved
    // You might want to redirect to an error page or handle it in another way
    header("Location: /profile.php");
}


if (isset($_POST['save'])) {

    // Check if the organizationClubID is set
    if (!isset($_POST['organizationClubID'])) {
        echo 'Organization Club ID is missing.';
        exit();
    }

    // Create a new instance of OrganizationProposal
    $orgProposal = new OrganizationProposal();

    // Sanitize input data for proposal
    $proposalSubject = htmlentities($_POST['proposalSubject']);
    $proposalDescription = htmlentities($_POST['proposalDescription']);

    // Set properties in $orgProposal
    $orgProposal->proposalSubject = $proposalSubject;
    $orgProposal->proposalDescription = $proposalDescription;
    
    // Set the organizationClubID property in $orgProposal
    $orgProposal->organizationClubID = $organizationClubID;

    // Handle file upload
    $target_dir = "../images/proposal_files/";
    $proposalFile = '';
    if ($_FILES['myfile']['name']) {
        $imageFileType = strtolower(pathinfo($_FILES["myfile"]["name"], PATHINFO_EXTENSION));
        $proposalFile = $target_dir . uniqid() . '.' . $imageFileType;
        $uploadOk = 1;

        // Check if file is a valid file
        if ($_FILES["myfile"]["size"] > 500000000000000) {
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowed_extensions = array("jpg", "png", "jpeg", "gif", "pdf", "doc", "docx", "xls", "xlsx");
        if (!in_array($imageFileType, $allowed_extensions)) {
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["myfile"]["tmp_name"], $proposalFile)) {
                // File uploaded successfully
            } else {
                // Error uploading file
                $proposalFile = ''; // Reset the file path
            }
        }

    }

    // Validate input fields
    if (validate_field3($proposalSubject) && validate_field3($proposalDescription)) {

    // Add proposal and org_proposal
    if ($orgProposal->addProposalWithOrgProposal($organizationClubID, $proposalSubject, $proposalDescription, $proposalFile)) {
        // Redirect to organization.php with the organizationClubID parameter
        header("Location: organization.php?id=$organizationClubID");
        exit();
    } else {
        echo 'An error occurred while adding to the database.';
    }

    }
}

?>




<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Organization';
$courses = 'active';
require_once('../include/head.php');
?>

<body style="height: 100vh; overflow: hidden;">
    <?php
    require_once('../include/nav-panel.php');
    require_once('../classes/organizationproposal.class.php');
    require_once('../tools/functions.php');

    $orgProposal = new OrganizationProposal();

    $orgProposalArray = $orgProposal->show($organizationClubID);
    $counter = 1;

// Check if the proposalID is set for deletion
if (isset($_POST['delete_proposal'])) {
    $proposalID = $_POST['delete_proposal'];
    $orgProposal = new OrganizationProposal();

    // Fetch organizationClubID from session
    if (isset($_SESSION['organizationClubID'])) {
        $organizationClubID = $_SESSION['organizationClubID'];

        // Call the delete method
        if ($orgProposal->delete($proposalID)) {
            // Redirect back to the same page after deletion
            echo "<script>window.location.href='organization.php?id=$organizationClubID';</script>";
            exit();
        } else {
            echo "Failed to delete proposal.";
        }
    } else {
        echo "Organization Club ID is missing.";
    }
}

?>

    <section class="overlay"></section>

    <div class="main min-vh-100 organization-panel">
        <div class=" mt-4">

                <div class="row d-flex">
                    <div class="OrgProfileNav col-12 col-md-4 d-flex  align-items-center flex-column">
                        <img src="<?php echo isset($orgDetails->orgClubImage) ? $orgDetails->orgClubImage : '../images/profile_pic/default-profile.png'; ?>" alt="User Image" class="orgProfile-image"> 
                        <h4 class="orgLabel pt-2 mb-5"><?php echo $orgDetails->ocName;?></h4>
                        <a href="orgClubProfile.php?id=<?= $_SESSION['organizationClubID']; ?>" 
                            class="orgNavButtons me-2 d-block mb-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class='bx bx-edit-alt icon-edit me-2 text-center'></i>
                                    <span class="nav-org-label">Edit Organization</span> 
                                </div>
                            </a>


                        <button type="button" class=" orgNavButtons justify-content-center align-items-center me-2" data-bs-toggle="modal" data-bs-target="#addProposalModal">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class='bx bx-plus-circle icon-add me-2 text-center'></i>
                                <span class="nav-org-label ps-2">
                                    Send Proposal
                                </span> 
                            </div>
                        </button>
                    </div>

                    <div class="orgProposalList col-12 col-md-8 ps-lg-3">
                        <div class="row d-flex ">
                            <div class="col d-flex justify-content-start align-items-center py-4">
                                <div class="col d-flex flex-column">
                                <p class="label">Filter by:  </p>
                                    <div class="dropdown">
                                        <button class="btn btn-custom-filter dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <?php
                                            // Set the default label based on the filter parameter
                                            $filter = $_GET['filter'] ?? '';
                                            switch ($filter) {
                                                case 'All':
                                                    echo 'All Proposals';
                                                    break;
                                                case 'Pending':
                                                    echo 'Pending';
                                                    break;
                                                case 'Completed':
                                                    echo 'Completed';
                                                    break;
                                                case 'Ongoing':
                                                    echo 'Ongoing';
                                                    break;
                                                default:
                                                    echo 'Filter';
                                                    break;
                                            }
                                            ?>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                            <li><a class="dropdown-item <?php echo ($filter == 'All') ? 'active' : ''; ?>" href="?filter=All">All Proposals</a></li>
                                            <li><a class="dropdown-item <?php echo ($filter == 'Pending') ? 'active' : ''; ?>" href="?filter=Pending">Pending</a></li>
                                            <li><a class="dropdown-item <?php echo ($filter == 'Completed') ? 'active' : ''; ?>" href="?filter=Completed">Completed</a></li>
                                            <li><a class="dropdown-item <?php echo ($filter == 'Ongoing') ? 'active' : ''; ?>" href="?filter=Ongoing">Ongoing</a></li>
                                        </ul>
                                    </div>
                                </div>

                            <div class="container d-flex justify-content-end mt-3">
                                <div class="position-relative w-50" style="max-width: 400px;">
                                    <input type="text" id="search-bar-cus" class="form-control ps-5" placeholder="Search Proposals...">
                                    <i class="bi bi-search search-icon"></i>
                                </div>
                            </div>
                            </div>

                        </div>

                        <!-- Pending Proposals DIV -->
                        <div class="row d-flex mt-4">
                            <?php 
                                $filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
                                if ($orgProposalArray) {
                                foreach ($orgProposalArray as $proposal) {
                                    if ($filter === 'All' || $proposal['status'] === $filter) {
                            ?>
                                    <div class=" col-12 col-lg-6 mb-3">
                                        <!-- List View DIV -->
                                        <div class="proposal_card_div d-flex flex-column mx-1">
                                        <div class="d-flex justify-content-between">
                                            <p class="status"><?= $proposal['status'] ?></p>
                                        </div>
                                        <div class="header_card d-flex align-items-center  mb-2">
                                            <h4 class="event_title">Proposal Title: <?= $proposal['proposalSubject'] ?></h4>
                                        </div>
                                        <?php
                                            // Truncate the proposal description if it exceeds 120 characters
                                            $proposalDescription = $proposal['proposalDescription'];
                                            if (strlen($proposalDescription) > 120) {
                                                $proposalDescription = substr($proposalDescription, 0, 120) . '...';
                                            }
                                        ?>
                                        <p class="caption caption-limit">Description:<br><?= $proposalDescription ?></p>
                                        <div class="btn-container d-flex justify-content-end align-items-center align-content-center">
                                            <button class="cta me-0" data-bs-toggle="modal" data-bs-target="#proposalModal<?= $proposal['proposalID'] ?>">
                                            <span>View More</span>
                                            <svg width="15px" height="10px" viewBox="0 0 13 10">
                                                <path d="M1,5 L11,5"></path>
                                                <polyline points="8 1 12 5 8 9"></polyline>
                                            </svg>
                                            </button>
                                            <!-- <a class="view_more view_more_btn" data-bs-toggle="modal" data-bs-target="#proposalModal<?= $proposal['proposalID'] ?>">View More</a> -->
                                        </div>



                                    </div>

                                        <!-- End of List View DIV -->
                                    </div>

                                    <!-- Proposal Details Modal -->
                                    <div class="modal fade" id="proposalModal<?= $proposal['proposalID'] ?>" tabindex="-1" role="dialog" aria-labelledby="proposalModalLabel<?= $proposal['proposalID'] ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered application-modal" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body mx-lg-4 mb-3">
                                                    <div class="application-form-heading d-flex justify-content-between my-3">
                                                        <h5 class="modal-title mb-3" id="proposalModalLabel<?= $proposal['proposalID'] ?>">Proposal Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <!-- Proposal Details -->
                                                    <h3 class="club-name mb-3"><?= $proposal['proposalSubject'] ?></h3>
                                                    <p class="whole-description mt-4 mb-5"><?= $proposal['proposalDescription'] ?></p>

                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <form method="post" action="organization.php">
                                                            <input type="hidden" name="delete_proposal" value="<?= $proposal['proposalID'] ?>">
                                                            <a href="#" class="join-btn hide" data-bs-toggle="modal" data-bs-target="#"><span class="">Cancel</span></a>
                                                            <button type="submit" class="join-btn hide me-3" onclick="return confirm('Are you sure you want to delete this proposal?')">
                                                                <span class="">Delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                $counter++;
                                if ($counter % 3 == 0) {
                                    echo '</div><div class="row d-flex">';
                                }
                            }
                        }
                        } else {
                            echo "<p>No proposals found.</p>";
                        }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="orgHeader d-flex justify-content-between align-items-center">

                </div>
                

            <!-- Add Proposal Modal -->
            <div class="modal fade" id="addProposalModal" tabindex="-1" aria-labelledby="addProposalModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-body-centered">
                    <div class="modal-content modal-modification">
                        <div class="header-modal d-flex justify-content-between">
                            <h5 class="modal-title mt-4 ms-4" id="addProposalModalLabel">Send Proposal</h5>
                            <button type="button" class="btn-close mt-4 me-4" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body mx-2 mt-2">
                            <form method="post" enctype="multipart/form-data">
                                <div class="row d-flex justify-content-center my-1">
                                    <div class="input-group flex-column mb-3">
                                        <label for="proposalSubject" class="label">Proposal Subject</label>
                                        <input type="text" name="proposalSubject" id="proposalSubject" class="input-1" placeholder="Enter Subject" required value="<?php if(isset($_POST['proposalSubject'])) { echo $_POST['proposalSubject']; }else if(isset($orgProposal->proposalSubject)) { echo $orgProposal->proposalSubject; } ?>">
                                        <div></div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center my-1">
                                    <div class="input-group flex-column mb-3">
                                        <label for="proposalDescription" class="label">Description</label>
                                        <textarea id="proposalDescription" name="proposalDescription" class="input-1" rows="4" cols="50" placeholder="Write brief description" required value="<?php if(isset($_POST['proposalDescription'])) { echo $_POST['proposalDescription']; }else if(isset($orgProposal->proposalDescription)) { echo $orgProposal->proposalDescription; } ?>"></textarea>
                                        <div></div>
                                    </div>
                                </div>

                                <div class="input-group flex-column mb-3">
                                    <label for="myfile" class="label">File Upload</label>
                                    <input type="file" name="myfile" id="myfile" accept=".xlsx,.xls,.doc,.docx,.png,.pdf,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" <?php if (isset($_FILES['myfile']['name'])) {echo 'value="' . $_FILES['myfile']['name'] . '"';} ?>>
                                </div>

                                
                                <input type="hidden" name="organizationClubID" value="<?php echo $organizationClubID; ?>">

                                <div class="modal-action-btn d-flex justify-content-end">
                                    <button type="button" class="btn cancel-btn mb-3 me-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="save" class="add-btn-proposal2 mb-3 me-4">Send Proposal</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>


            
        </div>

    </div>

    <script>
    document.getElementById('search-bar-cus').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let eventCards = document.querySelectorAll('.proposal_card_div');

        eventCards.forEach(function(card) {
            let title = card.querySelector('.event_title').textContent.toLowerCase();
            let description = card.querySelector('.caption').textContent.toLowerCase();

            if (title.includes(searchText) || description.includes(searchText)) {
                card.parentElement.style.display = 'block';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    });
    </script>

    <?php
    require_once('../include//js.php');
    ?>

</body>

</html>
