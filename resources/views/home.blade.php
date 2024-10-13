<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Interface</title>

    <!-- Google Fonts -->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins&amp;display=swap'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3/dist/notiflix-aio-3.2.5.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style_home2.css') }}"> <!-- External CSS -->
</head>
<body>
<x-user-dropdown-menu/>

<!-- Main Container -->
<div class="wrapper">
    <div class="main_box">
        <div class="main-header">
            <span>Main Page</span>
        </div>
        <div class="buttons-container">
            <button class="button" onclick="goToAppointment()">Book an Appointment</button>
            <button class="button" onclick="goToVaccine()">Vaccine Booking</button> <!-- Vaccine Booking Button -->
            <button class="button" onclick="goToDrugBooking()">Dispense Medication Booking</button>
            <!-- Medication Booking Button -->
        </div>
    </div>
</div>
<x-popup-message/>

<script>
    // Navigate to the Appointment Booking page
    function goToAppointment() {
        window.location.href = "{{ route('showClinic') }}"; // Adjust to your route name
    }

    // Navigate to the Vaccine Booking page
    function goToVaccine() {
        window.location.href = "{{ route('emergency') }}"; // Adjust to your route name
    }

    // Navigate to the Medication Booking page
    function goToDrugBooking() {
        window.location.href = "{{ route('dispense_medications') }}"; // Adjust to your route name
    }
</script>

<style>
    /* Background styling */
    body {
        background-image: url("{{ asset('images/backgrond.webp') }}"); /* Set background image */
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
</style>

</body>
</html>
