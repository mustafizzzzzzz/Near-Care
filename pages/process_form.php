<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'hospital';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve form data
    $patientName = $_POST['patient-name'] ?? '';
    $age = $_POST['age'] ?? 0; // Assuming default age as 0, update it as needed
    $disease = $_POST['disease'] ?? '';
    $hospitalId = $_POST['hospital_id'] ?? 0; // Assuming default hospital ID as 0, update it as needed
    $bookingDate = $_POST['booking-date'] ?? '';

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO patients (patient_name, age, disease, hospital_id, booking_date) VALUES (:patient_name, :age, :disease, :hospital_id, :booking_date)");
    $stmt->bindParam(':patient_name', $patientName);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':disease', $disease);
    $stmt->bindParam(':hospital_id', $hospitalId);
    $stmt->bindParam(':booking_date', $bookingDate);
    $stmt->execute();

    // Redirect to a confirmation page after successful insertion
    header("Location: confirmation.php");
    exit(); // Ensure no code executes after redirection
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>