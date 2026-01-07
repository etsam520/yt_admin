@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Subjects</span>
    </nav>
    <div class="table-controls">
        <div class="search-container">
            <input type="text" id="user-search" class="form-control" placeholder="Search Subject..." onkeyup="filterTable()">
        </div>
        <div class="dashboard-actions">
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">Add Subject</a>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="user-table" id="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Trade</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subjects as $subject)
                <tr>
                    <td>{{ $subject->id }}</td>
                    <td>{{ $subject->name }}</td>
                    <td>{{ $subject->trade->name }}</td>
                    <td>
                        <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" style="display:inline;">
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
            {{ $subjects->links('pagination.custom') }}
        </div>
        <div class="pagination-info">
            Showing {{ $subjects->firstItem() }} to {{ $subjects->lastItem() }} of {{ $subjects->total() }} results
        </div>
</div>
@endsection
