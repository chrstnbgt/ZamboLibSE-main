<?php
require_once('../include/head.php');
require_once('../classes/events.class.php');
require_once('../classes/userfull.class.php');
require_once('../tools/functions.php');
require_once('../classes/club_application.class.php');

session_start();

// Check if the user is logged in and has appropriate permissions
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'user') {
    header('location: ../index.php');
    exit();
}

$title = 'Participant Registration';
$courses = 'active';

$clubID = isset($_GET['id']) ? $_GET['id'] : null;

if (!$clubID) {
    header('location: ../index.php');
    exit();
}

$events = new ClubForm();
$clubform = new ClubForm();

$record = $events->fetch($clubID);

if (!$record) {
    header('location: ../index.php');
    exit();
}

$clubName = $record['clubName'];

$user = new User(); // Assuming you have a User class

// Check if the user is already registered for the event
$userID = $_SESSION['userID'];
$clubMembershipID = $clubform->getClubMembershipID($clubID, $userID);
$registeredData = $clubform->fetchClubRegistrationAnswers($clubID, $clubMembershipID, $userID);

if ($registeredData) {
    // User is already registered, display filled-up form data
    $isRegistered = true;
} else {
    // User is not registered, fetch registration questions
    $isRegistered = false;
    $clubApplicationFormData = $clubform->fetchClubFormQuestions($clubID);
}

if (isset($_POST['submit-answers'])) {
    $answers = $_POST['longTextField'] ?? array();

    $success = $clubform->insertClubFormAnswers($clubID, $userID, $answers);

    if ($success) {
        header("Location: clubs.php");
        exit();
    } else {
        $errorMessage = "Failed to submit registration answers.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once('../include/head2.php'); ?>

<body>
    <?php require_once('../include/nav-panel.php'); ?>

    <section class="overlay"></section>

    <div class="main min-vh-100">
        <div class="row d-flex justify-content-center">
            <div class="form-card col-12 col-md-8 col-lg-6 mt-lg-4 mb-5">
                <div class="header-form mb-3">
                    <h3 class="form-header mb-3 ps-2 mt-2">Application Form to Join the Club</h3>
                    <h4 class="event_title_form ps-2"><?php echo $clubName; ?></h4>
                    <?php if ($isRegistered) { ?>
                        <p class="note-already mt-3">You're already apply on this club, just wait for the Club Manager's Approval of your application.</p>
                    <?php } else { ?>
                        <p class="form-instructions mt-3 ps-2">Please fill in your information.</p>
                    <?php } ?>
                </div>

                <div class="user-information">
                    <div class="personal-information">
                        <h3 class="sub-headerLabel d-flex">Personal Information <span class="note mt-1">Automatic Filled</span></h3>
                        <div class="row d-flex justify-content-center justify-content-lg-between">
                            <div class="input-group flex-column field_size4">
                                <label for="firstName" class="label mb-2">First Name</label>
                                <input type="text" name="firstName" id="firstName" class="input-1" value="<?php echo $user->userFirstName ?>" readonly>
                            </div>
                            <div class="input-group flex-column field_size5">
                                <label for="middleInitial" class="label mb-2">M.I. </label>
                                <input type="text" name="middleInitial" id="middleInitial" class="input-1" value="<?php echo substr($user->userMiddleName, 0, 1); ?>" readonly>
                            </div>
                            <div class="input-group flex-column field_size4 mb-4">
                                <label for="lastName" class="label mb-2">Last Name</label>
                                <input type="text" name="lastName" id="lastName" class="input-1" value="<?php echo $user->userLastName ?>" readonly>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center justify-content-lg-between">
                            <div class="input-group flex-column field_size3">
                                <label for="userEmail" class="label mb-2">Email</label>
                                <input type="email" name="userEmail" id="userEmail" class="input-1 me-lg-3" value="<?php echo $user->userEmail ?>" readonly>
                            </div>
                            <div class="input-group flex-column field_size3 mb-4">
                                <label for="userContactNo" class="label mb-2">Contact Number</label>
                                <input type="text" name="userContactNo" id="userContactNo" class="input-1" value="<?php echo $user->userContactNo ?>" readonly>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center justify-content-lg-between">
                            <div class="input-group flex-column field_size3">
                                <label for="userBirthdate" class="label mb-2">Birthdate</label>
                                <input type="date" name="userBirthdate" id="userBirthdate" class="input-1 me-lg-3" value="<?php echo $user->userBirthdate ?>" readonly>
                            </div>
                            <div class="input-group flex-column field_size3 mb-4">
                                <label for="userGender" class="label mb-2">Gender</label>
                                <input type="text" name="userGender" id="userGender" class="input-1" value="<?php echo $user->userGender ?>" readonly>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center justify-content-lg-between">
                            <div class="input-group flex-column field_size6">
                                <label for="userProvince" class="label mb-2">Province, City</label>
                                <input type="text" name="userProvince" id="userProvince" class="input-1" value="<?php echo $user->userProvince . ",  "  . $user->userCity; ?>" readonly>
                            </div>
                            <div class="input-group flex-column field_size5">
                                <label for="userRegion" class="label mb-2">Region</label>
                                <input type="text" name="userRegion" id="userRegion" class="input-1 me-lg-3" value="<?php echo $user->userRegion ?>" readonly>
                            </div>
                        </div>

                        <!-- Other input fields -->
                        <!-- Example:
                        <div class="row d-flex justify-content-center justify-content-lg-between">
                            <div class="input-group flex-column field_size3">
                                <label for="otherField" class="label mb-2">Other Field</label>
                                <input type="text" name="otherField" id="otherField" class="input-1" value="<?php //echo $user->otherField ?>" readonly>
                            </div>
                            <div class="input-group flex-column field_size3 mb-4">
                                <label for="anotherField" class="label mb-2">Another Field</label>
                                <input type="text" name="anotherField" id="anotherField" class="input-1" value="<?php //echo $user->anotherField ?>" readonly>
                            </div>
                        </div>
                        -->
                    </div>



                    <div class="row d-flex mt-3">
                        <div class="col-12">
                            <div class="answer-fieldcard">
                                <h3 class="sub-headerLabel d-flex">Provide the Following <span class="note mt-1">Answer the following questions</span></h3>
                                <form method="post" action="">
                                    <?php if ($isRegistered) { ?>
                                        <!-- Display already filled-up form data -->
                                        <?php foreach ($registeredData as $data) { ?>
                                            <div class="mb-3">
                                                <label  class="label ps-1"><?php echo $data['cfQuestion']; ?></label>
                                                <textarea class="form-control input-1"  rows="3" readonly><?php echo $data['cfAnswer']; ?></textarea>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <!-- Display registration form -->
                                        <?php if ($clubApplicationFormData) { ?>
                                            <?php foreach ($clubApplicationFormData as $data) { ?>
                                                <div class="mb-3">
                                                    <label for="longTextField_<?php echo $data['clubFormQuestionID']; ?>" class="label ps-1"><?php echo $data['cfQuestion']; ?></label>
                                                    <textarea class="form-control input-1" id="longTextField_<?php echo $data['clubFormQuestionID']; ?>" name="longTextField[<?php echo $data['clubFormQuestionID']; ?>]" rows="3" placeholder="Write a brief explanation." required><?php echo isset($data['cfAnswer']) ? $data['cfAnswer'] : ''; ?></textarea>
                                                </div>
                                            <?php } ?>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" name="submit-answers" class="submit-form mt-3">Submit</button>
                                            </div>
                                        <?php } else { ?>
                                            <p>No registration form data available for this event.</p>
                                        <?php } ?>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('../include/footer.php'); ?>
    <?php require_once('../include/js.php'); ?>

</body>

</html>
