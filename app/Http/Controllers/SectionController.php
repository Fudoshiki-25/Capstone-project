<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public const IDEAL_SIZE = 15;
    public const MAX_SIZE   = 30;

    /**
     * POST /admin/sections/generate
     * Auto-generates sections for a grade level: groups every 'approved'
     * (not yet sectioned) student in that grade into evenly-sized sections
     * of up to MAX_SIZE each (e.g. 31 students -> two sections of 16 and 15,
     * not one full section of 30 plus a near-empty one). AM/PM session is
     * a per-student schedule preference only — it does not split sections.
     * Assigning a section also moves the student from 'approved' to 'enrolled'.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
        ]);

        $gradeLevel = $request->input('grade_level');

        if (! $request->user()->canManageGrade($gradeLevel)) {
            abort(403, 'You are not assigned to manage ' . $gradeLevel . '.');
        }

        // Locks the candidate rows for the duration of the transaction, so a
        // double-click (or two admins triggering this back-to-back) can't
        // both read the same unsectioned students before either commits —
        // the second request blocks until the first finishes, then sees
        // zero unsectioned students left instead of double-processing them.
        return DB::transaction(function () use ($gradeLevel) {
            $students = StudentEnrollment::where('grade_level', $gradeLevel)
                ->where('status', 'approved')
                ->whereNull('section_id')
                ->orderBy('last_name')
                ->lockForUpdate()
                ->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'message' => 'No approved, unsectioned students found for ' . $gradeLevel . '.',
                ], 422);
            }

            $total       = $students->count();
            $numSections = max(1, (int) ceil($total / self::MAX_SIZE));
            $base        = intdiv($total, $numSections);
            $remainder   = $total % $numSections;

            // Figure out how many sections already exist for this grade so new
            // ones are numbered after them instead of restarting at 1.
            $existingCount = Section::where('grade_level', $gradeLevel)->count();

            $studentIndex = 0;
            $createdSections = [];

            for ($i = 0; $i < $numSections; $i++) {
                $sectionSize = $base + ($i < $remainder ? 1 : 0);

                $section = Section::create([
                    'grade_level' => $gradeLevel,
                    'name'        => $gradeLevel . ' - Section ' . ($existingCount + $i + 1),
                ]);
                $createdSections[] = $section;

                for ($j = 0; $j < $sectionSize; $j++) {
                    $student = $students[$studentIndex];
                    $student->update([
                        'section_id' => $section->id,
                        'status'     => 'enrolled',
                    ]);
                    $studentIndex++;
                }
            }

            return response()->json([
                'success'  => true,
                'message'  => count($createdSections) . ' section(s) created for ' . $gradeLevel . ' (' . $total . ' student(s)).',
                'sections' => $createdSections,
            ]);
        });
    }

    /**
     * GET /admin/sections
     * Returns every section with its grade level and roster, for the
     * Sections tab's master list.
     */
    public function index()
    {
        $sections = Section::withCount('students')
            ->with(['students' => fn ($q) => $q->orderBy('last_name')])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return response()->json(['sections' => $sections]);
    }

    /**
     * PATCH /admin/students/{enrollment}/transfer
     * Moves an already-placed (approved/enrolled) student to a different
     * grade level and/or section. Giving a section name finds-or-creates
     * that section (same convention as generate()) and marks the student
     * 'enrolled'; leaving it blank drops the student back to 'approved',
     * awaiting the next Generate Sections run for the new grade.
     */
    public function transfer(Request $request, StudentEnrollment $enrollment)
    {
        $request->validate([
            'grade_level'  => 'required|string|max:20',
            'section_name' => 'nullable|string|max:100',
            'reason'       => 'nullable|string|max:500',
        ]);

        if (! in_array($enrollment->status, ['approved', 'enrolled'], true)) {
            return response()->json([
                'message' => 'Only approved or enrolled students can be transferred.',
            ], 422);
        }

        $gradeLevel  = $request->input('grade_level');
        $sectionName = trim((string) $request->input('section_name'));

        // Must be allowed to manage both the student's current grade and
        // the grade they're being moved into.
        if (! $request->user()->canManageGrade($enrollment->grade_level) || ! $request->user()->canManageGrade($gradeLevel)) {
            abort(403, 'You are not assigned to manage this transfer.');
        }

        if ($sectionName !== '') {
            $section = Section::firstOrCreate([
                'grade_level' => $gradeLevel,
                'name'        => $sectionName,
            ]);

            $enrollment->update([
                'grade_level' => $gradeLevel,
                'section_id'  => $section->id,
                'status'      => 'enrolled',
            ]);
        } else {
            $enrollment->update([
                'grade_level' => $gradeLevel,
                'section_id'  => null,
                'status'      => 'approved',
            ]);
        }

        \App\Models\ActivityLog::record(
            $request->user(),
            'Transferred Student',
            trim($enrollment->first_name . ' ' . $enrollment->last_name) . ' → ' . $gradeLevel . ($sectionName !== '' ? ' (' . $sectionName . ')' : ''),
            'success'
        );

        return response()->json([
            'success' => true,
            'message' => trim($enrollment->first_name . ' ' . $enrollment->last_name) . ' transferred successfully.',
        ]);
    }
}
