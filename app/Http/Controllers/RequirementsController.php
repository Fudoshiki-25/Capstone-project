<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentRequirement;
use App\Models\StudentEnrollment;
use App\Support\ImageUploadStorer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirementsController extends Controller
{
    private const DOC_LABELS = [
        'birth_certificate'   => 'PSA Birth Certificate',
        'report_card'         => 'Form 138 (Report Card)',
        'good_moral'          => 'Good Moral Certificate',
        'medical_certificate' => 'Medical Certificate / Doctor\'s Assessment',
    ];

    /**
     * Confirm the enrollment exists and belongs to the logged-in parent.
     * Aborts with 403/404 otherwise.
     */
    private function authorizedEnrollment(int $enrollmentId): StudentEnrollment
    {
        $parent = Auth::guard('parent')->user();

        $enrollment = StudentEnrollment::where('id', $enrollmentId)
            ->where('user_id', $parent->id)
            ->firstOrFail();

        return $enrollment;
    }

    /**
     * GET /requirements?enrollment_id=xxx
     * Returns uploaded requirements for an enrollment (keyed by document_type).
     */
    public function index(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|integer',
        ]);

        $enrollment = $this->authorizedEnrollment((int) $request->query('enrollment_id'));

        $docs = EnrollmentRequirement::where('enrollment_id', $enrollment->id)->get();

        $requirements = [];
        foreach ($docs as $doc) {
            $requirements[$doc->document_type] = [
                'path'           => $doc->path,
                'url'            => $doc->url,
                'document_label' => $doc->document_label,
                'uploaded_at'    => $doc->updated_at->toDateTimeString(),
                'status'         => $doc->status,
                'feedback'       => $doc->feedback,
            ];
        }

        return response()->json([
            'requirements' => $requirements,
        ]);
    }

    /**
     * POST /requirements/upload
     * Handles document file upload and saves/updates the row in enrollment_requirements.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'enrollment_id' => 'required|integer',
            'file'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        $enrollment = $this->authorizedEnrollment((int) $request->input('enrollment_id'));

        $docType = $request->input('document_type');

        // Store the file (downscaled if it's a photo — document scans stay
        // legible at 1600px wide, but a phone-camera photo doesn't need to
        // be saved at full multi-megapixel resolution).
        $path = ImageUploadStorer::store(
            $request->file('file'),
            'requirements/' . $enrollment->user_id . '/' . $enrollment->id,
            'public'
        );

        $docLabel = self::DOC_LABELS[$docType] ?? ucfirst(str_replace('_', ' ', $docType));

        // One row per (enrollment_id, document_type) — re-uploading replaces the row
        // and resets it back to 'pending' so a resubmitted file is reviewed again.
        $requirement = EnrollmentRequirement::updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'document_type' => $docType,
            ],
            [
                'document_label' => $docLabel,
                'path'           => $path,
                'status'         => 'pending',
                'feedback'       => null,
                'reviewed_at'    => null,
            ]
        );

        return response()->json([
            'record' => [
                'document_type'  => $requirement->document_type,
                'document_label' => $requirement->document_label,
                'url'            => $requirement->url,
            ],
        ]);
    }

    /**
     * PATCH /admin/requirements/{requirement}/flag-resubmit
     * Admin flags a specific uploaded document (e.g. a blurry photo) so the
     * parent is prompted to re-upload it, with a note on what was wrong.
     */
    public function flagResubmit(Request $request, EnrollmentRequirement $requirement)
    {
        $request->validate([
            'feedback' => 'required|string|max:500',
        ]);

        $requirement->update([
            'status'      => 'needs_resubmit',
            'feedback'    => $request->input('feedback'),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parent has been notified to resubmit this document.',
        ]);
    }
}