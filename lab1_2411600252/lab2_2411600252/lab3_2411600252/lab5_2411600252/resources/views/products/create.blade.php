@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
    <div class="d-flex justify-content-between flex-wrap align-items-center pb-2 mb-3 border-bottom">
        <h2>Add Product</h2>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Inventory
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}">
                @include('products._form')
            </form>
        </div>
    </div>
@endsection
