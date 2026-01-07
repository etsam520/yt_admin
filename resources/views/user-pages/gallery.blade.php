@extends('user-pages.layouts.app')

@section('content')

	<section id="ed-breadcrumb" class="ed-breadcrumb-sec" data-background="{{ asset('assets/user/img/bg/bread-bg.jpg') }}">
		<div class="container">
			<div class="ed-breadcrumb-content">
				<div class="ed-breadcrumb-text text-center headline ul-li">
					<h2 class="bread_title">Gallery</h2>
					<ul>
						<li><a href="#">Home</a></li>
						<li>Gallery</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
	<section id="ed-gallery-feed" class="ed-gallery-feed-sec pt-100 pb-100">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 grid-item ed-gallery-img grid-size-50  zoom-gallery">
					<div class="item-img-icon">
						<div class="item-img">
							<img src="{{ asset('assets/user/img/gallery/glr1.jpg') }}" alt="">
						</div>
					</div>
				</div>
				<div class="col-lg-3 grid-item ed-gallery-img grid-size-25  zoom-gallery">
					<div class="item-img-icon">
						<div class="item-img">
							<img src="{{ asset('assets/user/img/gallery/glr2.jpg') }}" alt="">
						</div>
					</div>
				</div>
				<div class="col-lg-3 grid-item ed-gallery-img grid-size-25  zoom-gallery">
					<div class="item-img-icon">
						<div class="item-img">
							<img src="{{ asset('assets/user/img/gallery/glr3.jpg') }}" alt="">
						</div>
					</div>
				</div>
				<div class="col-lg-3 grid-item ed-gallery-img grid-size-50  zoom-gallery">
					<div class="item-img-icon">
						<div class="item-img">
							<img src="{{ asset('assets/user/img/gallery/glr4.jpg') }}" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection