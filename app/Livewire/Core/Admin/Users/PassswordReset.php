<?php

namespace App\Livewire\Core\Admin\Users;

use Throwable;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

use Symfony\Component\HttpKernel\Log\Logger;
use function Symfony\Component\Translation\t;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PassswordReset extends Component
{

    public ?string $email = null;
    #locked
    public ?string $userId = null;

    public ?string $password = null;

    public function getUsersProperty()
    {


        return User::select(['id', 'email'])->get();
    }


    public function resetPassword()
    {
        try {
            if ($this->userId) {
                $user = User::find($this->userId);

                // dd($user);

                if (!$user) {
                    $this->dispatch(
                        'toastMagic',
                        status: 'error',
                        title: 'Finding Error',
                        message: 'User not found'
                    );
                    return;
                }

                $this->validate([
                    'password' => 'required|min:8'
                ]);

                $user->password = Hash::make($this->password);
                $user->save(); 

                $this->dispatch(
                    'toastMagic',
                    status: 'success',
                    title: 'Password Reset',
                    message: 'Password reset successfully'
                );
            }
        } catch (Throwable $e) {
            Logger($e);
            $this->dispatch(
                'toastMagic',
                status: 'error',
                title: 'Error',
                message: 'Something went wrong'
            );
        }
    }
    public function render()
    {
        return view('livewire.core.admin.users.passsword-reset');
    }
}
