<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Time Slots</title>
    <link rel="stylesheet" href="{{ asset('css/style_time.css') }}">
</head>

<body>
<x-user-dropdown-menu/>

<div class="container">
    <h1>Available Time Slots</h1>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="error-message">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Display success message -->
    @if (session('success'))
        <div class="success-message">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Day Buttons -->
    <div class="day-bar" id="day-bar">
        @foreach($availableSlotsByDate as $date => $data)
            <button data-date="{{ $date }}">
                <span class="date">{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</span><br>
                <span class="day">{{ $data['day'] }}</span>
            </button>
        @endforeach
    </div>

    <!-- Time Slots Grid -->
    <div class="time-slot-grid" id="time-slots">
        <!-- Time slots will be dynamically inserted here -->
    </div>

    <!-- Book Now Button -->
    <div class="book-btn">
        <button id="book-now">Book Now</button>
    </div>

    <!-- Hidden Form -->
    <form id="booking-form" method="POST" action="{{ route('book.slot') }}">
        @csrf
        <input type="hidden" name="time_slot" id="time_slot_input" value="">
        <input type="hidden" name="appointment_date" id="appointment_date_input" value="">
    </form>
</div>

<script>
    // Data from the controller
    const freeSlots = @json($availableSlotsByDate);

    let selectedDate = null;
    let selectedTimeSlotId = null;

    document.addEventListener('DOMContentLoaded', function() {
        const dayButtons = document.querySelectorAll('.day-bar button');

        // Attach event listeners to day buttons
        dayButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove 'active' class from all buttons
                dayButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Set selected date
                selectedDate = this.getAttribute('data-date');

                // Load time slots for the selected date
                loadTimeSlots(selectedDate);
            });
        });

        // Automatically select the first day
        if (dayButtons.length > 0) {
            dayButtons[0].click();
        }

        // Book Now button handler
        document.getElementById('book-now').addEventListener('click', function() {
            if (selectedTimeSlotId && selectedDate) {
                // Set form values
                document.getElementById('time_slot_input').value = selectedTimeSlotId;
                document.getElementById('appointment_date_input').value = selectedDate;

                // Submit the form
                document.getElementById('booking-form').submit();
            } else {
                alert('Please select a date and time slot.');
            }
        });
    });

    function loadTimeSlots(date) {
        const timeSlotsContainer = document.getElementById('time-slots');
        timeSlotsContainer.innerHTML = ''; // Clear previous slots

        const slots = freeSlots[date]?.slots;

        if (!slots || slots.length === 0) {
            timeSlotsContainer.innerHTML = '<p>No available time slots for this date.</p>';
            return;
        }

        slots.forEach(slot => {
            const card = document.createElement('div');
            card.classList.add('time-slot');

            const button = document.createElement('button');
            button.textContent = slot.start_time;

            card.addEventListener('click', function() {
                // Remove 'selected' class from all time slots
                document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');

                // Set selected time slot ID
                selectedTimeSlotId = slot.id;
            });

            card.appendChild(button);
            timeSlotsContainer.appendChild(card);
        });
    }
</script>
<style>
    body {
        background-image: url("{{ asset('images/backgrond.webp') }}");
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        position: relative;
    }
</style>
</body>

</html>
