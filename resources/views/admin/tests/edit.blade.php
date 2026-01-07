@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.tests.index') }}" class="breadcrumb-item">Online Tests</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Edit Test</span>
    </nav>
    <h2>Edit Test: {{ $test->test_name }}</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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

    <form method="POST" action="{{ route('admin.tests.update', $test) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-section">
            <h3>Test Details</h3>
            <div class="form-row">
                <div class="form-col">
                    <label for="test_name">Test Name <span class="required">*</span></label>
                    <input type="text" name="test_name" id="test_name" class="form-control" value="{{ old('test_name', $test->test_name) }}" required>
                    @error('test_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label for="series_name">Series Name</label>
                    <input type="text" name="series_name" id="series_name" class="form-control" value="{{ old('series_name', $test->series_name) }}">
                    @error('series_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label for="type">Type <span class="required">*</span></label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="free" {{ old('type', $test->type) == 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ old('type', $test->type) == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    @error('type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label for="test_instructions">Test Instructions</label>
                <textarea name="test_instructions" id="test_instructions" class="form-control" rows="3">{{ old('test_instructions', $test->test_instructions) }}</textarea>
                @error('test_instructions')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-section">
            <h3>Test Settings</h3>
            <div class="form-row">
                <div class="form-col">
                    <label for="no_of_questions">Number of Questions <span class="required">*</span></label>
                    <input type="number" name="no_of_questions" id="no_of_questions" class="form-control" min="1" value="{{ old('no_of_questions', $test->no_of_questions) }}" required>
                    @error('no_of_questions')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label for="total_marks">Total Marks <span class="required">*</span></label>
                    <input type="number" name="total_marks" id="total_marks" class="form-control" min="1" value="{{ old('total_marks', $test->total_marks) }}" required>
                    @error('total_marks')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label for="total_duration_minutes">Duration (Minutes) <span class="required">*</span></label>
                    <input type="number" name="total_duration_minutes" id="total_duration_minutes" class="form-control" min="1" value="{{ old('total_duration_minutes', $test->total_duration_minutes) }}" required>
                    @error('total_duration_minutes')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label for="maximum_attempts">Maximum Attempts <span class="required">*</span></label>
                    <input type="number" name="maximum_attempts" id="maximum_attempts" class="form-control" min="1" value="{{ old('maximum_attempts', $test->maximum_attempts) }}" required>
                    @error('maximum_attempts')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-row checkbox-group">
                <div class="form-col">
                    <label><input type="checkbox" name="is_shuffle_questions" value="1" {{ old('is_shuffle_questions', $test->is_shuffle_questions) ? 'checked' : '' }}> Shuffle Questions</label>
                    @error('is_shuffle_questions')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label><input type="checkbox" name="is_display_pause_option" value="1" {{ old('is_display_pause_option', $test->is_display_pause_option) ? 'checked' : '' }}> Display Pause Option</label>
                    @error('is_display_pause_option')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label><input type="checkbox" name="is_display_rank" value="1" {{ old('is_display_rank', $test->is_display_rank) ? 'checked' : '' }}> Display Rank</label>
                    @error('is_display_rank')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label><input type="checkbox" name="is_show_total_students" value="1" {{ old('is_show_total_students', $test->is_show_total_students) ? 'checked' : '' }}> Show Total Students</label>
                    @error('is_show_total_students')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Solutions</h3>
            <div class="form-row">
                <div class="form-col">
                    <label for="solution_pdf">Solution PDF</label>
                    <input type="file" name="solution_pdf" id="solution_pdf" class="form-control" accept=".pdf">
                    <small>Current: {{ $test->solution_pdf ? basename($test->solution_pdf) : 'None' }}</small>
                    @error('solution_pdf')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label for="solution_video">Solution Video</label>
                    <input type="file" name="solution_video" id="solution_video" class="form-control" accept=".mp4">
                    <small>Current: {{ $test->solution_video ? basename($test->solution_video) : 'None' }}</small>
                    @error('solution_video')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-row checkbox-group">
                <div class="form-col">
                    <label><input type="checkbox" name="is_show_solution_pdf" value="1" {{ old('is_show_solution_pdf', $test->is_show_solution_pdf) ? 'checked' : '' }}> Show Solution PDF</label>
                    @error('is_show_solution_pdf')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label><input type="checkbox" name="is_show_solution_video" value="1" {{ old('is_show_solution_video', $test->is_show_solution_video) ? 'checked' : '' }}> Show Solution Video</label>
                    @error('is_show_solution_video')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Schedule</h3>
            <div class="form-row">
                <div class="form-col">
                    <label for="start_date">Start Date <span class="required">*</span></label>
                    <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $test->start_date->format('Y-m-d\TH:i')) }}" required>
                    @error('start_date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-col">
                    <label for="end_date">End Date <span class="required">*</span></label>
                    <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $test->end_date->format('Y-m-d\TH:i')) }}" required>
                    @error('end_date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Materials</h3>
            <div class="form-group">
                <label for="test_material_pdf">Test Material PDF</label>
                <input type="file" name="test_material_pdf" id="test_material_pdf" class="form-control" accept=".pdf">
                <small>Current: {{ $test->test_material_pdf ? basename($test->test_material_pdf) : 'None' }}</small>
                @error('test_material_pdf')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_allow_download_material" value="1" {{ old('is_allow_download_material', $test->is_allow_download_material) ? 'checked' : '' }}> Allow Download Material</label>
                @error('is_allow_download_material')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-section">
            <h3>Publish</h3>
            <div class="form-group">
                <label><input type="checkbox" name="is_published" value="1" {{ old('is_published', $test->is_published) ? 'checked' : '' }}> Publish Test</label>
                @error('is_published')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary" style="width: 150px;">Update Test</button>
        </div>
    </form>
</div>
@endsection
