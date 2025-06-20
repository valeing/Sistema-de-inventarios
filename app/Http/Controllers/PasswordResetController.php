<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    // Mostrar la vista de solicitud de restablecimiento
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Mostrar el formulario para restablecer la contraseña
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Enviar el enlace de restablecimiento
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', '¡Enlace enviado! Revisa tu correo electrónico.');
        } else {
            return back()->withErrors(['email' => 'Error al enviar el enlace. Intenta de nuevo más tarde.']);
        }
    }

    // Procesar el restablecimiento de contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Redirige de vuelta al formulario con el mensaje de éxito
            return redirect()->route('password.reset', ['token' => $request->token])
                            ->with('status', '¡Contraseña actualizada correctamente!');
        } else {
            return back()->withErrors(['email' => 'El enlace ha expirado o es inválido.']);
        }
    }
}