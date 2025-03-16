<?php
    //resume session here to fetch session values
    session_start();
    // Check if the user is logged in
    if (!isset($_SESSION['userID'])) {      // Redirect to the login page or another page as needed
      header("Location: ../index.php");
      exit();
    } 

    // Fetch information for the logged-in user
    $userID = $_SESSION['userID'];

?>




<!DOCTYPE html>
<html lang="en">

<?php
  $title = 'Zamboanga City Library';
  $courses = 'active';
  require_once('../include/head.php');
?>

<body>
  <?php
    require_once('../include/nav-panel.php');
    require_once('../classes/events.class.php');
    require_once ('../tools/functions.php');

    $events = new Events();

    $eventsArray = $events->show();
    $counter = 1;

  ?>
      <section class="overlay"></section>
      
      <div class="main min-vh-100">
        <div class="container mt-3">
          <!-- NAVIGATIONS -->
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="true">Events</button>
            </li>

            <li class="nav-item" role="presentation">
              <button class="nav-link" id="announcements-tab" data-bs-toggle="tab" data-bs-target="#announcements" type="button" role="tab" aria-controls="announcements" aria-selected="false">Announcements</button>
            </li>
          </ul>

          <!-- CONTENTS -->
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                <div class="container mt-4">
                    <?php 
                        require_once('../classes/announcements.class.php');

                        $announcements = new Announcements();

                        $announcementsArray = $announcements->showAnnouncements();
                        $counter = 1;
                    ?>
                    <h4 class="ms-3">Announcements</h4>
                    <div class="row d-flex">
                        <?php
                        if ($announcementsArray) {
                            $counter = 0;
                            foreach ($announcementsArray as $item) {
                        ?>
                                <div class=" col-12 col-lg-4 mb-3">
                                    <!-- List View DIV -->
                                    <div class="announcements-list d-flex flex-column mx-1 my-2">
                                        <div class="d-flex justify-content-between">
                                        </div>
                                        <div class="header_card d-flex align-items-center  mb-2">
                                            <h4 class="event_title"><?= $item['eaTitle'] ?></h4>
                                        </div>
                                        <!-- Hold Data -->
                                        <div class="class d-none">
                                            <?= $item['eaStartDate'] ?>
                                            <?= $item['eaEndDate'] ?>
                                            <?= $item['eaStartTime'] ?>
                                            <?= $item['eaEndTime'] ?>
                                        </div>
                                        <p class="date_time d-flex align-items-center">
                                            <i class="bx bx-calendar-plus icon pe-2"></i>
                                            <span class="">
                                                <?php
                                                $formattedStartDate = date('F j, Y', strtotime($item['eaStartDate']));
                                                $formattedEndDate = date('F j, Y', strtotime($item['eaEndDate']));
                                                $formattedStartTime = date('h:ia', strtotime($item['eaStartTime']));
                                                $formattedEndTime = date('h:ia', strtotime($item['eaEndTime']));

                                                // Check if start and end date are the same
                                                if ($formattedStartDate == $formattedEndDate) {
                                                    echo "{$formattedStartDate} | {$formattedStartTime} - {$formattedEndTime}";
                                                } else {
                                                    echo "{$formattedStartDate} - {$formattedEndDate} | {$formattedStartTime} - {$formattedEndTime}";
                                                }
                                                ?>
                                            </span>
                                        </p>
                                        <p class="club-description"><?= strlen($item['eaDescription']) > 100 ? substr($item['eaDescription'], 0, 100) . '...' : $item['eaDescription'] ?></p>
                                    </div>
                                </div>
                        <?php
                                $counter++;
                                if ($counter % 3 == 0) {
                                    echo '</div><div class="row d-flex">';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>


          <div class="tab-pane fade show active" id="events" role="tabpanel" aria-labelledby="events-tab">
          <div class="container">
                <?php 
                $events = new Events();
                $eventsArray = $events->show();
                $filter = $_GET['filter'] ?? 'All';

                // Sort events by start date (latest first)
                usort($eventsArray, function($a, $b) {
                    return strtotime($b['eventStartDate'] . ' ' . $b['eventStartTime']) - strtotime($a['eventStartDate'] . ' ' . $a['eventStartTime']);
                });

                // Add event status and status color
                $currentDate = date('Y-m-d');
                $currentTime = date('H:i:s');
                foreach ($eventsArray as &$event) {
                    $eventStartDate = $event['eventStartDate'];
                    $eventStartTime = $event['eventStartTime'];
                    $eventEndDate = $event['eventEndDate'];
                    $eventEndTime = $event['eventEndTime'];

                    if ($currentDate < $eventStartDate || ($currentDate == $eventStartDate && $currentTime < $eventStartTime)) {
                        $event['status'] = "Upcoming";
                        $event['statusColor'] = "white";
                    } elseif ($currentDate > $eventEndDate || ($currentDate == $eventEndDate && $currentTime > $eventEndTime)) {
                        $event['status'] = "Completed";
                        $event['statusColor'] = "white";
                    } else {
                        $event['status'] = "Ongoing";
                        $event['statusColor'] = "white";
                    }
                }
                unset($event); // Unset the reference to avoid unintended modifications

                // Filter events based on the selected filter
                $filteredEvents = array_filter($eventsArray, function($event) use ($filter) {
                    return $filter === 'All' || $event['status'] === $filter;
                });

                // Show only the latest 3 events initially
                $visibleEvents = array_slice($filteredEvents, 0, 3);
                $remainingEvents = array_slice($filteredEvents, 3);
                ?>

                <div class="col d-flex justify-content-start align-items-center py-4">
                    <h4 class="heading-label-2 ms-3 me-3 mt-2">Events</h4>
                    <div class="col d-flex flex-column">
                        <p class="label mt-2 me-2">Filter by:</p>
                        <div class="dropdown">
                            <button class="btn btn-custom-filter dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php
                                switch ($filter) {
                                    case 'All': echo 'All'; break;
                                    case 'Upcoming': echo 'Upcoming'; break;
                                    case 'Completed': echo 'Completed'; break;
                                    case 'Ongoing': echo 'Ongoing'; break;
                                    default: echo 'Filter'; break;
                                }
                                ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item <?= ($filter == 'All') ? 'active' : '' ?>" href="?filter=All">All</a></li>
                                <li><a class="dropdown-item <?= ($filter == 'Upcoming') ? 'active' : '' ?>" href="?filter=Upcoming">Upcoming</a></li>
                                <li><a class="dropdown-item <?= ($filter == 'Completed') ? 'active' : '' ?>" href="?filter=Completed">Completed</a></li>
                                <li><a class="dropdown-item <?= ($filter == 'Ongoing') ? 'active' : '' ?>" href="?filter=Ongoing">Ongoing</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="container d-flex justify-content-end mt-3">
                        <div class="position-relative w-50" style="max-width: 400px;">
                            <input type="text" id="search-bar-cus" class="form-control ps-5" placeholder="Search events...">
                            <i class="bi bi-search search-icon"></i>
                        </div>
                    </div>


                </div>

                <div id="event-list" class="row d-flex">
                    <?php foreach ($visibleEvents as $event): ?>
                        <div class="col-12 col-lg-4 mb-3">
                            <div class="list_view_div d-flex flex-column mx-1 sm-my-2">
                                <div class="d-flex justify-content-end">
                                    <p class="status" style="color: <?= $event['statusColor'] ?>;"><?= $event['status'] ?></p>
                                </div>
                                <div class="header_card d-flex align-items-center mb-2">
                                    <h4 class="event_title"><?= $event['eventTitle'] ?></h4>
                                </div>
                                <p class="date_time d-flex align-items-center">
                                    <i class="bx bx-calendar-plus icon pe-2"></i>
                                    <span>
                                        <?php
                                        $formattedStartDate = date('F j, Y', strtotime($event['eventStartDate']));
                                        $formattedEndDate = date('F j, Y', strtotime($event['eventEndDate']));
                                        $formattedStartTime = date('h:ia', strtotime($event['eventStartTime']));
                                        $formattedEndTime = date('h:ia', strtotime($event['eventEndTime']));

                                        if ($formattedStartDate == $formattedEndDate) {
                                            echo "{$formattedStartDate} | {$formattedStartTime} - {$formattedEndTime}";
                                        } else {
                                            echo "{$formattedStartDate} - {$formattedEndDate} | {$formattedStartTime} - {$formattedEndTime}";
                                        }
                                        ?>
                                    </span>
                                </p>
                                <p class="club-description"><?= strlen($event['eventDescription']) > 100 ? substr($event['eventDescription'], 0, 100) . '...' : $event['eventDescription'] ?></p>
                                <div class="btn-container d-flex justify-content-end">
                                    <a class="view_more view_more_btn" href="./event-details.php?id=<?= $event['eventID']; ?>">View More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($remainingEvents)): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <button id="show-more-btn" class="btn btn-custom">Show More</button>
                    </div>
                    <div id="remaining-events" style="display: none;">
                        <?php foreach ($remainingEvents as $event): ?>
                            <div class="col-12 col-lg-4 mb-3">
                                <div class="list_view_div d-flex flex-column mx-1 sm-my-2">
                                    <div class="d-flex justify-content-end">
                                        <p class="status" style="color: <?= $event['statusColor'] ?>;"><?= $event['status'] ?></p>
                                    </div>
                                    <div class="header_card d-flex align-items-center mb-2">
                                        <h4 class="event_title"><?= $event['eventTitle'] ?></h4>
                                    </div>
                                    <p class="date_time d-flex align-items-center">
                                        <i class="bx bx-calendar-plus icon pe-2"></i>
                                        <span>
                                            <?php
                                            $formattedStartDate = date('F j, Y', strtotime($event['eventStartDate']));
                                            $formattedEndDate = date('F j, Y', strtotime($event['eventEndDate']));
                                            $formattedStartTime = date('h:ia', strtotime($event['eventStartTime']));
                                            $formattedEndTime = date('h:ia', strtotime($event['eventEndTime']));

                                            if ($formattedStartDate == $formattedEndDate) {
                                                echo "{$formattedStartDate} | {$formattedStartTime} - {$formattedEndTime}";
                                            } else {
                                                echo "{$formattedStartDate} - {$formattedEndDate} | {$formattedStartTime} - {$formattedEndTime}";
                                            }
                                            ?>
                                        </span>
                                    </p>
                                    <p class="club-description"><?= strlen($event['eventDescription']) > 100 ? substr($event['eventDescription'], 0, 100) . '...' : $event['eventDescription'] ?></p>
                                    <div class="btn-container d-flex justify-content-end">
                                        <a class="view_more view_more_btn" href="./event-details.php?id=<?= $event['eventID']; ?>">View More</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

    <script>
    document.getElementById('show-more-btn').addEventListener('click', function() {
        const remainingEvents = document.getElementById('remaining-events').innerHTML;
        document.getElementById('event-list').innerHTML += remainingEvents;
        this.remove(); // Remove the "Show More" button
    });
    </script>

    <script>
    document.getElementById('search-bar-cus').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let eventCards = document.querySelectorAll('.list_view_div');

        eventCards.forEach(function(card) {
            let title = card.querySelector('.event_title').textContent.toLowerCase();
            let description = card.querySelector('.club-description').textContent.toLowerCase();

            if (title.includes(searchText) || description.includes(searchText)) {
                card.parentElement.style.display = 'block';
            } else {
                card.parentElement.style.display = 'none';
            }
        });
    });
    </script>


          </div>

          </div>
        </div>
      </div>

              <div class="container col-6 mt-5"></div>
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
