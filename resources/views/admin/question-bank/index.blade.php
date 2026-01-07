@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Question Bank</span>
    </nav>
    <div class="table-controls">
        <div class="search-container">
            <input type="text" id="user-search" class="form-control" placeholder="Search Question..." onkeyup="filterTable()">
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('admin.question-bank.create') }}" class="btn btn-primary">Add Question</a>

            <a href="{{ route('admin.question-bank.bulk-upload') }}" class="btn btn-primary">Bulk Uploads</a>

        </div>

    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="user-table" id="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Trade Group</th>
                <th>Trade</th>
                <th>Subject</th>
                <th>Chapter</th>
                <th>Topic</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($questions as $question)
                <tr>
                    <td>{{ $question->id }}</td>
                    <td>{{ Str::limit($question->question_text, 50) }}</td>
                    <td>{{ $question->tradeGroup->name }}</td>
                    <td>{{ $question->trade->name }}</td>
                    <td>{{ $question->subject->name }}</td>
                    <td>{{ $question->chapter->name }}</td>
                    <td>{{ $question->topic->name }}</td>
                    <td>
                        <a href="{{ route('admin.question-bank.edit', $question) }}" class="btn btn-edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.question-bank.destroy', $question) }}" method="POST" style="display:inline;">
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
            {{ $questions->links('pagination.custom') }}
        </div>
        <div class="pagination-info">
            Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} results
        </div>
</div>
@endsection
