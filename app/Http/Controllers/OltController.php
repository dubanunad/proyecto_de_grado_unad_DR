<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;

class OltController extends Controller
{
    public function index()
    {
        $olts = Olt::where('branch_id', session('branch_id'))
            ->simplePaginate(8);

        foreach ($olts as $olt) {
            $datos = $olt->obtenerDatosRemotos();
            $olt->status_text = $datos['status']; // Para mostrar en la vista
        }

        return view('gestisp.olts.index', compact('olts'));
    }
    public function create()
    {
        return view('gestisp.olts.create');
    }

    public function store(Request $request)
    {
        $branchId = session('branch_id');
        $validated = $request->validate([
            'name' => 'required|string|min:5|max:255',
            'ip_address' => 'required|ip',
            'ssh_port' => 'required|numeric|min:1|max:65535',
            'telnet_port' => 'nullable|numeric|min:1|max:65535',
            'snmp_port' => 'nullable|numeric|min:1|max:65535',
            'read_snmp_comunity' => 'nullable|string|max:255',
            'write_snmp_comunity' => 'nullable|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
        ]);

        $validated['branch_id'] = $branchId;

        // Cifrar la contraseña
         $validated['password'] = bcrypt($validated['password']);

        // Crear el registro
        Olt::create($validated);

        return redirect()->route('olts.index')->with('success', 'OLT creada correctamente.');
    }
    public function ontsAutofind(Olt $olt)
    {
        try {
            $ssh = new SSH2($olt->ip_address, (int) $olt->ssh_port);

            if (!$ssh->login($olt->username, $olt->getPlainPassword())) {
                return response()->json(['error' => 'No se pudo conectar a la OLT'], 500);
            }

            $ssh->setTimeout(2);
            $ssh->write("enable\n");
            $ssh->read('#');

            $ssh->setTimeout(3);
            $ssh->write("display ont autofind all\n");
            $output = $ssh->read('#');

            $onts = $this->processOntsAutofind($output);

            return response()->json($onts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function processOntsAutofind($output)
    {
        $onts = [];
        $bloques = preg_split('/Number\s+:/', $output, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($bloques as $bloque) {
            $ont = [];

            if (preg_match('/F\/S\/P\s+:\s+([0-9\/]+)/', $bloque, $m)) {
                $ont['fspon'] = $m[1];
            }
            if (preg_match('/Ont SN\s+:\s+([0-9A-F]+)\s+\((.*?)\)/i', $bloque, $m)) {
                $ont['ont_sn_hex'] = $m[1];
                $ont['ont_sn'] = $m[2];
            }
            if (preg_match('/Ont EquipmentID\s+:\s+(.*?)\s*$/m', $bloque, $m)) {
                $ont['equipment_id'] = $m[1];
            }
            if (preg_match('/VendorID\s+:\s+(.*?)\s*$/m', $bloque, $m)) {
                $ont['vendor'] = $m[1];
            }
            if (preg_match('/Ont autofind time\s+:\s+(.*?)\s*$/m', $bloque, $m)) {
                $ont['autofind_time'] = $m[1];
            }

            if (!empty($ont)) {
                $onts[] = $ont;
            }
        }

        return $onts;
    }
}
