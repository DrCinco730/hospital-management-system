<!DOCTYPE html>
<html lang="ar"> <!-- تعديل اللغة إلى العربية -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>واجهة النظام</title> <!-- تعديل عنوان الصفحة -->

    <!-- Google Fonts -->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins&amp;display=swap'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3/dist/notiflix-aio-3.2.5.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style_home_ar.css') }}"> <!-- External CSS -->
</head>
<body style="direction: rtl;"> <!-- ضبط الاتجاه إلى RTL -->
<x-user-dropdown-menu/>
<x-popup-message/>
<!-- Main Container -->
<div class="wrapper">
    <div class="main_box">
        <div class="main-header">
            <span>الصفحة الرئيسية</span> <!-- تعديل النص -->
        </div>
        <div class="buttons-container">
            <button class="button" onclick="goToAppointment()">حجز موعد</button> <!-- تعديل النص -->
            <button class="button" onclick="goToVaccine()">حجز لقاح</button> <!-- تعديل النص -->
            <button class="button" onclick="goToDrugBooking()">حجز صرف دواء</button> <!-- تعديل النص -->
        </div>
    </div>
</div>


<script>
    // Navigate to the Appointment Booking page
    function goToAppointment() {
        window.location.href = "{{ route('showClinic') }}"; // Adjust to your route name
    }

    // Navigate to the Vaccine Booking page
    function goToVaccine() {
        window.location.href = "{{ url('/vaccine/showClinic') }}"; // Adjust to your route name
    }

    // Navigate to the Medication Booking page
    function goToDrugBooking() {
        window.location.href = "{{ url('/medicine/showClinic') }}"; // Adjust to your route name
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
