<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
class EmployeeController extends Controller
{
public function index()
{
    $employees = User::query()
        ->where('role', 'admin')
        ->orWhere('role', 'engineer')
        ->orWhere('role', 'employee')
        ->get();

    return view('employees.index', compact('employees'));
}

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,engineer,employee',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect('/employees')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }
}
