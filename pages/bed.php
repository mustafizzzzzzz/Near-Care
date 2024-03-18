<?php
require('db_connect.php');


// Initialize variables
$hospitalData = [];
$bedAvailability = [];


// Check if the hospital is set in the URL
if (isset($_GET['hospital'])) {
    $hospitalName = $_GET['hospital'];


    // Fetch hospital data from 'hospitals' table
    $hospitalQuery = $conn->prepare("SELECT hospital_id, hospital_address, bed_cost FROM hospitals WHERE hospital_name = ?");
    $hospitalQuery->bind_param("s", $hospitalName);
    if ($hospitalQuery->execute()) {
        $hospitalResult = $hospitalQuery->get_result();
        if ($hospitalResult->num_rows > 0) {
            $hospitalData = $hospitalResult->fetch_assoc();
            $hospitalId = $hospitalData['hospital_id'];


            // Fetch bed availability data from 'bed_availability' table
            $bedAvailabilityQuery = $conn->prepare("SELECT bed_id, is_available FROM bed_availability WHERE hospital_id = ?");
            $bedAvailabilityQuery->bind_param("i", $hospitalId);
            if ($bedAvailabilityQuery->execute()) {
                $bedAvailabilityResult = $bedAvailabilityQuery->get_result();


                if ($bedAvailabilityResult->num_rows > 0) {
                    while ($row = $bedAvailabilityResult->fetch_assoc()) {
                        $bedAvailability[$row['bed_id']] = $row['is_available'];
                    }
                } else {
                    echo "No bed availability data found for '$hospitalName'.";
                }
            } else {
                echo "Error fetching bed availability data: " . $conn->error;
            }
        } else {
            echo "No hospital data found for '$hospitalName'.";
        }
    } else {
        echo "Error fetching hospital data: " . $conn->error;
    }
} else {
    echo "No hospital selected.";
}


// Handle bed selection and update in the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selected_bed'])) {
        $selectedBed = $_POST['selected_bed'];


        // Update the bed availability status in the database
        $updateBedQuery = $conn->prepare("UPDATE bed_availability SET is_available = 0 WHERE hospital_id = ? AND bed_id = ?");
        $updateBedQuery->bind_param("is", $hospitalId, $selectedBed);
        if (!$updateBedQuery->execute()) {
            echo "Error updating bed availability: " . $conn->error;
        }


        // Redirect to the confirmation page with the selected bed information
        header("Location: confirmation.php?hospital_id=$hospitalId&selected_bed=$selectedBed");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Near Care-Hospital Bed Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./style/bed.css">
</head>
<body>
<main>
    <header>
        <h2><?php echo $hospitalName; ?></h2>
    </header>
    <div class="hospital-info">
        <!-- Display hospital information dynamically -->
        <div class="detail-item">
            <label for="hospitalAddress">Address:</label>
            <span id="hospitalAddress"><?php echo isset($hospitalData['hospital_address']) ? $hospitalData['hospital_address'] : ''; ?></span>
        </div>
        <div class="detail-item">
            <label for="bedCost">Bed Cost:</label>
            <span id="bedCost" class="bed-cost"><?php echo isset($hospitalData['bed_cost']) ? $hospitalData['bed_cost'] : ''; ?></span>
        </div>
    </div>
    <!-- Display bed availability dynamically -->
    <form method="post" action="">
        <div class="card">
            <div class="card-header py-3 d-flex bg-transparent border-bottom-0">
                <h6 class="mb-0 fw-bold">Hospital Room Booking Status</h6>
            </div>
            <div class="card-body">
                <div class="room_book">
                    <div class="row row-cols-2 row-cols-sm-4 row-cols-md-6 row-cols-lg-6 g-3">
                        <?php
                        foreach ($bedAvailability as $bedId => $availability) {
                            echo '<div class="room col">';
                            if ($availability) {
                                echo '<input type="checkbox" name="selected_bed" value="' . $bedId . '" id="' . $bedId . '" checked>';
                            } else {
                                echo '<input type="checkbox" disabled id="' . $bedId . '">';
                            }
                            
                            echo '<label for="' . $bedId . '"><i class="fa fa-bed icon" aria-hidden="true"></i><span class="text-muted">' . $bedId;
                            if (!$availability) {
                                echo '  (Booked)';
                            }
                            echo '</span></label>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <button class="cta-button" type="submit">Book Now</button>
        </div>
    </form>
</main>
</body>
</html>