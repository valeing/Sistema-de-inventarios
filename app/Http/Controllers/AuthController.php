<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Models\Resguardante;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de registro de usuarios.
     */
    public function create()
    {
        $prefix = app('prefix'); // Obtiene el prefijo dinámico según el rol del usuario
        return view("$prefix.users.register");
    }


    /**
     * Manejar el registro de usuarios.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $isFirstUser = User::count() === 0;

        if ($isFirstUser) {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => 1, // Administrador
                'is_active' => true,
            ]);

            return redirect('/login')->with('message', 'Registro exitoso. Por favor, inicie sesión como Administrador.');
        } else {
            Notification::create([
                'email' => $request->email,
                'password' => Crypt::encrypt($request->password), // Encriptar la contraseña
                'nombre_completo' => $request->name,
                'requested_at' => now(),
                'seen' => false,
            ]);

            return redirect()->back()->with('message', 'Tu registro está en revisión por el administrador.');
        }
    }

    /**
     * Manejar el inicio de sesión de usuarios.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['message' => 'Tu cuenta está en revisión por el administrador.'])->withInput();
            }

            switch ($user->role_id) {
                case 1: // Administrador
                    return redirect('/admin')->with('message', 'Inicio de sesión exitoso como Administrador.');
                case 2: // Resguardante
                    return redirect('/resguardante')->with('message', 'Inicio de sesión exitoso.');
                case 3: // Operador
                    return redirect('/operador')->with('message', 'Inicio de sesión exitoso.');
                default:
                    Auth::logout();
                    return back()->withErrors(['message' => 'Rol no válido para acceder al sistema.'])->withInput();
            }
        }

        return back()->withErrors(['message' => 'Credenciales incorrectas.'])->withInput();
    }

    /**
     * Obtener notificaciones no vistas.
     */
    public function getNotifications()
    {
        $notifications = Notification::where('seen', false)
            ->orderBy('requested_at', 'desc')
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Mostrar detalles de una notificación y sus relacionadas.
     */
    public function showNotification($id)
    {
        $prefix = app('prefix'); // Obtiene el prefijo dinámico según el rol del usuario
        $notification = Notification::findOrFail($id);

        $relatedNotifications = Notification::where('email', $notification->email)->get();

        foreach ($relatedNotifications as $relatedNotification) {
            if (!empty($relatedNotification->password)) {
                $relatedNotification->password = Crypt::decrypt($relatedNotification->password);
            }
        }

        return view("$prefix.notifications.show", compact('notification', 'relatedNotifications'));
    }


    /**
     * Listar usuarios registrados.
     */
    public function index(Request $request)
    {
        $prefix = app('prefix'); // Obtiene el prefijo según el rol del usuario autenticado
        $rol = $request->input('rol');
        $search = $request->input('search');

        $query = User::with('role')
            ->orderByRaw("FIELD(role_id, 1, 3, 2)") // Orden: Administrador (1), Operador (3), Resguardante (2)
            ->orderBy('created_at', 'desc');

        // 🔹 Filtrar por rol si se selecciona uno
        if (!empty($rol)) {
            $query->whereHas('role', function ($q) use ($rol) {
                $q->where('name', $rol);
            });
        }

        // 🔹 Aplicar búsqueda dentro del rol filtrado
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Obtener los usuarios
        $users = $query->paginate(8)->appends([
            'rol' => $rol,
            'search' => $search
        ]);

        // 🔹 Si la solicitud es AJAX, retornar JSON con los datos de los usuarios
        if ($request->ajax()) {
            return response()->json(
                $users->map(function ($user) use ($prefix) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->name ?? 'Sin rol',
                        'url' => route("$prefix.users.show", $user->id)
                    ];
                })
            );
        }

        return view("$prefix.users.index", compact('users', 'rol', 'search'));
    }





    /**
     * Registrar un nuevo usuario.
     */
    public function store(Request $request)
    {
        $prefix = app('prefix'); // Obtiene el prefijo dinámico según el rol del usuario autenticado

        // Validación de datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|integer',
            'resguardante_id' => 'nullable|integer|exists:resguardantes,id_resguardante',
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => true,
        ]);

        $successMessage = 'Usuario registrado correctamente.';
        $infoMessage = null;
        $warningMessage = null;

        // Asignación de resguardante
        if ($request->role_id == 2 && $request->filled('resguardante_id')) {
            $resguardante = Resguardante::find($request->resguardante_id);
            if ($resguardante) {
                $resguardante->user_id = $user->id;
                $resguardante->save();
                $infoMessage = 'Resguardante asignado correctamente.';
            }
        }

        // Envío de correo con manejo de errores
        try {
            Mail::to($user->email)->send(new \App\Mail\UserRegistered($user, $request->password));
            $successMessage .= ' Credenciales enviadas por correo.';
        } catch (\Exception $e) {
            \Log::error('Error al enviar credenciales: ' . $e->getMessage());
            $warningMessage = 'El usuario se creó correctamente pero falló el envío de credenciales.';
        }

        // Construcción de respuesta con mensajes
        $redirect = redirect()->route("$prefix.users.index")->with('success', $successMessage);

        if ($infoMessage) {
            $redirect = $redirect->with('info', $infoMessage);
        }

        if ($warningMessage) {
            $redirect = $redirect->with('warning', $warningMessage);
        }

        return $redirect;
    }



    /**
     * Marcar notificación como vista.
     */
    public function markNotificationAsSeen($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['seen' => true]);

        return redirect()->back()->with('message', 'Notificación marcada como vista.');
    }

    /**
     * Eliminar notificación.
     */
    public function destroyNotification($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('message', 'Notificación eliminada correctamente.');
    }

    /**
     * Editar un usuario.
     */
    public function edit($id)
    {
        $prefix = app('prefix'); // Obtiene el prefijo dinámico según el rol del usuario autenticado
        $user = User::findOrFail($id);

        return view("$prefix.users.edit", compact('user'));
    }


    /**
     * Actualizar usuario.
     */
    public function update(Request $request, $id)
    {
        $prefix = app('prefix'); // Obtiene el prefijo dinámico basado en el rol autenticado

        $request->validate([
            'name' => 'required|string|max:255',
            'role_id' => 'required|integer',
            'resguardante_id' => 'nullable|exists:resguardantes,id_resguardante',
        ]);

        $user = User::findOrFail($id);

        // 🔹 Si el usuario tiene un resguardante asignado, desvincularlo antes de asignar otro
        if ($user->resguardante) {
            $user->resguardante->update(['user_id' => null]);
        }

        // 🔹 Actualizar datos del usuario
        $user->update([
            'name' => $request->name,
            'role_id' => $request->role_id,
        ]);

        // 🔹 Si el nuevo rol es "Resguardante" y hay un nuevo resguardante, asignarlo
        if ($request->role_id == 2 && $request->filled('resguardante_id')) {
            $resguardante = Resguardante::findOrFail($request->resguardante_id);

            // Verifica si el resguardante ya está asignado a otro usuario
            if ($resguardante->user_id && $resguardante->user_id != $user->id) {
                return redirect()->route("$prefix.users.index")->with('error', 'Este resguardante ya está asignado a otro usuario.');
            }

            // Asigna el resguardante al usuario
            $resguardante->update(['user_id' => $user->id]);
        }

        return redirect()->route("$prefix.users.index")->with('success', 'Usuario actualizado correctamente.');
    }




    /**
     * Eliminar un usuario.
     */
    public function destroy(User $user)
    {
        $prefix = app('prefix'); // Obtiene el prefijo dinámico según el rol autenticado

        // 🔹 Si el usuario es un resguardante, desvincularlo antes de eliminarlo
        if ($user->resguardante) {
            $user->resguardante->update(['user_id' => null]);
        }

        // 🔹 Eliminar el usuario
        $user->delete();

        return redirect()->route("$prefix.users.index")->with('success', 'Usuario eliminado correctamente.');
    }


    public function show($id)
    {
        // Obtiene el prefijo dinámico basado en el rol del usuario autenticado
        $prefix = app('prefix');

        // Busca el usuario por su ID y carga su rol
        $user = User::with('role')->findOrFail($id);

        // Retorna la vista correspondiente según el rol del usuario autenticado
        return view("$prefix.users.show", compact('user'));
    }


    public function getAllUsers()
    {
        $users = User::select('id', 'name', 'email', 'created_at')
            ->with('role:id,name') // 🔹 Cargar solo el ID y nombre del rol
            ->get();

        return response()->json($users);
    }




    public function buscarResguardante(Request $request)
    {
        $query = $request->input('query');

        $resguardantes = Resguardante::whereNull('user_id') // Solo traer los que no tienen usuario asignado
            ->where(function ($q) use ($query) {
                $q->where('nombre_apellido', 'LIKE', "%$query%")
                    ->orWhere('numero_empleado', 'LIKE', "%$query%")
                    ->orWhere('telefono', 'LIKE', "%$query%");
            })
            ->with([
                'departamento' => function ($query) {
                    $query->select('id_departamento', 'nombre'); // Asegurar que obtenemos el nombre
                }
            ])
            ->take(10)
            ->get();

        return response()->json($resguardantes);
    }

    public function desvincularResguardante($userId)
    {
        $user = User::findOrFail($userId);

        if (!$user->resguardante) {
            return response()->json(['error' => 'El usuario no tiene resguardante asignado.'], 404);
        }

        // Elimina la relación con el resguardante
        $user->resguardante->update(['user_id' => null]);

        return response()->json(['success' => 'Resguardante desvinculado correctamente.']);
    }







}
