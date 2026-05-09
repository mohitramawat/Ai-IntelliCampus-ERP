<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class TeacherController extends Controller
{
    public function index()
    {
        $departments = Department::where('is_active', true)->get();
        return view('writer.teachers.index', compact('departments'));
    }

    public function datatable(Request $request)
    {
        $query = Teacher::with(['user.roles', 'department'])
            ->select('teachers.*')
            ->when($request->department_id, function($q) use ($request) {
                return $q->where('department_id', $request->department_id);
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', function($row) {
                return $row->user->name ?? 'N/A';
            })
            ->addColumn('email', function($row) {
                return $row->user->email ?? 'N/A';
            })
            ->addColumn('dept_name', function($row) {
                return $row->department->name ?? 'N/A';
            })
            ->addColumn('status_badge', function($row) {
                $status = strtolower($row->status ?? 'active');
                $colors = [
                    'active'   => 'bg-green-100 text-green-700',
                    'inactive' => 'bg-red-100 text-red-700',
                    'on_leave' => 'bg-amber-100 text-amber-700'
                ];
                $color = $colors[$status] ?? 'bg-gray-100 text-gray-700';
                $label = ucfirst(str_replace('_', ' ', $status));
                return '<span class="px-2.5 py-1 rounded-full text-[11px] font-bold ' . $color . '">' . $label . '</span>';
            })
            ->addColumn('action', function($row) {
                $isHod = $row->user->hasRole('hod');
                $hodColor = $isHod ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-gray-100 text-gray-500 border-gray-200';
                $hodIcon  = $isHod ? 'stars' : 'star_rate';
                $hodTitle = $isHod ? 'Remove HOD Role' : 'Assign HOD Role';

                return '
                    <div class="flex gap-2 justify-center">
                        <button onclick="toggleHod('.$row->id.', \''.addslashes($row->user->name).'\', '.$isHod.')" class="w-[30px] h-[30px] rounded-lg border flex items-center justify-center transition-all hover:bg-purple-600 hover:text-white hover:border-purple-600 '.$hodColor.'" title="'.$hodTitle.'">
                            <span class="material-symbols-outlined text-[18px]">'.$hodIcon.'</span>
                        </button>
                        <button onclick="editTeacher('.$row->id.')" class="btn-icon-edit" title="Edit">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>
                        <button onclick="deleteTeacher('.$row->id.', \''.addslashes($row->user->name).'\')" class="btn-icon-del" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'employee_code' => 'required|unique:teachers,employee_code',
            'gender'        => 'required|in:male,female,other',
            'phone_number'  => 'nullable|string|max:20',
            'qualification' => 'nullable|string',
            'status'        => 'required|in:active,inactive,on_leave',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make('Teacher@123'), // Default password
            ]);
            $user->assignRole('teacher');

            // 2. Create Teacher Profile
            Teacher::create([
                'user_id'          => $user->id,
                'department_id'    => $request->department_id,
                'employee_code'    => strtoupper($request->employee_code),
                'gender'           => $request->gender,
                'phone_number'     => $request->phone_number,
                'qualification'    => $request->qualification,
                'experience_years' => $request->experience_years ?? 0,
                'joining_date'     => $request->joining_date ?? now(),
                'status'           => $request->status,
            ]);

            DB::commit();
            return response()->json(['message' => 'Teacher created successfully. Default password: Teacher@123']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to build teacher profile: ' . $e->getMessage()], 500);
        }
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('user');
        return response()->json($teacher);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $teacher->user_id,
            'department_id' => 'required|exists:departments,id',
            'employee_code' => 'required|unique:teachers,employee_code,' . $teacher->id,
            'status'        => 'required|in:active,inactive,on_leave',
        ]);

        try {
            DB::beginTransaction();

            $teacher->user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);

            $teacher->update([
                'department_id'    => $request->department_id,
                'employee_code'    => strtoupper($request->employee_code),
                'gender'           => $request->gender,
                'phone_number'     => $request->phone_number,
                'qualification'    => $request->qualification,
                'experience_years' => $request->experience_years,
                'joining_date'     => $request->joining_date,
                'status'           => $request->status,
            ]);

            DB::commit();
            return response()->json(['message' => 'Teacher profile updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            DB::beginTransaction();
            $user = $teacher->user;
            $teacher->delete();
            $user->delete();
            DB::commit();
            return response()->json(['message' => 'Teacher removed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Delete failed.'], 500);
        }
    }

    public function toggleHod(Teacher $teacher)
    {
        try {
            $user = $teacher->user;
            if ($user->hasRole('hod')) {
                $user->removeRole('hod');
                $message = 'HOD role removed successfully.';
            } else {
                $user->assignRole('hod');
                $message = 'HOD role assigned successfully.';
            }
            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Action failed: ' . $e->getMessage()], 500);
        }
    }
}
