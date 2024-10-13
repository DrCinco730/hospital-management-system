<?php


namespace App\Services;

use App\Models\UserAdmin;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    public function validateUserCredentials(array $credentials): ?UserAdmin
    {
        $user = UserAdmin::where('username', $credentials['username'])->first();

        // Check if user exists and the password is correct
        if ($user && Hash::check($credentials['password'], $user->password)) {
            return $user;
        }

        return null;
    }

    public function loginAs(UserAdmin $user): array
    {
        if ($user->type === 'admin') {
            $adminUser = Admin::find($user->id);
            Auth::guard('admin')->login($adminUser);
            return ['path' => 'admin', 'message' => 'Welcome Admin!'];
        } else {
            $regularUser = User::find($user->id);
            Auth::login($regularUser);
            return ['path' => 'home', 'message' => 'Welcome User!'];
        }
    }
}
