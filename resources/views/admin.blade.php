<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins&display=swap'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3/dist/notiflix-aio-3.2.5.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="{{ asset('css/style_admin.css') }}">

    <style>
        /* General Popup Styling */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .popup-content {
            width: 300px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .popup-content h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .popup-content input[type="text"] {
            width: 200px;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .popup-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 10px;
        }

        .popup-button {
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            min-width: 100px;
        }

        .download-btn {
            background-color: #4da6ff;
        }

        .close-btn {
            background-color: #ff6b6b;
        }
    </style>
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
        <li>
            <button class="button" id="notifications" >
                <i class="fas fa-bell"></i> Notifications
                <span id="notificationCount" class="notification-count">3</span>
            </button>
        </li>
        <li><button class="button" onclick="showForm('clinicForm', 'Add Clinic')">
                <i class="fas fa-clinic-medical"></i> Add Clinic
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
                <i class="fas fa-user-cog"></i> Add General Staff
            </button>
        </li>
        <!-- Clinic Management Section -->
        <li>
            <button class="button" onclick="showForm('clinicManagement', 'Clinic Management')">
                <i class="fas fa-clinic-medical"></i> <i class="fas fa-cogs"></i> Clinic Management
            </button>
        </li>
        <li><button class="button" id="doctorBook">
                <i class="fas fa-calendar-check"></i> Doctor Booking
            </button></li>
        <li><button class="button" id="medicationBookingButton" >
                <i class="fas fa-pills"></i> Medication Booking
            </button></li>
        <li><button class="button" id="VaccineBookingButton">
                <i class="fas fa-syringe"></i> Vaccine Booking
            </button></li>
        <li><button class="button" onclick="showForm('viewUsers', 'View Users')">
                <i class="fas fa-users"></i> View Patients
            </button></li>
        <li>
            <button class="button" onclick="showForm('viewDoctors', 'View Doctors')">
                <i class="fas fa-user-md"></i> View Doctors
            </button>
        </li>
        <li><button id="addAdminButton" class="button" onclick="showForm('addAdminForm', 'Add Admin')">
                <i class="fas fa-user-tie"></i> Add Admin
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

        {{--        <div class="welcome-text">--}}
        {{--            <p>Select one of the options on the left to manage clinics, doctors, bookings, or view users.</p>--}}
        {{--        </div>--}}

        <div id="clinicForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('clinic/add') }}">
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
        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Display error message using Notiflix Report without background color
                    Notiflix.Report.failure(
                        'Error!',
                        '{{ session('error') }}',
                        'OK',
                        {
                            messageMaxLength: 400,
                            plainText: true,
                            cssAnimationStyle: 'zoom',
                            backOverlay: false,
                            failure: {
                                backgroundColor: 'transparent',
                                textColor: '#333',
                            }
                        }
                    );
                });
            </script>
        @endif


        <!-- Clinic Management Section -->
        <div id="clinicManagement" class="content-section">
            {{--            <h2>Clinic Management</h2>--}}
            <div class="clinic-cards-container">
                @foreach($clinics as $clinic)
                    <div class="clinic-card" id="clinic-{{ $clinic['id'] }}">
                        <img src="{{ asset('images/image.webp') }}" alt="Clinic Image" class="clinic-card-img">
                        <div class="card-content">
                            <h3>{{ $clinic['name'] }}</h3>

                            <!-- زر عرض المعلومات -->
                            <!-- Button to show clinic details -->
                            <button class="button show-info-button" data-clinic-id="{{ $clinic['id'] }}">Show Details</button>
                            <input type="hidden" id="reportUrl-{{ $clinic['id'] }}" value="{{ url('/report/' . $clinic['id']) }}">
                            <button class="button download-button" onclick="openPopup('{{ $clinic['id'] }}')">
                                <i class="fa fa-download"></i> Download Report
                            </button>





                            <div class="clinic-card-actions">
                                <!-- زر التعديل -->
                                <button class="button edit-button" data-clinic-id="{{ $clinic['id'] }}">Edit</button>

                                <!-- زر الحذف -->
                                <button class="button delete-button" onclick="deleteClinic('{{ $clinic['name'] }}','{{ $clinic['id'] }}')">Delete</button>

                            </div>
                            <div class="download-report-container">
                                <!-- زر تنزيل التقرير -->

                            </div>

                            <div class="details-section" id="details-{{ $clinic['id'] }}" style="display: none;">
                                <p>Additional information is not available.</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Add Doctor Form -->
        <div id="doctorForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('doctor/add') }}">
                @csrf

                <!-- Doctor Name -->
                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="doctorName" value="{{ old('name') }}" required>
                    <label class="label" for="doctorName">Doctor Name</label>
                    <i class="icon fas fa-user-md"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="input_box">
                    <input type="email" name="email" class="input-field" id="email" value="{{ old('email') }}" required>
                    <label for="email" class="label">Email</label>
                    <i class="bx bx-envelope icon"></i>
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Username -->
                <div class="input_box">
                    <input type="text" name="username" class="input-field" id="Username" value="{{ old('username') }}" required>
                    <label class="label" for="Username">Username</label>
                    <i class="icon fas fa-user"></i>
                    @error('username')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input_box">
                    <input type="password" name="password" class="input-field" id="Password" required>
                    <label class="label" for="Password">Password</label>
                    <i class="icon fas fa-lock"></i>
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="input_box">
                    <input type="password" name="password_confirmation" class="input-field" id="PasswordConfirmation" required>
                    <label class="label" for="PasswordConfirmation">Confirm Password</label>
                    <i class="icon fas fa-lock"></i>
                    @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Specialty -->
                <div class="input_box">
                    <select name="specialty" class="input-field" id="doctorSpecialty" required>
                        <option value="" disabled selected>Select Specialty</option>
{{--                    <input type="text" name="specialty" class="input-field" id="doctorSpecialty" value="{{ old('specialty') }}" required>--}}
{{--                    <label class="label" for="doctorSpecialty">Specialty</label>--}}
                        @foreach($doctors_specialties as $specialty)
                            <option value="{{ $specialty->name }}" {{ old('specialty')  == $specialty->name ? 'selected' : '' }}>{{ $specialty->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-stethoscope"></i>
                    @error('specialty')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Clinic Selection -->
                <div class="input_box">
                    <select name="clinic_id" class="input-field" id="doctorClinic" required>
                        <option value="" disabled selected>Select Clinic</option>
                        @foreach($clinics as $clinic)
                            <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-hospital"></i>
                    @error('clinic_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Experience -->
                <div class="input_box">
                    <input type="number" name="experience" class="input-field" id="doctorExperience" value="{{ old('experience') }}" required min="0">
                    <label class="label" for="doctorExperience">Years of Experience</label>
                    <i class="icon fas fa-briefcase"></i>
                    @error('experience')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add Doctor</button>
            </form>
        </div>

    @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Notiflix.Report.success('Success!', '{{ session('success') }}', 'OK');
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Notiflix.Report.failure('Error!', '{{ session('error') }}', 'OK');
                });
            </script>
        @endif

        <!-- Add Nurse Form -->
        <div id="nurseForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('add-nurse') }}">
                @csrf <!-- Adding CSRF token for security -->

                <!-- Nurse Name -->
                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="nurseName" value="{{ old('name') }}" required>
                    <label class="label" for="nurseName">Nurse Name</label>
                    <i class="icon fas fa-user-nurse"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Specialty -->
                <div class="input_box">
                    <input type="text" name="specialty" class="input-field" id="nurseSpecialty" value="{{ old('specialty') }}" required>
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
                            <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-hospital"></i>
                    @error('clinic_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Experience -->
                <div class="input_box">
                    <input type="number" name="experience" class="input-field" id="nurseExperience" value="{{ old('experience') }}" required min="0">
                    <label class="label" for="nurseExperience">Years of Experience</label>
                    <i class="icon fas fa-briefcase"></i>
                    @error('experience')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="input_box">
                    <input type="email" name="email" class="input-field" id="nurseEmail" value="{{ old('email') }}" required>
                    <label class="label" for="nurseEmail">Email</label>
                    <i class="icon fas fa-envelope"></i>
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Username -->
                <div class="input_box">
                    <input type="text" name="username" class="input-field" id="nurseUsername" value="{{ old('username') }}" required>
                    <label class="label" for="nurseUsername">Username</label>
                    <i class="icon fas fa-user"></i>
                    @error('username')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input_box">
                    <input type="password" name="password" class="input-field" id="nursePassword" required>
                    <label class="label" for="nursePassword">Password</label>
                    <i class="icon fas fa-lock"></i>
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="input_box">
                    <input type="password" name="password_confirmation" class="input-field" id="nursePasswordConfirmation" required>
                    <label class="label" for="nursePasswordConfirmation">Confirm Password</label>
                    <i class="icon fas fa-lock"></i>
                    @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add Nurse</button>
            </form>
        </div>


        <!-- Add General Staff Form -->
        <!-- Add General Staff Form -->
        <div id="staffForm" class="content-section" style="display: none;">
            <form class="form-container" method="post" action="{{ route('add-staff') }}">
                @csrf <!-- Adding CSRF token for security -->

                <!-- Staff Name -->
                <div class="input_box">
                    <input type="text" name="name" class="input-field" id="staffName" value="{{ old('name') }}" required>
                    <label class="label" for="staffName">Staff Name</label>
                    <i class="icon fas fa-user"></i>
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Role -->
                <div class="input_box">
                    <input type="text" name="role" class="input-field" id="staffRole" value="{{ old('role') }}" required>
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
                            <option value="{{ $clinic->id }}" {{ old('clinic_id') == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                    <i class="icon fas fa-hospital"></i>
                    @error('clinic_id')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Experience -->
                <div class="input_box">
                    <input type="number" name="experience" class="input-field" id="staffExperience" value="{{ old('experience') }}" required min="0">
                    <label class="label" for="staffExperience">Years of Experience</label>
                    <i class="icon fas fa-briefcase"></i>
                    @error('experience')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="input_box">
                    <input type="email" name="email" class="input-field" id="staffEmail" value="{{ old('email') }}" required>
                    <label class="label" for="staffEmail">Email</label>
                    <i class="icon fas fa-envelope"></i>
                    @error('email')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Username -->
                <div class="input_box">
                    <input type="text" name="username" class="input-field" id="staffUsername" value="{{ old('username') }}" required>
                    <label class="label" for="staffUsername">Username</label>
                    <i class="icon fas fa-user"></i>
                    @error('username')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="input_box">
                    <input type="password" name="password" class="input-field" id="staffPassword" required>
                    <label class="label" for="staffPassword">Password</label>
                    <i class="icon fas fa-lock"></i>
                    @error('password')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="input_box">
                    <input type="password" name="password_confirmation" class="input-field" id="staffPasswordConfirmation" required>
                    <label class="label" for="staffPasswordConfirmation">Confirm Password</label>
                    <i class="icon fas fa-lock"></i>
                    @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="input-submit">Add General Staff</button>
            </form>
        </div>

        <!-- View Doctors Section -->
        <div id="viewDoctors" class="content-section" style="display: none;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Doctor Name</th>
                    <th>Specialty</th>
                    <th>Clinic</th>
                    <th>Experience</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($doctors as $index => $doctor)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $doctor->name }}</td>
                        <td>{{ $doctor->specialty }}</td>
                        <td>{{ $doctor->clinic->name }}</td> <!-- Assuming doctor is related to a clinic -->
                        <td>{{ $doctor->experience }} years</td>
                        <td>{{ $doctor->status ? 'Inactive' : 'Active' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
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


        <!-- Medication Booking Section -->
        <div id="medicationBooking" class="content-section" style="display: none;">

        </div>

        <!-- Vaccine Booking Section -->
        <div id="vaccineBooking" class="content-section" style="display: none;">

        </div>

        <!-- View Users Section -->
        <div id="viewUsers" class="content-section" style="display: none;">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>District</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->city->name }}</td>
                        <td>{{ $user->district->name }}</td>
                        <td>{{ $user->deleted_at ? 'Inactive' : 'Active' }}</td> <!-- Adjust based on your actual status field -->
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div id='editClinicForm' class="content-section" style="display: none;">
        </div>

        <div id='showClinicDetails' class="content-section" style="display: none;">
        </div>

        <div id='showDoctors' class="content-section" style="display: none;">
        </div>

        <div id="doctorBooking" class="content-section" style="display: none;">
        </div>

        <div id="notificationsSection" class="content-section" style="display: none;">

        </div>





    </div>
    <div class="popup-overlay" id="popupOverlay" style="display: none;">
        <div class="popup-content">
            <h3>Select Report Date Range</h3>
            <input type="text" id="startDate" placeholder="Start Date" readonly>
            <input type="text" id="endDate" placeholder="End Date" readonly>
            <div class="popup-buttons" >
                <button class="popup-button download-btn" onclick="downloadReport()">Download</button>
                <button class="popup-button close-btn" onclick="closePopup()">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
    let clinicID ;



    async function fetchDateFromAPI(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error fetching data from API');
            const data = await response.json();  // تحليل JSON
            return data.appointment_date;        // استرجاع تاريخ الموعد
        } catch (error) {
            console.error("An error occurred:", error);
            return "today";  // عودة إلى اليوم في حال حدوث خطأ
        }
    }

    // جلب التاريخ من API ثم تهيئة حقل نهاية التقرير
   function dateFormat(idClinicReport) {
       fetchDateFromAPI(`/lastDate/${idClinicReport}`).then(dateFromAPI => {
           flatpickr("#startDate", {
               altInput: true,
               altFormat: "F j, Y",
               dateFormat: "Y-m-d",
               minDate: dateFromAPI,  // الحد الأدنى هو التاريخ المسترجع من API
               maxDate: "today"
           });
           flatpickr("#endDate", {
               altInput: true,
               altFormat: "F j, Y",
               dateFormat: "Y-m-d",
               minDate: dateFromAPI,  // الحد الأدنى هو التاريخ المسترجع من API
               maxDate: "today"   // الحد الأقصى هو نفس التاريخ أيضاً
           });
       });
   }
    // دالة لفتح النافذة المنبثقة
    function openPopup(ID) {
        document.getElementById("popupOverlay").style.display = "flex";
        document.getElementsByClassName("popup-buttons").value = ID;
        dateFormat(ID);
    }

    // دالة لإغلاق النافذة المنبثقة
    function closePopup() {
        document.getElementById("popupOverlay").style.display = "none";
    }


    // function downloadReport() {
    //     const startDate = document.getElementById("startDate").value;
    //     const endDate = document.getElementById("endDate").value;
    //
    //     if (!startDate || !endDate) {
    //         Notiflix.Notify.failure("Please select both start and end dates.");
    //         return;
    //     }
    //
    //     const url = document.getElementById(`reportUrl-${tempID}`).value;
    //
    //     // Make an AJAX request to fetch the PDF with POST request
    //     fetch(url, {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //         },
    //         body: JSON.stringify({ startDate, endDate })  // Add the dates to the request body
    //     })
    //         .then(response => response.blob())
    //         .then(blob => {
    //             const downloadUrl = URL.createObjectURL(blob);
    //             const a = document.createElement('a');
    //             a.href = downloadUrl;
    //             a.download = `report_${tempID}.pdf`;
    //             document.body.appendChild(a);
    //             a.click();
    //             document.body.removeChild(a);
    //             URL.revokeObjectURL(downloadUrl);
    //         })
    //         .catch(error => console.error('Error downloading report:', error));
    //
    //     closePopup();
    // }


    function downloadReport() {
        const tempID = document.getElementsByClassName("popup-buttons").value;

        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("endDate").value;

        if (!startDate || !endDate) {
            Notiflix.Notify.failure("Please select both start and end dates.");
            return;
        }

        const url = `${document.getElementById(`reportUrl-${tempID}`).value}?startDate=${startDate}&endDate=${endDate}`;

        // Make an AJAX request to fetch the PDF
        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/pdf',
            },
        })
            .then(response => response.blob())
            .then(blob => {
                const downloadUrl = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = `report_${startDate}_${endDate}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(downloadUrl);
            })
            .catch(error => console.error('Error downloading report:', error));

        closePopup();
    }


</script>
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
    function clearNotificationCount() {
        document.getElementById('notificationCount').style.display = 'none';
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
        sections.forEach(section => {
            section.classList.remove('animate__animated', 'animate__fadeIn'); // Remove previous animation classes
            section.style.display = 'none';
        });

        // Show the requested section with animation
        const selectedSection = document.getElementById(formId);
        selectedSection.style.display = 'block';
        selectedSection.classList.add('animate__animated', 'animate__fadeIn'); // Add animation classes

        // Update the title based on the function
        document.getElementById('dynamicTitle').innerText = title;
    }


    function deleteClinic(clinicName, clinicId) {
        if (confirm(`Are you sure you want to delete ${clinicName}?`)) {
            $.ajax({
                url: `/clinic/${clinicId}/delete`, // Make sure this route exists in your routes file
                type: 'GET', // Use the DELETE HTTP method
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
                },
                success: function(response) {
                    if (response) {
                        // Successfully deleted, remove clinic from the UI
                        $(`#clinic-${clinicId}`).remove();
                        Notiflix.Notify.success('Clinic deleted successfully.');
                    } else {
                        // Handle server response error
                        Notiflix.Notify.failure(response.message || 'Failed to delete the clinic.');
                    }
                },
                error: function(xhr) {
                    Notiflix.Notify.failure('An error occurred while trying to delete the clinic.');
                }
            });
        }
    }

</script>


<style>
    .input-error {
        border-color: red;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
        display: block;
    }


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
    /* resources/css/app.css */

    /* Clinic Card Styling */
    .clinic-card {
        flex: 1 1 180px;
        max-width: 220px;
        min-height: 358px;
        border-radius: 15px;
        background: linear-gradient(145deg, #ffffff, #f9f9f9);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
    }

    .clinic-card.expanded {
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease-in-out;
    }

    .clinic-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
    }

    .clinic-card-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        transition: filter 0.3s ease;
    }

    .clinic-card:hover .clinic-card-img {
        filter: brightness(1);
    }

    .card-content {
        padding: 15px;
        color: #333;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
    }

    .card-content h3 {
        font-size: 18px;
        color: #1e1e1e;
        margin-bottom: 8px;
        font-weight: bold;
    }

    .edit-button,
    .delete-button {
        flex-grow: 1;
        padding: 8px 10px;
        font-size: 10px;
        border-radius: 25px;
        color: #fff;
        border: none;
        transition: background 0.3s ease, transform 0.3s ease;
        margin: 2px;
    }

    .edit-button {
        background-color: #e08e0b;
    }

    .edit-button:hover {
        background-color: #e08e0b;
        transform: scale(1.05);
    }

    .delete-button {
        background-color: #d62c1a;
    }

    .delete-button:hover {
        background-color: #d62c1a;
        transform: scale(1.05);
    }

    .show-info-button {
        margin-top: 10px;
        background-color: #15926c;
        color: #fff;
        border: none;
        padding: 8px 16px;
        font-size: 12px;
        border-radius: 25px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .show-info-button:hover {
        background-color: #15926c;
        transform: scale(1.05);
    }

    .details-section {
        display: none;
    }

    .clinic-card-actions {
        display: flex;
        justify-content: space-around;
        margin-top: 10px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination .page-item {
        margin: 0 5px;
    }

    .pagination .page-link {
        padding: 8px 12px;
        color: #007bff;
        text-decoration: none;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .pagination .page-link:hover {
        background-color: #f1f1f1;
    }



</style>

<script>

    function fetchRecords(page) {
        $.ajax({
            url: page,
            success: function (response) {
                $('#notificationsSection').html(response).show();
                showForm('notificationsSection', 'Notifications');
            }
        });
    }

</script>

<script>
    $(document).ready(function() {
        $('.button').on('click', function(event) {
            const targetId = event.target.id;

            if(targetId === "page"){
                let page = $(this).attr('name')

                fetchRecords(page);
            }

            else if (targetId === 'doctorBook') {
                loadDoctorBooking();
            } else if ($(this).hasClass('edit-button')) {
                const clinicId = $(this).data('clinic-id');
                loadEditClinicForm(clinicId);
            } else if ($(this).hasClass('show-info-button')) {
                const clinicId = $(this).data('clinic-id');
                loadClinicInfo(clinicId);
            } else if (targetId === 'notifications'){
                loadNotifictions();
            } else if(targetId === 'medicationBookingButton'){
                loadPharmDoctorBooking();
            } else if (targetId === 'VaccineBookingButton'){
                loadVaccineDoctorBooking();
            }
        });


        function loadNotifictions(){
            $.get('/records', function(response) {
                $('#notificationsSection').html(response).show();
                showForm('notificationsSection', 'Notifications');
                clearNotificationCount();
            }).fail(function() {
                alert('Error loading doctor details.');
            });
        }

        function loadDoctorBooking() {
            $.get('/doctor/details/all', function(response) {
                $('#showClinicDetails').html(response).show();
                showForm('showClinicDetails', 'Doctor Booking');
            }).fail(function() {
                alert('Error loading doctor details.');
            });
        }

        function loadVaccineDoctorBooking() {
            $.get('/doctor/details/Vaccination', function(response) {
                $('#medicationBooking').html(response).show();
                showForm('medicationBooking', 'Medication Booking');
            }).fail(function() {
                alert('Error loading doctor details.');
            });
        }

        function loadPharmDoctorBooking() {
            $.get('/doctor/details/pharmacist', function(response) {
                $('#vaccineBooking').html(response).show();
                showForm('vaccineBooking', 'Vaccine Booking');
            }).fail(function() {
                alert('Error loading doctor details.');
            });
        }

        function loadEditClinicForm(clinicId) {
            $.get(`/clinic/${clinicId}/details/edit`, function(response) {
                $('#editClinicForm').html(response).show();
                showForm('editClinicForm', 'Edit Clinic Staff');
            }).fail(function() {
                alert('Error loading clinic details.');
            });
        }

        function loadClinicInfo(clinicId) {
            $.get(`/clinic/${clinicId}/details/clinic`, function(response) {
                $('#showClinicDetails').html(response).show();
                showForm('showClinicDetails', 'Clinic Staff Overview');
            }).fail(function() {
                alert('Error loading clinic details.');
            });
        }
    });

</script>

<script>
    $(document).ready(function() {
        // Use event delegation to handle dynamically loaded buttons
        $(document).on('click', '.select-doctor-button', function() {
            // Get the clinic_id from the button's data attribute
            const clinicId = $(this).data('clinic-id');

            // Make the AJAX GET request, including the clinic_id in the URL
            $.ajax({
                url: `/doctor/${clinicId}/booking`, // dynamically add clinic_id here
                type: 'GET',
                success: function(response) {
                    // Load the returned HTML into the editClinicForm section
                    $('#doctorBooking').html(response).show();
                    showForm('doctorBooking', 'Doctor Booking ');
                },
                error: function(xhr) {
                    alert('Error loading clinic details. Please try again.');
                }
            });
        });
    });
    function updateNotificationCount(count) {
        const notificationCount = document.getElementById('notificationCount');
        notificationCount.innerText = count;
        notificationCount.style.display = count > 0 ? 'inline-block' : 'none';
    }
</script>
<script>

    document.addEventListener('DOMContentLoaded', function() {
        const Count = @json($count_notification);
        updateNotificationCount(Count);
    });
</script>
{{--<script>--}}
{{--    $(document).ready(function() {--}}
{{--        $('.a').on('click', function(event) {--}}
{{--        // event.preventDefault();--}}
{{--        let page = $(this).attr('href')--}}
{{--        fetchRecords(page);--}}
{{--    });--}}

{{--   --}}

{{--</script>--}}


</body>

</html>
