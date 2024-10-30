<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <!-- تضمين Flatpickr للواجهة التاريخية -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/style_time22.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body {
            background-image: url("{{ asset('images/backgrond.webp') }}");
            /* تأكد من مسار الصورة الصحيح */
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
<x-user-dropdown-menu/>
<div class="wrapper">
    <div class="calendar_box">
        <div class="calendar-header">
            <h2>Select a Date and Time</h2>
        </div>
        <div id="calendar" class="flatpickr-inline"></div>
        <div id="times-container">
            <p id="instruction">Select a date to view available times.</p>
            <div id="times-list"></div>
        </div>
        <div class="book-btn">
            <button id="book-now" disabled onclick="submitBooking()"><span>🕒</span> Book Now</button>
        </div>
    </div>
</div>


<form id="booking-form" method="POST" action="{{url($path)}}">
    @csrf
    <input type="hidden" name="time_slot" id="time_slot_input" value="">
    <input type="hidden" name="appointment_date" id="appointment_date_input" value="">
</form>
<script>
    // Initialize availableTimes from controller data
    const availableTimes = @json($availableTimes) ?? {};
    const enabledDates = Object.keys(availableTimes);

    let selectedDate = null;
    let selectedTime = null;

    // Get today's date in 'YYYY-MM-DD' format based on local timezone
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const formattedToday = `${yyyy}-${mm}-${dd}`;

    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#calendar", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            inline: true,
            minDate: "today",
            locale: "en", // Change dynamically if needed
            enable: enabledDates,
            disable: [
                function(date) {
                    // Disable Saturdays (6) and Sundays (0)
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ],
            onChange: function (selectedDates, dateStr) {
                selectedDate = dateStr;
                displayAvailableTimes(dateStr);
            },
        });
    });

    function displayAvailableTimes(date) {
        const timesList = document.getElementById('times-list');
        const instruction = document.getElementById('instruction');
        timesList.innerHTML = '';
        selectedTime = null;
        document.getElementById('book-now').disabled = true;
        document.getElementById('book-now').setAttribute('aria-disabled', 'true');

        if (availableTimes[date] && availableTimes[date].slots.length > 0) {
            instruction.textContent = 'Select a preferred time:';

            availableTimes[date].slots.forEach(slot => {
                // Disable past slots if the selected date is today
                if (date === formattedToday) {
                    const [hour, minute, second] = slot.start_time.split(':').map(Number);
                    const slotTime = new Date();
                    slotTime.setHours(hour, minute, second, 0);

                    if (slotTime < today) {
                        // Skip adding this slot as it's in the past
                        return;
                    }
                }

                const timeElement = document.createElement('button');
                timeElement.className = 'time-slot';
                timeElement.innerText = formatTime(slot.start_time);
                timeElement.setAttribute('type', 'button');
                timeElement.setAttribute('role', 'option');
                timeElement.setAttribute('aria-selected', 'false');
                timeElement.setAttribute('tabindex', '0');

                timeElement.addEventListener('click', function () {
                    clearSelectedSlots();
                    timeElement.classList.add('selected');
                    timeElement.setAttribute('aria-selected', 'true');
                    selectedTime = slot.start_time;
                    selectTimeSlot(date, slot.id, slot.start_time);
                });

                timeElement.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        timeElement.click();
                    }
                });

                timesList.appendChild(timeElement);
            });

            // Handle case where all slots are in the past
            if (timesList.children.length === 0) {
                instruction.textContent = 'No available times for this day.';
            }
        } else {
            instruction.textContent = 'No available times for this day.';
        }
    }

    function formatTime(timeString) {
        const [hour, minute] = timeString.split(':');
        const hourInt = parseInt(hour, 10);
        const amPm = hourInt >= 12 ? 'PM' : 'AM';
        const formattedHour = hourInt % 12 || 12;
        return `${formattedHour}:${minute} ${amPm}`;
    }

    function selectTimeSlot(date, timeSlotId, time) {
        // If using composite ID
        // const compositeId = `${date}-${timeSlotId}`;
        document.getElementById('time_slot_input').value = timeSlotId;
        document.getElementById('appointment_date_input').value = date;

        document.getElementById('book-now').disabled = false;
        document.getElementById('book-now').setAttribute('aria-disabled', 'false');
        document.getElementById('book-now').focus();
    }

    function submitBooking() {
        if (selectedDate && selectedTime) {
            const formattedTime = formatTime(selectedTime);
            if (confirm(`Confirm booking on ${selectedDate} at ${formattedTime}?`)) {
                document.getElementById('booking-form').submit();
            }
        } else {
            alert("Please select a date and time before booking.");
        }
    }

    function clearSelectedSlots() {
        const allTimeSlots = document.querySelectorAll('.time-slot');
        allTimeSlots.forEach(slot => {
            slot.classList.remove('selected');
            slot.setAttribute('aria-selected', 'false');
        });
    }
</script>

<style>
    div.flatpickr-days div.dayContainer span.flatpickr-day.flatpickr-disabled {
        color: #161515;
        /* رمادي للأيام غير المتاحة */
        opacity: 0.5;
        cursor: not-allowed;
        /* عدم السماح بالنقر */
        pointer-events: none;
        /* منع التفاعل */
    }
</style>

</body>
</html>
