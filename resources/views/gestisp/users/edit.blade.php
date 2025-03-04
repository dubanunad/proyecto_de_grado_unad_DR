@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <div class="card p-3">
        <h2>ADMINISTRAR USUARIOS</h2>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="identity_number" class="form-label">Número de Identidad</label>
                            <input type="text" class="form-control" id="identity_number" name="identity_number" value="{{ $user->identity_number }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="number_phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="number_phone" name="number_phone" value="{{ $user->number_phone }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ $user->address }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="branch-role-container">
                            @foreach($userBranches as $index => $userBranch)
                                <div class="branch-role-pair mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="branch" class="form-label">Sucursal</label>
                                                <select class="form-control branch-select" name="branches[{{ $index }}][branch_id]">
                                                    <option value="">Seleccione</option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->id }}" {{ $branch->id == $userBranch->id ? 'selected' : '' }}>
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <label for="rol" class="form-label">Rol</label>
                                                <select class="form-control role-select" name="branches[{{ $index }}][role_id]">
                                                    <option value="">Seleccione</option>
                                                    @foreach($roles as $rol)
                                                        <option value="{{ $rol->id }}" {{ $rol->id == $userBranch->pivot->role_id ? 'selected' : '' }}>
                                                            {{ $rol->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger remove-branch-role" style="margin-top: 30px;">X</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-branch-role" class="btn btn-primary">Agregar otra sucursal</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-2">Guardar Usuario</button>
            </form>
        </div>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let pairIndex = document.querySelectorAll('.branch-role-pair').length;

            document.getElementById('add-branch-role').addEventListener('click', function() {
                const container = document.getElementById('branch-role-container');
                const newPair = document.createElement('div');
                newPair.classList.add('branch-role-pair', 'mb-3');
                newPair.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="branch" class="form-label">Sucursal</label>
                        <select class="form-control branch-select" name="branches[${pairIndex}][branch_id]">
                            <option value="">Seleccione</option>
                            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-control role-select" name="branches[${pairIndex}][role_id]">
                            <option value="">Seleccione</option>
                            @foreach($roles as $rol)
                <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                            @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger remove-branch-role" style="margin-top: 30px;">X</button>
        </div>
    </div>
`;
                container.appendChild(newPair);
                pairIndex++;
            });

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-branch-role')) {
                    event.target.closest('.branch-role-pair').remove();
                }
            });
        });
    </script>
@endsection
