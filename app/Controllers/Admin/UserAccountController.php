<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controllers\AdminController;
use App\Core\Facade\Session;
use App\Core\Http\Attributes\RouteAttr;
use App\Core\Http\Request;
use App\Model\User;

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

        $email = trim((string) ($request->email ?? ''));
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

        $currentPassword = (string) ($request->current_password ?? '');
        $password = (string) ($request->password ?? '');
        $confirmed = (string) ($request->confirmed ?? '');

        if (!password_verify($currentPassword, $user->password)) {
            return response()->back()->withError('La password attuale non e corretta.');
        }

        if (strlen($password) < 8) {
            return response()->back()->withError('La nuova password deve contenere almeno 8 caratteri.');
        }

        if ($password !== $confirmed) {
            return response()->back()->withError('Le password non coincidono.');
        }

        User::query()->where('id', $user->id)->update([
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

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
