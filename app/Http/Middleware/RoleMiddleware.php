<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Definir los roles por ID
        $rolesMap = [
            'Administrador' => 1,
            'Resguardante' => 2, // 🔹 Agregado Resguardante (ID: 2)
            'Operador' => 3,
        ];

        $userRoleId = Auth::user()->role_id ?? null; // Obtener el role_id del usuario

        // Verificar si el usuario tiene uno de los roles permitidos
        if (!in_array($userRoleId, array_values($rolesMap))) {
            abort(403, 'No tiene acceso a esta sección.');
        }

        return $next($request);
    }
}
