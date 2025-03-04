<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //Proteger rutas
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.permission:users.index')->only('index');
        $this->middleware('check.permission:users.create')->only('create', 'store');
        $this->middleware('check.permission:users.edit')->only('edit', 'update');
        $this->middleware('check.permission:users.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::simplePaginate(12);
        return view('gestisp.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        $roles = Role::all();
        return view('gestisp.users.create', compact('branches', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validación de los datos con mensajes personalizados
            $validatedData = $request->validate([
                'identity_number' => 'required|string|max:20|unique:users,identity_number',
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'number_phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'branches' => 'required|array', // Ahora esperamos un array de sucursales
                'branches.*.branch_id' => 'required|exists:branches,id', // Cada sucursal debe existir
                'branches.*.role_id' => 'required|exists:roles,id', // Cada sucursal debe tener un rol válido
            ], [
                'identity_number.unique' => 'El número de identidad ya está en uso.',
                'email.unique' => 'El correo electrónico ya está registrado.',
                'branches.*.branch_id.exists' => 'La sucursal seleccionada no es válida.',
                'branches.*.role_id.exists' => 'El rol seleccionado no es válido.',
            ]);

            // Creación del usuario
            $user = User::create([
                'identity_number' => $validatedData['identity_number'],
                'name' => $validatedData['name'],
                'last_name' => $validatedData['last_name'],
                'number_phone' => $validatedData['number_phone'],
                'address' => $validatedData['address'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Asignar sucursales y roles
            foreach ($validatedData['branches'] as $branchData) {
                $user->branches()->attach($branchData['branch_id'], ['role_id' => $branchData['role_id']]);

                // Obtener el nombre del rol a partir del role_id
                $role = Role::find($branchData['role_id']);

                if ($role) {
                    // Asignar el rol al usuario
                    $user->assignRole($role->name);
                } else {
                    throw new \Exception("El rol con ID {$branchData['role_id']} no existe.");
                }
            }

            Log::info("Usuario creado con éxito: ID {$user->id}, Email: {$user->email}");

            return redirect()->route('users.index')->with('success-create', 'Usuario creado correctamente.');

        } catch (ValidationException $e) {
            Log::warning("Error de validación al crear usuario: " . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (Exception $e) {
            Log::error("Error al crear usuario: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error inesperado. Inténtalo nuevamente.')->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $branches = Branch::all();
        $roles = Role::all();
        $userBranches = $user->branches()->withPivot('role_id')->get(); // Sucursales con su rol asignado
        return view('gestisp.users.edit', compact('user', 'branches', 'roles', 'userBranches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validación de datos
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'number_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'branches' => 'nullable|array',
            'branches.*.branch_id' => 'exists:branches,id',
            'branches.*.role_id' => 'exists:roles,id',
        ]);

        // Actualizar datos del usuario
        $user->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'number_phone' => $request->number_phone,
            'address' => $request->address,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        // Actualizar relación de sucursales y roles
        $user->branches()->detach(); // Eliminar relaciones actuales

        if ($request->has('branches')) {
            foreach ($request->branches as $branch) {
                if (!empty($branch['branch_id']) && !empty($branch['role_id'])) {
                    $user->branches()->attach($branch['branch_id'], ['role_id' => $branch['role_id']]);
                }
            }
        }

        return redirect()->route('users.index')->with('success-update', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
