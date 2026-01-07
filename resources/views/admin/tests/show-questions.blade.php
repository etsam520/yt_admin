@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.tests.index') }}" class="breadcrumb-item">Online Tests</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Questions for {{ $test->test_name }}</span>
    </nav>

    <h2>Questions for {{ $test->test_name }}</h2>
    <div class="dashboard-actions">
        <a href="{{ route('admin.tests.bulk-upload-questions', $test) }}" class="btn btn-primary">Upload More Questions</a>
        <a href="{{ route('admin.tests.index') }}" class="btn btn-primary">Back to Tests</a>
        <!-- Language Switcher -->
        <form method="POST" action="{{ route('admin.tests.switch-language', $test) }}" style="display:inline;">
            @csrf
            <select name="language" onchange="this.form.submit()">
                <option value="en" {{ session('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                <option value="hi" {{ session('language', 'en') == 'hi' ? 'selected' : '' }}>Hindi</option>
            </select>
        </form>
    </div>

    @if ($questions->isEmpty())
        <p>No questions have been added to this test yet.</p>
    @else
        <table class="user-table" id="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Option A</th>
                    <th>Option B</th>
                    <th>Option C</th>
                    <th>Option D</th>
                    <th>Correct Option</th>
                    <th>Solution</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questions as $question)
                    <tr>
                        <td>{{ $question->id }}</td>
                        <td>{{ session('language', 'en') == 'hi' ? ($question->question_text_hindi ?? $question->question_text) : $question->question_text }}</td>
                        <td>{{ session('language', 'en') == 'hi' ? ($question->option_a_hindi ?? $question->option_a) : $question->option_a }}</td>
                        <td>{{ session('language', 'en') == 'hi' ? ($question->option_b_hindi ?? $question->option_b) : $question->option_b }}</td>
                        <td>{{ session('language', 'en') == 'hi' ? ($question->option_c_hindi ?? $question->option_c) : $question->option_c }}</td>
                        <td>{{ session('language', 'en') == 'hi' ? ($question->option_d_hindi ?? $question->option_d) : $question->option_d }}</td>
                        <td>{{ strtoupper($question->correct_option) }}</td>
                        <td>{{ session('language', 'en') == 'hi' ? ($question->solution_hindi ?? ($question->solution ?? 'N/A')) : ($question->solution ?? 'N/A') }}</td>
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
    @endif
</div>
@endsection
