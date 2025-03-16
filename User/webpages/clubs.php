<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
} 

$userID = $_SESSION['userID'];

$title = 'Clubs';
$courses = 'active';
require_once('../include/head.php');
?>

<body>
    <?php
    require_once('../include/nav-panel.php');
    require_once('../classes/club.class.php');
    require_once ('../tools/functions.php');

    $clubs = new Clubs();

    $clubsArray = $clubs->show();
    $clubsArray2 = $clubs->showMembership($userID);
    ?>

    <section class="overlay"></section>

    <div class="main min-vh-100 club-page-bg">
        <div class="content-feed col-12 col-md-12">
            <div class="row">
                <div class="col-12">
                    <h3 class="d-flex pt-5 mb-4 ms-2">List of Clubs</h3>
                </div>
            </div>

            <div class="row d-flex">
                <?php
                if ($clubsArray) {
                    foreach ($clubsArray as $item) {
                        $isMember = false;
                        $isPending = false;
                        $isApproved = false;
                        
                        foreach ($clubsArray2 as $club) {
                            if ($club['clubID'] == $item['clubID'] && $club['cmStatus'] == 'Pending') {
                                $isPending = true;
                                break;
                            }
                            if ($club['clubID'] == $item['clubID'] && $club['cmStatus'] == 'Approved') {
                                $isApproved = true;
                                break;
                            }
                        }
                        ?>
                        <div class=" col-12 col-lg-4 mb-3">
                            <div class="club-card d-flex flex-column mx-2">
                                <div class="row d-flex align-content-center justify-content-center align-items-center mb-3 mt-2">
                                    <div class="col-3 club-seal">
                                        <img src="../images/dict_logo.png" alt="">
                                    </div>

                                    <div class="col-9 club">
                                        <h3 class="club-name ps-3"><?= $item['clubName'] ?></h3>
                                    </div>
                                </div>
                                <div class="club-details ms-2">
                                    <!-- <p class="member-count">21 members</p> -->
                                    <p class="age-group">
                                        <?php 
                                        if ($item['clubMinAge'] == 0 && $item['clubMaxAge'] == 0) {
                                            echo "For All Ages";
                                        } else {
                                            echo "For Ages " . $item['clubMinAge'] . " - " . $item['clubMaxAge'];
                                        }
                                        ?>
                                    </p>

                                    <p class="club-description"><?= $item['clubDescription'] ?></p>

                                    <?php
                                    // Calculate the user's age
                                    $userBirthdate = new DateTime($user->userBirthdate); // User's birthdate from the record
                                    $currentDate = new DateTime(); // Current date
                                    $userAge = $currentDate->diff($userBirthdate)->y; // Calculate age in years

                                    // Check if the user's age is within the club's age range
                                    $isEligible = ($userAge >= $item['clubMinAge'] && $userAge <= $item['clubMaxAge']);
                                    ?>

                                    <div class="btn-container d-flex justify-content-end">
                                        <?php if ($isEligible) { ?>
                                            <?php if ($isMember) { ?>
                                                <!-- If the user is already a member -->
                                                <a href="./club-details.php?id=<?= $item['clubID'] ?>" class="view_more_btn" style="text-decoration: none; color: inherit;">View More</a>
                                            <?php } elseif ($isPending) { ?>
                                                <!-- If the membership status is pending -->
                                                <a href="./application-form-club.php?id=<?= $item['clubID'] ?>" class="view_more_btn" style="text-decoration: none; color: inherit;">Pending</a>
                                            <?php } elseif ($isApproved) { ?>
                                                <!-- If the membership status is approved -->
                                                <a href="./club-details.php?id=<?= $item['clubID'] ?>" class="view_more_btn" style="text-decoration: none; color: inherit;">View More</a>
                                            <?php } else { ?>
                                                <!-- If the user is not a member yet -->
                                                <a href="#" class="view_more_btn hide" style="text-decoration: none; color: inherit;" data-bs-toggle="modal" data-bs-target="#joinClubModal<?= $item['clubID'] ?>">Join</a>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <!-- If the user is not eligible (age is outside the club's range) -->
                                            <p class="text-danger">You cannot join this Club.</p>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Club Application Modal -->
                        <div class="modal fade" id="joinClubModal<?= $item['clubID'] ?>" tabindex="-1" role="dialog" aria-labelledby="joinClubModalLabel<?= $item['clubID'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered application-modal" role="document">
                                <div class="modal-content">
                                    <div class="modal-body mx-lg-4 mb-3">
                                        <div class="application-form-heading d-flex justify-content-between my-3">
                                            <h5 class="modal-title mb-3" id="joinClubModalLabel<?= $item['clubID'] ?>">Club Application Form</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <!-- Club Details -->
                                        <h3 class="club-name mb-3"><?= $item['clubName'] ?></h3>
                                        <p class="member-count">Total number of members: 21</p>
                                        <p class="age-group"> Can join age <?= $item['clubMinAge'] ?> - <?= $item['clubMaxAge'] ?></p>
                                        <p class="whole-description mt-4 mb-5"><?= $item['clubDescription'] ?></p>

                                        <div class="join-club d-flex justify-content-between align-items-center">
                                            <div class="question">Do you want to Join?</div>
                                            <a href="./application-form-club.php?id=<?= $item['clubID'] ?>" class="join-btn hide" data-bs-toggle="modal">Join Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

            </div>
        </div>
    </div>

    <?php
    require_once('../include/footer.php');
    require_once('../include/js.php');
    ?>
</body>
</html>
