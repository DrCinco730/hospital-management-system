<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\City;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\User;
use App\Models\UserAdmin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function showAdmin()
    {
        // Fetch cities with districts
        $cities = City::with('districts')->get();

        // Fetch all clinics
        $clinics = Clinic::all();

        // Fetch users along with related city and district
        $users = User::with('city', 'district')->get();

        // Return view with cities, clinics, and users data
        return view('admin', compact('cities', 'clinics', 'users'))
            ->with('success', 'You have successfully logged in.');
    }

    /**
     * Store a newly created clinic in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addClinic(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'city_id' => 'required|integer|exists:cities,id',
            'district_id' => 'required|integer|exists:districts,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:15',
        ]);

        // Handle validation failures
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create new clinic using validated data
        Clinic::create($request->only(['name', 'city_id', 'district_id', 'address', 'phone']));

        // Redirect back with success message
        return redirect()->back()->with('success', 'Clinic has been successfully added!');
    }

    /**
     * Store a newly created doctor in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addDoctor(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'clinic_id' => 'required|integer|exists:clinics,id',
            'experience' => 'required|integer|min:0',
        ]);

        // Handle validation failures
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create new doctor using validated data
        Doctor::create($request->only(['name', 'specialty', 'clinic_id']) + ['experience_years' => $request->experience]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Doctor has been successfully added!');
    }

    /**
     * Store a newly created admin in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addAdmin(Request $request)
    {
        try {
            // Custom validation rules and messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:admins,username',
                'email' => 'required|string|email|max:255|unique:admins,email',
                'password' => 'required|string|min:8',
            ], [
                'name.required' => 'Please enter a name.',
                'name.max' => 'The name must not exceed 255 characters.',
                'username.required' => 'Please enter a username.',
                'username.max' => 'The username must not exceed 255 characters.',
                'username.unique' => 'This username is already taken.',
                'email.required' => 'Please enter an email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'This email address is already taken.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'The password must be at least 8 characters long.',
            ]);

            // If validation fails, redirect back with errors
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('showForm', 'addAdminForm');
            }

            // Extra username and email checks
            if (UserAdmin::where('username', $request->username)->exists() || Admin::where('email', $request->email)->exists() || User::where('email', $request->email)->exists()) {
                return redirect()->back()->with('error', 'This username or email address is already in use.')->withInput()->with('showForm', 'addAdminForm');
            }

            // Create new admin entry in database
            Admin::create($request->only(['name', 'username', 'email', 'password']));

            // Redirect back with success message
            return redirect()->back()->with('success', 'Admin created successfully!');
        } catch (Exception $e) {
            // Catch unexpected errors and return generic error message
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }
}
