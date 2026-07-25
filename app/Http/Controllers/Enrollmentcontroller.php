<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentRequirement;
use App\Models\StudentEnrollment;
use App\Support\ImageUploadStorer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    /**
     * Map the form's payMethod values to the DB enum values.
     */
    private const PAY_METHOD_MAP = [
        'GCash'         => 'gcash',
        'Maya'          => 'maya',
        'Bank Transfer' => 'bank_transfer',
        'Cash'          => 'cash',
    ];

    /**
     * Documents required before "Enroll Now" can be clicked, depending on
     * grade level and whether the student is new or already has a record
     * at the school:
     *   - Kinder: PSA Birth Certificate + Medical Certificate
     *   - Old/returning student (any grade): Report Card only
     *   - New student, Grade 1+: Report Card + PSA Birth Certificate
     */
    public static function requiredDocumentTypes(string $gradeLevel, string $studentType): array
    {
        if ($gradeLevel === 'Kinder') {
            return ['birth_certificate', 'medical_certificate'];
        }

        if ($studentType === 'old') {
            return ['report_card'];
        }

        return ['report_card', 'birth_certificate'];
    }

    /**
     * Shared validation rules for store/update.
     */
    private function rules(): array
    {
        return [
            'first_name'         => 'required|string|max:100',
            'middle_name'        => 'nullable|string|max:100',
            'last_name'          => 'required|string|max:100',
            'suffix'             => 'nullable|string|max:20',
            'lrn'                => 'nullable|string|max:20',
            'grade_level'        => 'required|string|max:20',
            'student_type'       => 'required|in:new,old',
            'birthday'           => 'required|date',
            'birth_place'        => 'required|string|max:150',
            'last_school'        => 'nullable|string|max:150',
            'address'            => 'required|string',
            'mother_name'        => 'required|string|max:150',
            'father_name'        => 'required|string|max:150',
            'guardian_name'      => 'required|string|max:150',
            'emergency_contact'  => 'required|string|max:20',
            'classSession'       => 'required|in:AM,PM',
            'payMethod'          => 'required|in:GCash,Maya,Bank Transfer,Cash',
            'paymentPlan'        => 'required|in:monthly,quarterly',
            'proof_of_payment'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    /**
     * Reverse map: DB enum value -> the label used in the form's radio inputs.
     */
    private const PAY_METHOD_REVERSE_MAP = [
        'gcash'         => 'GCash',
        'maya'          => 'Maya',
        'bank_transfer' => 'Bank Transfer',
        'cash'          => 'Cash',
    ];

    /**
     * GET /enrollment/{enrollment}
     * Returns this enrollment's full Step 1 data so the application-form
     * modal can pre-fill itself when the parent clicks "Edit" on their
     * child's card. Field names match the form's input `name` attributes
     * directly so the frontend can just loop and assign.
     */
    public function show(Request $request, StudentEnrollment $enrollment)
    {
        $parent = Auth::guard('parent')->user();

        if ($enrollment->user_id !== $parent->id) {
            abort(403, 'You do not have permission to view this enrollment.');
        }

        return response()->json([
            'enrollment' => [
                'id'                => $enrollment->id,
                'first_name'        => $enrollment->first_name,
                'middle_name'       => $enrollment->middle_name === 'N/A' ? '' : $enrollment->middle_name,
                'last_name'         => $enrollment->last_name,
                'suffix'            => $enrollment->suffix === 'N/A' ? '' : $enrollment->suffix,
                'lrn'               => $enrollment->lrn === 'N/A' ? '' : $enrollment->lrn,
                'grade_level'       => $enrollment->grade_level,
                'student_type'      => $enrollment->student_type,
                'birthday'          => $enrollment->birthday
                    ? \Carbon\Carbon::parse($enrollment->birthday)->format('Y-m-d')
                    : '',
                'birth_place'       => $enrollment->birth_place,
                'last_school'       => $enrollment->last_school,
                'address'           => $enrollment->address,
                'mother_name'       => $enrollment->mother_name,
                'father_name'       => $enrollment->father_name,
                'guardian_name'     => $enrollment->guardian_name,
                'emergency_contact' => $enrollment->emergency_contact,
                'classSession'      => $enrollment->preferred_session,
                'payMethod'         => self::PAY_METHOD_REVERSE_MAP[$enrollment->payment_method] ?? '',
                'paymentPlan'       => $enrollment->payment_plan,
                'status'            => $enrollment->status,
            ],
        ]);
    }

    /**
     * POST /enrollment
     * Step 1 save — creates a new student_enrollment record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $parent = Auth::guard('parent')->user();

        $path = ImageUploadStorer::store(
            $request->file('proof_of_payment'),
            'proof_of_payment/' . $parent->id,
            'public'
        );

        $enrollment = StudentEnrollment::create([
            'user_id'            => $parent->id,
            'first_name'         => $validated['first_name'],
            'middle_name'        => $validated['middle_name'] ?: 'N/A',
            'last_name'          => $validated['last_name'],
            'suffix'             => $validated['suffix'] ?: 'N/A',
            'lrn'                => $validated['lrn'] ?: 'N/A',
            'grade_level'        => $validated['grade_level'],
            'student_type'       => $validated['student_type'],
            'birthday'           => $validated['birthday'],
            'birth_place'        => $validated['birth_place'],
            'last_school'        => $validated['last_school'] ?? null,
            'address'            => $validated['address'],
            'mother_name'        => $validated['mother_name'],
            'father_name'        => $validated['father_name'],
            'guardian_name'      => $validated['guardian_name'],
            'emergency_contact'  => $validated['emergency_contact'],
            'preferred_session'  => $validated['classSession'],
            'payment_method'     => self::PAY_METHOD_MAP[$validated['payMethod']],
            'payment_plan'       => $validated['paymentPlan'],
            'proof_of_payment'   => $path,
            'status'             => 'draft', // Step 2 not done yet — not a Home tab card until finalize()
        ]);

        return response()->json([
            'message'    => 'Step 1 saved. You can now upload requirements below.',
            'enrollment' => [
                'id'         => $enrollment->id,
                'name'       => trim($enrollment->first_name . ' ' . $enrollment->last_name),
                'grade'      => $enrollment->grade_level,
                'session'    => $enrollment->preferred_session,
                'pay_method' => $validated['payMethod'], // original label for display
                'status'     => $enrollment->status,
            ],
        ], 201);
    }

    /**
     * PUT/PATCH /enrollment/{enrollment}
     * Step 1 edit — updates an existing record (e.g. before admin approval).
     */
    public function update(Request $request, StudentEnrollment $enrollment)
    {
        $parent = Auth::guard('parent')->user();

        if ($enrollment->user_id !== $parent->id) {
            abort(403, 'You do not have permission to edit this enrollment.');
        }

        $rules = $this->rules();
        // proof_of_payment is already required via $this->rules() — the parent
        // must reselect a file every time they edit, since browsers can't
        // pre-fill file inputs for security reasons.

        $validated = $request->validate($rules);

        $data = [
            'first_name'         => $validated['first_name'],
            'middle_name'        => $validated['middle_name'] ?: 'N/A',
            'last_name'          => $validated['last_name'],
            'suffix'             => $validated['suffix'] ?: 'N/A',
            'lrn'                => $validated['lrn'] ?: 'N/A',
            'grade_level'        => $validated['grade_level'],
            'student_type'       => $validated['student_type'],
            'birthday'           => $validated['birthday'],
            'birth_place'        => $validated['birth_place'],
            'last_school'        => $validated['last_school'] ?? null,
            'address'            => $validated['address'],
            'mother_name'        => $validated['mother_name'],
            'father_name'        => $validated['father_name'],
            'guardian_name'      => $validated['guardian_name'],
            'emergency_contact'  => $validated['emergency_contact'],
            'preferred_session'  => $validated['classSession'],
            'payment_method'     => self::PAY_METHOD_MAP[$validated['payMethod']],
            'payment_plan'       => $validated['paymentPlan'],
        ];

        // Always replace the proof-of-payment file, since it's required on
        // every edit (old file becomes orphaned — acceptable tradeoff for
        // keeping the data model simple; not deleted here to avoid risk of
        // removing a file the admin may still be reviewing mid-edit).
        $data['proof_of_payment'] = ImageUploadStorer::store(
            $request->file('proof_of_payment'),
            'proof_of_payment/' . $parent->id,
            'public'
        );

        $enrollment->update($data);

        return response()->json([
            'message'    => 'Enrollment updated successfully.',
            'enrollment' => [
                'id'         => $enrollment->id,
                'name'       => trim($enrollment->first_name . ' ' . $enrollment->last_name),
                'grade'      => $enrollment->grade_level,
                'session'    => $enrollment->preferred_session,
                'pay_method' => $validated['payMethod'],
                'status'     => $enrollment->status,
            ],
        ]);
    }

    /**
     * Returns the list of required document types still missing for this enrollment.
     * Empty array means all required documents are uploaded.
     */
    public static function missingDocumentTypes(StudentEnrollment $enrollment): array
    {
        $uploadedTypes = EnrollmentRequirement::where('enrollment_id', $enrollment->id)
            ->pluck('document_type')
            ->toArray();

        $required = self::requiredDocumentTypes($enrollment->grade_level, $enrollment->student_type);

        return array_values(array_diff($required, $uploadedTypes));
    }

    /**
     * POST /enrollment/{enrollment}/finalize
     * "Enroll Now" — parent confirms Step 2 is complete and wants to finish
     * enrolling this child. This is the moment status flips from 'draft' to
     * 'pending', which is also the moment the child's card appears on the
     * Home tab (Home only queries non-draft enrollments).
     */
    public function finalize(Request $request, StudentEnrollment $enrollment)
    {
        $parent = Auth::guard('parent')->user();

        if ($enrollment->user_id !== $parent->id) {
            abort(403, 'You do not have permission to finalize this enrollment.');
        }

        if ($enrollment->status !== 'draft') {
            return response()->json([
                'message' => 'This enrollment has already been finalized.',
            ], 422);
        }

        $missing = self::missingDocumentTypes($enrollment);

        if (!empty($missing)) {
            return response()->json([
                'message' => 'Please upload all required documents before enrolling.',
                'missing' => $missing,
            ], 422);
        }

        $enrollment->update(['status' => 'pending']);

        // Generate the tuition installment schedule now that the plan
        // (monthly/quarterly, chosen in Step 1) and grade level are final.
        \App\Models\TuitionPlan::generateForEnrollment($enrollment);

        return response()->json([
            'message'    => 'Enrollment complete! Your child has been added to your Home tab and is awaiting admin review.',
            'enrollment' => [
                'id'     => $enrollment->id,
                'status' => $enrollment->status,
            ],
        ]);
    }

    /**
     * DELETE /enrollment/{enrollment}
     * Parent decided not to enroll this child after all. Only allowed while
     * status is still 'pending' — once the admin has approved/enrolled the
     * child, the parent can no longer delete it themselves.
     * Hard delete: removes the student_enrollment row, its
     * enrollment_requirements rows (via DB cascade), AND the actual files
     * on disk (proof of payment + every uploaded document), so nothing is
     * left orphaned in storage.
     */
    public function destroy(Request $request, StudentEnrollment $enrollment)
    {
        $parent = Auth::guard('parent')->user();

        if ($enrollment->user_id !== $parent->id) {
            abort(403, 'You do not have permission to delete this enrollment.');
        }

        if (!in_array($enrollment->status, ['draft', 'pending'])) {
            return response()->json([
                'message' => 'This enrollment can no longer be deleted because it has already been reviewed by the admin.',
            ], 422);
        }

        // Delete every uploaded requirement file from disk before the DB
        // cascade removes the rows (cascade only cleans up the rows, not
        // the actual files in storage).
        $requirements = EnrollmentRequirement::where('enrollment_id', $enrollment->id)->get();
        foreach ($requirements as $requirement) {
            Storage::disk('public')->delete($requirement->path);
        }

        // Delete the proof-of-payment file too.
        if ($enrollment->proof_of_payment) {
            Storage::disk('public')->delete($enrollment->proof_of_payment);
        }

        $enrollment->delete(); // cascades to enrollment_requirements rows in the DB

        return response()->json([
            'message' => 'Enrollment deleted.',
        ]);
    }

    /**
     * PATCH /admin/applications/{enrollment}/approve
     * Admin approves a pending application. This is the transition that
     * makes the student eligible for sectioning (SectionController::generate
     * only picks up status='approved' students).
     */
    public function approve(Request $request, StudentEnrollment $enrollment)
    {
        if ($enrollment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can be approved.',
            ], 422);
        }

        $enrollment->update(['status' => 'approved']);

        \App\Models\ActivityLog::record(
            $request->user(),
            'Approved Application',
            'Applicant: ' . trim($enrollment->first_name . ' ' . $enrollment->last_name) . ' (' . $enrollment->grade_level . ')',
            'success'
        );

        return response()->json([
            'success' => true,
            'message' => trim($enrollment->first_name . ' ' . $enrollment->last_name) . ' has been approved.',
        ]);
    }
}