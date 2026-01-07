@extends('user-pages.layouts.app')

@section('content')
    <section id="ed-breadcrumb" class="ed-breadcrumb-sec" data-background="{{ asset('assets/user/img/bg/bread-bg.jpg') }}">
        <div class="container">
            <div class="ed-breadcrumb-content">
                <div class="ed-breadcrumb-text text-center headline ul-li">
                    <h2 class="bread_title">Contact Us</h2>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li>Contact Us </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="ed-cp-cta" class="ed-cp-cta-sec pt-130 pb-100">
        <div class="container">
            <div class="ed-cp-cta-content">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="ed-cp-cta-item d-flex">
                            <div class="item-icon d-flex align-items-center justify-content-center">
                                <img src="{{ asset('assets/user/img/icon/cpi1.svg') }}" alt="">
                            </div>
                            <div class="item-text headline pera-content">
                                <h3>Location</h3>
                                <p>D316 / Sherkhan Building Infront Of Urdu Academy, Bibhuti Khand, Gomti Nagar Lucknow - 226010</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="ed-cp-cta-item d-flex">
                            <div class="item-icon d-flex align-items-center justify-content-center">
                                <img src="{{ asset('assets/user/img/icon/cpi2.svg') }}" alt="">
                            </div>
                            <div class="item-text headline pera-content">
                                <h3>Phone Number</h3>
                                <p>+91 7007846073</p>
                                <p>Available: Mon-Sat (9 AM - 8 PM)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="ed-cp-cta-item d-flex">
                            <div class="item-icon d-flex align-items-center justify-content-center">
                                <img src="{{ asset('assets/user/img/icon/cpi3.svg') }}" alt="">
                            </div>
                            <div class="item-text headline pera-content">
                                <h3>Email Address</h3>
                                <p>info@ytbook.in</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="ed-cp-form" class="ed-cp-form-sec position-relative">
        <span class="ed-cp-bg position-absolute"><img src="{{ asset('assets/user/img/about/cp4.png') }}" alt=""></span>
        <div class="container">
            <div class="ed-cp-form-content position-relative d-flex justify-content-end">
                <div class="ed-cp-form position-relative">
                    <div class="gt-client-review-form cp_ver mt-40">
                        <h3>Send Us a Message</h3>
                        <form action="#" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" name="name" placeholder="Full Name" required>
                                </div>
                                <div class="col-md-12">
                                    <input type="email" name="email" placeholder="Email Address" required>
                                </div>
                                <div class="col-md-12">
                                    <div class="ed-cp-select">
                                        <select name="course_interest" required>
                                            <option value="">Select Course Interest</option>
                                            <option value="railway">Railway Exams</option>
                                            <option value="ssc">SSC Preparation</option>
                                            <option value="banking">Banking Exams</option>
                                            <option value="upsc">UPSC Coaching</option>
                                            <option value="iti">ITI Courses</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <textarea name="message" rows="3" placeholder="Your Message" required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="text-white">Submit Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="ed-cp-faq" class="ed-cp-faq-sec position-relative pt-120 pb-100">
        <div class="container">
            <div class="ed-sec-title headline text-center pera-content">
                <div class="subtitle wow fadeInRight" data-wow-delay="100ms" data-wow-duration="1000ms">Common Questions
                </div>
                <h2 class="sec_title ed-sec-tt-anim ed-has-anim">Frequently Asked Questions</h2>
            </div>
            <div class="ed-evt-text mt-30">
                <div class="ed-faq-content">
                    <div class="ed-faq-accordion">
                        <div class="accordion" id="accordionExample_31">
                            <div class="accordion-item faq_active wow fadeInUp" data-wow-delay="300ms"
                                data-wow-duration="1000ms">
                                <h2 class="accordion-header" id="heading10">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
                                        <span><img src="{{ asset('assets/user/img/icon/ic9.png') }}" alt=""> How do I enroll in a course?</span>
                                    </button>
                                </h2>
                                <div id="collapse10" class="accordion-collapse collapse show" aria-labelledby="heading10"
                                    data-bs-parent="#accordionExample_31">
                                    <div class="accordion-body ">
                                        <div class="bi-faq-text pera-content">
                                            Simply sign up on YTBOOK, browse our course catalog, select your target exam (Railway, SSC, Banking, UPSC, or ITI), and click "Enroll Now." You can choose from free courses or premium plans with live classes and personalized mentoring.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wow fadeInUp" data-wow-delay="400ms" data-wow-duration="1000ms">
                                <h2 class="accordion-header" id="heading13">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse13" aria-expanded="false" aria-controls="collapse13">
                                        <span><img src="{{ asset('assets/user/img/icon/ic9.png') }}" alt=""> What exams does YTBOOK cover?</span>
                                    </button>
                                </h2>
                                <div id="collapse13" class="accordion-collapse collapse" aria-labelledby="heading13"
                                    data-bs-parent="#accordionExample_31">
                                    <div class="accordion-body ">
                                        <div class="bi-faq-text pera-content">
                                            YTBOOK provides comprehensive preparation for all major Indian government exams including Railway (RRB NTPC, Group D), SSC (CGL, CHSL, MTS), Banking (IBPS PO, SBI Clerk), UPSC (Prelims & Mains), and ITI trade courses with practical training.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wow fadeInUp" data-wow-delay="500ms" data-wow-duration="1000ms">
                                <h2 class="accordion-header" id="heading14">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse14" aria-expanded="false" aria-controls="collapse14">
                                        <span><img src="{{ asset('assets/user/img/icon/ic9.png') }}" alt=""> Are the classes live or recorded?</span>
                                    </button>
                                </h2>
                                <div id="collapse14" class="accordion-collapse collapse" aria-labelledby="heading14"
                                    data-bs-parent="#accordionExample_31">
                                    <div class="accordion-body ">
                                        <div class="bi-faq-text pera-content">
                                            YTBOOK offers both live interactive classes and recorded sessions. Live classes allow you to ask questions in real-time, while recorded videos let you study at your own pace. All premium students get access to both formats for maximum flexibility.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item  wow fadeInUp" data-wow-delay="300ms" data-wow-duration="1000ms">
                                <h2 class="accordion-header" id="heading88">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse88" aria-expanded="true" aria-controls="collapse88">
                                        <span><img src="{{ asset('assets/user/img/icon/ic9.png') }}" alt="">Do you provide study materials and mock tests?</span>
                                    </button>
                                </h2>
                                <div id="collapse88" class="accordion-collapse collapse" aria-labelledby="heading88"
                                    data-bs-parent="#accordionExample_31">
                                    <div class="accordion-body">
                                        <div class="bi-faq-text pera-content">
                                            Yes! All enrolled students receive comprehensive study materials including PDFs, practice question banks, daily quizzes, and full-length mock tests designed to simulate actual exam patterns. Our materials are regularly updated based on latest exam trends.
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