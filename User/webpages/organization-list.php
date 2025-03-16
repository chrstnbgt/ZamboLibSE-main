<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: ../index.php");
    exit();
} 

$userID = $_SESSION['userID'];

$title = 'Organization Club List';
$courses = 'active';
require_once('../include/head.php');
?>

<body>
    <?php
    require_once('../include/nav-panel.php');
    require_once('../classes/organizationclub.class.php');
    require_once ('../tools/functions.php');

    // Instantiate OrganizationClub class
    $orgClub = new OrganizationClub();

    // Fetch organization clubs
    $organizationClubs = $orgClub->fetchOrganizationClubs($userID);

    // Check if organizationClubs is false, meaning an error occurred
    if ($organizationClubs === false) {
        echo "Error fetching organization clubs.";
        // You may add additional error handling here, such as redirecting to an error page
        exit();
    }
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
                <?php foreach ($organizationClubs as $item) : ?>
                    <div class=" col-12 col-lg-4 mb-3">
                        <div class="club-card club-card2 d-flex flex-column mx-2">
                            <div class="row d-flex align-content-center justify-content-center align-items-center mb-3 mt-2">
                            <div class="col-3 club-seal">
                                <div class="square-image">
                                    <img src="<?= $item['orgClubImage'] ?>" alt="Club Image">
                                </div>
                            </div>

                            <div class="col-9 club">
                                <?php if ($item['ocStatus'] == 'Approved'): ?>
                                    <h3 class="club-name d-flex ps-3"><?= $item['ocName'] ?><span class="tags-approved ms-2"><?= $item['ocStatus'] ?></span></h3>
                                <?php elseif ($item['ocStatus'] == 'Pending'): ?>
                                    <h3 class="club-name d-flex ps-3"><?= $item['ocName'] ?><span class="tags-pending ms-2"><?= $item['ocStatus'] ?></span></h3>
                                <?php else: ?>
                                    <h3 class="club-name d-flex ps-3"><?= $item['ocName'] ?></h3>
                                <?php endif; ?>
                            </div>

                            </div>
                            <div class="club-details ms-2">
                                <p class="create-datetime">Created At: <?= date('M d, Y h:i a', strtotime($item['ocCreatedAt'])) ?></p>
                                <!-- You may adjust the display of other details as needed -->
                                <div class="btn-container d-flex justify-content-end">
                                    <?php if ($item['ocStatus'] == 'Approved') : ?>
                                        <!-- Button to view club details -->
                                        <a href="./organization.php?id=<?= $item['organizationClubID'] ?>" class="view_more_btn" style="text-decoration: none; color: inherit;">Open</a>
                                    <?php else : ?>
                                        <!-- Button to view club details -->
                                        <a href="#" class="view_more_btn" style="text-decoration: none; color: inherit;" data-bs-toggle="modal" data-bs-target="#joinClubModal<?= $item['organizationClubID'] ?>">View More</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Club Application Modal -->
                    <div class="modal fade" id="joinClubModal<?= $item['organizationClubID'] ?>" tabindex="-1" role="dialog" aria-labelledby="joinClubModalLabel<?= $item['organizationClubID'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered application-modal" role="document">
                            <div class="modal-content">
                                <div class="modal-body mx-lg-4 mb-3">
                                    <div class="application-form-heading d-flex justify-content-between my-3">
                                        <h3 class="club-name d-flex"><?= $item['ocName'] ?><span class="tags-approved ms-2"><?= $item['ocStatus'] ?></span></h3>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <!-- <p class="affiliation-label"><span class="data-field">Type: </span><?= $item['organizationClubType'] ?></p> -->
                                    <p class="affiliation-label"><span class="data-field">Email: </span><?= $item['ocEmail'] ?></p>
                                    <p class="affiliation-label"><span class="data-field">Contact #: </span><?= $item['ocContactNumber'] ?></p>
                                    <p class="affiliation-label"><span class="data-field">Created At: </span><?= date('M d, Y h:i a', strtotime($item['ocCreatedAt'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php
    require_once('../include/footer.php');
    require_once('../include/js.php');
    ?>
</body>
</html>
