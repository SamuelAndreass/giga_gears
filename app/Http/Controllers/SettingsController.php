<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOwnerRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\CustomerProfile;
use App\Models\SellerStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Events\Registered;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cusT = CustomerProfile::where('user_id', $user->id)->first();
        $store = $user->sellerStore;

        if (! $store) abort(404, 'Store tidak ditemukan.');

        return view('seller.store-setting', [
            'user' => $user,
            'store' => $store,
            'cusT' => $cusT,
        ]);
    }

    public function updateOwner(UpdateOwnerRequest $request)
    {
        $user  = Auth::user();
        $custT = CustomerProfile::where('user_id', $user->id)->first();

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $emailChanged = (
                isset($validated['owner_email']) &&
                $validated['owner_email'] !== $user->email
            );

            $user->name  = $validated['owner_name']  ?? $user->name;
            $user->email = $validated['owner_email'] ?? $user->email;

            if ($request->hasFile('owner_photo')) {
                if ($custT && $custT->avatar_path && Storage::disk('public')->exists($custT->avatar_path)) {
                    Storage::disk('public')->delete($custT->avatar_path);
                }

                $path = $request->file('owner_photo')->store('seller_photos', 'public');

                if (!$custT) {
                    $custT = new CustomerProfile();
                    $custT->user_id = $user->id;
                }

                $custT->avatar_path = $path;
            }

            if ($emailChanged) {
                $user->email_verified_at = null;
            }

            $user->save(); 
            if ($custT) {
                $custT->save();    
            }

            DB::commit();

            $msg = 'Informasi pemilik berhasil diperbarui.' . ($emailChanged ? ' Mohon cek email untuk verifikasi ulang.' : '');
            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update store (toko) fields: name, address, avatar.
     * Avatar uploaded to storage/app/public/store_avatars
     */
    public function updateStore(UpdateStoreRequest $request)
    {
            $user = Auth::user();
            $store = $user->sellerStore;

            if (! $store) {
                return back()->withErrors(['store' => 'Store tidak ditemukan.']);
            }

            $validated = $request->validated();

            DB::beginTransaction();
            try {
                $storeData = [];
                if (array_key_exists('store_name', $validated)) {
                    $storeData['store_name'] = $validated['store_name'];
                }
                if (array_key_exists('store_address', $validated)) {
                    $storeData['store_address'] = $validated['store_address'];
                }
                if (array_key_exists('store_phone', $validated)) {
                    $storeData['store_phone'] = $validated['store_phone'];
                }

                if ($request->hasFile('store_logo')) {
                    if ($store->store_logo && Storage::disk('public')->exists($store->store_logo)) {
                        Storage::disk('public')->delete($store->store_logo);
                    }
                    $path = $request->file('store_logo')->store('store_logos', 'public');
                    $storeData['store_logo'] = $path;
                }

                $store->update($storeData);

                DB::commit();

                return back()->with('success', 'Detail toko berhasil diperbarui.');
            } catch (\Throwable $e) {
                DB::rollBack();
                \Log::error('UpdateStore error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return back()->withErrors(['error' => 'Gagal memperbarui data toko. Silakan coba lagi.']);
            }
    }

    /**
     * Change password for current user
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak cocok.'])
                ->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function destroyStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'confirm_delete' => ['required', 'in:DELETE'],
        ], [
            'confirm_delete.in' => 'Anda harus mengetik "DELETE" untuk mengkonfirmasi penghapusan store.',
        ]);

        $store = SellerStore::where('user_id', $user->id)->first();

        if (! $store) {
            return back()->withErrors(['store' => 'Store tidak ditemukan.']);
        }

        DB::beginTransaction();
        try {
            if ($store->avatar_path && Storage::disk('public')->exists($store->avatar_path)) {
                Storage::disk('public')->delete($store->avatar_path);
                $store->avatar_path = null;
            }

            $store->status = 'inactive'; 
            $store->products()->update(['status' => 'inactive']);
            $user->update(['is_seller' => false]);
            $store->save();

            DB::commit();

            return redirect()
                ->route('dashboard')
                ->with('success', 'Store berhasil dinonaktifkan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors([
                'store' => 'Gagal menonaktifkan store. Silakan coba lagi.',
            ]);
        }
    }
}
