<?php
// Assume this file is fetch_hospitals.php

// Include your database connection
require('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve the division and area from the POST request
    $division = $_POST['division'];
    $area = $_POST['area'];

    // Fetch division ID
    $sqlDivision = "SELECT division_id FROM divisions WHERE division_name = ?";
    $stmtDivision = $conn->prepare($sqlDivision);
    $stmtDivision->bind_param("s", $division);
    $stmtDivision->execute();
    $resultDivision = $stmtDivision->get_result();

    if ($resultDivision->num_rows > 0) {
        $rowDivision = $resultDivision->fetch_assoc();
        $divisionId = $rowDivision['division_id'];

        // Fetch area ID
        $sqlArea = "SELECT area_id FROM areas WHERE division_id = ? AND area_name = ?";
        $stmtArea = $conn->prepare($sqlArea);
        $stmtArea->bind_param("is", $divisionId, $area);
        $stmtArea->execute();
        $resultArea = $stmtArea->get_result();

        if ($resultArea->num_rows > 0) {
            $rowArea = $resultArea->fetch_assoc();
            $areaId = $rowArea['area_id'];

            // Fetch hospitals based on division ID and area ID
            // Fetch hospitals based on division ID and area ID
$sqlHospitals = "SELECT DISTINCT hospital_name FROM hospitals WHERE division_id = ? AND area_id = ?";
$stmtHospitals = $conn->prepare($sqlHospitals);
$stmtHospitals->bind_param("ii", $divisionId, $areaId);
$stmtHospitals->execute();
$resultHospitals = $stmtHospitals->get_result();

$hospitals = array();
if ($resultHospitals->num_rows > 0) {
    while ($rowHospital = $resultHospitals->fetch_assoc()) {
        $hospitals[] = $rowHospital['hospital_name'];
    }
}

// Remove duplicate hospitals (if any) using array_unique
$hospitals = array_unique($hospitals);

// Return hospital names as JSON
echo json_encode($hospitals);

        } else {
            echo "No hospitals found for the selected area.";
        }
    } else {
        echo "Division not found.";
    }

    // Close the database connection
    $conn->close();
}
?>
