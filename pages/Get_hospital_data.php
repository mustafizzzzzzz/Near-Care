<!-- get_hospital_data.php -->
<?php
require('db_connect.php');
require('functions.php');

// Get the hospital name from the URL
$hospitalName = isset($_GET['hospital']) ? $_GET['hospital'] : null;

// Use getHospitalData function from functions.php
$hospitalData = getHospitalData($hospitalName, $conn);

// Return the hospital data as JSON
header('Content-Type: application/json');
echo json_encode($hospitalData);

?>
