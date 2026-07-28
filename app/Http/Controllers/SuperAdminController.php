<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\EnrollmentPeriod;
use App\Models\GradeEnrollmentSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends Controller
{
    /* ──────────────────────────────────────────────
       ADMIN ACCOUNTS
    ────────────────────────────────────────────── */

    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'assigned_grade' => 'nullable|string|max:20',
            'password'       => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $admin = User::create([
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'role'           => 'admin',
            'assigned_grade' => $data['assigned_grade'] ?: null,
            'is_active'      => true,
        ]);

        \App\Models\ActivityLog::record(
            $request->user(),
            'Created Admin Account',
            "New admin: {$admin->first_name} {$admin->last_name}" . ($admin->assigned_grade ? " assigned to {$admin->assigned_grade}" : ' (All Grades)'),
            'purple'
        );

        return response()->json(['success' => true, 'admin' => $admin]);
    }

    public function updateAdmin(Request $request, User $admin)
    {
        $data = $request->validate([
            'email'          => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id)],
            'assigned_grade' => 'nullable|string|max:20',
        ]);

        $admin->update([
            'email'          => $data['email'],
            'assigned_grade' => $data['assigned_grade'] ?: null,
        ]);

        \App\Models\ActivityLog::record($request->user(), 'Updated Admin Account', "Admin: {$admin->first_name} {$admin->last_name}", 'info');

        return response()->json(['success' => true]);
    }

    public function resetAdminPassword(Request $request, User $admin)
    {
        $tempPassword = \Illuminate\Support\Str::random(10);
        $admin->update(['password' => Hash::make($tempPassword)]);

        // TODO: wire up actual email notification with $tempPassword
        \App\Models\ActivityLog::record($request->user(), 'Reset Admin Password', "Admin: {$admin->first_name} {$admin->last_name}", 'warning');

        return response()->json(['success' => true]);
    }

    public function toggleAdminActive(Request $request, User $admin)
    {
        $data = $request->validate(['is_active' => 'required|boolean']);
        $admin->update(['is_active' => $data['is_active']]);

        \App\Models\ActivityLog::record(
            $request->user(),
            $data['is_active'] ? 'Activated Admin Account' : 'Deactivated Admin Account',
            "Admin: {$admin->first_name} {$admin->last_name}",
            $data['is_active'] ? 'success' : 'danger'
        );

        return response()->json(['success' => true]);
    }

    /* ──────────────────────────────────────────────
       ENROLLMENT PERIOD
    ────────────────────────────────────────────── */

    public function saveEnrollmentPeriod(Request $request)
    {
        $data = $request->validate([
            'id'          => 'nullable|exists:enrollment_periods,id',
            'school_year' => 'required|string|max:20',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
        ]);

        if (!empty($data['id'])) {
            $period = EnrollmentPeriod::findOrFail($data['id']);
            $period->update([
                'school_year' => $data['school_year'],
                'start_date'  => $data['start_date'],
                'end_date'    => $data['end_date'],
            ]);
        } else {
            // Close any previously open period before creating a new current one
            EnrollmentPeriod::where('is_open', true)->update(['is_open' => false]);
            $period = EnrollmentPeriod::create([
                'school_year' => $data['school_year'],
                'start_date'  => $data['start_date'],
                'end_date'    => $data['end_date'],
                'is_open'     => true,
            ]);
        }

        \App\Models\ActivityLog::record(
            $request->user(),
            'Enrollment Period Updated',
            "SY {$period->school_year}: {$data['start_date']} – {$data['end_date']}",
            'purple'
        );

        return response()->json(['success' => true, 'period' => $period]);
    }

    public function toggleEnrollmentPeriod(Request $request, EnrollmentPeriod $period)
    {
        $data = $request->validate(['is_open' => 'required|boolean']);
        $period->update(['is_open' => $data['is_open']]);

        \App\Models\ActivityLog::record(
            $request->user(),
            $data['is_open'] ? 'Opened Enrollment' : 'Closed Enrollment',
            "SY {$period->school_year}",
            'purple'
        );

        return response()->json(['success' => true]);
    }

    /**
     * PUT /superadmin/grade-settings
     * Sets which grade levels currently accept enrollment applications.
     */
    public function updateGradeSettings(Request $request)
    {
        $data = $request->validate([
            'grades'             => 'required|array',
            'grades.*.grade'     => 'required|string',
            'grades.*.is_open'   => 'required|boolean',
        ]);

        foreach ($data['grades'] as $g) {
            GradeEnrollmentSetting::updateOrCreate(
                ['grade_level' => $g['grade']],
                ['is_open' => $g['is_open']]
            );
        }

        \App\Models\ActivityLog::record($request->user(), 'Updated Grade-Level Enrollment Settings', null, 'purple');

        return response()->json(['success' => true]);
    }

    /* ──────────────────────────────────────────────
       ANNOUNCEMENTS
    ────────────────────────────────────────────── */

    public function storeAnnouncement(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'message'       => 'required|string',
            'show_from'     => 'nullable|date',
            'show_until'    => 'nullable|date',
            'status'        => 'required|in:active,inactive',
            'show_as_popup' => 'boolean',
        ]);

        $announcement = Announcement::create([
            ...$data,
            'created_by'      => $request->user()->id,
            'created_by_name' => trim($request->user()->first_name . ' ' . $request->user()->last_name),
        ]);

        \App\Models\ActivityLog::record($request->user(), 'Created Announcement', $announcement->title, 'purple');

        return response()->json($this->announcementPayload($announcement));
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'message'       => 'required|string',
            'show_from'     => 'nullable|date',
            'show_until'    => 'nullable|date',
            'status'        => 'required|in:active,inactive',
            'show_as_popup' => 'boolean',
        ]);

        $announcement->update($data);

        \App\Models\ActivityLog::record($request->user(), 'Updated Announcement', $announcement->title, 'info');

        return response()->json($this->announcementPayload($announcement));
    }

    public function deleteAnnouncement(Request $request, Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        \App\Models\ActivityLog::record($request->user(), 'Deleted Announcement', $title, 'danger');

        return response()->json(['success' => true]);
    }

    public function toggleAnnouncementStatus(Request $request, Announcement $announcement)
    {
        $announcement->update(['status' => $announcement->status === 'active' ? 'inactive' : 'active']);

        \App\Models\ActivityLog::record($request->user(), 'Toggled Announcement Status', $announcement->title, 'info');

        return response()->json(['success' => true, 'status' => $announcement->status]);
    }

    /**
     * Normalize an Announcement into the shape the super-admin dashboard's
     * front-end announcement list/edit-form expects (matches $announcementsJs).
     */
    private function announcementPayload(Announcement $announcement): array
    {
        return [
            'id'      => $announcement->id,
            'title'   => $announcement->title,
            'message' => $announcement->message,
            'from'    => optional($announcement->show_from)->format('Y-m-d'),
            'until'   => optional($announcement->show_until)->format('Y-m-d'),
            'status'  => $announcement->status,
            'popup'   => (bool) $announcement->show_as_popup,
            'by'      => $announcement->created_by_name,
            'date'    => $announcement->created_at->format('F j, Y'),
        ];
    }
}