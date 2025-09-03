<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isTechnician()) {
            return redirect()->route('technician.dashboard');
        }

        // Customer dashboard
        $appointments = Appointment::where('customer_email', $user->email)
            ->with(['serviceType', 'technician.user'])
            ->orderBy('scheduled_at', 'desc')
            ->get();

        $totalAppointments = $appointments->count();
        $pendingAppointments = $appointments->where('status', 'pending')->count();
        $confirmedAppointments = $appointments->where('status', 'confirmed')->count();
        $completedAppointments = $appointments->where('status', 'completed')->count();

        $recentAppointments = $appointments->take(10);

        return view('customer.dashboard', compact(
            'totalAppointments',
            'pendingAppointments',
            'confirmedAppointments',
            'completedAppointments',
            'recentAppointments'
        ));
    }
}
