@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.trade-groups.index') }}" class="breadcrumb-item">Trade Groups</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Edit</span>
    </nav>

    <form method="POST" action="{{ route('admin.trade-groups.update', $tradeGroup) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $tradeGroup->name }}" required>
        </div>
        <button style="width: 100px;" type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
