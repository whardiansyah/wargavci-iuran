@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Tabungan Umroh</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tabungan_umroh.update', $tabunganUmroh) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('tabungan_umroh._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
