@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.tests.index') }}" class="breadcrumb-item">Online Tests</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">View Test Details</span>
    </nav>
    <h2>Test Details: {{ $test->test_name }}</h2>

    <div class="form-section">
        <h3>Test Details</h3>
        <div class="form-row">
            <div class="form-col">
                <strong>Test Name:</strong> {{ $test->test_name }}
            </div>
            <div class="form-col">
                <strong>Series Name:</strong> {{ $test->series_name ?? 'N/A' }}
            </div>
            <div class="form-col">
                <strong>Type:</strong> {{ ucfirst($test->type) }}
            </div>
        </div>
        <div class="form-group">
            <strong>Test Instructions:</strong> {{ $test->test_instructions ?? 'N/A' }}
        </div>
    </div>

    <div class="form-section">
        <h3>Test Settings</h3>
        <div class="form-row">
            <div class="form-col">
                <strong>Number of Questions:</strong> {{ $test->no_of_questions }}
            </div>
            <div class="form-col">
                <strong>Total Marks:</strong> {{ $test->total_marks }}
            </div>
            <div class="form-col">
                <strong>Duration (Minutes):</strong> {{ $test->total_duration_minutes }}
            </div>
            <div class="form-col">
                <strong>Maximum Attempts:</strong> {{ $test->maximum_attempts }}
            </div>
        </div>
        <div class="form-row">
            <div class="form-col">
                <strong>Shuffle Questions:</strong> {{ $test->is_shuffle_questions ? 'Yes' : 'No' }}
            </div>
            <div class="form-col">
                <strong>Display Pause Option:</strong> {{ $test->is_display_pause_option ? 'Yes' : 'No' }}
            </div>
            <div class="form-col">
                <strong>Display Rank:</strong> {{ $test->is_display_rank ? 'Yes' : 'No' }}
            </div>
            <div class="form-col">
                <strong>Show Total Students:</strong> {{ $test->is_show_total_students ? 'Yes' : 'No' }}
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Solutions</h3>
        <div class="form-row">
            <div class="form-col">
                <strong>Solution PDF:</strong> {{ $test->solution_pdf ? basename($test->solution_pdf) : 'None' }}
            </div>
            <div class="form-col">
                <strong>Solution Video:</strong> {{ $test->solution_video ? basename($test->solution_video) : 'None' }}
            </div>
        </div>
        <div class="form-row">
            <div class="form-col">
                <strong>Show Solution PDF:</strong> {{ $test->is_show_solution_pdf ? 'Yes' : 'No' }}
            </div>
            <div class="form-col">
                <strong>Show Solution Video:</strong> {{ $test->is_show_solution_video ? 'Yes' : 'No' }}
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Schedule</h3>
        <div class="form-row">
            <div class="form-col">
                <strong>Start Date:</strong> {{ $test->start_date->format('Y-m-d H:i') }}
            </div>
            <div class="form-col">
                <strong>End Date:</strong> {{ $test->end_date->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Materials</h3>
        <div class="form-group">
            <strong>Test Material PDF:</strong> {{ $test->test_material_pdf ? basename($test->test_material_pdf) : 'None' }}
        </div>
        <div class="form-group">
            <strong>Allow Download Material:</strong> {{ $test->is_allow_download_material ? 'Yes' : 'No' }}
        </div>
    </div>

    <div class="form-section">
        <h3>Publish</h3>
        <div class="form-group">
            <strong>Published:</strong> {{ $test->is_published ? 'Yes' : 'No' }}
        </div>
    </div>

    <div class="form-group">
        <a href="{{ route('admin.tests.index') }}" class="btn btn-primary">Back to Tests</a>
    </div>
</div>
@endsection
