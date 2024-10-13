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
</head>
<body>

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

<!-- نموذج الحجز -->
<form id="booking-form" method="POST" action="{{ route('book.slot') }}">
    @csrf
    <input type="hidden" name="time_slot" id="time_slot_input" value="">
    <input type="hidden" name="appointment_date" id="appointment_date_input" value="">
</form>

<script>
    // إعداد البيانات (هنا ستضع البيانات المتاحة لديك للجلسات)
    const availableTimes = @json($availableTimes);

    let selectedDate = null;
    let selectedTime = null;

    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#calendar", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            inline: true,
            onChange: function (selectedDates, dateStr) {
                selectedDate = dateStr;
                displayAvailableTimes(dateStr);
            },
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                const dateObj = dayElem.dateObj;
                const formattedDate = dateObj.toISOString().split('T')[0];

                if (!(formattedDate in availableTimes)) {
                    dayElem.classList.add("disabled");
                }
            }
        });
    });

    // عرض الأوقات المتاحة حسب التاريخ
    function displayAvailableTimes(date) {
        const timesList = document.getElementById('times-list');
        const instruction = document.getElementById('instruction');
        timesList.innerHTML = '';
        selectedTime = null;
        document.getElementById('book-now').disabled = true;

        if (availableTimes[date] && availableTimes[date].slots.length > 0) {
            instruction.textContent = 'Select a preferred time:';

            availableTimes[date].slots.forEach(slot => {
                const timeElement = document.createElement('div');
                timeElement.className = 'time-slot';
                timeElement.innerText = formatTime(slot.start_time);

                timeElement.addEventListener('click', function () {
                    clearSelectedSlots();
                    timeElement.classList.add('selected');
                    selectedTime = slot.start_time;
                    selectTimeSlot(slot.id, slot.start_time, date);
                });

                timesList.appendChild(timeElement);
            });
        } else {
            instruction.textContent = 'No available times for this day.';
        }
    }

    // تنسيق الوقت إلى AM/PM
    function formatTime(timeString) {
        const [hour, minute] = timeString.split(':');
        const amPm = hour >= 12 ? 'PM' : 'AM';
        const formattedHour = hour % 12 || 12;
        return `${formattedHour}:${minute} ${amPm}`;
    }

    // تحديد الوقت المحدد لتحديث النموذج
    function selectTimeSlot(timeSlotId, time, date) {
        document.getElementById('time_slot_input').value = timeSlotId;
        document.getElementById('appointment_date_input').value = date;

        document.getElementById('book-now').disabled = false; // تمكين زر الحجز
    }

    // دالة لإرسال النموذج عند الضغط على الزر "Book Now"
    function submitBooking() {
        if (selectedDate && selectedTime) {
            document.getElementById('booking-form').submit();
        } else {
            alert("Please select a date and time before booking.");
        }
    }

    // إزالة التأثير من الأوقات السابقة عند اختيار وقت جديد
    function clearSelectedSlots() {
        const allTimeSlots = document.querySelectorAll('.time-slot');
        allTimeSlots.forEach(slot => slot.classList.remove('selected'));
    }
</script>

</body>
</html>
