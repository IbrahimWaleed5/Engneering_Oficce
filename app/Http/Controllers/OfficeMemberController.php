<?php

namespace App\Http\Controllers;

use App\Models\EngineeringSpecialty;
use App\Models\OfficeMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OfficeMemberController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | عرض أعضاء المكتب
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $managerMembership = $this->getManagerMembership(
            $request
        );

        $office = $managerMembership->office;

        $members = OfficeMember::query()
            ->where('office_id', $office->id)
            ->with([
                'user:id,name,email,phone,profile_photo,role',
                'specialty',
                'approver:id,name',
            ])
            ->orderByRaw(
                "
                CASE office_role
                    WHEN 'owner' THEN 1
                    WHEN 'manager' THEN 2
                    WHEN 'engineer' THEN 3
                    ELSE 4
                END
                "
            )
            ->latest('joined_at')
            ->paginate(20)
            ->withQueryString();

        $specialties = EngineeringSpecialty::query()
            ->orderBy('name')
            ->get();

        $statistics = [
            'all' => OfficeMember::query()
                ->where('office_id', $office->id)
                ->count(),

            'active' => OfficeMember::query()
                ->where('office_id', $office->id)
                ->where('status', 'active')
                ->count(),

            'engineers' => OfficeMember::query()
                ->where('office_id', $office->id)
                ->where('office_role', 'engineer')
                ->where('status', 'active')
                ->count(),

            'managers' => OfficeMember::query()
                ->where('office_id', $office->id)
                ->whereIn(
                    'office_role',
                    ['owner', 'manager']
                )
                ->where('status', 'active')
                ->count(),
        ];

        return view(
            'office.members',
            compact(
                'office',
                'members',
                'specialties',
                'statistics',
                'managerMembership'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | تحديث بيانات عضو المكتب
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        OfficeMember $officeMember
    ): RedirectResponse {
        $managerMembership = $this->getManagerMembership(
            $request
        );

        abort_unless(
            (int) $managerMembership->office_id
            === (int) $officeMember->office_id,
            403,
            'لا يمكنك تعديل أعضاء مكتب آخر.'
        );

        if ($officeMember->office_role === 'owner') {
            return back()->with(
                'error',
                'لا يمكن تعديل صلاحيات مالك المكتب.'
            );
        }

        $validated = $request->validate(
            [
                'position' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'specialty_id' => [
                    'nullable',
                    'exists:engineering_specialties,id',
                ],

                'office_role' => [
                    'required',
                    Rule::in([
                        'manager',
                        'engineer',
                        'employee',
                    ]),
                ],

                'status' => [
                    'required',
                    Rule::in([
                        'active',
                        'inactive',
                    ]),
                ],
            ],
            [
                'position.max' =>
                    'المسمى الوظيفي يجب ألا يتجاوز 150 حرفًا.',

                'specialty_id.exists' =>
                    'التخصص المختار غير موجود.',

                'office_role.required' =>
                    'يجب تحديد دور العضو داخل المكتب.',

                'office_role.in' =>
                    'دور العضو داخل المكتب غير صحيح.',

                'status.required' =>
                    'يجب تحديد حالة العضو.',

                'status.in' =>
                    'حالة العضو غير صحيحة.',
            ]
        );

        /*
        | المدير العادي لا يستطيع تعيين مدير آخر
        | أو تعديل عضو يحمل صلاحية مدير.
        */
        if (
            $managerMembership->office_role !== 'owner'
            && (
                $officeMember->office_role === 'manager'
                || $validated['office_role'] === 'manager'
            )
        ) {
            abort(
                403,
                'مالك المكتب فقط يستطيع إدارة صلاحيات المديرين.'
            );
        }

        $officeMember->update([
            'position' => $validated['position'],

            'specialty_id' =>
                $validated['specialty_id'],

            'office_role' =>
                $validated['office_role'],

            'status' => $validated['status'],

            'left_at' =>
                $validated['status'] === 'inactive'
                    ? now()
                    : null,
        ]);

        return back()->with(
            'success',
            'تم تحديث بيانات عضو المكتب بنجاح.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | إزالة عضو من المكتب
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        OfficeMember $officeMember
    ): RedirectResponse {
        $managerMembership = $this->getManagerMembership(
            $request
        );

        abort_unless(
            (int) $managerMembership->office_id
            === (int) $officeMember->office_id,
            403,
            'لا يمكنك إزالة أعضاء مكتب آخر.'
        );

        if ($officeMember->office_role === 'owner') {
            return back()->with(
                'error',
                'لا يمكن إزالة مالك المكتب.'
            );
        }

        if (
            $officeMember->office_role === 'manager'
            && $managerMembership->office_role !== 'owner'
        ) {
            abort(
                403,
                'مالك المكتب فقط يستطيع إزالة مدير.'
            );
        }

        if (
            (int) $officeMember->user_id
            === (int) $request->user()->id
        ) {
            return back()->with(
                'error',
                'لا يمكنك إزالة نفسك من المكتب بهذه الطريقة.'
            );
        }

        $officeMember->update([
            'status' => 'inactive',
            'left_at' => now(),
        ]);

        return back()->with(
            'success',
            'تمت إزالة العضو من فريق المكتب.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | الحصول على عضوية المدير أو المالك
    |--------------------------------------------------------------------------
    */

    private function getManagerMembership(
        Request $request
    ): OfficeMember {
        $membership = $request
            ->user()
            ->managedOfficeMemberships()
            ->with('office')
            ->where('status', 'active')
            ->first();

        abort_unless(
            $membership !== null,
            403,
            'ليس لديك صلاحية إدارة أعضاء مكتب.'
        );

        abort_unless(
            in_array(
                $membership->office_role,
                ['owner', 'manager'],
                true
            ),
            403,
            'ليس لديك صلاحية إدارة أعضاء المكتب.'
        );

        return $membership;
    }
}
