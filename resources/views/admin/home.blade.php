@extends('layouts.admin')

@section('content')
<div class="dashboard">
    <h2>Admin Dashboard</h2>
    <div class="welcome-section">
        <p>Welcome, {{ Auth::user()->name }}!</p>
        <p>Role: {{ Auth::user()->role }}</p>
        <p>This is your admin control panel.</p>
    </div>
    <div class="dashboard-actions">
        <a href="{{ route('logout') }}" class="btn btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
@endsection
