@extends('user-pages.layouts.app')

@section('content')
    <section id="ed-breadcrumb" class="ed-breadcrumb-sec" data-background="{{ asset('assets/user/img/bg/bread-bg.jpg') }}">
        <div class="container">
            <div class="ed-breadcrumb-content">
                <div class="ed-breadcrumb-text text-center headline ul-li">
                    <h2 class="bread_title">Services</h2>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li>Services </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="ed-prg6" class="ed-prg6-sec position-relative">
        <span class="ed-prg6-shape1 right_view position-absolute"><img src="{{ asset('assets/user/img/shape/cap.png') }}"
                alt=""></span>
        <span class="ed-prg6-shape2 left_view position-absolute"><img src="{{ asset('assets/user/img/shape/book7.png') }}"
                alt=""></span>
        <div class="ed-prg6-top-shape position-absolute">
            <svg width="1920" height="568" viewBox="0 0 1920 568" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_f_261_209)">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0 110.248V110H0.0217181C0.69003 105.895 16.6561 89.339 30.3979 89.339H90.2182C93.8256 89.339 101.778 90.8968 108.942 92.3003C116.379 93.757 122.966 95.0475 122.966 94.2574C122.966 93.7702 127.959 93.834 134.808 93.9215C149.771 94.1127 173.594 94.4172 173.594 89.339C173.594 81.9361 226.556 78.8961 227.893 78.8961C227.931 78.8961 228.046 78.8957 228.233 78.895C234.586 78.872 324.477 78.5462 350.865 83.8643C378.03 89.339 385.024 83.8643 385.024 83.8643L456.461 78.8961L473.838 87.4783L504.58 83.8643L665.646 65.2889C665.646 65.2889 698.421 69.1475 718.444 69.1475C738.467 69.1475 785.606 65.2889 785.606 65.2889L841.416 69.1475C841.416 69.1475 933.568 70.4795 1014.38 78.8961C1095.19 87.3127 1199.64 82.9769 1199.64 82.9769L1273.69 56.8724H1308.78L1349.07 45.4127H1362.71H1401.35L1418.85 36.9832L1564.55 45.4127H1593.87L1615.07 36.9832C1615.07 36.9832 1651.43 38.246 1651.43 41.8293C1651.43 45.4127 1676.94 45.4127 1676.94 45.4127C1676.94 45.4127 1740.61 23.577 1786.43 23.577C1832.25 23.577 1866.21 16 1866.21 16L1920 28.4822V110V110.248V552H0V110.248Z"
                        fill="#050505" fill-opacity="0.12" />
                </g>
                <defs>
                    <filter id="filter0_f_261_209" x="-16" y="0" width="1952" height="568" filterUnits="userSpaceOnUse"
                        color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                        <feGaussianBlur stdDeviation="8" result="effect1_foregroundBlur_261_209" />
                    </filter>
                </defs>
            </svg>
        </div>
        <div class="ed-prg6-bottom-shape position-absolute">
            <svg width="1952" height="568" viewBox="0 0 1952 568" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_f_261_212)">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M1936 457.752V458H1935.98C1935.31 462.105 1919.34 478.661 1905.6 478.661H1845.78C1842.17 478.661 1834.22 477.103 1827.06 475.7C1819.62 474.243 1813.03 472.952 1813.03 473.743C1813.03 474.23 1808.04 474.166 1801.19 474.078C1786.23 473.887 1762.41 473.583 1762.41 478.661C1762.41 486.064 1709.44 489.104 1708.11 489.104C1708.07 489.104 1707.95 489.104 1707.77 489.105C1701.41 489.128 1611.52 489.454 1585.13 484.136C1557.97 478.661 1550.98 484.136 1550.98 484.136L1479.54 489.104L1462.16 480.522L1431.42 484.136L1270.35 502.711C1270.35 502.711 1237.58 498.853 1217.56 498.853C1197.53 498.853 1150.39 502.711 1150.39 502.711L1094.58 498.853C1094.58 498.853 1002.43 497.521 921.622 489.104C840.814 480.687 736.361 485.023 736.361 485.023L662.311 511.128H627.221L586.933 522.587H573.295H534.654L517.152 531.017L371.454 522.587H342.133L320.927 531.017C320.927 531.017 284.573 529.754 284.573 526.171C284.573 522.587 259.062 522.587 259.062 522.587C259.062 522.587 195.392 544.423 149.571 544.423C103.75 544.423 69.7894 552 69.7894 552L16 539.518V458V457.752V15.9999H1936V457.752Z"
                        fill="#050505" fill-opacity="0.12" />
                </g>
                <defs>
                    <filter id="filter0_f_261_212" x="0" y="0" width="1952" height="568" filterUnits="userSpaceOnUse"
                        color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape" />
                        <feGaussianBlur stdDeviation="8" result="effect1_foregroundBlur_261_212" />
                    </filter>
                </defs>
            </svg>
        </div>
        <div class="ed-prg6-wrap">
            <div class="container">
                <div class="ed-prg6-top-content d-flex justify-content-between flex-wrap align-items-end">
                    <div class="ed-sec-title-6 headline-6 pera-content">
                        <h2 class="sec_title ed-sec-tt-anim ed-has-anim">Top Government Exam Preparation Courses<span
                                class="has-dot">.</span>
                        </h2>
                    </div>
                    {{-- <div class="ed-prg-filter-btn">
                        <div class="button-group p-filter-btn  clearfix">
                            <button class="filter-button is-checked" data-filter="*">/All Programs </button>
                            <button class="filter-button" data-filter=".business">/Bachelor</button>
                            <button class="filter-button" data-filter=".design"> /Masters</button>
                            <button class="filter-button" data-filter=".health">/Development</button>
                        </div>
                    </div> --}}
                </div>
                <div class="ed-prg6-content mt-55">
                    <div class="filtr-container-area ed-filter6-area grid clearfix"
                        data-isotope="{ &quot;masonry&quot;: { &quot;columnWidth&quot;: 0 } }">
                        <div class="grid-sizer"></div>
                        <div class="grid-item grid-size-25 business design health" data-category="business design health">
                            <div class="ed-prg6-item">
                                <div class="inner-item">
                                    <div class="item-text-img">
                                        {{-- <div class="item-img">
                                            <img src="{{ asset('assets/user/img/course/prog1.jpg') }}" alt="">
                                        </div> --}}
                                        <div class="item-text headline-6 pera-content">
                                            <h3 class="course_title href-underline"><a href="">Railway Exams - RRB NTPC &
                                                    Group D</a>
                                            </h3>
                                            <p>Complete preparation for Railway RRB NTPC, Group D, ALP with 300+ mock tests
                                                and previous year...</p>
                                            <div class="crs-meta">
                                                <a href="#">Government Exam</a>
                                                <a href="#">6 Months</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-arrow text-center">
                                        <a href="">
                                            <span>Enroll Now</span>
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M20.4895 6.83381C17.6615 6.83381 15.0839 4.24264 15.0839 1.39714V0.230469H12.7639V1.39714C12.7639 3.4668 13.6664 5.40814 15.0828 6.83381H0.769531V9.16715H15.0828C13.6664 10.5928 12.7639 12.5341 12.7639 14.6038V15.7705H15.0839V14.6038C15.0839 11.7583 17.6615 9.16715 20.4895 9.16715H21.6495V6.83381H20.4895Z"
                                                    fill="#050505" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid-item grid-size-25 business  health" data-category="business  health">
                            <div class="ed-prg6-item">
                                <div class="inner-item">
                                    <div class="item-text-img">
                                        {{-- <div class="item-img">
                                            <img src="{{ asset('assets/user/img/course/prog2.jpg') }}" alt="">
                                        </div> --}}
                                        <div class="item-text headline-6 pera-content">
                                            <h3 class="course_title href-underline"><a href="">SSC CGL, CHSL & MTS
                                                    Complete</a></h3>
                                            <p>Master SSC exams with General Intelligence, Awareness, and English
                                                Comprehension modules...</p>
                                            <div class="crs-meta">
                                                <a href="#">Government Exam</a>
                                                <a href="#">8 Months</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-arrow text-center">
                                        <a href="">
                                            <span>Enroll Now</span>
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M20.4895 6.83381C17.6615 6.83381 15.0839 4.24264 15.0839 1.39714V0.230469H12.7639V1.39714C12.7639 3.4668 13.6664 5.40814 15.0828 6.83381H0.769531V9.16715H15.0828C13.6664 10.5928 12.7639 12.5341 12.7639 14.6038V15.7705H15.0839V14.6038C15.0839 11.7583 17.6615 9.16715 20.4895 9.16715H21.6495V6.83381H20.4895Z"
                                                    fill="#050505" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid-item grid-size-25  design" data-category="design">
                            <div class="ed-prg6-item">
                                <div class="inner-item">
                                    <div class="item-text-img">
                                        {{-- <div class="item-img">
                                            <img src="{{ asset('assets/user/img/course/prog3.jpg') }}" alt="">
                                        </div> --}}
                                        <div class="item-text headline-6 pera-content">
                                            <h3 class="course_title href-underline"><a href="">Banking Exams - SBI, IBPS &
                                                    RBI</a></h3>
                                            <p>Comprehensive preparation for all banking exams with Quantitative Aptitude,
                                                Reasoning, ...</p>
                                            <div class="crs-meta">
                                                <a href="#">Banking</a>
                                                <a href="#">10 Months</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-arrow text-center">
                                        <a href="">
                                            <span>Enroll Now</span>
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M20.4895 6.83381C17.6615 6.83381 15.0839 4.24264 15.0839 1.39714V0.230469H12.7639V1.39714C12.7639 3.4668 13.6664 5.40814 15.0828 6.83381H0.769531V9.16715H15.0828C13.6664 10.5928 12.7639 12.5341 12.7639 14.6038V15.7705H15.0839V14.6038C15.0839 11.7583 17.6615 9.16715 20.4895 9.16715H21.6495V6.83381H20.4895Z"
                                                    fill="#050505" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid-item grid-size-25 health" data-category="health">
                            <div class="ed-prg6-item">
                                <div class="inner-item">
                                    <div class="item-text-img">
                                        {{-- <div class="item-img">
                                            <img src="{{ asset('assets/user/img/course/prog4.jpg') }}" alt="">
                                        </div> --}}
                                        <div class="item-text headline-6 pera-content">
                                            <h3 class="course_title href-underline"><a href="">UPSC Civil Services
                                                    Complete</a></h3>
                                            <p>Prelims + Mains + Interview preparation with Current Affairs daily and Answer
                                                Writing Practice...</p>
                                            <div class="crs-meta">
                                                <a href="#">UPSC</a>
                                                <a href="#">12 Months</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-arrow text-center">
                                        <a href="">
                                            <span>Enroll Now</span>
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M20.4895 6.83381C17.6615 6.83381 15.0839 4.24264 15.0839 1.39714V0.230469H12.7639V1.39714C12.7639 3.4668 13.6664 5.40814 15.0828 6.83381H0.769531V9.16715H15.0828C13.6664 10.5928 12.7639 12.5341 12.7639 14.6038V15.7705H15.0839V14.6038C15.0839 11.7583 17.6615 9.16715 20.4895 9.16715H21.6495V6.83381H20.4895Z"
                                                    fill="#050505" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid-item grid-size-25 business design health" data-category="business design health">
                            <div class="ed-prg6-item">
                                <div class="inner-item">
                                    <div class="item-text-img">
                                        {{-- <div class="item-img">
                                            <img src="{{ asset('assets/user/img/course/prog5.jpg') }}" alt="">
                                        </div> --}}
                                        <div class="item-text headline-6 pera-content">
                                            <h3 class="course_title href-underline"><a href="">ITI Trade Courses &
                                                    Training</a>
                                            </h3>
                                            <p>All Trade Subjects with Practical Training Videos and Job Placement
                                                Support...</p>
                                            <div class="crs-meta">
                                                <a href="#">ITI</a>
                                                <a href="#">6-12 Months</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-arrow text-center">
                                        <a href="">
                                            <span>Enroll Now</span>
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M20.4895 6.83381C17.6615 6.83381 15.0839 4.24264 15.0839 1.39714V0.230469H12.7639V1.39714C12.7639 3.4668 13.6664 5.40814 15.0828 6.83381H0.769531V9.16715H15.0828C13.6664 10.5928 12.7639 12.5341 12.7639 14.6038V15.7705H15.0839V14.6038C15.0839 11.7583 17.6615 9.16715 20.4895 9.16715H21.6495V6.83381H20.4895Z"
                                                    fill="#050505" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grid-item grid-size-25 design health" data-category="design health">
                            <div class="ed-prg6-item">
                                <div class="inner-item">
                                    <div class="item-text-img">
                                        {{-- <div class="item-img">
                                            <img src="{{ asset('assets/user/img/course/prog6.jpg') }}" alt="">
                                        </div> --}}
                                        <div class="item-text headline-6 pera-content">
                                            <h3 class="course_title href-underline"><a href="">Defence Exams - NDA, CDS &
                                                    AFCAT</a></h3>
                                            <p>Complete preparation for Indian Defence forces with Physical Training, Mock
                                                Tests, and ...</p>
                                            <div class="crs-meta">
                                                <a href="#">Defence</a>
                                                <a href="#">9 Months</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item-arrow text-center">
                                        <a href="">
                                            <span>Enroll Now</span>
                                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M20.4895 6.83381C17.6615 6.83381 15.0839 4.24264 15.0839 1.39714V0.230469H12.7639V1.39714C12.7639 3.4668 13.6664 5.40814 15.0828 6.83381H0.769531V9.16715H15.0828C13.6664 10.5928 12.7639 12.5341 12.7639 14.6038V15.7705H15.0839V14.6038C15.0839 11.7583 17.6615 9.16715 20.4895 9.16715H21.6495V6.83381H20.4895Z"
                                                    fill="#050505" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ed-btn-6 text-center mt-40">
                        <a href="">
                            <span>View All Courses </span>
                            <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4"
                                    d="M16.5013 7.83244H1.5013C1.28029 7.83244 1.06833 7.74464 0.912046 7.58836C0.755766 7.43208 0.667969 7.22012 0.667969 6.9991C0.667969 6.77809 0.755766 6.56613 0.912046 6.40985C1.06833 6.25357 1.28029 6.16577 1.5013 6.16577H16.5013C16.7223 6.16577 16.9343 6.25357 17.0906 6.40985C17.2468 6.56613 17.3346 6.77809 17.3346 6.9991C17.3346 7.22012 17.2468 7.43208 17.0906 7.58836C16.9343 7.74464 16.7223 7.83244 16.5013 7.83244Z"
                                    fill="white"></path>
                                <path
                                    d="M10.6691 13.6666C10.5043 13.6666 10.3432 13.6177 10.2062 13.5261C10.0692 13.4345 9.96242 13.3044 9.89936 13.1521C9.8363 12.9999 9.8198 12.8324 9.85194 12.6707C9.88408 12.5091 9.96342 12.3606 10.0799 12.2441L15.3241 6.99993L10.0799 1.75577C9.92813 1.5986 9.84413 1.3881 9.84603 1.1696C9.84793 0.951101 9.93557 0.742091 10.0901 0.587584C10.2446 0.433077 10.4536 0.345436 10.6721 0.343537C10.8906 0.341639 11.1011 0.425634 11.2583 0.577433L17.0916 6.41077C17.2478 6.56704 17.3356 6.77896 17.3356 6.99993C17.3356 7.2209 17.2478 7.43283 17.0916 7.5891L11.2583 13.4224C11.102 13.5787 10.8901 13.6666 10.6691 13.6666Z"
                                    fill="white"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection