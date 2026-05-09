<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Available staff roles (excludes student & teacher — they have their own systems).
     */
    private array $staffRoles = ['admin', 'hod', 'accounts', 'writer'];

    public function index()
    {
        $staffMembers = StaffProfile::with(['user.roles', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $roles = $this->staffRoles;
        return view('admin.staff.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:6'],
            'role'          => ['required', Rule::in($this->staffRoles)],
            'employee_code' => ['nullable', 'string', 'max:50', 'unique:staff_profiles,employee_code'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone_number'  => ['nullable', 'string', 'max:20'],
            'gender'        => ['nullable', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'joining_date'  => ['nullable', 'date'],
            'address'       => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request) {
            // 1. Create User (login credentials)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Assign Role
            $user->assignRole($request->role);

            // 3. Create Staff Profile (personal details)
            StaffProfile::create([
                'user_id'       => $user->id,
                'department_id' => $request->department_id,
                'employee_code' => $request->employee_code,
                'staff_type'    => $request->role,
                'joining_date'  => $request->joining_date,
                'phone_number'  => $request->phone_number,
                'address'       => $request->address,
                'gender'        => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'status'        => 'active',
            ]);
        });

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff member created successfully! They can now login with their email & password.');
    }

    public function edit(StaffProfile $staff)
    {
        $staff->load(['user.roles', 'department']);
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $roles = $this->staffRoles;
        return view('admin.staff.edit', compact('staff', 'departments', 'roles'));
    }

    public function update(Request $request, StaffProfile $staff)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'unique:users,email,' . $staff->user_id],
            'role'          => ['required', Rule::in($this->staffRoles)],
            'employee_code' => ['nullable', 'string', 'max:50', 'unique:staff_profiles,employee_code,' . $staff->id],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone_number'  => ['nullable', 'string', 'max:20'],
            'gender'        => ['nullable', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'joining_date'  => ['nullable', 'date'],
            'address'       => ['nullable', 'string'],
            'password'      => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($request, $staff) {
            // Update User
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $staff->user->update($userData);

            // Update Role
            $staff->user->syncRoles([$request->role]);

            // Update Staff Profile
            $staff->update([
                'department_id' => $request->department_id,
                'employee_code' => $request->employee_code,
                'staff_type'    => $request->role,
                'joining_date'  => $request->joining_date,
                'phone_number'  => $request->phone_number,
                'address'       => $request->address,
                'gender'        => $request->gender,
                'date_of_birth' => $request->date_of_birth,
            ]);
        });

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(StaffProfile $staff)
    {
        DB::transaction(function () use ($staff) {
            $staff->user->delete();
            $staff->delete();
        });

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff member removed.');
    }
}
