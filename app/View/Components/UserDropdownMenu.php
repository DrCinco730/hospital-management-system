<?php

namespace App\View\Components;

use AllowDynamicProperties;
use App\Services\LoginService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

#[AllowDynamicProperties]
class UserDropdownMenu extends Component
{
    public string $name;
    public string $email;

    /**
     * Create a new component instance.
     */
    public function __construct(protected LoginService $loginService)
    {
        $user = $this->loginService->isAuthenticatedAcrossGuards();

        if ($user) {
            $this->name = $user->first_name . ' ' . $user->last_name ?? $user->name ?? 'Guest';
            $this->email = $user->email ?? 'Not provided';
        } else {
            $this->name = 'Guest';
            $this->email = 'Not provided';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-dropdown-menu', [
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
