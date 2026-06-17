<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'employee')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->paginate(20)->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $password = $validated['password'] ?? Str::random(12);

        $employee = new User();
        $employee->name = $validated['name'];
        $employee->email = $validated['email'];
        $employee->password = Hash::make($password);
        $employee->role = 'employee';
        $employee->is_active = true;
        $employee->email_verified_at = now(); // admin-created accounts are pre-verified
        $employee->save();

        $message = empty($validated['password'])
            ? "Employee created. Temporary password: {$password}"
            : 'Employee created successfully.';

        return redirect()->route('admin.employees.index')->with('success', $message);
    }

    public function edit(User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee->id)],
        ]);

        $employee->update($validated);

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted.');
    }

    public function deactivate(User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $employee->is_active = ! $employee->is_active;
        $employee->save();

        $state = $employee->is_active ? 'reactivated' : 'deactivated';

        return back()->with('success', "Employee {$state}.");
    }

    public function resetPassword(Request $request, User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $validated = $request->validate([
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $password = $validated['password'] ?? Str::random(12);
        $employee->update(['password' => Hash::make($password)]);

        return back()->with('success', "Password reset. New password: {$password}");
    }
}
