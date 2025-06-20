<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Determinar el prefijo basado en el rol del usuario autenticado
    private function getPrefix()
    {
        $role = auth()->user()->role->name ?? '';
        return $role === 'Administrador' ? 'admin' : 'operador';
    }

    public function index(Request $request)
    {
        $prefix = $this->getPrefix();

        $notifications = Notification::orderBy('requested_at', 'desc')->paginate(8);

        // ✅ Si es una petición AJAX (como la de fetch), devolvemos JSON:
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        }

        // 🖥️ Si es una visita normal al navegador, renderiza la vista:
        $groupedNotifications = $notifications->groupBy('email');
        return view("{$prefix}.notifications.index", compact('groupedNotifications', 'notifications', 'prefix'));
    }


    public function show($id)
    {
        $prefix = $this->getPrefix();

        $notification = Notification::findOrFail($id);
        $relatedNotifications = Notification::where('email', $notification->email)->get();

        return view("{$prefix}.notifications.show", [
            'notification' => $notification,
            'relatedNotifications' => $relatedNotifications,
            'prefix' => $prefix
        ]);
    }

    public function markAsSeen($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['seen' => true]);

        return back()->with('success', 'Notificación marcada como vista.');
    }

    public function markAllAsSeen($email)
    {
        Notification::where('email', $email)->update(['seen' => true]);

        return back()->with('success', 'Todas las notificaciones marcadas como vistas.');
    }

    public function destroy($id)
    {
        $prefix = $this->getPrefix();

        $notification = Notification::findOrFail($id);
        $email = $notification->email;
        $notification->delete();

        $relatedNotifications = Notification::where('email', $email)->get();

        if ($relatedNotifications->count() > 0) {
            return redirect()->route('notifications.show', ['id' => $relatedNotifications->first()->id])
                ->with('success', 'Notificación eliminada correctamente.');
        } else {
            return redirect()->route('notifications.index')
                ->with('success', 'Notificación eliminada correctamente.');
        }
    }

    public function destroyAll($email)
    {
        Notification::where('email', $email)->delete();

        return back()->with('success', 'Todas las notificaciones del usuario han sido eliminadas.');
    }
}
