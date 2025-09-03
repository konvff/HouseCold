<?php

namespace App\Http\Controllers;

use App\Models\TechnicianAvailability;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TechnicianAvailabilityController extends Controller
{


    public function index()
    {
        $availabilities = TechnicianAvailability::with('technician.user')
            ->orderBy('technician_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $technicians = Technician::with('user')->get();

        return view('technician-availabilities.index', compact('availabilities', 'technicians'));
    }

    public function create()
    {
        $technicians = Technician::with('user')->get();
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return view('technician-availabilities.create', compact('technicians', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'technician_id' => 'required|exists:technicians,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Check for overlapping availability
        $overlapping = TechnicianAvailability::where('technician_id', $request->technician_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('is_active', true)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['error' => 'This time slot overlaps with existing availability for this technician.']);
        }

        TechnicianAvailability::create($request->all());

        return redirect()->route('technician-availabilities.index')
            ->with('success', 'Availability time slot created successfully!');
    }

    public function edit(TechnicianAvailability $availability)
    {
        $technicians = Technician::with('user')->get();
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return view('technician-availabilities.edit', compact('availability', 'technicians', 'daysOfWeek'));
    }

    public function update(Request $request, TechnicianAvailability $availability)
    {
        $request->validate([
            'technician_id' => 'required|exists:technicians,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Check for overlapping availability (excluding current record)
        $overlapping = TechnicianAvailability::where('technician_id', $request->technician_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('is_active', true)
            ->where('id', '!=', $availability->id)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['error' => 'This time slot overlaps with existing availability for this technician.']);
        }

        $availability->update($request->all());

        return redirect()->route('technician-availabilities.index')
            ->with('success', 'Availability time slot updated successfully!');
    }

    public function destroy(TechnicianAvailability $availability)
    {
        $availability->delete();

        return redirect()->route('technician-availabilities.index')
            ->with('success', 'Availability time slot removed successfully!');
    }

    // Technician's own availability management
    public function myAvailability()
    {
        $technician = Auth::user()->technician;

        if (!$technician) {
            return redirect()->route('home')->withErrors(['error' => 'Access denied. Technician account required.']);
        }

        $availabilities = TechnicianAvailability::where('technician_id', $technician->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return view('technician-availabilities.my-availability', compact('availabilities', 'daysOfWeek'));
    }

    public function storeMyAvailability(Request $request)
    {
        $technician = Auth::user()->technician;

        if (!$technician) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Check for overlapping availability
        $overlapping = TechnicianAvailability::where('technician_id', $technician->id)
            ->where('day_of_week', $request->day_of_week)
            ->where('is_active', true)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['error' => 'This time slot overlaps with existing availability.']);
        }

        $request->merge(['technician_id' => $technician->id]);
        TechnicianAvailability::create($request->all());

        return redirect()->route('technician-availabilities.my-availability')
            ->with('success', 'Availability time slot added successfully!');
    }

    public function updateMyAvailability(Request $request, TechnicianAvailability $availability)
    {
        $technician = Auth::user()->technician;

        if (!$technician || $availability->technician_id !== $technician->id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean'
        ]);

        // Check for overlapping availability (excluding current record)
        $overlapping = TechnicianAvailability::where('technician_id', $technician->id)
            ->where('day_of_week', $request->day_of_week)
            ->where('is_active', true)
            ->where('id', '!=', $availability->id)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['error' => 'This time slot overlaps with existing availability.']);
        }

        $availability->update($request->all());

        return redirect()->route('technician-availabilities.my-availability')
            ->with('success', 'Availability time slot updated successfully!');
    }

    public function destroyMyAvailability(TechnicianAvailability $availability)
    {
        $technician = Auth::user()->technician;

        if (!$technician || $availability->technician_id !== $technician->id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $availability->delete();

        return redirect()->route('technician-availabilities.my-availability')
            ->with('success', 'Availability time slot removed successfully!');
    }
}
