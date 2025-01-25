@extends('adminlte::page')

@section('title', 'Facturas')

@section('content')
    <div class="card  mt-3">
       <div class="card-head pt-3">
           <div class="row d-flex justify-content-between mb-4 pr-3">
               <div class="col-md-8">
                   <h2 class="ml-2 P3">LISTADO DE FACTURAS</h2>
               </div>
               <div class="col-md-2  text-center text-md-right mb-2">
                   <form action="{{ route('invoices.generate') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas generar las facturas?');">
                       @csrf
                       <button type="submit" class="btn btn-primary col-8">Generar Facturas</button>
                   </form>
               </div>

               <div class="col-md-2 text-center text-md-left">
                   <a href="" class="btn btn-danger col-8" title="Generar PDF de facturas pendientes">Generar PDF <i class="far fa-file-pdf"></i></a>
               </div>

           </div>

           @if(session('success'))
               <div class="alert alert-success">
                   {{ session('success') }}
               </div>
           @endif

           @if(session('error'))
               <div class="alert alert-danger">
                   {{ session('error') }}
               </div>
           @endif
       </div>
       <div class="card-body">
           <div class="table-responsive">
               <table class="table table-hover">
                   <tr>
                       <th>ID</th>
                       <th>Cliente</th>
                       <th>Fecha de emisión</th>
                       <th>Fecha de vencimiento</th>
                       <th>Saldo</th>
                       <th>Estado</th>
                       <th></th>
                   </tr>

                   @foreach($invoices as $invoice)
                       <tr>
                       <td>{{$invoice->id}}</td>
                       <td>{{$invoice->contract->client->name}} {{$invoice->contract->client->last_name}}</td>
                       <td>{{$invoice->issue_date}}</td>
                       <td>{{$invoice->due_date}}</td>
                       <td>{{$invoice->total}}</td>
                       <td>{{$invoice->status}}</td>
                           <td><a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-info"><i class="far fa-eye"></i></a></td>
                       </tr>
                   @endforeach
               </table>
           </div>
       </div>
        <div class="text-center">
            {{ $invoices->links() }}
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const button = this.querySelector('button');
            button.disabled = true;
            button.textContent = 'Generando...';
        });
    </script>
@endsection

