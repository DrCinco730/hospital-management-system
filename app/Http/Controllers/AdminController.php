<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\City;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\District;
use App\Models\GeneralStaff;
use App\Models\Nurse;
use App\Models\Record;
use App\Models\Specialty;
use App\Models\User;
use App\Models\Login;
use Exception;
use Illuminate\Http\RedirectResponse;
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
        $clinics = Clinic::withoutTrashed()->get();;

        // Fetch users along with related city and district
        $users = User::with('city', 'district')->withoutTrashed()->get();

        $doctors = Doctor::with('clinic')->withoutTrashed()->get();

        $doctors_specialties = Specialty::all()->makeHidden(['created_at', 'updated_at']);

        $count_notification = Record::where("status","unread")->count();

//        return response()->json($users);

//         Return view with cities, clinics, and users data
        return view('admin', compact('cities', 'clinics', 'users','doctors','doctors_specialties','count_notification'))
            ->with('success', 'You have successfully logged in.');
    }

    public function ClinicDetails(int $clinic_id,string $page=null)
    {
        $clinics_details = Clinic::with(['doctors','nurses','general_staff','city','district'])->where('id',$clinic_id)->get();
        $clinics_details->makeHidden(['created_at', 'updated_at', 'deleted_at','address','city_id','district_id','phone']);

        $clinics_details->each(function ($clinic) {
            $clinic->doctors->makeHidden(['created_at', 'updated_at', 'deleted_at','clinic_id','email','experience','username']);
            $clinic->nurses->makeHidden(['created_at', 'updated_at', 'deleted_at','clinic_id','email','experience','username']);
            $clinic->general_staff->makeHidden(['created_at', 'updated_at', 'deleted_at','clinic_id','email','experience','username']);
            $clinic->city->makeHidden(['created_at', 'updated_at', 'deleted_at','clinic_id','id']);
            $clinic->district->makeHidden(['created_at', 'updated_at', 'deleted_at','city_id','clinic_id','population','id']);
        });

        $appointments = Appointment::with('doctor.clinic')
            ->whereHas('doctor', function ($query) use ($clinic_id) {
                $query->where('clinic_id', $clinic_id);
            })
            ->get();
        $counts = count($appointments);
//        return response()->json($clinics_details);
        if($page === 'edit'){
            return view('edit', ['clinics'  => $clinics_details,'counts' => $counts]);
        } else {
            return view('clinic', ['clinics'  => $clinics_details,'counts' => $counts]);
        }
    }

    public function showDoctor()
    {
        $doctors = Doctor::withoutTrashed()->with('clinic')
            ->get()
            ->makeHidden(['created_at', 'updated_at', 'deleted_at','clinic_id','email','experience','username']);

        $doctors->each(function ($doctor) {
            $doctor->clinic->makeHidden(['created_at', 'updated_at', 'deleted_at','address','city_id','district_id','phone','id']);
        });

//        return response()->json($doctors);
//
        return view("doctor", ['doctors' => $doctors]);
    }

    public function DoctorBooking(int $doctor_id){
        $appointments = Appointment::with(['timeSlot', 'patient', 'doctor' => function($query) {
            $query->withTrashed(); // Include soft-deleted doctors
        }])
            ->where('doctor_id', $doctor_id)
            ->get()
            ->makeHidden(['created_at', 'updated_at', 'deleted_at', 'doctor_id', 'patient_id' ,'time_id']);

        $appointments->each(function ($i) {
            $i->timeSlot->makeHidden(['end_time','duration','id']);
            $i->patient->makeHidden(['created_at', 'updated_at', 'deleted_at','clinic_id','email','username','city_id','district_id','id_number','date_of_birth']);
            $i->doctor->makeHidden(['created_at', 'updated_at', 'deleted_at','clinic_id','email','experience','username','id','specialty']);

        });
//        return response()->json($appointments);
        return view('doctorBooking', ['appointments' => $appointments]);
    }

    /**
     * Store a newly created clinic in storage.
     *
     * @param Request $request
     * @return RedirectResponse
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function addDoctor(Request $request)
    {
        try {
            // Custom validation rules and messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'specialty' => 'required|string|exists:specialties,name',
                'clinic_id' => 'required|integer|exists:clinics,id',
                'experience' => 'required|integer|min:0',
                'email' => 'required|string|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'Please enter the doctor\'s name.',
                'name.max' => 'The doctor\'s name must not exceed 255 characters.',
                'specialty.required' => 'Please enter a specialty for the doctor.',
                'specialty.max' => 'The specialty must not exceed 255 characters.',
                'clinic_id.required' => 'Please select a valid clinic.',
                'experience.required' => 'Please enter years of experience.',
                'experience.integer' => 'The experience must be a valid number.',
                'email.required' => 'Please enter an email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'This email address is already taken.',
                'username.required' => 'Please enter a username.',
                'username.max' => 'The username must not exceed 255 characters.',
                'username.unique' => 'This username is already taken.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'The password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Handle validation failures
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Check for username/email conflicts
            if (Login::where('username', $request->username)->exists() ||
                User::where('email', $request->email)->exists()) {
                return redirect()->back()->with('error', 'This username or email address is already in use.')->withInput();
            }

            // Retrieve district population to set doctor limit
            $districtId = Clinic::where('id', $request->clinic_id)->first()->district_id;
            $population = District::where('id', $districtId)->first()->population;

            // Define doctor limit based on population
            $doctorLimit = 3;
            if ($population > 10000 && $population <= 20000) {
                $doctorLimit = 6;
            } elseif ($population > 20000 && $population <= 30000) {
                $doctorLimit = 10;
            }

            // Check if clinic doctor count exceeds the limit
            $clinicDoctorCount = Doctor::where('clinic_id', $request->clinic_id)->count();
            if ($clinicDoctorCount >= $doctorLimit) {
                return redirect()->back()->with('error', 'You have exceeded the maximum number of doctors allowed.');
            }

            // Create new doctor entry with hashed password
            $doctor = Doctor::create([
                'name' => $request->name,
                'specialty' => $request->specialty,
                'clinic_id' => $request->clinic_id,
                'experience' => $request->experience,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password, // Hash the password
            ]);



            // Redirect with success message
            return redirect()->back()->with('success', 'Doctor has been successfully added!');

        } catch (Exception $e) {
            // Catch unexpected errors
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }



    /**
     * Store a newly created admin in storage.
     *
     * @param Request $request
     * @return RedirectResponse
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
            if (Login::where('username', $request->username)->exists() || Admin::where('email', $request->email)->exists() || User::where('email', $request->email)->exists()) {
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

    public function addNurse(Request $request)
    {
        try {
            // Custom validation rules and messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'specialty' => 'required|string|max:255',
                'clinic_id' => 'required|integer|exists:clinics,id',
                'experience' => 'required|integer|min:0',
                'email' => 'required|string|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'Please enter the nurse\'s name.',
                'name.max' => 'The nurse\'s name must not exceed 255 characters.',
                'specialty.required' => 'Please enter a specialty for the nurse.',
                'specialty.max' => 'The specialty must not exceed 255 characters.',
                'clinic_id.required' => 'Please select a valid clinic.',
                'experience.required' => 'Please enter years of experience.',
                'experience.integer' => 'The experience must be a valid number.',
                'email.required' => 'Please enter an email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'This email address is already taken.',
                'username.required' => 'Please enter a username.',
                'username.max' => 'The username must not exceed 255 characters.',
                'username.unique' => 'This username is already taken.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'The password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Handle validation failures
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('showForm', 'nurseForm');
            }

            // Retrieve district ID and population
            $districtId = Clinic::find($request->clinic_id)->district_id;
            $population = District::find($districtId)->population;

            // Set nurse limit based on population range
            $nurseLimit = 3;
            if ($population > 10000 && $population <= 20000) {
                $nurseLimit = 6;
            } elseif ($population > 20000 && $population <= 30000) {
                $nurseLimit = 10;
            } elseif ($population > 30000 ) {
                $nurseLimit = 10;
            }


            // Check current nurse count in the clinic
            $clinicNurseCount = Nurse::where('clinic_id', $request->clinic_id)->count();
            if ($clinicNurseCount >= $nurseLimit) {
                return redirect()->back()->with('error', 'You have exceeded the maximum number of nurses allowed.')->with('showForm', 'nurseForm');
            }

            // Create new nurse entry with hashed password
            Nurse::create([
                'name' => $request->name,
                'specialty' => $request->specialty,
                'clinic_id' => $request->clinic_id,
                'experience' => $request->experience,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password,
            ]);

            // Redirect back with success message
            return redirect()->back()->with('success', 'Nurse created successfully!');

        } catch (Exception $e) {
            // Handle any unexpected errors
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.')->with('showForm', 'nurseForm');
        }
    }



    public function addGeneralStaff(Request $request)
    {
        try {
            // Custom validation rules and messages
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'clinic_id' => 'required|integer|exists:clinics,id',
                'experience' => 'required|integer|min:0',
                'email' => 'required|string|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'Please enter the staff member\'s name.',
                'name.max' => 'The name must not exceed 255 characters.',
                'role.required' => 'Please specify the role of the staff member.',
                'role.max' => 'The role must not exceed 255 characters.',
                'clinic_id.required' => 'Please select a valid clinic.',
                'experience.required' => 'Please enter years of experience.',
                'experience.integer' => 'Experience must be a valid number.',
                'email.required' => 'Please enter an email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'This email address is already taken.',
                'username.required' => 'Please enter a username.',
                'username.max' => 'The username must not exceed 255 characters.',
                'username.unique' => 'This username is already taken.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'The password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Handle validation failures
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('showForm', 'staffForm');
            }

            // Retrieve district ID and population
            $districtId = Clinic::find($request->clinic_id)->district_id;
            $population = District::find($districtId)->population;

            // Set general staff limit based on population range
            $staffLimit = 3;

            if ($population > 10000 && $population <= 20000) {
                $staffLimit = 5;
            } elseif ($population > 20000 && $population <= 30000) {
                $staffLimit = 8;
            } elseif ($population > 30000 ) {
                $staffLimit = 8;
            }

            // Check current general staff count in the clinic
            $clinicStaffCount = GeneralStaff::where('clinic_id', $request->clinic_id)->count();
            if ($clinicStaffCount >= $staffLimit) {
                return redirect()->back()->with('error', 'You have exceeded the maximum number of general staff allowed.')->with('showForm', 'staffForm');
            }

            // Create new general staff entry with hashed password
            GeneralStaff::create([
                'name' => $request->name,
                'role' => $request->role,
                'clinic_id' => $request->clinic_id,
                'experience' => $request->experience,
                'email' => $request->email,
                'username' => $request->username,
                'password' => $request->password,
            ]);

            // Redirect back with success message
            return redirect()->back()->with('success', 'General staff member created successfully!');

        } catch (Exception $e) {
            // Handle any unexpected errors
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.')->with('showForm', 'staffForm');
        }
    }

    public function deleteClinic(int $clinicId)
    {
        try {

            // Validate the clinic ID to ensure it exists
            $clinic = Clinic::find($clinicId);

            if (!$clinic) {
                return redirect()->back()->with('error', 'Clinic not found.');
            }

            // Soft delete associated doctors, nurses, and general staff
            Doctor::where('clinic_id', $clinicId)->delete();
            Nurse::where('clinic_id', $clinicId)->delete();
            GeneralStaff::where('clinic_id', $clinicId)->delete();

            // Delete appointments associated with doctors in this clinic
            Appointment::whereIn('doctor_id', Doctor::where('clinic_id', $clinicId)->pluck('id'))->delete();

            // Soft delete the clinic itself
            $clinic->delete();

            // Return a success message
            return response()->json('success');
        } catch (Exception $e) {
            // Handle any unexpected errors and return an error message
            return response()->json([]);
        }
    }
    public function deleteSome(Request $request)
    {
        try {
            // Retrieve IDs from the request for doctors, nurses, and general staff
            $doctorIds = $request->input('doctor_ids', []);
            $nurseIds = $request->input('nurse_ids', []);
            $generalStaffIds = $request->input('general_staff_ids', []);

            // Validate the input to ensure it's an array
            if (!is_array($doctorIds) || !is_array($nurseIds) || !is_array($generalStaffIds)) {
                return redirect()->back()->with('error', 'Invalid data format.');
            }

            // Delete doctors, nurses, and general staff by their IDs using soft deletes if applicable
            if (!empty($doctorIds)) {
                foreach ($doctorIds as $id) {
                    Doctor::find($id)?->delete(); // Use null safe operator to avoid errors if not found
                }
            }

            if (!empty($nurseIds)) {
                foreach ($nurseIds as $id) {
                    Nurse::find($id)?->delete();
                }
            }

            if (!empty($generalStaffIds)) {
                foreach ($generalStaffIds as $id) {
                    GeneralStaff::find($id)?->delete();
                }
            }

            // Return a success message to the user
            return redirect()->back()->with('success', 'Selected staff members have been successfully deleted.');

        } catch (Exception $e) {
            // Catch any unexpected errors and return an error message
            return redirect()->back()->with('error', 'An unexpected error occurred while trying to delete the staff members.');
        }
    }


    public function records()
    {
        $record = Record::all()->reverse();

        Record::where('status', '!=', 'read')->update(['status' => 'read']);
        //        return response()->json($record);
        return view("notification",compact('record'));
    }
}
