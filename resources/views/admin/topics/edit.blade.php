@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.topics.index') }}" class="breadcrumb-item">Topics</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Edit</span>
    </nav>

    <form method="POST" action="{{ route('admin.topics.update', $topic) }}">
        @csrf
        @method('PUT')
        <div class="form-row">
        <div class="form-col">
            <label for="trade_group_id">Trade Group</label>
            <select name="trade_group_id" id="trade_group_id" class="form-control" required>
                <option value="">Select Trade Group</option>
                @foreach ($tradeGroups as $tradeGroup)
                    <option value="{{ $tradeGroup->id }}" {{ $topic->chapter->subject->trade->trade_group_id == $tradeGroup->id ? 'selected' : '' }}>{{ $tradeGroup->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-col">
            <label for="trade_id">Trade</label>
            <select name="trade_id" id="trade_id" class="form-control" required>
                <option value="">Select Trade</option>
                @foreach ($trades as $trade)
                    <option value="{{ $trade->id }}" {{ $topic->chapter->subject->trade_id == $trade->id ? 'selected' : '' }}>{{ $trade->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-col">
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" class="form-control" required>
                <option value="">Select Subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ $topic->chapter->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-col">
            <label for="chapter_id">Chapter</label>
            <select name="chapter_id" id="chapter_id" class="form-control" required>
                <option value="">Select Chapter</option>
                @foreach ($chapters as $chapter)
                    <option value="{{ $chapter->id }}" {{ $topic->chapter_id == $chapter->id ? 'selected' : '' }}>{{ $chapter->name }}</option>
                @endforeach
            </select>
        </div>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $topic->name }}" required>
        </div>
        <button style="width: 100px;" type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
document.getElementById('trade_group_id').addEventListener('change', function() {
    fetchTrades(this.value);
});
document.getElementById('trade_id').addEventListener('change', function() {
    fetchSubjects(this.value);
});
document.getElementById('subject_id').addEventListener('change', function() {
    fetchChapters(this.value);
});

function fetchTrades(tradeGroupId) {
    const tradeSelect = document.getElementById('trade_id');
    tradeSelect.innerHTML = '<option value="">Loading...</option>';
    tradeSelect.disabled = true;
    fetch(`/admin/fetch-trades?trade_group_id=${tradeGroupId}`)
        .then(response => response.json())
        .then(trades => {
            tradeSelect.innerHTML = '<option value="">Select Trade</option>';
            trades.forEach(trade => {
                tradeSelect.innerHTML += `<option value="${trade.id}">${trade.name}</option>`;
            });
            tradeSelect.disabled = false;
        });
}

function fetchSubjects(tradeId) {
    const subjectSelect = document.getElementById('subject_id');
    subjectSelect.innerHTML = '<option value="">Loading...</option>';
    subjectSelect.disabled = true;
    fetch(`/admin/fetch-subjects?trade_id=${tradeId}`)
        .then(response => response.json())
        .then(subjects => {
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            subjects.forEach(subject => {
                subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
            });
            subjectSelect.disabled = false;
        });
}

function fetchChapters(subjectId) {
    const chapterSelect = document.getElementById('chapter_id');
    chapterSelect.innerHTML = '<option value="">Loading...</option>';
    chapterSelect.disabled = true;
    fetch(`/admin/fetch-chapters?subject_id=${subjectId}`)
        .then(response => response.json())
        .then(chapters => {
            chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
            chapters.forEach(chapter => {
                chapterSelect.innerHTML += `<option value="${chapter.id}">${chapter.name}</option>`;
            });
            chapterSelect.disabled = false;
        });
}
</script>
@endsection
