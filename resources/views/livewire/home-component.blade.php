<div>
    <section class="tp-banner-container">
        <div class="tp-banner">
            <ul>
                <li data-transition="slidevertical" data-slotamount="1" data-masterspeed="1000"
                    data-saveperformance="off" data-title="Slide">
                    <img src = "{{asset('assets/img/slide/1.jpg')}}" alt="fullslide1" data-bgposition="center center"
                        data-kenburns="on" data-duration="6000" data-ease="Linear.easeNone" data-bgfit="130"
                        data-bgfitend="100" data-bgpositionend="right center">
                </li>
                <li data-transition="slidehorizontal" data-slotamount="1" data-masterspeed="1000"
                    data-saveperformance="off" data-title="Slide">
                    <img src = "{{asset('assets/img/slide/2.jpg')}}" alt="fullslide1" data-bgposition="top center"
                        data-kenburns="on" data-duration="6000" data-ease="Linear.easeNone" data-bgfit="130"
                        data-bgfitend="100" data-bgpositionend="right center">
                </li>
            </ul>
            <div class="tp-bannertimer"></div>
        </div>
        <div class="filter-title">
            <div class="title-header">
                <h2 style="color:#fff;">BOOK A SERVICE</h2>
                <p class="lead">Book a service at very affordable price, </p>
            </div>
            <div class="filter-header">
                <form id="sform" action="searchservices" method="post">                        
                    <input type="text" id="q" name="q" required="required" placeholder="What Services do you want?"
                        class="input-large typeahead" autocomplete="off">
                    <input type="submit" name="submit" value="Search">
                </form>
            </div>
        </div>
    </section>
    <section class="content-central">
        <div class="semiboxshadow text-center">
            <img src = "{{asset('assets/img/img-theme/shp.png')}}" class="img-responsive" alt="">
        </div>
        <div class="content_info" style="background-color: white;">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center mb-4">
                            <h2 class="section-title">Our Services</h2>
                            <p class="section-subtitle">Choose from our wide range of professional services</p>
                        </div>
                        <div class="services-grid">
                            <div class="row">
                                @foreach($categories as $category)
                                <div class="col-md-4 mb-4">
                                    <div class="card service-category-card h-100 shadow-hover">
                                        <div class="card-img-container">
                                            <img 
                                                src="{{ asset('images/service-categories/' . $category->image) }}" 
                                                class="card-img-top category-image" 
                                                alt="{{ $category->name }}"
                                            >
                                            <div class="category-overlay">
                                                <h5 class="category-title">{{ $category->name }}</h5>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text text-muted">{{ Str::limit($category->description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a 
                                                    href="{{ route('customer.service_providers', ['categoryId' => $category->id]) }}" 
                                                    class="btn btn-primary rounded-circle btn-lg p-3"
                                                >
                                                    Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
            .service-item {
                display: block;
                text-decoration: none;
                color: #333;
                transition: transform 0.3s ease;
            }
            .service-item:hover {
                transform: scale(1.05);
            }
            .service-icon {
                width: 80px;
                height: 80px;
                margin: 0 auto 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .service-icon img {
                max-width: 100%;
                max-height: 100%;
            }
            .service-title {
                font-size: 14px;
                font-weight: 600;
                margin: 0;
            }
            .section-title {
                color: #333;
                font-weight: 700;
                margin-bottom: 15px;
            }
            .section-subtitle {
                color: #666;
                margin-bottom: 30px;
            }
            .service-book-btn .btn-primary {
                background-color: #007bff;
                border-color: #007bff;
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                transition: background-color 0.3s ease;
            }
            .service-book-btn .btn-primary:hover {
                background-color: #0056b3;
            }
            </style>
        </div>
    </section>
</div>
