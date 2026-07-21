<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\EngineeringSpecialty;
use Illuminate\Http\Request;

class EngineerSpecialtyController extends Controller
{
    public function edit(Request $request)
    {
        $specialties = EngineeringSpecialty::query()
            ->orderBy('name')
            ->get();

        $employeeProfile = EmployeeProfile::query()
            ->where('user_id', $request->user()->id)
            ->first();

        return view(
            'engineer.specialty',
            compact(
                'specialties',
                'employeeProfile'
            )
        );
    }

    public function update(Request $request)
{
    $validated = $request->validate([
        'specialty_id' => [
            'required',
            'integer',
            'exists:engineering_specialties,id',
        ],

        'bio' => [
            'nullable',
            'string',
            'max:2000',
        ],
    ]);

    EmployeeProfile::updateOrCreate(
        [
            'user_id' => $request->user()->id,
        ],
        [
            'specialty_id' => $validated['specialty_id'],
            'bio' => $validated['bio'] ?? null,
        ]
    );

    return redirect()
        ->route('engineer.specialty.edit')
        ->with('success', 'تم حفظ التخصص والنبذة بنجاح');
}
}
