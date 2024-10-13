<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Doctor</title>
    <link rel="stylesheet" href="{{ asset('css/clinic-style.css') }}">
    <style>
        body {
            background-image: url("{{ asset('images/backgrond.webp') }}");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>

<body>
<div class="wrapper">
    <div class="main_box">
        <div class="main-header">
            <span>Select Doctor</span>
        </div>
        <div class="buttons-container">
            @foreach ($doctors as $doctor)
                <a href="{{ route('saveDoctor', ['doctor_id' => $doctor->id]) }}" class="clinic-card">
                    <img src="{{ asset('images/image_dector.webp') }}" alt="Doctor">
                    <div class="card-content">
                        <h2>{{ $doctor->name }}</h2>
                        <button type="button" class="button">Book Now</button>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<script>
    function selectDoctor(doctorName) {
        alert(`Selected: ${doctorName}`);
    }
</script>
</body>

</html>
