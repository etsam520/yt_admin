<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpWord\Writer\Word2007\Part\Rels;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();
        // Filter by role (e.g., /api/admin/users?role=teacher)
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        // Search by name, email, or phone (e.g., /api/admin/users?search=John)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm);
            });
        }
        // Example: /api/admin/users?sort_by=name&sort_order=asc
        $sortBy = $request->input('sort_by', 'created_at'); // Default sort by created_at
        $sortOrder = $request->input('sort_order', 'desc'); // Default sort order desc
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->input('per_page', 15); // Allow frontend to specify items per page
        $users = $query->paginate($perPage); // This returns a LengthAwarePaginator instance
        return UserResource::collection($users)->response()->setStatusCode(200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|digits:10',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:teacher,student',
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/all'), $imageName);
            // Save relative path or just filename
            $validated['image'] = 'uploads/all/' . $imageName;
        }
        $validated['password'] = bcrypt($validated['password']);
        $user = \App\Models\User::create($validated);
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['nullable', 'digits:10'],
                'role' => ['required', 'in:teacher,student,admin'], // Adjust roles if needed
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    // This rule ensures the email is unique, but ignores the current user's email
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            ];
            $validatedData = $request->validate($rules);
            if ($request->hasFile('image')) {
                $newImage = $request->file('image');
                $newImageName = time() . '.' . $newImage->getClientOriginalExtension();
                $destinationPath = public_path('uploads/all');

                if ($user->image && File::exists(public_path($user->image))) {
                    File::delete(public_path($user->image));
                }
                $newImage->move($destinationPath, $newImageName);
                $validatedData['image'] = 'uploads/all/' . $newImageName; // Save new relative path
            }

            if (isset($validatedData['password']) && !empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
                unset($validatedData['password_confirmation']); // Also remove confirmation
            }

            $user->update($validatedData);

            return response()->json([
                'message' => 'User updated successfully!',
                'user' => $user->fresh() // Get the latest data from DB including any updated image path
            ], 200);
        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Validation Failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage(), ['exception' => $e, 'user_id' => $user->id ?? 'N/A']);
            return response()->json([
                'message' => 'Failed to update user. Please try again later.',
                'error' => $e->getMessage() // Include error message for debugging (remove in production)
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response()->json([
            'message' => 'User deleted successfully!'
        ]);
    }
}
