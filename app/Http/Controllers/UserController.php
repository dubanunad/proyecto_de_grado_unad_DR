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
        $this->middleware('can:users.index')->only('index');
        $this->middleware('can:users.create')->only('create', 'store');
        $this->middleware('can:users.edit')->only('edit', 'update');
        $this->middleware('can:users.destroy')->only('destroy');
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
                'rol' => 'required|exists:roles,id',
                'branch' => 'required|exists:branches,id',
            ], [
                'identity_number.unique' => 'El número de identidad ya está en uso.',
                'email.unique' => 'El correo electrónico ya está registrado.',
                'rol.exists' => 'El rol seleccionado no es válido.',
                'branch.exists' => 'La sucursal seleccionada no es válida.',
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

            // Asignar rol y sucursal
            $user->roles()->attach($validatedData['rol']);
            $user->branches()->attach($validatedData['branch']);

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
