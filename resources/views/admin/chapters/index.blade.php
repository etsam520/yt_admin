@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Chapters</span>
    </nav>
    <div class="table-controls">
        <div class="search-container">
            <input type="text" id="user-search" class="form-control" placeholder="Search Chapters..." onkeyup="filterTable()">
        </div>
        <div class="dashboard-actions">
        <a href="{{ route('admin.chapters.create') }}" class="btn btn-primary">Add Chapter</a>
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
                <th>Subject</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chapters as $chapter)
                <tr>
                    <td>{{ $chapter->id }}</td>
                    <td>{{ $chapter->name }}</td>
                    <td>{{ $chapter->subject->name }}</td>
                    <td>
                        <a href="{{ route('admin.chapters.edit', $chapter) }}" class="btn btn-edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" style="display:inline;">
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
            {{ $chapters->links('pagination.custom') }}
        </div>
        <div class="pagination-info">
            Showing {{ $chapters->firstItem() }} to {{ $chapters->lastItem() }} of {{ $chapters->total() }} results
        </div>
</div>
@endsection
