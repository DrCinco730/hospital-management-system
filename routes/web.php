<?php

use App\Http\Controllers\AdminController;
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
    Route::get('/showTimes', [AppointmentController::class, 'showTimeChoose'])->name('showTimes');

    Route::get("/times",function(){return view("times");})->name('times');
    Route::get('/book-appointment', [AppointmentController::class, 'showBookingForm'])->name('appointment.form'); // Show booking form
    Route::post('/book-appointment', [AppointmentController::class, 'storePatientSymptoms'])->name('appointment.store');
    Route::get('/cancel-appointment', [AppointmentController::class, 'cancelAppointment'])->name('cancel.appointment');

});

//Route::get('/admin', [AdminController::class, 'showAdmin'])->name('admin')->middleware("auth:admin");
Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('/admin', [AdminController::class, 'showAdmin'])->name('admin');
    Route::post('/add-clinic', [AdminController::class, 'addClinic'])->name('add-clinic');
    Route::post('/add-doctor', [AdminController::class, 'addDoctor'])->name('add-doctor');
    Route::post('/add-admin', [AdminController::class, 'addAdmin'])->name('add-admin');
});


Route::get('/account_created',function(){return view("AccountCreated");})->name('account_created');




Route::get('register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegistrationController::class, 'register']);

Route::get('otp/verify', [RegistrationController::class, 'showOtpForm'])->name('otp.verify');
Route::post('otp/verify', [RegistrationController::class, 'verifyOtp']);

Route::post('otp/resend', [RegistrationController::class, 'resendOtp'])->name('otp.resend');



Route::get("/success",function(){return view("test");})->name('success');
