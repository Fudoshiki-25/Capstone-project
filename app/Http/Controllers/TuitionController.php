<?php

namespace App\Http\Controllers;

use App\Models\GradeTuitionFee;
use App\Models\StudentEnrollment;
use App\Models\TuitionPayment;
use App\Support\ImageUploadStorer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TuitionController extends Controller
{
    /**
     * GET /tuition?enrollment_id=xxx
     * Parent-facing: returns the tuition plan + full installment schedule
     * for one of their own children.
     */
    public function show(Request $request)
    {
        $request->validate(['enrollment_id' => 'required|integer']);

        $parent = Auth::guard('parent')->user();

        $enrollment = StudentEnrollment::where('id', $request->query('enrollment_id'))
            ->where('user_id', $parent->id)
            ->firstOrFail();

        if (! in_array($enrollment->status, ['approved', 'enrolled'], true)) {
            return response()->json([
                'message' => 'Tuition & Payments unlocks once this child\'s enrollment is approved.',
            ], 403);
        }

        $plan = $enrollment->tuitionPlan()->with('payments')->first();

        if (! $plan) {
            return response()->json(['plan' => null, 'payments' => []]);
        }

        return response()->json([
            'plan'     => [
                'plan_type'    => $plan->plan_type,
                'total_amount' => (float) $plan->total_amount,
                'down_payment' => (float) $plan->down_payment,
            ],
            'downPayment' => [
                'amount'         => (float) $plan->down_payment,
                'payment_method' => self::methodLabel($enrollment->payment_method),
                'submitted_at'   => $enrollment->created_at->format('M j, Y g:i A'),
            ],
            'payments' => $plan->payments->sortBy('installment_number')->values()->map(fn ($p) => [
                'id'                  => $p->id,
                'installment_number'  => $p->installment_number,
                'amount_due'          => (float) $p->amount_due,
                'due_date'            => $p->due_date->format('M j, Y'),
                'status'              => $p->status,
                'proof_of_payment'    => $p->proof_of_payment ? asset('storage/' . $p->proof_of_payment) : null,
                'payment_method'      => self::methodLabel($p->payment_method),
                'submitted_at'        => $p->submitted_at?->format('M j, Y g:i A'),
                'verified_at'         => $p->paid_at?->format('M j, Y g:i A'),
                'feedback'            => $p->feedback,
            ]),
        ]);
    }

    /**
     * GET /tuition/history
     * Parent-facing: a single combined payment history across ALL of this
     * parent's children — down payments + every installment ever submitted,
     * most recent first.
     */
    public function history(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        $enrollments = StudentEnrollment::where('user_id', $parent->id)
            ->whereIn('status', ['approved', 'enrolled'])
            ->with('tuitionPlan.payments')
            ->get();

        $rows = collect();

        foreach ($enrollments as $enrollment) {
            $childName = trim($enrollment->first_name . ' ' . $enrollment->last_name);
            $plan = $enrollment->tuitionPlan;

            if (! $plan) {
                continue;
            }

            // Down payment — captured at enrollment, treated as immediately paid.
            $rows->push([
                'child'          => $childName,
                'label'          => 'Upon Enrollment (Down Payment)',
                'amount'         => (float) $plan->down_payment,
                'payment_method' => self::methodLabel($enrollment->payment_method),
                'submitted_at'   => $enrollment->created_at->format('M j, Y g:i A'),
                'verified_at'    => $enrollment->created_at->format('M j, Y g:i A'),
                'status'         => 'paid',
            ]);

            foreach ($plan->payments as $p) {
                if (! $p->submitted_at) {
                    continue; // never submitted yet — nothing to show in history
                }

                $rows->push([
                    'child'          => $childName,
                    'label'          => 'Installment ' . $p->installment_number,
                    'amount'         => (float) $p->amount_due,
                    'payment_method' => self::methodLabel($p->payment_method),
                    'submitted_at'   => $p->submitted_at->format('M j, Y g:i A'),
                    'verified_at'    => $p->paid_at?->format('M j, Y g:i A'),
                    'status'         => $p->status,
                ]);
            }
        }

        return response()->json([
            'history' => $rows->sortByDesc('submitted_at')->values(),
        ]);
    }

    private static function methodLabel(?string $method): ?string
    {
        return match ($method) {
            'gcash'         => 'GCash',
            'maya'          => 'Maya',
            'bank_transfer' => 'Bank Transfer',
            'cash'          => 'Cash',
            default         => $method,
        };
    }

    /**
     * POST /tuition/payments/{payment}/upload-proof
     * Parent uploads proof of payment for one installment. Moves it from
     * 'unpaid' (or 'needs_resubmit'-style flagged 'unpaid' with feedback)
     * to 'pending', awaiting admin verification.
     */
    public function uploadProof(Request $request, TuitionPayment $payment)
    {
        $request->validate([
            'file'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_method' => 'required|in:gcash,maya,bank_transfer,cash',
        ]);

        $parent = Auth::guard('parent')->user();

        if ($payment->plan->enrollment->user_id !== $parent->id) {
            abort(403, 'You do not have permission to update this payment.');
        }

        if ($payment->status === 'paid') {
            return response()->json([
                'message' => 'This installment has already been verified and can no longer be changed.',
            ], 422);
        }

        $path = ImageUploadStorer::store(
            $request->file('file'),
            'tuition_payments/' . $parent->id . '/' . $payment->tuition_plan_id,
            'public'
        );

        $payment->update([
            'proof_of_payment' => $path,
            'payment_method'   => $request->input('payment_method'),
            'submitted_at'     => now(),
            'status'           => 'pending',
            'feedback'         => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proof of payment submitted. Awaiting admin verification.',
        ]);
    }

    /**
     * POST /admin/tuition/payments/{payment}/verify
     * Admin confirms a pending payment as received.
     */
    public function verify(Request $request, TuitionPayment $payment)
    {
        if (! $request->user()->canManageGrade($payment->plan->enrollment->grade_level)) {
            abort(403, 'You are not assigned to manage this student\'s grade level.');
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Only a submitted (pending) payment can be verified.',
            ], 422);
        }

        $payment->update([
            'status'      => 'paid',
            'paid_at'     => now(),
            'verified_by' => $request->user()->id,
            'feedback'    => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as paid.',
        ]);
    }

    /**
     * POST /admin/tuition/payments/{payment}/reject
     * Admin flags a submitted proof as wrong/unclear — sends it back to the
     * parent as 'unpaid' with a note, same pattern as requirement resubmits.
     */
    public function reject(Request $request, TuitionPayment $payment)
    {
        $request->validate(['feedback' => 'required|string|max:500']);

        if (! $request->user()->canManageGrade($payment->plan->enrollment->grade_level)) {
            abort(403, 'You are not assigned to manage this student\'s grade level.');
        }

        if ($payment->status !== 'pending') {
            return response()->json([
                'message' => 'Only a submitted (pending) payment can be rejected.',
            ], 422);
        }

        $payment->update([
            'status'      => 'unpaid',
            'feedback'    => $request->input('feedback'),
            'paid_at'     => null,
            'verified_by' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parent has been notified to resubmit this payment.',
        ]);
    }

    /**
     * PUT /superadmin/tuition/grade-fees
     * Superadmin sets the annual tuition rate per grade level, plus the
     * flat enrollment_fee (down payment) that applies to every grade.
     */
    public function updateGradeFees(Request $request)
    {
        $request->validate([
            'fees'                 => 'required|array',
            'fees.*.grade_level'   => 'required|string',
            'fees.*.annual_amount' => 'required|numeric|min:0',
            'enrollment_fee'       => 'nullable|numeric|min:0',
        ]);

        if ($request->filled('enrollment_fee')) {
            \App\Models\EnrollmentPeriod::current()?->update([
                'enrollment_fee' => $request->input('enrollment_fee'),
            ]);
        }

        foreach ($request->input('fees') as $fee) {
            GradeTuitionFee::updateOrCreate(
                ['grade_level' => $fee['grade_level']],
                ['annual_amount' => $fee['annual_amount']]
            );
        }

        return response()->json(['success' => true, 'message' => 'Tuition rates updated.']);
    }
}
