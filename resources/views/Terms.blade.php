@extends('layout.app')
@section('content')
<div class="container py-5">
    <h1>Terms of Use &amp; Privacy Policy</h1>
    <p class="text-muted">
        Official School Portal Guidelines &amp; Data Privacy Notice<br>
        Version: {{ config('app.terms_version', '1.0') }} — Effective August 2026
    </p>
    <p class="text-muted">
        Scope: Applicable to all students, parents, and legal guardians accessing
        the official school portal system.
    </p>

    <h2>1. Acceptance of Terms</h2>
    <p>By accessing, browsing, or logging into this school portal system, you
    signify your explicit agreement to abide by these Terms of Use and our
    Data Privacy policies. If you do not agree with any part of these terms,
    you must immediately discontinue use of the portal.</p>

    <h2>2. Authorized User Access (Strictly for Our School)</h2>
    <p>Access to this system is an exclusive privilege granted solely to
    authorized individuals belonging to our school community:</p>
    <ul>
        <li><strong>Eligible Users:</strong> Only verified parents, legal
        guardians, and officially enrolled students of our school are
        permitted to create accounts and access the portal.</li>
        <li><strong>Account Security:</strong> Users are fully responsible for
        maintaining the confidentiality of their login credentials. Any
        activity conducted under your account will be deemed your
        responsibility.</li>
        <li><strong>Prohibition:</strong> Unauthorized access, sharing of
        credentials with third parties, or attempts to breach system security
        are strictly prohibited and will result in administrative
        disciplinary actions.</li>
    </ul>

    <h2>3. Parental Consent for Minors</h2>
    <p>Where enrollment or account activity concerns a minor student, the
    parent or legal guardian completing registration or uploading information
    does so <strong>on the student's behalf</strong>, and by doing so provides
    consent for the collection and processing of that student's personal data
    as described in these Terms. The school treats the parent/guardian, not
    the minor, as the consenting party for all data submitted through this
    portal.</p>

    <h2>4. Payment Disclaimer &amp; No Direct Transactions</h2>
    <p>To ensure clarity regarding financial operations within this system,
    users acknowledge and agree that:</p>
    <div class="alert alert-warning">
        <strong>Important Financial Notice:</strong> This web portal cannot
        process money, credit card transactions, or online fund transfers
        directly. The system is completely disconnected and has no API
        integrations with GCash, Maya, online banking platforms, or any
        third-party payment gateways.
    </div>
    <p>All financial obligations, tuition fees, and school contributions must
    be settled exclusively through our designated official external payment
    channels (e.g., bank over-the-counter deposits, accredited partner
    centers, or physical school cashier).</p>

    <h2>5. Upload-Only Purpose</h2>
    <p>The core function of this platform regarding financial records is
    strictly administrative verification:</p>
    <ul>
        <li>Users must complete payments through authorized external channels
        prior to using the portal.</li>
        <li>The system is utilized solely for uploading, storing, and
        reviewing digital proof of payment (such as scanned deposit slips or
        official transaction screenshots).</li>
        <li>Uploading fraudulent, altered, or invalid transaction receipts is
        strictly illegal and subject to school sanctions and legal
        repercussions.</li>
    </ul>

    <h2>6. Data Privacy Act of 2012 Compliance</h2>
    <p>We value your fundamental right to privacy. In compliance with
    Republic Act No. 10173, otherwise known as the Data Privacy Act (DPA) of
    2012, and its implementing rules and regulations:</p>
    <ul>
        <li><strong>Collection of Information:</strong> We collect personal
        data, student records, contact details, and payment documentation
        strictly for legitimate institutional, academic, and administrative
        purposes.</li>
        <li><strong>Processing &amp; Storage:</strong> All data provided
        through this portal is processed lawfully and stored securely within
        restricted databases equipped with appropriate technical and
        organizational security measures against unauthorized access,
        disclosure, or alteration.</li>
        <li><strong>Data Retention:</strong> Student and parent/guardian
        records are retained for the duration of the student's enrollment at
        the school, plus a period thereafter as required for academic
        record-keeping and reporting to relevant education authorities.
        Payment verification documents are retained only as long as necessary
        for financial audit and reconciliation purposes.</li>
        <li><strong>User Consent:</strong> By logging into and using this
        system, you provide explicit consent for the school to collect,
        process, retain, and review your submitted information and documents
        for valid educational administration.</li>
        <li><strong>Data Rights:</strong> Under the DPA, data subjects retain
        rights to access, correct, or object to the processing of their
        personal data by contacting our designated Data Protection Officer
        (DPO).</li>
        <li><strong>Data Protection Officer Contact:</strong> Inquiries,
        concerns, or requests regarding your personal data may be directed to
        the school's Data Protection Officer at
        <strong>[DPO name / email / phone — to be filled in by school
        administration]</strong>.</li>
    </ul>

    <h2>7. Data Breach Notification</h2>
    <p>In the event of a personal data breach that poses a real risk of
    serious harm to affected students, parents, or guardians, the school will
    notify the National Privacy Commission and affected data subjects in
    accordance with the timelines and procedures required under RA 10173 and
    its implementing rules.</p>

    <h2>8. Limitation of Liability</h2>
    <p>The school administration shall not be held liable for system
    downtimes caused by external internet service providers, user error
    during receipt uploads, or financial discrepancies arising from errors
    made during external channel transactions.</p>

    <h2>9. Modifications to Terms</h2>
    <p>The school reserves the right to amend, update, or modify these Terms
    of Use at any time. Continued use of the portal following any adjustments
    constitutes your acceptance of the revised terms.</p>

    <hr>
    <p class="text-muted small">
        &copy; School Administration Portal. All Rights Reserved. Compliant
        with the Data Privacy Act of 2012 (RA 10173).
    </p>
</div>
@endsection