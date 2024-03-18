<?php
// get_available_beds.php


// Database connection
$host = 'localhost';
$dbname = 'hospital';
$username = 'root';
$password = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}


if (isset($_GET['hospital_name'])) {
    $hospitalName = $_GET['hospital_name'];


    // Use prepared statements to prevent SQL injection
    $stmtHospital = $pdo->prepare("SELECT hospital_id FROM hospitals WHERE LOWER(TRIM(hospital_name)) = LOWER(:hospital_name)");
    $stmtHospital->bindParam(':hospital_name', $hospitalName);
    $stmtHospital->execute();
    $hospitalDetails = $stmtHospital->fetch(PDO::FETCH_ASSOC);


    if ($hospitalDetails) {
        $hospitalId = $hospitalDetails['hospital_id'];


        // Fetch available beds for the selected hospital
        $stmtBeds = $pdo->prepare("SELECT bed_id FROM bed_availability WHERE hospital_id = :hospital_id AND is_available = 1");
        $stmtBeds->bindParam(':hospital_id', $hospitalId);
        $stmtBeds->execute();
        $availableBeds = $stmtBeds->fetchAll(PDO::FETCH_ASSOC);


        // Return the data in JSON format
        header('Content-Type: application/json');
        echo json_encode(['availableBeds' => $availableBeds]);
        exit();
    } else {
        // Handle the case where the hospital is not found
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Hospital not found']);
        exit();
    }
}


// Handle the case where the hospital_name parameter is not set
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request']);
exit();
?>
