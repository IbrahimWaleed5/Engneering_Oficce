<?php

namespace App\Http\Controllers;

use App\Models\OfficeMembershipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OfficeApplicationFileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | عرض أو تنزيل ملف من طلب انضمام مهندس
    |--------------------------------------------------------------------------
    |
    | الملفات محفوظة داخل التخزين الخاص، لذلك لا يمكن فتحها مباشرة
    | باستخدام asset(). يُسمح بالوصول إلى:
    |
    | 1. المهندس صاحب الطلب.
    | 2. مدير أو مالك المكتب المرتبط بالطلب.
    | 3. مدير النظام Admin.
    |
    */

    public function membershipApplicationFile(
        Request $request,
        OfficeMembershipApplication $officeMembershipApplication,
        string $type
    ): StreamedResponse {
        $user = $request->user();

        abort_unless(
            $user !== null,
            403,
            'يجب تسجيل الدخول للوصول إلى الملف.'
        );

        abort_unless(
            in_array($type, ['cv', 'certificate'], true),
            404,
            'نوع الملف غير صحيح.'
        );

        $isAdmin = $user->role === 'admin';

        $isApplicationOwner =
            (int) $officeMembershipApplication->engineer_id
            === (int) $user->id;

        $isOfficeManager = $user
            ->managedOfficeMemberships()
            ->where(
                'office_id',
                $officeMembershipApplication->office_id
            )
            ->exists();

        abort_unless(
            $isAdmin
            || $isApplicationOwner
            || $isOfficeManager,
            403,
            'ليس لديك صلاحية الوصول إلى هذا الملف.'
        );

        $path = match ($type) {
            'cv' => $officeMembershipApplication->cv_path,
            'certificate' =>
                $officeMembershipApplication->certificate_path,
        };

        abort_if(
            empty($path),
            404,
            'لم يتم العثور على مسار الملف.'
        );

        abort_unless(
            Storage::exists($path),
            404,
            'الملف غير موجود في التخزين.'
        );

        $extension = pathinfo(
            $path,
            PATHINFO_EXTENSION
        );

        $engineerName = $officeMembershipApplication
            ->engineer()
            ->value('name') ?? 'engineer';

        $safeEngineerName = preg_replace(
            '/[^\pL\pN\-_]+/u',
            '-',
            $engineerName
        );

        $fileName = match ($type) {
            'cv' =>
                'CV-'
                . $safeEngineerName
                . '.'
                . $extension,

            'certificate' =>
                'Certificate-'
                . $safeEngineerName
                . '.'
                . $extension,
        };

        return Storage::download(
            $path,
            $fileName
        );
    }
}
