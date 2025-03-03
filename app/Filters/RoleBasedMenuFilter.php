<?php

namespace App\Filters;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Spatie\Permission\Models\Role;

class RoleBasedMenuFilter implements FilterInterface
{
    public function transform($item)
    {
        // Si no hay configuración de permisos, mostrar el elemento
        if (!isset($item['can'])) {
            return $item;
        }

        $permission = $item['can'];

        // Obtener el rol activo desde la sesión
        $currentRoleId = session('current_role_id');

        if (!$currentRoleId) {
            return false; // No mostrar si no hay rol seleccionado
        }

        $role = Role::find($currentRoleId);

        // Verificar si el rol tiene el permiso
        if ($role && $role->hasPermissionTo($permission)) {
            return $item;
        }

        return false; // No mostrar este elemento en el menú
    }
}
