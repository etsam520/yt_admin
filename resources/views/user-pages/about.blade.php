@extends('user-pages.layouts.app')

@section('content')
    <section id="ed-breadcrumb" class="ed-breadcrumb-sec" data-background="{{ asset('assets/user/img/bg/bread-bg.jpg') }}" style="background-color: #00000063;
    background-blend-mode: darken;">
        <div class="container">
            <div class="ed-breadcrumb-content">
                <div class="ed-breadcrumb-text text-center headline ul-li">
                    <h2 class="bread_title">About Us</h2>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li>About Us </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="ed-about-1" class="ed-about-sec-1 pt-30 pb-85">
        <div class="container">
            <div class="ed-about-content d-flex align-items-center">
                <div class="ed-ab-img1 ed_left_img">
                    <img src="{{ asset('assets/user/img/about/ab1.png') }}" alt="">
                </div>
                <div class="ed-ab-text1">
                    <div class="ed-sec-title ed-text headline pera-content">
                        <div class="rate-slug wow fadeInRight" data-wow-delay="100ms" data-wow-duration="1000ms"><i
                                class="fa-solid fa-star"></i> India's Leading Exam Preparation Platform</div>
                        <h2 class="sec_title ed-sec-tt-anim ed-has-anim">Achieve Your Dream Job</h2>
                        <p>YTBOOK is dedicated to transforming the future of competitive exam preparation in India. We connect ambitious students with expert teachers, providing comprehensive courses for Railway, SSC, Banking, UPSC, and ITI exams through our innovative online learning platform.
                        </p>
                    </div>
                    <div class="ed-ab-review-wrap mt-25 mb-40 d-flex align-items-center justify-content-between">
                        <div class="ed-ab-review headline ul-li">
                            <h3>4.8+</h3>
                            <ul>
                                <li><i class="fa-solid fa-star"></i></li>
                                <li><i class="fa-solid fa-star"></i></li>
                                <li><i class="fa-solid fa-star"></i></li>
                                <li><i class="fa-solid fa-star"></i></li>
                                <li><i class="fa-solid fa-star"></i></li>
                            </ul>
                            <span>Student Reviews</span>
                        </div>
                        <div class="ed-ab-review-list ul-li-block">
                            <ul>
                                <li class="top_view"><i class="fa-solid fa-circle-check"></i> Live Interactive Classes</li>
                                <li class="top_view"><i class="fa-solid fa-circle-check"></i> Expert Faculty Guidance
                                </li>
                                <li class="top_view"><i class="fa-solid fa-circle-check"></i> Mock Tests & Study Material
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="ed-btn-1 btn-spin">
                        <a href="{{ route('contact') }}">Start Learning Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="ed-wc5" class="ed-wc5-sec pb-140 position-relative">
        <div class="container">
            <div class="ed-wc5-img-text d-flex">
                <div class="ed-wc5-text">
                    <div class="ed-sec-title headline-5 pera-content">
                        <div class="subtitle wow fadeInRight" data-wow-delay="300ms" data-wow-duration="1500ms">Why Choose
                            YTBOOK</div>
                        <h2 class="sec_title ed-sec-tt-anim ed-has-anim">Your Partner in Government Exam Success</h2>
                        <p>Join thousands of students who have achieved their dream government jobs through YTBOOK's comprehensive preparation programs and expert guidance.</p>
                    </div>
                    <div class="ed-wc5-item-list mt-30">
                        <div class="ed-wc5-item top_view d-flex">
                            <div class="item-icon d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/user/img/icon/ic37.svg') }}" alt="">
                            </div>
                            <div class="item-text headline-5 pera-content">
                                <h3>Expert Faculty Team</h3>
                                <p>Learn from experienced teachers who have successfully trained thousands of students for government exams across India.
                                </p>
                            </div>
                        </div>
                        <div class="ed-wc5-item top_view d-flex">
                            <div class="item-icon d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/user/img/icon/ic38.svg') }}" alt="">
                            </div>
                            <div class="item-text headline-5 pera-content">
                                <h3>Comprehensive Study Material</h3>
                                <p>Access extensive question banks, daily quizzes, mock tests, and exam-specific study materials designed by experts.
                                </p>
                            </div>
                        </div>
                        <div class="ed-wc5-item top_view d-flex">
                            <div class="item-icon d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/user/img/icon/ic39.svg') }}" alt="">
                            </div>
                            <div class="item-text headline-5 pera-content">
                                <h3>24/7 Learning Support</h3>
                                <p>Get instant doubt resolution, personalized guidance, and continuous support throughout your exam preparation journey.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ed-wc5-img-wrap position-relative">
                    <div class="ed-wc5-count headline-5 pera-content d-flex align-items-center">
                        <h3><span class="counter">50</span><sup>K+</sup></h3>
                        <p>Successful Students</p>
                    </div>
                    <div class="ed-wc5-img-wrapper">
                        <div class="item-img-1 d-flex justify-content-end">
                            <div class="inner-img ed-image-appear3">
                                <span><img class="ed-img-rvl_3" src="{{ asset('assets/user/img/about/wc4.jpg') }}" alt=""></span>
                            </div>
                        </div>
                        <div class="item-img-2 d-flex ">
                            <div class="inner-img ed-image-appear2">
                                <span><img class="ed-img-rvl_2" src="{{ asset('assets/user/img/about/wc3.jpg') }}" alt=""></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection