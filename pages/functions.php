<?php
// functions.php

function getBedAvailability($hospitalName, $conn) {
    // Assuming hospitals table has 'id' column
    $stmt = $conn->prepare("SELECT bed_id, is_available FROM bed_availability WHERE hospital_id = (SELECT hospital_id FROM hospitals WHERE hospital_name = ?)");
    $stmt->bind_param("s", $hospitalName);
    $stmt->execute();
    $result = $stmt->get_result();
    $bedAvailability = array();

    while ($row = $result->fetch_assoc()) {
        $bedAvailability[$row['bed_id']] = $row['is_available'];
    }

    return $bedAvailability;
}
?>
