<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                return redirect('/')->with('error', 'Unauthorized access. Admins only.');
            }
            return $next($request);
        }]);
    }

    public function index()
    {
        return view('admin.home');
    }
    public function profile()
    {
        return view('admin.profile', ['user' => Auth::user()]);
    }
}
