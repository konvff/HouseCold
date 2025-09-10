<?php

namespace App\Http\Controllers;

use App\Models\TechnicianAvailability;
use App\Models\Technician;
use App\Repositories\Contracts\TechnicianAvailabilityRepositoryInterface;
use App\Repositories\Contracts\TechnicianRepositoryInterface;
use App\Services\Contracts\TechnicianAvailabilityServiceInterface;
use App\Enums\DayOfWeek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TechnicianAvailabilityController extends Controller
{
    public function __construct(
        private TechnicianAvailabilityRepositoryInterface $availabilityRepository,
        private TechnicianRepositoryInterface $technicianRepository,
        private TechnicianAvailabilityServiceInterface $availabilityService
    ) {}


    public function index()
    {
        $availabilities = TechnicianAvailability::with('technician.user')
            ->orderBy('technician_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $technicians = $this->technicianRepository->getActiveTechnicians();

        return view('technician-availabilities.index', compact('availabilities', 'technicians'));
    }

    public function create()
    {
        $technicians = $this->technicianRepository->getActiveTechnicians();
        $daysOfWeek = DayOfWeek::getAllDays();

        return view('technician-availabilities.create', compact('technicians', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        try {
            $this->availabilityService->createAvailability($request->all());

            return redirect()->route('technician-availabilities.index')
                ->with('success', 'Availability time slot created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(TechnicianAvailability $availability)
    {
        $technicians = $this->technicianRepository->getActiveTechnicians();
        $daysOfWeek = DayOfWeek::getAllDays();

        return view('technician-availabilities.edit', compact('availability', 'technicians', 'daysOfWeek'));
    }

    public function update(Request $request, TechnicianAvailability $availability)
    {
        try {
            $this->availabilityService->updateAvailability($availability->id, $request->all());

            return redirect()->route('technician-availabilities.index')
                ->with('success', 'Availability time slot updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(TechnicianAvailability $availability)
    {
        $this->availabilityService->deleteAvailability($availability->id);

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

        $availabilities = $this->availabilityService->getTechnicianAvailabilities($technician->id);
        $daysOfWeek = DayOfWeek::getAllDays();

        return view('technician-availabilities.my-availability', compact('availabilities', 'daysOfWeek'));
    }

    public function storeMyAvailability(Request $request)
    {
        $technician = Auth::user()->technician;

        if (!$technician) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        try {
            $request->merge(['technician_id' => $technician->id]);
            $this->availabilityService->createAvailability($request->all());

            return redirect()->route('technician-availabilities.my-availability')
                ->with('success', 'Availability time slot added successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateMyAvailability(Request $request, TechnicianAvailability $technician_availability)
    {
        $technician = Auth::user()->technician;

        if (!$technician || $technician_availability->technician_id !== $technician->id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        try {
            $this->availabilityService->updateAvailability($technician_availability->id, $request->all());

            return redirect()->route('technician-availabilities.my-availability')
                ->with('success', 'Availability time slot updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroyMyAvailability(TechnicianAvailability $technician_availability)
    {
        $technician = Auth::user()->technician;

        if (!$technician || $technician_availability->technician_id !== $technician->id) {
            return back()->withErrors(['error' => 'Access denied.']);
        }

        $this->availabilityService->deleteAvailability($technician_availability->id);

        return redirect()->route('technician-availabilities.my-availability')
            ->with('success', 'Availability time slot removed successfully!');
    }
}
