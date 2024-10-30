<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Clinic</title>
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
            <span>Select Clinic</span>
        </div>
        <div class="buttons-container">
            @foreach ($clinics as $clinic)
                <div class="clinic-card" onclick="window.location.href='{{ url($path . '/' . $clinic->id) }}'">
                    <img src="{{ asset('images/image.webp') }}" alt="Clinic">
                    <div class="card-content">
                        <h2>{{ $clinic->name }}</h2>
                        <p class="clinic-location">
                            <span>City: {{ $clinic->city->name }}</span> &#x2192;
                            <span>District: {{ $clinic->district->name }}</span>
                        </p>
                        <button class="button">Book Now</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</body>

</html>
