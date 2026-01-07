@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.tests.index') }}" class="breadcrumb-item">Online Tests</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Bulk Upload Questions</span>
    </nav>

    <h2>Bulk Upload Questions for {{ $test->test_name }}</h2>
    <div class="dashboard-actions">
        <a href="{{ route('admin.tests.index') }}" class="btn btn-primary">Back to Tests</a>
        <!-- <a href="{{ asset('templates/questions-template.xlsx') }}" class="btn btn-primary">Download Excel Template</a> -->
        <a href="{{ asset('templates/questions-template.docx') }}" class="btn btn-primary">Download Word Template</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form style="margin-top: 15px;" action="{{ route('admin.tests.bulk-import-questions', $test) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="file">Upload Questions File <span class="required">*</span></label>
            <input type="file" name="file" id="file" class="form-control" accept=".doc,.docx" required>
            <!-- <small>Supported formats: Excel (.xlsx, .xls), Word (.doc, .docx).<br>
                <strong>For Excel:</strong> Use the template with headers: question_text, question_text_hindi, option_a, option_a_hindi, ..., correct_option, solution, solution_hindi. Any input (numbers, text, or mixed) in option fields will be converted to a string.<br>
                <strong>For Word:</strong> Use this format:<br>
                Question 1: [Text] (English)<br>
                Question 1: [Text] (Hindi)<br>
                Option A: [Text] (English)<br>
                Option A: [Text] (Hindi)<br>
                ...<br>
                Correct Option: [a/b/c/d]<br>
                Solution: [Text] (English)<br>
                Solution: [Text] (Hindi)<br>
                Separate questions with a blank line.</small> -->
        </div>
        <button style="width: 200px;" type="submit" class="btn btn-primary">Upload Questions</button>
    </form>
</div>
@endsection
