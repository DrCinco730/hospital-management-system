<?php
//
//namespace App\Http\Controllers;
//
//use Illuminate\Http\RedirectResponse;
//use Illuminate\Routing\Controller;
//use App\Models\Otp;
//use App\Models\User;
//use App\Models\City;
//use App\Mail\OtpMail;
//use Illuminate\Support\Facades\Mail;
//use Carbon\Carbon;
//use Illuminate\Http\Request;
//use Illuminate\View\View;
//use Random\RandomException;
//
//class RegistrationController extends Controller
//{
//    /**
//     * Show the user registration form.
//     *
//     * @return View
//     */
//    public function showRegistrationForm()
//    {
//        $cities = City::with('districts')->get();
//        return view('auth.register', compact('cities'));
//    }
//
//    /**
//     * Handle user registration and send OTP.
//     *
//     * @param Request $request
//     * @throws RandomException
//     * @return RedirectResponse
//     */
//    public function register(Request $request)
//    {
//        // Validate user input
//        $validatedData = $request->validate([
//            'first_name'    => 'required|string|max:255',
//            'last_name'     => 'required|string|max:255',
//            'username'      => 'required|string|max:255|unique:users',
//            'email'         => 'required|string|email|max:255|unique:users',
//            'password'      => 'required|string|min:8|confirmed',
//            'date_of_birth' => 'required|date|before:today',
//            'id_number'     => 'required|string|max:20|unique:users',
//            'city_id'       => 'required|exists:cities,id',
//            'district_id'   => 'required|exists:districts,id',
//            'region'        => 'required|in:north,south,west,east',
//            'gender'        => 'required|in:male,female,other',
//        ]);
//
//        session(['validatedData' => $validatedData]);
//
//        // Generate a random OTP (6 digits)
//        $otpCode = random_int(100000, 999999);
//
//        // Store OTP in the database
//        Otp::create([
//            'email'      => $validatedData['email'],
//            'otp_code'   => $otpCode,
//            'expires_at' => Carbon::now()->addMinutes(10),
//            'data'       => json_encode($validatedData),
//        ]);
//
//        // Determine mailer based on the email domain
//        $mailer = $this->determineMailer($validatedData['email']);
//
//        // Send OTP to user via email
//        Mail::mailer($mailer)->to($validatedData['email'])->send(new OtpMail($otpCode));
//
//        session(['email' => $validatedData['email']]);
//
//        return redirect()->route('otp.verify')->with('success', 'A verification code has been sent to your email.');
//    }
//
//    /**
//     * Show the OTP verification form.
//     *
//     * @return View
//     */
//    public function showOtpForm()
//    {
//        return view('auth.verify_otp');
//    }
//
//    /**
//     * Verify the OTP code.
//     *
//     * @param Request $request
//     * @return RedirectResponse
//     */
//    public function verifyOtp(Request $request)
//    {
//        $request->validate([
//            'otp' => 'required|digits:6',
//        ]);
//
//        $email = session('email');
//
//        if (!$email) {
//            return redirect()->route('register')->withErrors(['email' => 'Session expired. Please register again.']);
//        }
//
//        // Retrieve the latest OTP for the email
//        $otpRecord = Otp::where('email', $email)
//            ->where('expires_at', '>', Carbon::now())
//            ->latest()
//            ->first();
//
//        if (!$otpRecord) {
//            return redirect()->back()->withErrors(['otp' => 'Invalid or expired OTP.']);
//        }
//
//        if ($request->otp != $otpRecord->otp_code) {
//            return redirect()->back()->withErrors(['otp' => 'Incorrect OTP.']);
//        }
//
//        // Create the user account
//        $userData = json_decode($otpRecord->data, true);
//        User::create($userData);
//
//        // Redirect to account created page
//        return redirect()->intended('account_created')->with('success', 'Your account has been created successfully.');
//    }
//
//    /**
//     * Resend the OTP code.
//     *
//     * @param Request $request
//     * @throws RandomException
//     * @return RedirectResponse
//     */
//    public function resendOtp()
//    {
//        $email = session('email');
//
//        if (!$email) {
//            return redirect()->route('register')->withErrors(['email' => 'Session expired. Please register again.']);
//        }
//
//        $validatedData = session('validatedData');
//
//        // Generate a new OTP code
//        $otpCode = random_int(100000, 999999);
//        Otp::create([
//            'email'      => $email,
//            'otp_code'   => $otpCode,
//            'expires_at' => Carbon::now()->addMinutes(10),
//            'data'       => json_encode($validatedData),
//        ]);
//
//        // Send the OTP via the appropriate mailer
//        $mailer = $this->determineMailer($email);
//        Mail::mailer($mailer)->to($email)->send(new OtpMail($otpCode));
//
//        return redirect()->back()->with('success', 'A new verification code has been sent to your email.');
//    }
//
//    /**
//     * Determine the appropriate mailer based on the email domain.
//     *
//     * @param string $email
//     * @return string|null
//     */
//    private function determineMailer(string $email)
//    {
//        $localDomains = '@localhost';
//
//        if (str_contains($email, $localDomains)) {
//            return 'mercury';  // Use Mercury mailer for local domains
//        } else {
//            // Default to null (uses the default mailer)
//            return null;
//        }
//    }
//}


namespace App\Http\Controllers;

use App\Models\Otp;
use App\Services\OtpService;
use App\Services\UserService;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Random\RandomException;

class RegistrationController extends Controller
{
    protected OtpService $otpService;
    protected UserService $userService;

    public function __construct(OtpService $otpService, UserService $userService)
    {
        $this->otpService = $otpService;
        $this->userService = $userService;
    }

    public function showRegistrationForm(): View
    {
        $cities = City::with('districts')->get();
        return view('auth.register', compact('cities'));
    }

    /**
     * @throws RandomException
     */
    public function register(Request $request): RedirectResponse
    {
        $validatedData = $this->userService->validateUserData($request->all());
        session(['validatedData' => $validatedData]);

        $this->otpService->generateAndSendOtp($validatedData);

        session(['email' => $validatedData['email']]);
        return redirect()->route('otp.verify')->with('success', 'A verification code has been sent to your email.');
    }

    public function showOtpForm(): View
    {
        return view('auth.verify_otp');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['otp' => 'required|digits:6']);
        $email = session('email');

        if (!$email || !$this->otpService->verifyOtp($email, $request->otp)) {
            return redirect()->back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $userData = json_decode(Otp::where('email', $email)->latest()->first()->data, true);
        $this->userService->registerUser($userData);

        return redirect()->intended('account_created')->with('success', 'Your account has been created successfully.');
    }

    /**
     * @throws RandomException
     */
    public function resendOtp(): RedirectResponse
    {
        $email = session('email');
        $validatedData = session('validatedData');

        $this->otpService->generateAndSendOtp($validatedData);

        return redirect()->back()->with('success', 'A new verification code has been sent to your email.');
    }
}
