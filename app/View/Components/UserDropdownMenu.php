<?php

namespace App\View\Components;

use AllowDynamicProperties;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

#[AllowDynamicProperties] class UserDropdownMenu extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $user = Auth::user();

        if ($user) {
            $this->firstName = $user->first_name;
            $this->lastName = $user->last_name;
            $this->email = $user->email;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-dropdown-menu', [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
        ]);    }
}
