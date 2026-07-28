@extends('layout.app')


@section('content')

<style>
  :root {
    --phlc-gold:   #F5C800;
    --phlc-gold2:  #e0b400;
    --phlc-navy:   #1a2a5e;
    --phlc-navy2:  #111d42;
    --phlc-red:    #b91c1c;
    --phlc-green:  #2d6a2d;
  }

  /* NAV */
  .phlc-nav {
    background: var(--phlc-navy);
    border-bottom: 3px solid var(--phlc-gold);
  }
  .phlc-nav .nav-link {
    color: rgba(255,255,255,.75);
    font-size: 13.5px;
    text-decoration: none;
    transition: color .2s;
  }
  .phlc-nav .nav-link:hover { color: var(--phlc-gold); }

  /* HERO */
  .phlc-hero {
    background: linear-gradient(135deg, var(--phlc-navy2) 0%, var(--phlc-navy) 55%, #2d1b1b 100%);
    min-height: 520px;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
  }
  .phlc-hero::before {
    content:'';
    position:absolute;inset:0;
    background:
      radial-gradient(ellipse 600px 400px at 80% 50%, rgba(245,200,0,.08) 0%, transparent 70%),
      radial-gradient(ellipse 400px 300px at 10% 80%, rgba(185,28,28,.1) 0%, transparent 60%);
  }
  /* decorative gold ring behind logo area */
  .phlc-hero::after {
    content:'';
    position:absolute;
    width:480px;height:480px;
    border-radius:50%;
    border:2px solid rgba(245,200,0,.08);
    top:50%;left:64%;
    transform:translate(-50%,-50%);
    pointer-events:none;
  }
  .hero-badge {
    display:inline-block;
    background:rgba(245,200,0,.15);
    border:1px solid rgba(245,200,0,.35);
    color:var(--phlc-gold);
    font-size:11px;font-weight:700;
    letter-spacing:.1em;text-transform:uppercase;
    padding:5px 14px;border-radius:30px;
    margin-bottom:18px;
  }
  .hero-title {
    font-size:2.6rem;font-weight:800;
    color:#fff;line-height:1.2;
  }
  .hero-title span { color:var(--phlc-gold); }
  .hero-sub {
    color:rgba(255,255,255,.7);
    font-size:.95rem;line-height:1.75;
    max-width:560px;
  }
  .hero-cta-primary {
    background:var(--phlc-gold);color:var(--phlc-navy2);
    font-weight:700;font-size:14px;padding:11px 28px;
    border-radius:8px;border:none;text-decoration:none;
    transition:background .2s,transform .15s;display:inline-block;
  }
  .hero-cta-primary:hover { background:var(--phlc-gold2);transform:translateY(-2px);color:var(--phlc-navy2); }
  .hero-cta-outline {
    background:transparent;color:#fff;
    font-weight:600;font-size:14px;padding:10px 26px;
    border-radius:8px;border:1.5px solid rgba(255,255,255,.35);text-decoration:none;
    transition:border-color .2s,background .2s;display:inline-block;
  }
  .hero-cta-outline:hover { border-color:#fff;background:rgba(255,255,255,.07);color:#fff; }

  /* STATS STRIP */
  .stats-strip {
    background:var(--phlc-gold);
  }
  .stat-item { text-align:center;padding:18px 0; }
  .stat-num  { font-size:1.9rem;font-weight:800;color:var(--phlc-navy2);line-height:1; }
  .stat-lbl  { font-size:11.5px;color:var(--phlc-navy);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-top:3px; }

  /* FEATURES */
  .feature-card-phlc {
    background:#fff;
    border:1px solid #e8ecf4;
    border-radius:14px;
    padding:28px 24px;
    transition:box-shadow .25s,transform .25s;
    height:100%;
  }
  .feature-card-phlc:hover {
    box-shadow:0 8px 32px rgba(26,42,94,.12);
    transform:translateY(-4px);
  }
  .feat-icon {
    width:54px;height:54px;border-radius:12px;
    display:flex;align-items:center;justify-content:center;
    font-size:22px;margin-bottom:16px;flex-shrink:0;
  }
  .feat-icon.gold   { background:rgba(245,200,0,.15);color:var(--phlc-navy); }
  .feat-icon.navy   { background:rgba(26,42,94,.1);color:var(--phlc-navy); }
  .feat-icon.red    { background:rgba(185,28,28,.1);color:var(--phlc-red); }

  /* ABOUT */
  .about-section { background:#f8f9fc; }
  .about-accent {
    width:5px;height:48px;background:var(--phlc-gold);border-radius:3px;flex-shrink:0;margin-top:2px;
  }
  .value-chip {
    display:inline-flex;align-items:center;gap:6px;
    background:rgba(26,42,94,.07);color:var(--phlc-navy);
    font-size:12.5px;font-weight:600;padding:6px 14px;border-radius:20px;
  }

  /* ENROLLMENT STEPS */
  .step-num-phlc {
    width:38px;height:38px;border-radius:50%;
    background:var(--phlc-gold);color:var(--phlc-navy2);
    font-weight:800;font-size:16px;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
  }

  /* CONTACT */
  .contact-card {
    background:var(--phlc-navy);
    border-radius:16px;
    color:#fff;
    padding:32px;
  }
  .contact-item { display:flex;align-items:flex-start;gap:14px;margin-bottom:20px; }
  .contact-icon {
    width:40px;height:40px;border-radius:10px;
    background:rgba(245,200,0,.15);color:var(--phlc-gold);
    display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;
  }

  /* HERO WAVE */
  .phlc-wave { display:block;margin-top:-2px; }

  @media(max-width:767px){
    .hero-title{font-size:1.9rem;}
    .phlc-hero{min-height:auto;padding:60px 0 40px;}
  }
</style>
@push('styles')

<!-- NAV -->
<nav class="phlc-nav sticky-top py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <a href="{{ route('landingpage') }}" class="d-flex align-items-center gap-2 text-decoration-none">
      <img src="{{ asset('photo/logo.png') }}" alt="PHLC Logo" style="width:50px;height:50px;border-radius:50%;border:2px solid var(--phlc-gold);background:#fff;padding:2px;">
      <div>
        <div class="fw-bold text-white" style="font-size:14px;line-height:1.2">Premiere Heights Learning Center</div>
        <div style="font-size:10.5px;color:var(--phlc-gold);">Online Enrollment System</div>
      </div>
    </a>
    <div class="d-none d-md-flex gap-4 align-items-center">
      <a href="#features" class="nav-link">Features</a>
      <a href="#about"    class="nav-link">About</a>
      <a href="#contact"  class="nav-link">Contact</a>
    </div>
    <button class="btn d-md-none p-1" type="button" data-bs-toggle="collapse" data-bs-target="#phlcNavMobile" aria-controls="phlcNavMobile" aria-expanded="false" aria-label="Toggle navigation">
      <i class="bi bi-list text-white" style="font-size:26px"></i>
    </button>
  </div>
  <div class="collapse d-md-none" id="phlcNavMobile">
    <div class="container d-flex flex-column gap-1 pt-2 pb-1">
      <a href="#features" class="nav-link py-2">Features</a>
      <a href="#about"    class="nav-link py-2">About</a>
      <a href="#contact"  class="nav-link py-2">Contact</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="phlc-hero">
  <div class="container position-relative" style="z-index:1;padding:80px 0 60px">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <div class="hero-badge"><i class="bi bi-mortarboard-fill me-1"></i> SY 2026 – 2027 Enrollment Now Open</div>
        <h1 class="hero-title mb-3">
          Welcome to<br><span>Premiere Heights</span><br>Learning Center, Inc.
        </h1>
        <p class="hero-sub mb-4">
          Shaping futures, building character, and nurturing excellence since 2008.
          Enroll online today — simple, fast, and secure.
        </p>
       
      </div>
      <div class="col-lg-5 text-center d-none d-lg-block">
          <img src="{{ asset('photo/logo.png') }}" alt="PHLC Logo"
          style="width:280px;height:280px;object-fit:contain;background:#fff;border-radius:50%;padding:12px;
                 filter:drop-shadow(0 8px 32px rgba(245,200,0,.25))">
      </div>
    </div>
  </div>
  <svg class="phlc-wave position-absolute bottom-0 start-0 w-100" viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0,40 C360,0 1080,80 1440,20 L1440,60 L0,60 Z" fill="#F5C800"/>
  </svg>
</section>

<!-- STATS STRIP -->
<section class="stats-strip">
  <div class="container">
    <div class="row g-0">
      <div class="col-6 col-md-3 border-end border-opacity-25 border-warning">
        <div class="stat-item"><div class="stat-num">2008</div><div class="stat-lbl">Est. Year</div></div>
      </div>
      <div class="col-6 col-md-3 border-end border-opacity-25 border-warning">
        <div class="stat-item"><div class="stat-num">K–10</div><div class="stat-lbl">Grade Levels</div></div>
      </div>
      <div class="col-6 col-md-3 border-end border-opacity-25 border-warning">
        <div class="stat-item"><div class="stat-num">100%</div><div class="stat-lbl">Online Process</div></div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-item"><div class="stat-num">SY 26–27</div><div class="stat-lbl">Current Enrollment</div></div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section id="features" class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge" style="background:rgba(26,42,94,.08);border-color:rgba(26,42,94,.2);color:var(--phlc-navy);">What We Offer</div>
      <h2 class="fw-bold mb-2" style="font-size:2rem;color:#1e293b">Key Features</h2>
      <p class="text-muted" style="font-size:14.5px">Everything you need for a smooth enrollment experience</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-card-phlc">
          <div class="feat-icon gold"><i class="bi bi-person-plus-fill"></i></div>
          <h5 class="fw-bold mb-2" style="color:#1e293b">Easy Application</h5>
          <p class="text-muted mb-3" style="font-size:13.5px">Simple online admission form for new students — fill out, upload requirements, and submit in minutes.</p>
          <a href="{{ route('admission') }}" class="text-decoration-none fw-semibold" style="font-size:13.5px;color:var(--phlc-navy)">Apply Now →</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card-phlc">
          <div class="feat-icon navy"><i class="bi bi-person-check-fill"></i></div>
          <h5 class="fw-bold mb-2" style="color:#1e293b">Old Student Re-enrollment</h5>
          <p class="text-muted mb-3" style="font-size:13.5px">Returning students can log in, confirm their enrollment, and choose their preferred class session quickly.</p>
          <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="font-size:13.5px;color:var(--phlc-navy)">Login Now →</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card-phlc">
          <div class="feat-icon red"><i class="bi bi-shield-fill"></i></div>
          <h5 class="fw-bold mb-2" style="color:#1e293b">Admin Dashboard</h5>
          <p class="text-muted mb-3" style="font-size:13.5px">Comprehensive dashboard for administrators to manage students, sections, and enrollment records.</p>
          <a href="{{ route('login') }}?tab=admin" class="text-decoration-none fw-semibold" style="font-size:13.5px;color:var(--phlc-red)">Admin Access →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ENROLLMENT STEPS -->
<section class="py-5" style="background:#f8f9fc">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge" style="background:rgba(26,42,94,.08);border-color:rgba(26,42,94,.2);color:var(--phlc-navy);">How It Works</div>
      <h2 class="fw-bold mb-2" style="font-size:2rem;color:#1e293b">Enrollment Steps</h2>
      <p class="text-muted" style="font-size:14.5px">Three easy steps to complete your enrollment</p>
    </div>
    <div class="row g-4 justify-content-center" style="max-width:860px;margin:0 auto">
      <div class="col-md-4">
        <div class="feature-card-phlc text-center">
          <div class="step-num-phlc mx-auto mb-3">1</div>
          <h6 class="fw-bold mb-2" style="color:#1e293b">Submit Application</h6>
          <p class="text-muted" style="font-size:13px">Fill out the PHLC registration form — Old or New Student — and upload your proof of payment.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card-phlc text-center">
          <div class="step-num-phlc mx-auto mb-3">2</div>
          <h6 class="fw-bold mb-2" style="color:#1e293b">Submit Requirements</h6>
          <p class="text-muted" style="font-size:13px">Upload your PSA Birth Certificate, Form 138, and other required documents through the portal.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card-phlc text-center">
          <div class="step-num-phlc mx-auto mb-3">3</div>
          <h6 class="fw-bold mb-2" style="color:#1e293b">Wait for Confirmation</h6>
          <p class="text-muted" style="font-size:13px">Receive your schedule for uniform fitting and book distribution after admin review and confirmation.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="about-section py-5">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5 text-center">
        <img src="{{ asset('photo/logo.png') }}" alt="PHLC Logo"
          style="width:220px;height:220px;object-fit:contain;background:#fff;border-radius:50%;padding:10px;
                 filter:drop-shadow(0 6px 24px rgba(26,42,94,.18))">
      </div>
      <div class="col-lg-7">
        <div class="hero-badge" style="background:rgba(26,42,94,.08);border-color:rgba(26,42,94,.2);color:var(--phlc-navy);">Our Story</div>
        <h2 class="fw-bold mb-3" style="font-size:2rem;color:#1e293b">About PHLC</h2>
        <div class="d-flex gap-3 mb-3">
          <div class="about-accent"></div>
          <p class="text-muted mb-0" style="font-size:14px;line-height:1.8">
            Premiere Heights Learning Center, Inc. (PHLC) has been committed to delivering quality education since 2008.
            We offer a nurturing environment where students from Kinder to Grade 10 are empowered to grow academically,
            morally, and socially — ready to face the challenges of the future.
          </p>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <span class="value-chip"><i class="bi bi-star-fill text-warning me-1"></i> Excellence</span>
          <span class="value-chip"><i class="bi bi-heart-fill" style="color:var(--phlc-red)" ></i>&nbsp; Values-based</span>
          <span class="value-chip"><i class="bi bi-people-fill" style="color:var(--phlc-green)"></i>&nbsp; Community</span>
          <span class="value-chip"><i class="bi bi-lightbulb-fill text-warning me-1"></i> Innovation</span>
          <span class="value-chip"><i class="bi bi-shield-fill" style="color:var(--phlc-navy)"></i>&nbsp; Integrity</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT -->
<section id="contact" class="py-5 bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <div class="hero-badge" style="background:rgba(26,42,94,.08);border-color:rgba(26,42,94,.2);color:var(--phlc-navy);">Get In Touch</div>
      <h2 class="fw-bold mb-2" style="font-size:2rem;color:#1e293b">Contact Us</h2>
      <p class="text-muted" style="font-size:14.5px">Reach out to us for enrollment inquiries</p>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-lg-8">
        <div class="contact-card">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                <div>
                  <div class="fw-semibold mb-1" style="font-size:13px;color:var(--phlc-gold)">Address</div>
                  <div style="font-size:13.5px;color:rgba(255,255,255,.8)">Premiere Heights Learning Center, Inc.<br>Blk 3 Lot 29 & 31 Justice Corner Gladness St, Cityland Carmona City Cavite</div>
                </div>
              </div>
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                <div>
                  <div class="fw-semibold mb-1" style="font-size:13px;color:var(--phlc-gold)">Phone</div>
                  <div style="font-size:13.5px;color:rgba(255,255,255,.8)">Contact school for details</div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                <div>
                  <div class="fw-semibold mb-1" style="font-size:13px;color:var(--phlc-gold)">Email</div>
                  <div style="font-size:13.5px;color:rgba(255,255,255,.8)">Contact school for details</div>
                </div>
              </div>
              <div class="contact-item">
                <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
                <div>
                  <div class="fw-semibold mb-1" style="font-size:13px;color:var(--phlc-gold)">Office Hours</div>
                  <div style="font-size:13.5px;color:rgba(255,255,255,.8)">Monday – Friday<br>8:00 AM – 5:00 PM</div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection