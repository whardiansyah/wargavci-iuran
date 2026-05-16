@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="h3">Edit Anggota Umroh</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('anggota.update', $anggota) }}" method="POST">
                @csrf
                @method('PUT')
                @include('anggota._form')
            </form>
        </div>
    </div>
</div>
@endsection
