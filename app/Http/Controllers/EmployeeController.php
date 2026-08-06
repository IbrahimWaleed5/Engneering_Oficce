<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UniversalContentModerationService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly UniversalContentModerationService $moderationService
    ) {
    }

    public function index()
    {
        $employees = User::with([
            'employeeProfile.specialty',
            'engineerWorks',
        ])
            ->whereIn('role', [
                'admin',
                'engineer',
                'employee',
            ])
            ->latest()
            ->get();

        return view(
            'employees.index',
            compact('employees')
        );
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'role' => [
                'required',
                'in:admin,engineer,employee',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | فحص الاسم قبل إنشاء الحساب
        |--------------------------------------------------------------------------
        */

        $moderationResult =
            $this->moderationService->moderateText(
                user: $request->user(),
                text: trim($validated['name']),
                sourceType: 'employee_account_name',
                sourceId: null,
                context: [
                    'content_section' =>
                        'employee_management',

                    'recipient_role' =>
                        'admin',
                ]
            );

        if (! $moderationResult['allowed']) {
            return back()
                ->withInput(
                    $request->except('password')
                )
                ->with(
                    'error',
                    $moderationResult['user_message']
                );
        }

        User::create([
            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'password' =>
                bcrypt($validated['password']),

            'role' =>
                $validated['role'],

            'status' =>
                'active',
        ]);

        return redirect('/employees')
            ->with(
                'success',
                'تم إضافة الموظف بنجاح'
            );
    }
}
