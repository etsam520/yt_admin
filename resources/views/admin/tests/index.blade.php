@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Online Tests</span>
    </nav>
    <div class="table-controls">
        <div class="search-container">
            <input type="text" id="user-search" class="form-control" placeholder="Search Test..." onkeyup="filterTable()">
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('admin.tests.create') }}" class="btn btn-primary">Add New Test</a>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="user-table" id="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Test Name</th>
                <th>Series</th>
                <th>Type</th>
                <th>Questions</th>
                <th>Total Marks</th>
                <th>Duration (min)</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Published</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tests as $test)
                <tr>
                    <td>{{ $test->id }}</td>
                    <td>{{ $test->test_name }}</td>
                    <td>{{ $test->series_name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($test->type) }}</td>
                    <td>{{ $test->no_of_questions }}</td>
                    <td>{{ $test->total_marks }}</td>
                    <td>{{ $test->total_duration_minutes }}</td>
                    <td>{{ $test->start_date->format('Y-m-d H:i') }}</td>
                    <td>{{ $test->end_date->format('Y-m-d H:i') }}</td>
                    <td>{{ $test->is_published ? 'Yes' : 'No' }}</td>
                    <td>
                        <div class="dropdown" data-dropdown-id="{{ $test->id }}">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="actionsDropdown_{{ $test->id }}" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>
                            <div style="text-align: start;white-space: inherit;" class="dropdown-menu" id="dropdownMenu_{{ $test->id }}">
                                <a class="dropdown-item" href="{{ route('admin.tests.show-questions', $test) }}">View Questions</a>
                                <a class="dropdown-item" href="{{ route('admin.tests.bulk-upload-questions', $test) }}">Upload Questions</a>
                                <a class="dropdown-item" href="{{ route('admin.tests.edit', $test) }}">Edit Test Details</a>
                                <a class="dropdown-item" href="{{ route('admin.tests.view-details', $test) }}">View Test Details</a>
                                <form action="{{ route('admin.tests.toggle-publish', $test) }}" method="POST" style="display:inline; margin: 0;" class="dropdown-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item btn {{ $test->is_published ? 'btn-delete' : 'btn-primary' }}">
                                        {{ $test->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.tests.destroy', $test) }}" method="POST" style="display:inline; margin: 0;" class="dropdown-form" onsubmit="return confirm('Are you sure you want to delete this test?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item btn btn-delete">Delete Test</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination">
        {{ $tests->links('pagination.custom') }}
    </div>
    <div class="pagination-info">
        Showing {{ $tests->firstItem() }} to {{ $tests->lastItem() }} of {{ $tests->total() }} results
    </div>
</div>
<script>
function filterTable() {
    const input = document.getElementById('user-search').value.toLowerCase();
    const table = document.getElementById('user-table');
    const rows = table.getElementsByTagName('tr');
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let match = false;
        for (let j = 0; j < cells.length - 1; j++) {
            if (cells[j].textContent.toLowerCase().includes(input)) {
                match = true;
                break;
            }
        }
        rows[i].style.display = match ? '' : 'none';
    }
}

// Dropdown Toggle Logic
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const id = dropdown.getAttribute('data-dropdown-id');
        const button = dropdown.querySelector(`#actionsDropdown_${id}`);
        const menu = dropdown.querySelector(`#dropdownMenu_${id}`);

        if (button && menu) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const isOpen = menu.style.display === 'block';
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
                menu.style.display = isOpen ? 'none' : 'block';
                button.setAttribute('aria-expanded', !isOpen);
            });
        } else {
            console.error(`Dropdown elements not found for id: ${id}`);
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        dropdowns.forEach(dropdown => {
            const id = dropdown.getAttribute('data-dropdown-id');
            const button = dropdown.querySelector(`#actionsDropdown_${id}`);
            const menu = dropdown.querySelector(`#dropdownMenu_${id}`);
            if (button && menu && !dropdown.contains(e.target)) {
                menu.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
            }
        });
    });
});
</script>
@endsection
