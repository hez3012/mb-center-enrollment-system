<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Helpers\PhilippinesGeo;

class ProfileController extends Controller
{
    public function edit()
    {
        $user      = User::findOrFail(Auth::id());
        $geo       = new PhilippinesGeo();
        $regions   = $geo->getRegions();
        $provinces = $geo->getProvinces($user->region ?? '');
        $cities    = $geo->getCities($user->province ?? '');

        return view('admin.profile', compact('regions','provinces','cities'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'first_name'       => 'required|string|min:2|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'last_name'        => 'required|string|min:2|max:100',
            'sex'              => 'required|in:male,female,prefer_not_to_say,others',
            'sex_specify'      => 'nullable|string|max:100',
            'birthdate'        => 'nullable|date',
            'contact_number_1' => ['required','regex:/^09\d{9}$/'],
            'contact_number_2' => ['nullable','regex:/^09\d{9}$/'],
            'region'           => 'required|string|max:100',
            'province'         => 'required|string|max:100',
            'city'             => 'required|string|max:100',
            'barangay'         => 'required|string|min:4|max:100',
            'house_unit_no'    => 'required|string|min:1|max:100',
            'street'           => 'required|string|min:4|max:100',
            'zip_code'         => ['required','regex:/^\d{4}$/'],
            'email'            => 'required|email|unique:users,email,'.$user->user_id.',user_id',
            'username'         => 'required|string|min:4|max:50|unique:users,username,'.$user->user_id.',user_id',
            'password'         => 'nullable|string|min:6|confirmed',
            'profile_picture'  => 'nullable|image|mimes:jpg,jpeg,png|max:51200',
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
            'sex'              => $request->sex,
            'sex_specify'      => $request->sex === 'others' ? $request->sex_specify : null,
            'birthdate'        => $request->birthdate,
            'contact_number_1' => $request->contact_number_1,
            'contact_number_2' => $request->contact_number_2,
            'region'           => $request->region,
            'province'         => $request->province,
            'city'             => $request->city,
            'barangay'         => $request->barangay,
            'house_unit_no'    => $request->house_unit_no,
            'street'           => $request->street,
            'zip_code'         => $request->zip_code,
            'email'            => $request->email,
            'username'         => $request->username,
            'profile_picture'  => $picturePath,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        Log::info('Profile updated', ['user_id' => $user->user_id]);

        return back()->with('success','Profile updated successfully.');
    }

    public function deactivate()
    {
        $user = User::findOrFail(Auth::id());
        $user->update(['is_active' => 0]);
        Auth::logout();
        return redirect()->route('login')
            ->with('success','Your account has been deactivated.');
    }
}