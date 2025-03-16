<?php
require_once '../classes/userfull.class.php';
require_once  '../tools/functions.php';

  /*
      if user is not login then redirect to login page,
      this is to prevent users from accessing pages that requires
      authentication such as the dashboard
  */
  if (!isset($_SESSION['userID'])) {
    // Redirect to the login page or another page as needed
    header("Location: ../index.php");
    exit();
  } 

    // Fetch information for the logged-in user
    $userID = $_SESSION['userID'];

    // Fetch organization clubs associated with the user
    require_once('../classes/organizationclub.class.php');
    $organizationClub = new OrganizationClub();
    $organizationClubs = $organizationClub->fetchApprovedOrganizationClubs($userID);

    $user = new User();
    $record = $user->fetch($userID);
    $record2 = $user->fetch($userID);

    $record3 = $user->fetch($userID);  // Assuming that the fetch method returns user information
    if ($record2) {
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
        $user->userIDCard = $record['userIDCard'];
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
?>


<nav class="">
    <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <img src="../images/zc_lib_seal.png" alt="Logo Text" id="seal">
        <a href="../webpages/homepage.php" class="logo-name nav-top" id="logo-seal ">Zamboanga City Library</a>

    </div>

    <div class="account ">
        
    </div>
    <li class="nav-item dropdown d-flex align-items-center">
        <a class="nav-link dropdown-toggle text-dark" href="../webpages/profile.php" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?php echo isset($user->userImage) ? $user->userImage : '../images/profile_pic/default-profile.png'; ?>" alt="User Image" class="user-image"> 
            <span class="user-name"><?php echo $user->userFirstName . ' ' . $user->userLastName ?></span>
            </a>
                        
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li>
                <a  href="../webpages/profile.php" class="dropdown-item user-profile">
                    <img src="<?php echo isset($user->userImage) ? $user->userImage : '../images/profile_pic/default-profile.png'; ?>" alt="User Image" class="user-image"><?php echo $user->userFirstName . ' ' . $user->userLastName ?>
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item d-flex align-items-center align-items-center" href="./account-settings.php"><i class="bx bx-cog icon pe-1"></i>Account Settings</a></li>
            <li><a class="dropdown-item text-danger d-flex align-items-center align-items-center" href="../tools/logout.php"><i class="bx bx-log-out icon  pe-1"></i>Logout</a></li>
        </ul>
    </li>
    <div class="sidebar">
        <div class="logo">
        <img src="../images/zc_lib_seal.png" alt="Logo Text" id="seal">
        <span class="logo-name">Zamboanga City Library</span>
        </div>
        <div class="sidebar-content">
        <ul class="lists">
            <li class="list">
            <a href="../webpages/homepage.php" class="nav-link">
                <i class="bx bx-home-alt icon"></i>
                <span class="link">Home</span>
            </a>
            </li>
            <li class="list">
            <a href="../webpages/clubs.php" class="nav-link">
                <i class="bx bx-group icon"></i>
                <span class="link">Clubs</span>
            </a>
            </li>
            <li class="list">
            <!-- My Organization/Club with Nested Navigation -->
            <div class="list">
                <a href="../webpages/organization-list.php">
                <h3 class="nav-link">
                <i class='bx bxs-institution icon'></i>
                <span class="link">My Organization/Club</span>
                </h3>
                </a>
                <ul class="nested-nav">
                <?php if ($organizationClubs): ?>
                    <?php foreach ($organizationClubs as $orgClub): ?>
                        <li class="list">
                            <a href="../webpages/organization.php?id=<?php echo $orgClub['organizationClubID']; ?>" class="nav-link">
                            <div class="square-image-container">
                                <img src="<?= $orgClub['orgClubImage'] ?>" alt="User Image" class="nav-image">
                            </div>
                                <span class="link"><?= $orgClub['ocName'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Handle case where $organizationClubs is empty -->
                <?php endif; ?>


                <li class="list"><a href="../webpages/new-organization-form.php" class="nav-link link">
                    <i class="bx bx-message-square-add icon"></i>
                    <span class="link" >Add New Organization/ Club</span>
                </a></li>
                </ul>
            </div>
            <!-- End My Organization/Club with Nested Navigation -->
            <!-- <li class="list">
                <a href="../webpages/about_us.php" class="nav-link">
                <i class="bx bx-info-circle icon"></i>
                    <span class="link">About Us</span>
                </a>
            </li> -->
            </li>
        </ul>
        <div class="bottom-cotent">
            <li class="list">
            <a href="../webpages/account-settings.php" class="nav-link">
                <i class="bx bx-cog icon"></i>
                <span class="link">Settings</span>
            </a>
            </li>
            <li class="list">
            <a href="../tools/logout.php" class="nav-link">
                <i class="bx bx-log-out icon"></i>
                <span class="link">Logout</span>
            </a>
            </li>
        </div>
        </div>
    </div>
</nav>