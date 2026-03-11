<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Facade\Session;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Core\Exception\ValidationException;
use App\Model\User;
use App\Services\PasswordService;

class UserAccountController extends AdminController
{
    #[RouteAttr(path: 'edit-profile', method: 'GET', name: 'admin.account.edit')]
    public function edit()
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        return view('admin.edit-profile', compact('user'));
    }

    #[RouteAttr(path: 'edit-profile', method: 'POST', name: 'admin.account.update')]
    public function update(Request $request)
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        $email = trim($request->string('email'));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->back()->withError('Inserisci un indirizzo email valido.');
        }

        $existingUser = User::query()->where('email', $email)->first();
        if ($existingUser && (int) $existingUser->id !== (int) $user->id) {
            return response()->back()->withError('Questa email e gia in uso.');
        }

        User::query()->where('id', $user->id)->update(['email' => $email]);
        return response()->back()->withSuccess('Profilo aggiornato.');
    }

    #[RouteAttr(path: 'password', method: 'GET', name: 'admin.password')]
    public function passwordForm()
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        return view('admin.change-password', compact('user'));
    }

    #[RouteAttr(path: 'password', method: 'POST', name: 'admin.password.update')]
    public function passwordUpdate(Request $request)
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        try {
            PasswordService::changeByUser(
                user: $user,
                currentPassword: $request->string('current_password'),
                newPassword: $request->string('password'),
                confirmed: $request->string('confirmed')
            );
        } catch (ValidationException $e) {
            return response()->back()->withError($e->getMessage());
        }

        return response()->back()->withSuccess('Password aggiornata.');
    }

    private function currentUser(): ?object
    {
        $token = Session::get('AUTH_TOKEN');

        if (!$token) {
            return null;
        }

        return User::query()->where('token', $token)->first();
    }
}
