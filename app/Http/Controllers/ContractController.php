<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Exports\ContractsExport;
use App\Models\AditionalCharge;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\TechnicalOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:contracts.index')->only('index');
        $this->middleware('can:contracts.create')->only('create', 'store');
        $this->middleware('can:contracts.edit')->only('edit', 'update');
        $this->middleware('can:contracts.destroy')->only('destroy');
        $this->middleware('can:contracts.show')->only('show');
        $this->middleware('can:contracts.export')->only('export');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Inicializa la consulta base
        $query = Contract::query();

        // Filtrar por la sucursal actual si está configurada en la sesión
        if (session()->has('branch_id')) {
            $query->where('branch_id', session('branch_id'));
        }

        // Verificar si hay filtros adicionales para la búsqueda
        if ($request->filled('filter_field') && $request->filled('filter_value')) {
            $field = $request->filter_field;
            $value = $request->filter_value;

            // Mapear los campos de relaciones para su correcto uso
            $fieldMappings = [
                'client.identity_number' => 'clients.identity_number',
                'client.name' => 'clients.name',
                'client.last_name' => 'clients.last_name',
                'client.number_phone' => 'clients.number_phone',
                'client.email' => 'clients.email',
                'client.type_client' => 'clients.type_client',
                'contract.id' => 'contracts.id',
                'contract.address' => 'contracts.address',
                'contract.cpe_sn' => 'contracts.cpe_sn',
                'contract.user_pppoe' => 'contracts.user_pppoe',
                'contract.status' => 'contracts.status',
                'contract.social_stratum' => 'contracts.social_stratum',
                'contract.activation_date' => 'contracts.activation_date',
                'plan.name' => 'plans.name',
            ];

            if (array_key_exists($field, $fieldMappings)) {
                $mappedField = $fieldMappings[$field];

                // Aplicar los filtros, manejando relaciones
                if (str_contains($mappedField, 'clients')) {
                    $query->whereHas('client', function ($query) use ($mappedField, $value) {
                        $query->where(str_replace('clients.', '', $mappedField), 'like', '%' . $value . '%');
                    });
                } elseif (str_contains($mappedField, 'plans')) {
                    $query->whereHas('plan', function ($query) use ($mappedField, $value) {
                        $query->where(str_replace('plans.', '', $mappedField), 'like', '%' . $value . '%');
                    });
                } else {
                    $query->where($mappedField, 'like', '%' . $value . '%');
                }
            }
        }

        // Paginación flexible
        $perPage = $request->get('per_page', 8);
        $contracts = $query->paginate($perPage);

        return view('gestisp.contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Client $client)
    {
        // Obtener los datos necesarios para el formulario de creación
        $clients = Client::where('branch_id', session('branch_id'))->get(); // Todos los clientes de la sucursal
        $plans = Plan::where('branch_id', session('branch_id'))->get(); // Todos los planes disponibles
        $users = User::all(); // Todos los usuarios para asignar a un contrato

        // Devolver la vista con los datos necesarios
        return view('gestisp.contracts.create', compact( 'clients', 'plans', 'users', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->merge([
            'user_id' => Auth::user()->id,
            'branch_id' => session('branch_id')
        ]);

        $contract = $request->all();
        // Crear el contrato con los datos del formulario


        Contract::create($contract);
        // Redirigir con un mensaje de éxito
        return redirect()->route('clients.index')->with('success', 'Contrato creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        //
        // Obtener los datos
        $branches = Branch::all(); // Todas las sucursales
        $clients = Client::all(); // Todos los clientes
        $plans = Plan::all(); // Todos los planes disponibles
        $users = User::all(); // Todos los usuarios para asignar a un contrato
        $invoices = Invoice::where('contract_id', $contract->id)
                ->simplePaginate(6);
        $additionalCharges = AditionalCharge::where('contract_id', $contract->id)
            ->simplePaginate(6);
        $technicalOrders = TechnicalOrder::where('contract_id', $contract->id)->simplePaginate(6);

        // Devolver la vista con los datos necesarios
        return view('gestisp.contracts.show', compact('branches', 'clients', 'plans', 'users', 'contract', 'invoices', 'additionalCharges', 'technicalOrders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        //
    }

    public function export()
    {
        //Función para exportar los datos de los clientes a un excel
        return (new ContractsExport)->download('listado_de_contratos.xlsx');
    }
}
