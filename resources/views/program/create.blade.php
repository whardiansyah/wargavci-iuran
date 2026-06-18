@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Tambah Program</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('program.store') }}" method="POST">
                @csrf
                @include('program._form')
            </form>
        </div>
    </div>
</div>
@endsection
