<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorContoller;
use App\Http\Controllers\NurseController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AppointmentController;

// Route for the main page showing the login form
Route::get('/', function () {
    return redirect()->route('login');
//    return view('auth.login'); // Show the login page
});

// Routes for user authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); // Show login form
Route::post('/login', [LoginController::class, 'login'])->name('login.post'); // Process login
Route::get('/logout', [LoginController::class, 'logout'])->name('logout'); // Logout route

// Routes accessible without login
Route::get('/otp', function () {
    return view('auth.otp'); // Show OTP page
})->name('otp');

Route::get('/emergency', function () {
    return view('emergency'); // Show emergency page
})->name('emergency');

Route::get('/dispense_medications', function () {
    return view('dispense_medications'); // Show dispense medications page
})->name('dispense_medications');



// Group of routes that require authentication
Route::middleware('auth')->group(function () {

    Route::get('/home', fn () => view('home'))->name('home'); // Name the route for easier referencing
    Route::post("/bookslot",[AppointmentController::class,'bookSlot'])->name('book.slot');
    Route::post('/showTimes', [AppointmentController::class, 'showTimeChoose'])->name('showTimes');

    Route::get("/times",function(){return view("times");})->name('times');
    Route::get('/book-appointment', [AppointmentController::class, 'showBookingForm'])->name('appointment.form'); // Show booking form
    Route::post('/book-appointment', [AppointmentController::class, 'storePatientSymptoms'])->name('appointment.store');
    Route::get('/cancel-appointment', [AppointmentController::class, 'cancelAppointment'])->name('cancel.appointment');
    Route::get("/showClinic", [AppointmentController::class , 'showClinic'])->name('showClinic');
    Route::get("/showDoctor/{clinicId}", [AppointmentController::class , 'showDoctor'])->name('showDoctor');
    Route::get('/save-doctor', [AppointmentController::class, 'saveDoctor'])->name('saveDoctor');



});

//Route::get('/admin', [AdminController::class, 'showAdmin'])->name('admin')->middleware("auth:admin");
Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('/admin', [AdminController::class, 'showAdmin'])->name('admin');
    Route::post('/add-clinic', [AdminController::class, 'addClinic'])->name('add-clinic');
    Route::post('/add-doctor', [AdminController::class, 'addDoctor'])->name('add-doctor');
    Route::post('/add-admin', [AdminController::class, 'addAdmin'])->name('add-admin');
    Route::post("/add-nurse", [AdminController::class, 'addNurse'])->name('add-nurse');
    Route::post("/add-staff", [AdminController::class, 'addGeneralStaff'])->name('add-staff');
    Route::get("/showClinicDetails/{clinic_id}/{page}", [AdminController::class, 'ClinicDetails']);
    Route::get("/showDoctorDetails", [AdminController::class, 'showDoctor']);
    Route::get("/doctorBooking/{doctor_id}", [AdminController::class, 'DoctorBooking'])->name('doctorBooking');
    Route::post("/deleteSomething", [AdminController::class, 'deleteSome'])->name('deleteSomething');
    Route::get("/deleteClinic/{clinicID}", [AdminController::class, 'deleteClinic'])->name('deleteClinic');


});

Route::group(['middleware' => ['auth:doctor']], function () {
    Route::get("/doctor",[DoctorContoller::class,'showPatient'])->name("doctor");
});


Route::group(['middleware' => ['auth:nurse']], function () {
    Route::get("/nurse",[NurseController::class,'showPatient'])->name("nurse");
});

Route::group(['middleware' => ['auth:general_staff']], function () {
    Route::get("/nurse",[NurseController::class,'showPatient'])->name("nurse");
});

Route::get('/account_created',function(){return view("AccountCreated");})->name('account_created');




Route::get('register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegistrationController::class, 'register']);

Route::get('otp/verify', [RegistrationController::class, 'showOtpForm'])->name('otp.verify');
Route::post('otp/verify', [RegistrationController::class, 'verifyOtp']);

Route::post('otp/resend', [RegistrationController::class, 'resendOtp'])->name('otp.resend');



Route::get("/success",function(){return view("test");})->name('success');

