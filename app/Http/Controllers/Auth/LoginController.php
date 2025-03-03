<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    //Obtener las sucursales para el login
    public function getBranches(Request $request)
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['branches' => []]);
        }

        // Especificamos la tabla para cada columna
        $branches = $user->branches()
            ->select('branches.id', 'branches.name')  // Especificamos la tabla 'branches'
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return response()->json([
            'branches' => $branches
        ]);
    }
    protected function authenticated(Request $request, $user)
    {
        $branchId = $request->input('branch_id');

        if (!$branchId) {
            return redirect()->back()->withErrors(['branch_id' => 'Debe seleccionar una sucursal']);
        }

        // Verificar si el usuario tiene acceso a esta sucursal
        $branchRole = $user->branches()->where('branch_id', $branchId)->first();

        if (!$branchRole) {
            return redirect()->back()->withErrors(['branch_id' => 'No tiene acceso a esta sucursal']);
        }

        // Guardar la sucursal y el rol en la sesión
        session([
            'branch_id' => $branchId,
            'current_role_id' => $branchRole->pivot->role_id, // Guardar el role_id
        ]);

        // Actualizar la sucursal seleccionada en el usuario
        $user->update(['selected_branch_id' => $branchId]);

        return redirect()->intended($this->redirectPath());
    }


}
