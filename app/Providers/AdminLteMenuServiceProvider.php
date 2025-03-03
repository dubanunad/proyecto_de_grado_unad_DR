<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

class AdminLteMenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Antes de que se construya el menú, registramos nuestro event listener
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Obtenemos el rol actual desde la sesión
            $currentRoleId = session('current_role_id');

            if (!$currentRoleId) {
                return;
            }

            $role = Role::find($currentRoleId);

            if (!$role) {
                return;
            }

            // Obtener todos los permisos del rol
            $permissions = $role->permissions->pluck('name')->toArray();

            // Antes de que se construya el menú, modificamos la configuración original
            $this->modifyAdminLteConfig($permissions);
        });
    }

    /**
     * Modifica la configuración de AdminLTE para filtrar los elementos del menú.
     */
    protected function modifyAdminLteConfig(array $permissions): void
    {
        // Obtener la configuración actual del menú
        $menu = config('adminlte.menu', []);

        // Filtrar el menú basado en los permisos disponibles
        $filteredMenu = $this->filterMenuItems($menu, $permissions);

        // Actualizar la configuración del menú en tiempo de ejecución
        config(['adminlte.menu' => $filteredMenu]);
    }

    /**
     * Filtra los elementos del menú basado en permisos disponibles.
     */
    protected function filterMenuItems(array $items, array $permissions): array
    {
        $filteredItems = [];

        foreach ($items as $item) {
            // Si el elemento requiere un permiso específico
            if (isset($item['can'])) {
                // Si el usuario no tiene el permiso, saltar este elemento
                if (!in_array($item['can'], $permissions)) {
                    continue;
                }
            }

            // Procesar submenús recursivamente
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $filteredSubmenu = $this->filterMenuItems($item['submenu'], $permissions);

                // Si el submenú filtrado está vacío y no hay otras opciones en el elemento,
                // saltar este elemento
                if (empty($filteredSubmenu) && !isset($item['route']) && !isset($item['url'])) {
                    continue;
                }

                $item['submenu'] = $filteredSubmenu;
            }

            $filteredItems[] = $item;
        }

        return $filteredItems;
    }
}
