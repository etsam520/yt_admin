<?php

namespace App\Http\Resources\admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone, // Include phone if you want it in the list
            'created_at' => $this->created_at->format('Y-m-d H:i:s'), // Format dates
            // 'updated_at' => $this->updated_at->format('Y-m-d H:i:s'), // Optional: include update timestamp

            'image_url' => $this->when(
                $this->image,
                function () {
                    if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                        return $this->image; // Return it as is if it's already a URL
                    }
                    // return Storage::url($this->image);
                    return asset($this->image);
                },
                asset('images/default_avatar.png')
            ),

            'display_role' => ucfirst($this->role), // Capitalize the role for display

            // Example of conditionally merging more data (e.g., for admins only)
            // This 'mergeWhen' block will only be included if the current authenticated user is an admin.
            // You'd need to define an `isAdmin()` method on your User model for this to work.
            // $this->mergeWhen(
            //     $request->user() && $request->user()->isAdmin(), // Example condition
            //     [
            //         'internal_notes' => $this->internal_notes, // Assuming this field exists
            //         'last_login_ip' => $this->last_login_ip, // Assuming this field exists
            //     ]
            // ),
        ];
    }
}
