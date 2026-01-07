@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <!-- Breadcrumbs with Font Awesome Home Icon -->
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Users</span>
    </nav>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- <form method="GET" action="{{ route('admin.users.index') }}" class="search-container">
                <input type="text" name="search" class="form-control" placeholder="Search users..."
                    value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Search</button>
            </form> -->
    <div class="table-controls">
        <div class="search-container">
            <input type="text" id="user-search" class="form-control" placeholder="Search users..." onkeyup="filterTable()">
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New</a>
        </div>
    </div>

    <table class="user-table" id="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <span class="status-indicator {{ $user->role === 'admin' ? 'status-admin' : 'status-user' }}">
                            {{ $user->role === 'admin' ? 'Active' : 'Active' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure?')"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination">
            {{ $users->links('pagination.custom') }}
        </div>
        <div class="pagination-info">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
        </div>
</div>


@endsection
