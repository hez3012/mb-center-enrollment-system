<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('portal.profile');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'first_name'       => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|max:100',
            'contact_number_1' => 'required|string|max:20',
            'contact_number_2' => 'nullable|string|max:20',
            'email'            => 'required|email|unique:users,email,'.$user->user_id.',user_id',
            'username'         => 'required|string|min:4|max:50|unique:users,username,'.$user->user_id.',user_id',
            'password'         => 'nullable|string|min:8|confirmed',
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $picturePath = $user->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($picturePath) Storage::disk('public')->delete($picturePath);
            $picturePath = $request->file('profile_picture')
                ->store('profile_pictures/users','public');
        }

        $data = [
            'first_name'       => $request->first_name,
            'middle_name'      => $request->middle_name,
            'last_name'        => $request->last_name,
            'contact_number_1' => $request->contact_number_1,
            'contact_number_2' => $request->contact_number_2,
            'email'            => $request->email,
            'username'         => $request->username,
            'profile_picture'  => $picturePath,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success','Profile updated successfully.');
    }
}