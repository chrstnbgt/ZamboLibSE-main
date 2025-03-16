<?php
require_once('../classes/database.php');

// Set session timeout settings BEFORE session_start()
ini_set('session.gc_maxlifetime', 86400); // 24 hours
ini_set('session.cookie_lifetime', 86400); // 24 hours

session_start(); // Now start the session

// Refresh session on activity
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 86400)) {
    session_unset();
    session_destroy();
    header('Location: ./index.php'); // Redirect to login if session expires
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Prevent unauthorized access
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'admin') {
    header('Location: ./index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Librarians';
  $librarians = 'active-1';
  require_once('../include/head.php');
?>

<body>


    <div class="main">
        <div class="row">
            <?php
                require_once('../include/nav-panel.php');
            ?>

            <div class="col-12 col-md-8 col-lg-9">
                
                <div class="row pt-3 ps-4">
                    <div class="col-12 dashboard-header d-flex align-items-center justify-content-between">
                        <div class="heading-name">
                            <p class="pt-3">Librarian</p>
                        </div>

                    </div>

                    
                    <div class="row ps-2">
                        <div class="container ps-0 mb-2 ps-3 d-flex justify-content-between">
                                <div class="d-flex">
                                    <a href="../forms/add-librarian.php"><button type="button" class="btn add-btn justify-content-center align-items-center me-2" data-bs-toggle="modal" data-bs-target="#addLibrarianModal">
                                        <div class="d-flex align-items-center">
                                            <i class='bx bx-plus-circle button-action-icon me-2'></i>
                                            Add Librarian
                                        </div>
                                    </button></a>

                                </div>

                                <div class="d-flex">
                                    <div class="form-group col-12 col-lg-6 flex-sm-grow-1 flex-lg-grow-0 pe-2">
                                        <select name="librarian-designation" id="librarian-designation" class="form-select status-filter">
                                            <option value="">All Designations</option>
                                            <option value="Librarian 1">Librarian 1</option>
                                            <option value="Librarian 2">Librarian 2</option>
                                            <option value="Librarian 3">Librarian 3</option>
                                            <option value="Librarian 4">Librarian 4</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-12 col-lg-6 flex-sm-grow-1 flex-lg-grow-0 ps-2">
                                        <select name="librarian-employment" id="librarian-employment" class="form-select status-filter">
                                            <option value="">All Employment</option>
                                            <option value="Active">Active</option>
                                            <option value="No Longer in Service">No Longer in Service</option>
                                        </select>
                                    </div>

                                </div>      
                            </div>

                            <!-- Include for the Librarian modal -->

                        <div class="table-responsive">
                            <table id="kt_datatable_both_scrolls" class="table table-striped table-row-bordered gy-5 gs-7 user-table kt-datatable">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800">
                                        <th class="min-w-200px">#</th>
                                        <th class="min-w-200px">Librarian Name</th>
                                        <th class="min-w-150px">Designation</th>
                                        <th class="min-w-300px">Contact Number</th>
                                        <th class="min-w-200px">Email</th>
                                        <th class="min-w-100px">Employement</th>
                                        <th class="min-w-100px">Created At</th>
                                        <th class="min-w-100px">Updated At</th>
                                        <th scope="col" width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="librarianTableBody">
                                <?php
                                    include('showlibrarians.php')
                                ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php require_once('../include/js.php'); ?>
    <script>
        $(document).ready(function() {
            var table = $('#kt_datatable_both_scrolls').DataTable();

            $('#librarian-designation').on('change', function() {
                var status = $(this).val();
                table.column(2).search(status).draw();
            });

            $('#librarian-employment').on('change', function() {
                var status = $(this).val();
                table.column(5).search(status).draw();
            });
        });
    </script>

</body>
</html>