@extends('layout.app')

@section('content')

@php
    // The /parent route is protected by the "parent" guard (see routes/web.php),
    // not the default "web" guard — so we must fetch the user from that guard
    // explicitly, the same way RequirementsController and Authcontroller do.
    $user = Auth::guard('parent')->user();
    $initials = strtoupper(substr($user->first_name, 0, 1)) . strtoupper(substr($user->last_name, 0, 1));
    $fullName = $user->first_name . ' ' . $user->last_name;

    // Real Home tab cards: everything EXCEPT drafts. A 'draft' means Step 1
    // is saved but the parent hasn't finished Step 2 + clicked "Enroll Now"
    // yet, so it isn't a finished enrollment and shouldn't show as a card.
    $enrolledChildren = \App\Models\StudentEnrollment::where('user_id', $user->id)
        ->where('status', '!=', 'draft')
        ->orderBy('grade_level')
        ->orderBy('last_name')
        ->get();

    // Drafts: Step 1 saved, Step 2 not finished / "Enroll Now" not yet
    // clicked. These do NOT appear as Home tab cards — instead they show as
    // a separate "Continue Enrollment" banner so the parent can resume.
    $draftChildren = \App\Models\StudentEnrollment::where('user_id', $user->id)
        ->where('status', 'draft')
        ->orderBy('created_at')
        ->get();

    // Children who have cleared admin review — 'approved' (awaiting sectioning)
    // and 'enrolled' (sectioned) both count, since 'enrolled' is further along
    // the same pipeline. Gates Tuition & Payments specifically — money should
    // wait for actual approval, not just a complete document set.
    $approvedChildren = $enrolledChildren->whereIn('status', ['approved', 'enrolled'])->values();
    $hasEnrolledChildren = $approvedChildren->isNotEmpty();

    // Children whose profile can be viewed under My Children — every
    // required Step 2 document for THEIR grade/student-type is uploaded.
    // This is deliberately earlier than admin approval (matches the
    // "Enroll Now" completion rule), so it's a separate, looser gate than
    // $hasEnrolledChildren above — shared here so the sidebar nav and the
    // panel itself never disagree about which children are unlocked.
    $unlockedChildren = $enrolledChildren->filter(function ($child) {
        $required = \App\Http\Controllers\EnrollmentController::requiredDocumentTypes($child->grade_level, $child->student_type);
        $uploadedTypes = \App\Models\EnrollmentRequirement::where('enrollment_id', $child->id)
            ->pluck('document_type')
            ->toArray();
        return empty(array_diff($required, $uploadedTypes));
    })->values();
    $hasUnlockedChildProfile = $unlockedChildren->isNotEmpty();
@endphp

@include('parent.partials.topbar')

<div class="d-flex" style="min-height:calc(100vh - 62px)">

  {{-- OFFCANVAS SIDEBAR (mobile) --}}
  @include('parent.partials.sidebar-mobile')

  {{-- STATIC SIDEBAR (desktop) --}}
  @include('parent.partials.sidebar-desktop')

  <!-- MAIN CONTENT -->
  <div class="flex-grow-1 overflow-y-auto" style="background:#f1f5f9">

    @include('parent.panels.panel-home')

    @include('parent.panels.panel-my-children')

    @include('parent.panels.panel-tuition-payments')

    @include('parent.panels.panel-my-profile')

  </div><!-- /main -->

  @include('parent.modals.enrollment-announcement')
</div>

@endsection

@include('parent.partials.styles')

@include('parent.scripts.navigation')
@include('parent.scripts.requirements')
@include('parent.scripts.toast')
@include('parent.scripts.enrollment-form')



@stack('scripts')



@include('parent.scripts.announcement')