<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StudentProfileController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        return view('student.profile.index', compact('student'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:10240',
                'face_descriptor' => 'required|string', 
            ]);

            $student = Student::where('user_id', Auth::id())->firstOrFail();

            // Delete old picture if exists
            if ($student->profile_picture) {
                Storage::disk('public')->delete($student->profile_picture);
            }

            // Store new picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');

            $student->update([
                'profile_picture' => $path,
                'face_descriptor' => $request->face_descriptor,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture and face data updated successfully!',
                'path' => asset('storage/' . $path)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['profile_picture'][0] ?? 'Validation failed'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
