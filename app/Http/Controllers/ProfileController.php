<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class ProfileController extends Controller
{

    public function edit(Request $request): View
    {
        $user = Auth::user();
        $cprofile =$user->customerProfile;
        $profile = CustomerProfile::where('user_id', $user->id)->first();
        $pfp = $user->avatar_path;
        return view('customer.profile', [
            'user' => $user,
            'pfp' => $pfp,
            'profile' => $profile,
            'cprofile' => $cprofile,
        ]);
    }



    public function updateProfile(Request $request)
    {

        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'   => ['nullable', 'regex:/^\+?[0-9]{9,15}$/'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            return back()
                ->withErrors($validator, 'updateProfile')
                ->withInput();
        }

        $data = $validator->validated();

        DB::transaction(function () use ($user, $request, $data) {

            $user->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);

            $profileData = [
                'address' => $data['address'] ?? $user->customerProfile?->address,
            ];

            if ($request->filled('phone')) {
                $profileData['phone'] = $data['phone'];
            }

            $user->customerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        });


        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('success', 'profile-updated');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'updatePassword')->withInput();
        }


        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'], 'updatePassword')
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);


        return back()->with('success', 'password-updated');
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $user = $request->user();
        $cprofile = $user->customerProfile;
        $validator = Validator::make($request->all(), [
            'address' => ['required','string','max:255'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'updateAddress')->withInput();
        }

        $cprofile->update(['address' => $validator->validated()['address']]);

        return back()->with('status', 'address-updated');
    }
    


    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
