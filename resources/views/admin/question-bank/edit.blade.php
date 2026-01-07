@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.question-bank.index') }}" class="breadcrumb-item">Question Bank</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Edit Question</span>
    </nav>

    <form method="POST" action="{{ route('admin.question-bank.update', $question) }}">
        @csrf
        @method('PUT')
        <div class="form-row">
        <div class="form-col">
        <label for="trade_group_id">Trade Group</label>
            <select name="trade_group_id" id="trade_group_id" class="form-control" required>
                <option value="">Select Trade Group</option>
                @foreach ($tradeGroups as $tradeGroup)
                    <option value="{{ $tradeGroup->id }}" {{ $question->trade_group_id == $tradeGroup->id ? 'selected' : '' }}>{{ $tradeGroup->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-col">
        <label for="trade_id">Trade</label>
            <select name="trade_id" id="trade_id" class="form-control" required>
                <option value="">Select Trade</option>
                @foreach ($trades as $trade)
                    <option value="{{ $trade->id }}" {{ $question->trade_id == $trade->id ? 'selected' : '' }}>{{ $trade->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-col">
        <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" class="form-control" required>
                <option value="">Select Subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ $question->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        </div>

        <div class="form-row">
        <div class="form-col">
        <label for="chapter_id">Chapter</label>
            <select name="chapter_id" id="chapter_id" class="form-control" required>
                <option value="">Select Chapter</option>
                @foreach ($chapters as $chapter)
                    <option value="{{ $chapter->id }}" {{ $question->chapter_id == $chapter->id ? 'selected' : '' }}>{{ $chapter->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-col">
        <label for="topic_id">Topic</label>
            <select name="topic_id" id="topic_id" class="form-control" required>
                <option value="">Select Topic</option>
                @foreach ($topics as $topic)
                    <option value="{{ $topic->id }}" {{ $question->topic_id == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                @endforeach
            </select>
        </div>
        </div>

        <div class="form-group">
            <label for="question_text">Question</label>
            <textarea name="question_text" id="question_text" class="form-control" required>{{ $question->question_text }}</textarea>
        </div>

        <div class="form-row">
        <div class="form-col">
            <label for="option_a">Option A</label>
            <input type="text" name="option_a" id="option_a" class="form-control" value="{{ $question->option_a }}" required>
       </div>
       <div class="form-col">
            <label for="option_b">Option B</label>
            <input type="text" name="option_b" id="option_b" class="form-control" value="{{ $question->option_b }}" required>
        </div>
        <div class="form-col">
            <label for="option_c">Option C</label>
            <input type="text" name="option_c" id="option_c" class="form-control" value="{{ $question->option_c }}" required>
        </div>
        <div class="form-col">
            <label for="option_d">Option D</label>
            <input type="text" name="option_d" id="option_d" class="form-control" value="{{ $question->option_d }}" required>
        </div>
        <div class="form-col">
            <label for="correct_option">Correct Option</label>
            <select name="correct_option" id="correct_option" class="form-control" required>
                <option value="a" {{ $question->correct_option == 'a' ? 'selected' : '' }}>A</option>
                <option value="b" {{ $question->correct_option == 'b' ? 'selected' : '' }}>B</option>
                <option value="c" {{ $question->correct_option == 'c' ? 'selected' : '' }}>C</option>
                <option value="d" {{ $question->correct_option == 'd' ? 'selected' : '' }}>D</option>
            </select>
        </div>
        </div>

        <div class="form-group">
            <label for="solution">Solution (Optional)</label>
            <textarea name="solution" id="solution" class="form-control">{{ $question->solution }}</textarea>
        </div>
        <div class="form-group">
            <button style="width: 200px" type="submit" class="btn btn-primary">Update Question</button>
        </div>
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
document.getElementById('chapter_id').addEventListener('change', function() {
    fetchTopics(this.value);
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
                tradeSelect.innerHTML += `<option value="${trade.id}" ${trade.id == '{{ $question->trade_id }}' ? 'selected' : ''}>${trade.name}</option>`;
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
                subjectSelect.innerHTML += `<option value="${subject.id}" ${subject.id == '{{ $question->subject_id }}' ? 'selected' : ''}>${subject.name}</option>`;
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
                chapterSelect.innerHTML += `<option value="${chapter.id}" ${chapter.id == '{{ $question->chapter_id }}' ? 'selected' : ''}>${chapter.name}</option>`;
            });
            chapterSelect.disabled = false;
        });
}

function fetchTopics(chapterId) {
    const topicSelect = document.getElementById('topic_id');
    topicSelect.innerHTML = '<option value="">Loading...</option>';
    topicSelect.disabled = true;
    fetch(`/admin/fetch-topics?chapter_id=${chapterId}`)
        .then(response => response.json())
        .then(topics => {
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
            topics.forEach(topic => {
                topicSelect.innerHTML += `<option value="${topic.id}" ${topic.id == '{{ $question->topic_id }}' ? 'selected' : ''}>${topic.name}</option>`;
            });
            topicSelect.disabled = false;
        });
}
</script>
@endsection
