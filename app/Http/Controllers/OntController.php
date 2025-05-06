<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Models\Ont;
use Illuminate\Http\Request;

class OntController extends Controller
{
    //
    public function no_authorized_ont_index()
    {
        $olts = Olt::all();
        return view('gestisp.onts.no-authorized.index', compact('olts'));
    }

    public function authorized_ont_index()
    {
        $onts = Ont::where('branch_id', session('branch_id'))
        ->simplePaginate(8);
        return view('gestisp.onts.authorized.index', compact('onts'));
    }
}
