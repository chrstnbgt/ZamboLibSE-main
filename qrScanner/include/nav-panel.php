<?php
require_once '../classes/librarianfull.class.php';
require_once '../tools/functions.php';

// Check if the user is logged in
if (!isset($_SESSION['librarianID'])) {
    // Redirect to the login page or another page as needed
    header("Location: ../index.php");
    exit();
} 

// Fetch information for the logged-in user
$librarianID = $_SESSION['librarianID'];

$librarian = new Librarian();

$record3 = $librarian->fetch($librarianID);  // Assuming that the fetch method returns librarian information
if ($record3) {
    $librarian->librarianID = $record3['librarianID'];
    $librarian->librarianLastName = $record3['librarianLastName'];
    $librarian->librarianMiddleName = $record3['librarianMiddleName'];
    $librarian->librarianFirstName = $record3['librarianFirstName'];
    $librarian->librarianEmail = $record3['librarianEmail'];
    $librarian->librarianDesignation = $record3['librarianDesignation'];
    $librarian->librarianContactNo = $record3['librarianContactNo'];
    $librarian->librarianEmployment = $record3['librarianEmployment'];
    $librarian->librarianImage = $record3['librarianImage'];
} else {
    // Handle the case where user information couldn't be retrieved
    // You might want to redirect to an error page or handle it in another way
    header("Location: #");
    exit();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light px-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
        <img src="../images/zc_lib_seal.png" alt="" class="seal ms-2">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav text-center">
        <li class="nav-item">
          <a class="nav-link profile-name" href="#"><?php echo $librarian->librarianFirstName . ' ' . $librarian->librarianLastName ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link profile-name" href="../webpages/homepage.php">Homepage</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../tools/logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
