<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\CustomerProfile; // sesuaikan nama model profile-mu
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SocialAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth consent screen.
     */
    public function redirectToGoogle()
    {
        // Untuk web flow (session-enabled) jangan pakai stateless()
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback from Google OAuth.
     */
    public function handleGoogleCallback()
    {
        try {
            // Gunakan session-based flow untuk web app biasa
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed: '.$e->getMessage());
            return redirect()->route('login')->withErrors('Gagal menerima data dari Google. Silakan coba lagi.');
        }

        // Ambil data penting dari provider
        $providerId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName() ?? $googleUser->getNickname() ?? 'No Name';
        $avatarUrl = $googleUser->getAvatar();

        DB::beginTransaction();
        try {
            // 1) Jika ada user dengan google_id -> langsung login
            $user = User::where('google_id', $providerId)->first();
            if ($user) {
                // update avatar (prefer profile) jika ada
                $this->updateAvatar($user, $avatarUrl);
                Auth::login($user, true);
                DB::commit();
                return $this->redirectAfterLogin($user);
            }

            // 2) Jika ada user dengan email yang sama -> attach google_id & login
            $userByEmail = $email ? User::where('email', $email)->first() : null;
            if ($userByEmail) {
                // attach google_id if not set
                if (empty($userByEmail->google_id)) {
                    $userByEmail->google_id = $providerId;
                }
                // mark verified if not yet
                if (is_null($userByEmail->email_verified_at)) {
                    $userByEmail->email_verified_at = now();
                }
                $userByEmail->save();

                $this->ensureProfileExists($userByEmail);
                $this->updateAvatar($userByEmail, $avatarUrl);

                Auth::login($userByEmail, true);
                DB::commit();
                return $this->redirectAfterLogin($userByEmail);
            }

            // 3) Tidak ada user -> create baru + create profile fallback
            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(24)), // random password
                'google_id' => $providerId,
                'email_verified_at' => now(),
                // 'role' => 'customer', // opsional set default role jika ada kolom role
            ]);

            // Pastikan CustomerProfile dibuat (fallback, walau ada observer)
            $this->ensureProfileExists($newUser);

            // Simpan avatar ke profile (atau users.avatar sebagai fallback)
            $this->updateAvatar($newUser, $avatarUrl);

            Auth::login($newUser, true);
            DB::commit();

            return $this->redirectAfterLogin($newUser);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Social login/create error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->withErrors('Gagal login dengan Google. Silakan coba lagi.');
        }
    }

    /**
     * Ensure CustomerProfile (or equivalent) exists for the user.
     * If model not present or table missing, this method will silently ignore.
     */
    protected function ensureProfileExists(User $user): void
    {
        try {
            // Pastikan model CustomerProfile ada (sesuaikan namespace jika berbeda)
            if (class_exists(CustomerProfile::class)) {
                // reload relation if possible
                if (! $user->relationLoaded('customerProfile')) {
                    // assume relation name 'profile' exists: user->profile()
                    $user->loadMissing('customerProfile');
                }

                if (! $user->customerProfile) {
                    CustomerProfile::create([
                        'user_id' => $user->id,
                        // tambahkan kolom default profile jika diperlukan
                    ]);
                    // reload relation
                    $user->load('customerProfile');
                }
            }
        } catch (\Throwable $e) {
            // Jangan gagalkan seluruh flow jika profile creation gagal.
            Log::warning('ensureProfileExists failed: '.$e->getMessage());
        }
    }

    /**
     * Update avatar for user. Prefer saving to profile->avatar_path if profile exists,
     * otherwise save to users.avatar (if column exists).
     *
     * $existingPath param for storeAvatarFromUrl is handled inside updateAvatar.
     */
    protected function updateAvatar(User $user, ?string $avatarUrl): void
    {
        if (! $avatarUrl) {
            return;
        }

        try {
            // if profile exists and has avatar_path column, use it
            if ($user->relationLoaded('customerProfile') || method_exists($user, 'customerProfile')) {
                $profile = $user->customerProfile ?? null;
                if ($profile) {
                    $existing = $profile->avatar_path ?? null;
                    $path = $this->storeAvatarFromUrl($avatarUrl, $existing);
                    if ($path) {
                        // simpan ke profile
                        $profile->avatar_path = $path;
                        $profile->save();
                        return;
                    }
                }
            }

            // fallback: save to users.avatar if column exists
            if (SchemaHasColumn('users', 'avatar')) {
                $existing = $user->avatar ?? null;
                $path = $this->storeAvatarFromUrl($avatarUrl, $existing);
                if ($path) {
                    $user->avatar = $path;
                    $user->save();
                }
            }
        } catch (\Throwable $e) {
            Log::warning('updateAvatar failed: '.$e->getMessage());
        }
    }
    protected function redirectAfterLogin(User $user)
    {
        // SESUAIKAN dengan nama route kamu
        if ($user->role === 'seller') {
            return redirect()->route('seller.dashboard');    // contoh route
        }

        // default: customer
        return redirect()->route('customer.home');           // contoh route
    }


    /**
     * Download avatar from URL and store on public disk.
     * If $existingPath provided, delete it first.
     * Returns stored relative path (e.g. avatars/abc.jpg) or null.
     */
    protected function storeAvatarFromUrl(?string $url, ?string $existingPath = null): ?string
    {
        if (! $url) {
            return null;
        }

        // try/catch to be safe
        try {
            // determine extension from URL path, fallback jpg
            $ext = 'jpg';
            $parsed = pathinfo(parse_url($url, PHP_URL_PATH) ?: '');
            if (! empty($parsed['extension'])) {
                $ext = $parsed['extension'];
                // sanitize ext
                $ext = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'jpg';
            }

            // delete existing avatar if any
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }

            $filename = 'avatars/' . uniqid('g_') . '.' . $ext;

            // use @file_get_contents for simplicity; in production consider Guzzle
            $contents = @file_get_contents($url);
            if ($contents === false) {
                return null;
            }

            Storage::disk('public')->put($filename, $contents);

            return $filename;
        } catch (\Throwable $e) {
            Log::warning('storeAvatarFromUrl error: '.$e->getMessage());
            return null;
        }
    }
}

/**
 * Small helper function to check schema column existence.
 * Put this in a global helper file if you prefer; included here for completeness.
 */
if (! function_exists('SchemaHasColumn')) {
    function SchemaHasColumn(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            // if Schema facade not available or DB not configured, assume false
            \Illuminate\Support\Facades\Log::warning("SchemaHasColumn error: {$e->getMessage()}");
            return false;
        }
    }
}
