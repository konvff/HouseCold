<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Repositories\Contracts\ServiceTypeRepositoryInterface;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function __construct(
        private ServiceTypeRepositoryInterface $serviceTypeRepository
    ) {}
    public function index()
    {
        $serviceTypes = ServiceType::orderBy('name')->paginate(15);
        return view('service-types.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('service-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_types',
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'estimated_duration_minutes' => 'required|integer|min:15',
            'is_active' => 'boolean'
        ]);

        $this->serviceTypeRepository->create($request->all());

        return redirect()->route('service-types.index')
            ->with('success', 'Service type created successfully!');
    }

    public function show(ServiceType $serviceType)
    {
        $serviceType->load('appointments');
        return view('service-types.show', compact('serviceType'));
    }

    public function edit(ServiceType $serviceType)
    {
        return view('service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name,' . $serviceType->id,
            'description' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'estimated_duration_minutes' => 'required|integer|min:15',
            'is_active' => 'boolean'
        ]);

        $serviceType->update($request->all());

        return redirect()->route('service-types.show', $serviceType)
            ->with('success', 'Service type updated successfully!');
    }

    public function destroy(ServiceType $serviceType)
    {
        // Check if service type has appointments
        if ($serviceType->appointments()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete service type with existing appointments.']);
        }

        $serviceType->delete();

        return redirect()->route('service-types.index')
            ->with('success', 'Service type deleted successfully!');
    }
}
