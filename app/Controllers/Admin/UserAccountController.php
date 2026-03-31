<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Facade\Auth;
use App\Core\Controllers\AdminController;
use App\Core\Facade\Session;
use App\Core\Http\Attributes\Get;
use App\Core\Http\Attributes\Middleware;
use App\Core\Http\Attributes\Prefix;
use App\Core\Http\Attributes\Post;
use App\Core\Http\Request;
use App\Core\Exception\ValidationException;
use App\Model\User;
use App\Services\AuthSessionService;
use App\Services\PasswordService;
use App\Services\TotpService;

#[Prefix('/admin')]
#[Middleware('auth')]
class UserAccountController extends AdminController
{
    #[Get('edit-profile', 'admin.account.edit')]
    public function edit()
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        return view('admin.edit-profile', compact('user'));
    }

    #[Post('edit-profile', 'admin.account.update')]
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

    #[Get('password', 'admin.password')]
    public function passwordForm()
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        return view('admin.change-password', compact('user'));
    }

    #[Post('password', 'admin.password.update')]
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

    #[Get('security', 'admin.security')]
    public function security()
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        $setupSecret = null;
        $provisioningUri = null;

        if (!$user->two_factor_enabled) {
            $setupSecret = (string) Session::get('TWO_FACTOR_SETUP_SECRET', '');

            if ($setupSecret === '') {
                $setupSecret = TotpService::generateSecret();
                Session::set('TWO_FACTOR_SETUP_SECRET', $setupSecret);
            }

            $provisioningUri = TotpService::provisioningUri(
                (string) $user->email,
                $setupSecret,
                'Soft MVC'
            );
        }

        return inertia('Admin/Security', [
            'meta' => [
                'title' => 'Sicurezza account',
            ],
            'security' => [
                'user' => [
                    'email' => (string) ($user->email ?? ''),
                    'twoFactorEnabled' => (bool) ($user->two_factor_enabled ?? false),
                ],
                'setupSecret' => $setupSecret,
                'provisioningUri' => $provisioningUri,
            ],
        ]);
    }

    #[Post('security/two-factor/enable', 'admin.security.two-factor.enable')]
    public function enableTwoFactor(Request $request)
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        $setupSecret = (string) Session::get('TWO_FACTOR_SETUP_SECRET', '');

        if ($setupSecret === '') {
            return response()->back()->withError('Secret 2FA mancante. Ricarica la pagina sicurezza e riprova.');
        }

        if (!TotpService::verify($setupSecret, $request->string('code'))) {
            return response()->back()->withError('Il codice TOTP inserito non e valido.');
        }

        User::query()->where('id', $user->id)->update([
            'two_factor_secret' => $setupSecret,
            'two_factor_enabled' => 1,
        ]);

        Session::remove('TWO_FACTOR_SETUP_SECRET');

        return response()->back()->withSuccess('Autenticazione a due fattori abilitata.');
    }

    #[Post('security/two-factor/disable', 'admin.security.two-factor.disable')]
    public function disableTwoFactor()
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        User::query()->where('id', $user->id)->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => 0,
        ]);

        Session::remove('TWO_FACTOR_SETUP_SECRET');

        return response()->back()->withSuccess('Autenticazione a due fattori disattivata.');
    }

    #[Get('sessions', 'admin.sessions')]
    public function sessions()
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        $sessions = AuthSessionService::getForUser((int) $user->id);
        $currentSessionId = session_id();

        return inertia('Admin/Sessions', [
            'meta' => [
                'title' => 'Sessioni attive',
            ],
            'sessionsPage' => [
                'currentSessionId' => $currentSessionId,
                'user' => [
                    'email' => (string) ($user->email ?? ''),
                ],
                'sessions' => array_map(
                    static fn(object $session): array => [
                        'id' => (string) ($session->id ?? ''),
                        'ip' => (string) ($session->ip ?? ''),
                        'userAgent' => (string) ($session->user_agent ?? 'Sconosciuto'),
                        'lastActivity' => (string) ($session->last_activity ?? ''),
                        'createdAt' => (string) ($session->created_at ?? '-'),
                    ],
                    $sessions
                ),
            ],
        ]);
    }

    #[Post('sessions/{id}/terminate', 'admin.sessions.terminate')]
    public function terminateSession(string $id)
    {
        $user = $this->currentUser();

        if (!$user) {
            return response()->redirect('/login');
        }

        $session = AuthSessionService::find($id);

        if ($session === null || (int) $session->user_id !== (int) $user->id) {
            return response()->back()->withError('Sessione non trovata.');
        }

        if ($id === session_id()) {
            Auth::logout();
            return response()->redirect('/login');
        }

        AuthSessionService::terminate($id);

        return response()->back()->withSuccess('Sessione terminata.');
    }

    private function currentUser(): ?object
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }
}
