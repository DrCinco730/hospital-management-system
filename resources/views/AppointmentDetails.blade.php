<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation</title>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins&display=swap'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3/dist/notiflix-aio-3.2.5.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style_Appointment_Confirmation.css') }}">
</head>

<body>
<x-popup-message/>
<x-user-dropdown-menu/>


<button class="button return-button" onclick="goToHome()">
    <i class="fas fa-home"></i>
</button>
<div class="wrapper">
    <div class="appointment-box">
        <h2>Appointment Details</h2>
        <div class="appointment-details">
            <p><span>Appointment Date:</span> {{ $appointmentDetails['appointment_date'] }}</p>
            <p><span>Start Time:</span> {{ $appointmentDetails['start_time'] }}</p>
            <p><span>Symptoms:</span></p>
            <ul>
                @foreach ($appointmentDetails['symptoms'] as $symptom)
                    <li>{{ $symptom }}</li>
                @endforeach
            </ul>
        </div>
        <div class="countdown-title">Time Remaining Until Your Appointment:</div>
        <div class="countdown-container" id="countdown">
            <div class="countdown-item">
                <div id="days">0</div>
                <span>Days</span>
            </div>
            <div class="countdown-item">
                <div id="hours">0</div>
                <span>Hours</span>
            </div>
            <div class="countdown-item">
                <div id="minutes">0</div>
                <span>Minutes</span>
            </div>
            <div class="countdown-item">
                <div id="seconds">0</div>
                <span>Seconds</span>
            </div>
        </div>
        <button class="cancel-appointment-button" id="cancelButton">Cancel Appointment</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notiflix@3/dist/notiflix-aio-3.2.5.min.js"></script>
<script>
    function goToHome() {
        window.location.href = "/home"; // Change this to your actual home route
    }
    function cancelAppointment() {
        Notiflix.Confirm.show(
            'Cancel Appointment',
            'Do you really want to cancel this appointment?',
            'Yes, cancel it!',
            'No, keep it',
            function okCallback() {
                // Redirect to the cancel endpoint
                window.location.href = '{{ route('cancel.appointment') }}';
            },
            function cancelCallback() {
                Notiflix.Notify.info('Your appointment has not been cancelled.');
            }
        );
    }

    document.getElementById("cancelButton").addEventListener("click", cancelAppointment);

    function startCountdown() {
        const appointmentDate = new Date("{{ $appointmentDetails['appointment_date'] }}T{{ $appointmentDetails['start_time'] }}");

        function updateCountdown() {
            const now = new Date();
            const timeDifference = appointmentDate - now;

            if (timeDifference <= 0) {
                document.getElementById("countdown").innerHTML = "The appointment time has arrived!";
                clearInterval(interval);
                return;
            }

            const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

            document.getElementById("days").innerHTML = days;
            document.getElementById("hours").innerHTML = hours;
            document.getElementById("minutes").innerHTML = minutes;
            document.getElementById("seconds").innerHTML = seconds;
        }

        const interval = setInterval(updateCountdown, 1000);
        updateCountdown();
    }

    document.addEventListener("DOMContentLoaded", startCountdown);
</script>
<style>
    body {
        background-image: url("{{ asset('images/backgrond.webp') }}");
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
</style>
</body>

</html>
