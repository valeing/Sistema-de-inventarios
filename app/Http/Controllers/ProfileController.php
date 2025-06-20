<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;


class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function show()
    {
        $user = Auth::user();

        // Determinar el prefijo según el rol del usuario
        $prefix = match ($user->role->name) {
            'Administrador' => 'admin',
            'Operador' => 'operador',
            'Resguardante' => 'resguardante',
            default => 'default' // En caso de un rol no definido
        };

        // Verificar que el prefijo sea válido
        if (!in_array($prefix, ['admin', 'operador', 'resguardante'])) {
            abort(403, 'Acceso no autorizado');
        }

        return view("$prefix.mi-perfil.show", compact('user'));
    }



    /**
     * Actualizar la contraseña del usuario autenticado.
     */
    public function updatePassword(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        if (!$user) {
            return back()->withErrors(['message' => 'Usuario no autenticado.']);
        }

        // Validar la entrada del usuario
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // La confirmación debe coincidir con "new_password_confirmation"
        ]);

        // Verificar que la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        // Actualizar la contraseña de forma segura
        try {
            $user->password = Hash::make($request->new_password);
            $user->save(); // Aquí se usa save() correctamente en la instancia del usuario autenticado

            return back()->with('success', 'Contraseña actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Hubo un error al actualizar la contraseña. Inténtalo nuevamente.']);
        }
    }
}
