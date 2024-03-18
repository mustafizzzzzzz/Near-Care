<?php
require('db_connect.php');
session_start();


// Retrieve the hospital ID and selected bed from the query parameters
$hospitalId = $_GET['hospital_id'];
$selectedBed = $_GET['selected_bed'];


// Fetch hospital data
$hospitalQuery = $conn->prepare("SELECT hospital_name, hospital_address, bed_cost FROM hospitals WHERE hospital_id = ?");
$hospitalQuery->bind_param("i", $hospitalId);


if ($hospitalQuery->execute()) {
    $hospitalResult = $hospitalQuery->get_result();


    if ($hospitalResult->num_rows > 0) {
        $hospitalData = $hospitalResult->fetch_assoc();
        $hospitalName = $hospitalData['hospital_name'];
        $hospitalAddress = $hospitalData['hospital_address'];


        // Retrieve data from the session
        $patientName = $_SESSION['patientName'] ?? '';
        $age = $_SESSION['age'] ?? '';
        $disease = $_SESSION['disease'] ?? '';
        $area = $_SESSION['area'] ?? '';
        $hospitalName = $_SESSION['hospitalName'] ?? '';
        $hospitalAddress = $_SESSION['hospitalAddress'] ?? '';
        $bedCost = $_SESSION['bedCost'] ?? '';
        $selectedBed = $_SESSION['selectedBed'] ?? '';
        $bookingDate = $_SESSION['bookingDate'] ?? '';


        // Fixed service charge
        $fixedServiceCharge = 300;


        // Calculate total cost including the fixed service charge
        $totalCost = $bedCost + $fixedServiceCharge;
    } else {
        echo "No hospital data found for ID '$hospitalId'.";
        exit();
    }
} else {
    echo "Error fetching hospital data: " . $conn->error;
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">


<head>
    <title>Receipt</title>
    <link rel="stylesheet" href="./style/receipt.css">
</head>


<body>


    <div class="upper">
        <form method="post" action="preceipt.php">
            <input type="text" id="name1" placeholder="" value="<?php echo htmlspecialchars($patientName); ?>">
            <input type="text" id="disease1" placeholder="" value="<?php echo htmlspecialchars($disease); ?>">
            <input type="text" id="hospital1" placeholder="" value="<?php echo htmlspecialchars($hospitalName); ?>">
            <input type="text" id="bed1" placeholder="" value="<?php echo htmlspecialchars($bedCost); ?>">
            <input type="text" id="service1" placeholder="" value="">
            <input type="text" id="ambulance1" placeholder="" value="">
            <input type="text" id="total1" placeholder="" value="">
            <input type="hidden" name="hospital_id" value="<?php echo $hospitalId; ?>">
    <input type="hidden" name="selected_bed" value="<?php echo $selectedBed; ?>">
    <input type="submit" value="Book Bed">
        </form>
    </div>


    <div class="container">
        <h2>Receipt</h2>
        <label for="name" id="name">Patient Name: <?php echo htmlspecialchars($patientName); ?></label>
        <label for="age" id="age">Age: <?php echo htmlspecialchars($age); ?></label>
        <label for="disease" id="disease">Disease: <?php echo htmlspecialchars($disease); ?></label>
        <label for="area" id="area">Area: <?php echo htmlspecialchars($area); ?></label>
        <label for="hospital" id="hospital">Hospital Name: <?php echo htmlspecialchars($hospitalName); ?></label>
        <label for="hospital-address" id="hospital-address">Hospital Address: <?php echo htmlspecialchars($hospitalAddress); ?></label>
        <label for="bed" id="bed">Bed Cost: <?php echo htmlspecialchars($bedCost); ?></label>
        <label for="selected-bed" id="selected-bed">Selected Bed: <?php echo htmlspecialchars($selectedBed); ?></label>
        <label for="booking-date" id="booking-date">Booking Date: <?php echo htmlspecialchars($bookingDate); ?></label>
    </div>


    <script>
        // Fill in the form fields with the retrieved data
        document.getElementById('name1').value = "<?php echo htmlspecialchars($patientName); ?>";
        document.getElementById('disease1').value = "<?php echo htmlspecialchars($disease); ?>";
        document.getElementById('hospital1').value = "<?php echo htmlspecialchars($hospitalName); ?>";
        document.getElementById('bed1').value = "<?php echo htmlspecialchars($bedCost); ?>";
        document.getElementById('service1').value = "<?php echo htmlspecialchars($serviceCost); ?>";
        document.getElementById('ambulance1').value = "<?php echo htmlspecialchars($ambulanceCost); ?>";
        document.getElementById('total1').value = "<?php echo htmlspecialchars($totalCost); ?>";
    </script>


</body>


</html>
