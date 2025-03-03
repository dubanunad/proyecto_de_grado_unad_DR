<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Obtener el rol activo desde la sesión
        $currentRoleId = session('current_role_id');

        if ($currentRoleId) {
            // Obtener el rol
            $role = Role::find($currentRoleId);

            // Verificar si el rol tiene el permiso
            if ($role && $role->hasPermissionTo($permission)) {
                return $next($request);
            }
        }

        // Si no tiene el permiso, denegar acceso
        abort(403, 'No tienes permiso para realizar esta acción.');
    }
}
