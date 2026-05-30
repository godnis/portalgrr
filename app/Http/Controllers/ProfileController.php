<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'tab'  => $request->query('tab', 'dados'),
        ]);
    }

    /**
     * PATCH /profile -> name/email (Breeze)
     * Regra: SOMENTE nível 10 pode alterar nome/e-mail.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user  = $request->user();
        $nivel = (int) ($user->nivel ?? 0);
        $tab   = $request->query('tab', 'dados');

        // ✅ Somente nível 10+
        if ($nivel < 10) {
            return Redirect::route('profile.edit', ['tab' => $tab])
                ->with('status', 'profile-locked');
        }

        $validated = $request->validated();

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit', ['tab' => $tab])
            ->with('status', 'profile-updated');
    }

    /**
     * PUT /profile/avatar
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $tab  = $request->query('tab', 'dados');
        $user = $request->user();

        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'], // 3MB
        ]);

        // ✅ apaga avatar antigo (se existir)
        if (!empty($user->avatar_path) && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        // ✅ salva novo
        $path = $validated['avatar']->store('avatars', 'public');

        $user->avatar_path = $path;
        $user->save();

        return Redirect::route('profile.edit', ['tab' => $tab])
            ->with('status', 'avatar-updated');
    }

    /**
     * DELETE /profile/avatar
     */
    public function removeAvatar(Request $request): RedirectResponse
    {
        $tab  = $request->query('tab', 'dados');
        $user = $request->user();

        if (!empty($user->avatar_path) && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = null;
        $user->save();

        return Redirect::route('profile.edit', ['tab' => $tab])
            ->with('status', 'avatar-removed');
    }

    /**
     * PUT /profile/preferences
     * telefone/bio/tema/notify_ops
     *
     * ✅ tema: dark | light
     * ✅ padrão: light
     */
    public function updatePrefs(Request $request): RedirectResponse
    {
        $tab  = $request->query('tab', 'preferencias');
        $user = $request->user();

        $data = $request->validate([
            'telefone'   => ['nullable', 'string', 'max:30'],
            'bio'        => ['nullable', 'string', 'max:500'],

            // ✅ agora só aceita dark/light
            'tema'       => ['nullable', 'in:dark,light'],

            // checkbox opcional
            'notify_ops' => ['nullable', 'boolean'],
        ]);

        // ✅ normaliza checkbox (quando não vem, vira false)
        $data['notify_ops'] = (bool) $request->boolean('notify_ops');

        // ✅ se vier vazio/inválido, cai pra light
        $tema = strtolower(trim((string)($data['tema'] ?? '')));
        $data['tema'] = in_array($tema, ['dark', 'light'], true) ? $tema : 'light';

        /**
         * ✅ Salva apenas campos realmente existentes no fillable (seguro).
         * Se notify_ops não existir no seu model/DB, não quebra.
         */
        $fillable = $user->getFillable();

        foreach ($data as $key => $value) {
            if (in_array($key, $fillable, true)) {
                $user->{$key} = $value;
            }
        }

        $user->save();

        return Redirect::route('profile.edit', ['tab' => $tab])
            ->with('status', 'prefs-updated');
    }

    /**
     * PUT /password
     * Mantém a aba de segurança e usa error bag updatePassword
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = Hash::make((string) $request->password);
        $user->save();

        return Redirect::route('profile.edit', ['tab' => 'seguranca'])
            ->with('status', 'password-updated');
    }

    /**
     * DELETE /profile
     * Restrito por nível
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ((int) ($request->user()->nivel ?? 0) < 10) {
            return Redirect::route('profile.edit', ['tab' => 'seguranca'])
                ->with('error', 'Você não tem permissão para excluir a conta. Solicite via Diretoria.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if (!empty($user->avatar_path) && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}