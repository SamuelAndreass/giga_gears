<?php

namespace App\Http\Controllers;

use App\Models\Seminar;
use App\Models\SeminarRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeminarController extends Controller
{
    public function index()
    {
        $seminars = Seminar::where('status', '!=', 'cancelled')->orderBy('start_date', 'asc')->get();
        return view('seminar.index', compact('seminars'));
    }

    public function show($id)
    {
        $seminar = Seminar::findOrFail($id);
        $isRegistered = false;
        $registration = null;
        if (Auth::check()) {
            $registration = SeminarRegistration::where('user_id', Auth::id())
                ->where('seminar_id', $id)->first();
            $isRegistered = $registration ? true : false;
        }
        return view('seminar.show', compact('seminar', 'isRegistered', 'registration'));
    }

    public function register(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $seminar = Seminar::findOrFail($id);
        
        $existing = SeminarRegistration::where('user_id', Auth::id())
            ->where('seminar_id', $id)->first();
        
        if ($existing) {
            return back()->with('error', 'Anda sudah terdaftar untuk seminar ini');
        }

        if ($seminar->registered_count >= $seminar->capacity) {
            return back()->with('error', 'Seminar sudah penuh');
        }

        $nextSeatNumber = SeminarRegistration::where('seminar_id', $id)->max('seat_number') ?? 0;
        $nextSeatNumber += 1;

        $registration = SeminarRegistration::create([
            'user_id' => Auth::id(),
            'seminar_id' => $id,
            'status' => 'registered',
            'seat_number' => $nextSeatNumber,
        ]);

        return redirect()->route('seminar.show', $id)->with('success', 'success')->with('seat_number', $nextSeatNumber);
    }

    public function unregister($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        SeminarRegistration::where('user_id', Auth::id())
            ->where('seminar_id', $id)->delete();

        return back()->with('success', 'Anda berhasil membatalkan registrasi');
    }

    public function myRegistrations()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $registrations = SeminarRegistration::where('user_id', Auth::id())
            ->with('seminar')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('seminar.my-registrations', compact('registrations'));
    }
}
