<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    /**
     * Show the change password form.
     * Accessible by student at any time, but forced on first login.
     */
    public function show()
    {
        return view('student.password.change');
    }

    /**
     * Handle password change.
     *
     * Rules:
     * - Current password must be correct (security check)
     * - New password min 8 chars, confirmed
     * - Cannot be the same as current/temporary password
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password'      => ['required', 'string'],
            'new_password'          => [
                'required',
                'string',
                'min:8',
                'confirmed',              // needs new_password_confirmation field
                'different:current_password', // cannot reuse temporary password
            ],
            'new_password_confirmation' => ['required', 'string'],
        ], [
            'new_password.min'       => 'New password must be at least 8 characters.',
            'new_password.different' => 'New password must be different from your current password.',
            'new_password.confirmed' => 'New password and confirm password do not match.',
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password you entered is incorrect.',
            ])->withInput();
        }

        // Update password and clear the force-change flag
        $user->password              = Hash::make($request->new_password);
        $user->must_change_password  = false;
        $user->save();

        return redirect()
            ->route('student.dashboard')
            ->with('success', 'Password changed successfully. Keep it safe!');
    }
}
