<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins&display=swap'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3/dist/notiflix-aio-3.2.5.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style_admin.css') }}">
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="user-profile">
        <i class="fas fa-user-circle user-icon"></i>
        <div class="user-info">
            <span class="user-name">{{Auth::user()->name}}</span>

            <span class="user-email">{{Auth::user()->email}}</span>
        </div>
    </div>

    <!-- Divider -->
    <hr class="divider">

    <div class="sidebar-header">
        <h3>Dashboard</h3>
    </div>
    <ul class="sidebar-menu">
        <li><button class="button" onclick="showForm('clinicForm', 'Add Clinic')">
                <i class="fas fa-hospital"></i> Add Clinic
            </button></li>
        <li><button class="button" onclick="showForm('doctorForm', 'Add Doctor')">
                <i class="fas fa-user-md"></i> Add Doctor
            </button></li>
        <li>
            <button class="button" onclick="showForm('nurseForm', 'Add Nurse')">
                <i class="fas fa-user-nurse"></i> Add Nurse
            </button>
        </li>
        <li>
            <button class="button" onclick="showForm('staffForm', 'Add General Staff')">
                <i class="fas fa-users-cog"></i> Add General Staff
            </button>
        </li>
        <li><button class="button" onclick="showForm('doctorBooking', 'Doctor Booking')">
                <i class="fas fa-user-doctor"></i> Doctor Booking
            </button></li>
        <li><button class="button" onclick="showForm('medicationBooking', 'Medication Booking')">
                <i class="fas fa-pills"></i> Medication Booking
            </button></li>
        <li><button class="button" onclick="showForm('vaccineBooking', 'Vaccine Booking')">
                <i class="fas fa-syringe"></i> Vaccine Booking
            </button></li>
        <li><button class="button" onclick="showForm('viewUsers', 'View Users')">
                <i class="fas fa-users"></i> View Users
            </button></li>
        <li><button id="addAdminButton" class="button" onclick="showForm('addAdminForm', 'Add Admin')">
                <i class="fas fa-user-shield"></i> Add Admin
            </button></li>
        <li><button class="button" onclick="Logout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button></li>
    </ul>
</div>

<!-- Main Content Wrapper -->
<div class="wrapper">
    <div class="main_box">
        <div class="main-header">
            <span id="dynamicTitle">Admin Dashboard</span>
        </div>

        <div class="welcome-text">
            <p>Select one of the options on the left to manage clinics, doctors, bookings, or view users.</p>
        </div>

        <div id="clinicForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('add-clinic') }}">
                @csrf <!-- Adding CSRF token for security -->

                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="clinicName" required>
                    <label class="label" for="clinicName">Clinic Name</label>
                    <i class="icon fas fa-hospital"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <select name="city_id" class="input-field" id="city" onchange="updateDistricts()" required>
                        <option value="" disabled selected>Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-city"></i>
                    @error('city_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <select name="district_id" class="input-field" id="district" required>
                        <option value="" disabled selected>Select District</option>
                    </select>
                    <i class="icon fas fa-map"></i>
                    @error('district_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="text" name="address" class="input-field" id="clinicAddress" required>
                    <label class="label" for="clinicAddress">Address</label>
                    <i class="icon fas fa-map-marker-alt"></i>
                    @error('address')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="text" name="phone" class="input-field" id="clinicPhone" required>
                    <label class="label" for="clinicPhone">Phone</label>
                    <i class="icon fas fa-phone"></i>
                    @error('phone')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add Clinic</button>
            </form>
        </div>


        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Display success message using Notiflix Report without background color
                    Notiflix.Report.success(
                        'Success!',
                        '{{ session('success') }}',
                        'OK',
                        {
                            messageMaxLength: 400,
                            plainText: true, // This option can help make it look simpler
                            cssAnimationStyle: 'zoom', // Optional, to enhance presentation without too much distraction
                            backOverlay: false, // This disables the background overlay
                            success: {
                                backgroundColor: 'transparent', // Ensuring the success message has no background
                                textColor: '#333', // Set the text color as desired (default: black)
                            }
                        }
                    );
                });
            </script>
        @endif



        <!-- Add Doctor Form -->
        <div id="doctorForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('add-doctor') }}">
                @csrf

                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="doctorName" required>
                    <label class="label" for="doctorName">Doctor Name</label>
                    <i class="icon fas fa-user-md"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="text" name="specialty" class="input-field" id="doctorSpecialty" required>
                    <label class="label" for="doctorSpecialty">Specialty</label>
                    <i class="icon fas fa-stethoscope"></i>
                    @error('specialty')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <select name="clinic_id" class="input-field" id="doctorClinic" required>
                        <option value="" disabled selected>Select Clinic</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-hospital"></i>
                    @error('clinic_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="number" name="experience" class="input-field" id="doctorExperience" required min="0">
                    <label class="label" for="doctorExperience">Years of Experience</label>
                    <i class="icon fas fa-briefcase"></i>
                    @error('experience')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add Doctor</button>
            </form>
        </div>

        <!-- Add Nurse Form -->
        <div id="nurseForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('add-nurse') }}">
                @csrf <!-- Adding CSRF token for security -->

                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="nurseName" required>
                    <label class="label" for="nurseName">Nurse Name</label>
                    <i class="icon fas fa-user-nurse"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="text" name="specialty" class="input-field" id="nurseSpecialty" required>
                    <label class="label" for="nurseSpecialty">Specialty</label>
                    <i class="icon fas fa-stethoscope"></i>
                    @error('specialty')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Clinic Dropdown -->
                <div class="input_box">
                    <select name="clinic_id" class="input-field" id="nurseClinic" required>
                        <option value="" disabled selected>Select Clinic</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-hospital"></i>
                    @error('clinic_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="number" name="experience" class="input-field" id="nurseExperience" required min="0">
                    <label class="label" for="nurseExperience">Years of Experience</label>
                    <i class="icon fas fa-briefcase"></i>
                    @error('experience')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add Nurse</button>
            </form>
        </div>

        <!-- Add General Staff Form -->
        <div id="staffForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('add-staff') }}">
                @csrf <!-- Adding CSRF token for security -->

                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="staffName" required>
                    <label class="label" for="staffName">Staff Name</label>
                    <i class="icon fas fa-user"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="text" name="role" class="input-field" id="staffRole" required>
                    <label class="label" for="staffRole">Role</label>
                    <i class="icon fas fa-user-tag"></i>
                    @error('role')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Clinic Dropdown -->
                <div class="input_box">
                    <select name="clinic_id" class="input-field" id="staffClinic" required>
                        <option value="" disabled selected>Select Clinic</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-hospital"></i>
                    @error('clinic_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="number" name="experience" class="input-field" id="staffExperience" required min="0">
                    <label class="label" for="staffExperience">Years of Experience</label>
                    <i class="icon fas fa-briefcase"></i>
                    @error('experience')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add General Staff</button>
            </form>
        </div>


        <!-- Add Admin Form -->
        <div id="addAdminForm" class="content-section" style="display: none;">
            <form class="form-container" method="POST" action="{{ route('add-admin') }}">
                @csrf
                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="adminName" required>
                    <label class="label" for="adminName">Name</label>
                    <i class="icon fas fa-user"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="text" name="username" class="input-field" id="adminUsername" required>
                    <label class="label" for="adminUsername">Username</label>
                    <i class="icon fas fa-user"></i>
                    @error('username')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="email" name="email" class="input-field" value="{{ old('email') }}" required>
                    <label class="label">Email</label>
                    <i class="icon fas fa-envelope"></i>
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input_box">
                    <input type="password" name="password" class="input-field" id="adminPassword" required>
                    <label class="label" for="adminPassword">Password</label>
                    <i class="icon fas fa-lock"></i>
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add Admin</button>
            </form>
        </div>


        <!-- Other sections like Doctor Booking, Medication Booking, Vaccine Booking, etc. -->
        <div id="doctorBooking" class="content-section" style="display: none;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>Dr. Smith</td>
                    <td>2024-10-10</td>
                    <td>10:00 AM</td>
                    <td>Confirmed</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Medication Booking Section -->
        <div id="medicationBooking" class="content-section" style="display: none;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Medication</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Jane Smith</td>
                    <td>Aspirin</td>
                    <td>2024-10-12</td>
                    <td>2:00 PM</td>
                    <td>Pending</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Vaccine Booking Section -->
        <div id="vaccineBooking" class="content-section" style="display: none;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Vaccine</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Emily Johnson</td>
                    <td>COVID-19</td>
                    <td>2024-10-15</td>
                    <td>9:00 AM</td>
                    <td>Completed</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- View Users Section -->
        <div id="viewUsers" class="content-section" style="display: none;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->deleted_at ? 'Inactive' : 'Active' }}</td> <!-- Adjust based on your actual status field -->
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('showForm') === 'addAdminForm')
        // Trigger the 'Add Admin' button automatically
        document.getElementById('addAdminButton').click();
        @endif
    });
    function Logout() {
        window.location.href = '/logout';
    }

    // JavaScript to update district list based on selected city
    const cities = @json($cities); // Assuming $cities holds the JSON data you provided

    function updateDistricts() {
        const citySelect = document.getElementById("city");
        const districtSelect = document.getElementById("district");
        const selectedCityId = citySelect.value; // استخدام قيمة id للمدينة

        // تنظيف قائمة الأحياء
        districtSelect.innerHTML = '<option value="" disabled selected>Select District</option>';

        // ابحث عن المدينة بواسطة id
        const city = cities.find(city => city.id == selectedCityId);
        if (city && city.districts) {
            city.districts.forEach(district => {
                const option = document.createElement("option");
                option.value = district.id; // استخدام id الحي كقيمة
                option.textContent = district.name;
                districtSelect.appendChild(option);
            });
        } else {
            console.error('City or districts not found for the selected city ID');
        }
    }

    function showForm(formId, title) {
        // Hide all sections
        const sections = document.querySelectorAll('.content-section');
        sections.forEach(section => section.style.display = 'none');

        // Show the requested section
        document.getElementById(formId).style.display = 'block';

        // Update the title based on the function
        document.getElementById('dynamicTitle').innerText = title;
    }
</script>
<style>

    body {
        background-image: url("{{ asset('images/backgrond.webp') }}"); /* Set background image */
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        display: flex;
        direction: ltr;
        /* Left-to-Right Direction */
    }
    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
        display: block;
    }

</style>
</body>

</html>
