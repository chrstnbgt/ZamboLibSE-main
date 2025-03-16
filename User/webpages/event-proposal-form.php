<?php
    require_once '../classes/eventproposal.class.php';
    require_once '../classes/eventproposal.class.php';
    require_once '../tools/functions.php';
  
  if(isset($_POST['save'])){

    $proposal = new EventProposal();
    //sanitize
    $proposal->last_name = htmlentities($_POST['last_name']);
    $proposal->first_name = htmlentities($_POST['first_name']);
    $proposal->email = htmlentities($_POST['email']);
    $proposal->year_level = htmlentities($_POST['year_level']);
    $proposal->section = htmlentities($_POST['section']);
    $proposal->password = htmlentities($_POST['password']);
    $proposal->profile_pic = htmlentities($_POST['profile_pic']);

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
      $file_tmp = $_FILES['profile_pic']['tmp_name'];
      $file_size = $_FILES['profile_pic']['size'];

      // Ensure the file size is within an acceptable range (adjust as needed)
      $max_file_size = 5 * 1024 * 1024; // 5 MB
      if ($file_size > $max_file_size) {
          echo 'File size exceeds the limit.';
      } else {
          // Read the file contents
          $file_content = file_get_contents($file_tmp);

          // Update the user record in the database
          $user->profile_pic = $file_content;
      }
  }

    //validate
    if (validate_field($user->last_name) &&
    validate_field($user->first_name) &&
    validate_field($user->email) &&
    validate_field($user->year_level) &&
    validate_field($user->section) &&
    validate_field($user->password) &&
    validate_password($user->password) &&
    validate_email($user->email) && !$user->is_email_exist()){
        if($user->add()){
            header('location: courses.php');
        }else{
            echo 'An error occured while adding in the database.';
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<?php
  $title = 'Propose Event';
  $courses = 'active';
  require_once('../include/head.php');
?>

<body>
<?php
    require_once('../include/nav-panel.php');
?>

      <section class="overlay"></section>
      
      <div class="main">
        <div class="row d-flex justify-content-center">
            <!-- Registration Form -->
            <div class="form-card col-12 col-md-8 col-lg-6 mt-lg-4">
                <h3 class="form-header mb-3">Event Proposal Form</h3>
                <p class="form-instructions mt-3">Please fill in your information.</p>

                <div class="user-information">
                    <!-- Fill Form -->
                    <div class="row d-flex mt-3">
                        <div class="col-12">
                            <form>
                                <!-- Form Fields -->
                                <div class="mb-3">
                                    <label for="eventTitle" class="form-label">Proposal Subject</label>
                                    <input type="text" class="form-control" id="eventTitle" name="eventTitle">
                                </div>

                                <div class="mb-3">
                                    <label for="eventDescription" class="form-label">Description:</label>
                                    <textarea class="form-control" id="eventDescription" name="eventDescription" rows="4"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Total Capacity</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity">
                                </div>

                                <div class="mb-3">
                                    <label for="datetimes" class="form-label">Date</label>
                                    <input type="text" class="form-control" name="datetimes" />
                                </div>

                                <div class="mb-3">
                                    <label for="fileInput" class="form-label">Choose File</label>
                                    <input type="file" class="form-control" id="fileInput" name="file">
                                </div>

                                  

                                <!-- Add more form fields here -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
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