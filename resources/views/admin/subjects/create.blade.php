@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <nav class="breadcrumbs">
        <a href="{{ route('admin.home') }}" class="breadcrumb-item"><i class="fas fa-house"></i> Admin</a>
        <span class="breadcrumb-separator">></span>
        <a href="{{ route('admin.subjects.index') }}" class="breadcrumb-item">Subjects</a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-item active">Add</span>
    </nav>

    <form method="POST" action="{{ route('admin.subjects.store') }}">
        @csrf
        <div class="form-row">

<div class="form-col">
            <label for="trade_group_id">Trade Group</label>
            <select name="trade_group_id" id="trade_group_id" class="form-control" required>
                <option value="">Select Trade Group</option>
                @foreach ($tradeGroups as $tradeGroup)
                    <option value="{{ $tradeGroup->id }}">{{ $tradeGroup->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-col">
            <label for="trade_id">Trade</label>
            <select name="trade_id" id="trade_id" class="form-control" required disabled>
                <option value="">Select Trade</option>
            </select>
        </div>
        <div class="form-col">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        </div>

        <button style="width: 100px;" type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<script>
document.getElementById('trade_group_id').addEventListener('change', function() {
    fetchTrades(this.value);
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
</script>
@endsection
