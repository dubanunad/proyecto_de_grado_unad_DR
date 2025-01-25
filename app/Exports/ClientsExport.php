<?php
namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class ClientsExport implements FromQuery
{
    use Exportable;

    public function query()
    {
        return Client::query();
    }
}



