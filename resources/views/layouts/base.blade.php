<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>EaseLife - Simplifying home services for you</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="shortcut icon" href="">{{asset('assets/img/favicon.png')}} --}}
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('assets/css/theme-responsive.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('assets/css/chblue.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('assets/css/dtb/jquery.dataTables.min.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" media="screen">
    <link href="{{asset('assets/css/toastr.min.css')}}" rel="stylesheet" media="screen">        
    <script type="text/javascript" src="{{asset('assets/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/jquery-ui.1.10.4.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/toastr.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/modernizr.js')}}"></script>
    @livewireStyles 
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
</head>
<body>
    <div id="layout">
        <div class="info-head">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="visible-md visible-lg text-left">
                            <li><a href="tel:+8801869326580"><i class="fa fa-phone"></i> 01869326580</a></li>
                            <li><a href="mailto:contact@EaseLife.bd"><i class="fa fa-envelope"></i>
                                    contact@EaseLife.bd</a></li>
                        </ul>
                        <ul class="visible-xs visible-sm">
                            <li class="text-left"><a href="tel:+8801869326580"><i class="fa fa-phone"></i>
                                    +8801869326580</a></li>
                            <li class="text-right"><a href="index.php/changelocation.html"><i
                                        class="fa fa-map-marker"></i> Dhaka,Bangladesh</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="visible-md visible-lg text-right">
                            {{-- <li><i class="fa fa-comment"></i> Live Chat</li> --}}
                            <li><a href="index.php/changelocation.html"><i class="fa fa-map-marker"></i> Dhaka,Bangladesh</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <header id="header" class="header-v3">
            <nav class="flat-mega-menu">
                <label for="mobile-button"> <i class="fa fa-bars"></i></label>
                <input id="mobile-button" type="checkbox">

                <ul class="collapse">
                    <li class="title">
                        {{-- <h3 style="font-size: 2rem; color: #343a40; margin: 0; display: inline-block; position: relative;">
                            EaseLife
                            <span style="display: block; font-size: 1rem; color: #6c757d; margin-top: 4px; position: absolute; left: 50%; transform: translateX(-50%); white-space: nowrap;">
                              Simplifying Home Services for you
                            </span></h3> --}}
                        <a href="index.php.html"><img src = "{{asset('assets/img/easelife.PNG')}}"></a>
                    </li>
                    <li> <a href="{{route('home.service_categories')}}">Service Categories</a></li>
                    {{-- <li> <a href="javascript:void(0);">Air Conditioners</a>
                        <ul class="drop-down one-column hover-fade">
                            <li><a href="service-details/ac-wet-servicing.html">Wet Servicing</a></li>
                            <li><a href="service-details/ac-dry-servicing.html">Dry Servicing</a></li>
                            <li><a href="service-details/ac-installation.html">Installation</a></li>
                            <li><a href="service-details/ac-uninstallation.html">Uninstallation</a></li>
                            <li><a href="service-details/ac-gas-top-up.html">Gas Top Up</a></li>
                            <li><a href="service-details/ac-gas-refill.html">Gas Refill</a></li>
                            <li><a href="service-details/ac-repair.html">Repair</a></li>
                        </ul>
                    </li> --}}
                    <li> <a href="#">Appliances</a>
                        <ul class="drop-down one-column hover-fade">
                            <li><a href="servicesbycategory/11.html">Computer Repair</a></li>
                            <li><a href="servicesbycategory/12.html">TV</a></li>
                            <li><a href="servicesbycategory/1.html">AC</a></li>
                            <li><a href="servicesbycategory/14.html">Geyser</a></li>
                            <li><a href="servicesbycategory/21.html">Washing Machine</a></li>
                            <li><a href="servicesbycategory/22.html">Microwave Oven</a></li>
                            <li><a href="servicesbycategory/9.html">Chimney and Hob</a></li>
                            <li><a href="servicesbycategory/10.html">Water Purifier</a></li>
                            <li><a href="servicesbycategory/13.html">Refrigerator</a></li>
                        </ul>
                    </li>
                    <li> <a href="#">Home Needs</a>
                        <ul class="drop-down one-column hover-fade">
                            <li><a href="servicesbycategory/19.html">Laundry</a></li>
                            <li><a href="servicesbycategory/4.html">Electrical</a></li>
                            <li><a href="servicesbycategory/8.html">Pest Control</a></li>
                            <li><a href="servicesbycategory/7.html">Carpentry</a></li>
                            <li><a href="servicesbycategory/3.html">Plumbing </a></li>
                            <li><a href="servicesbycategory/20.html">Painting</a></li>
                            <li><a href="servicesbycategory/17.html">Movers &amp; Packers</a></li>
                            <li><a href="servicesbycategory/5.html">Shower Filters </a></li>
                        </ul>
                    </li>
                    <li> <a href="#">Home Cleaning</a>
                        <ul class="drop-down one-column hover-fade">
                            <li><a href="service-details/bedroom-deep-cleaning.html">Bedroom Deep Cleaning</a></li>
                            <li><a href="service-details/overhead-water-storage.html">Overhead Water Storage </a></li>
                            <li><a href="/service-details/tank-cleaning">Tank Cleaning</a></li>
                            <li><a href="service-details/underground-sump-cleaning.html">Underground Sump Cleaning</a>
                            </li>
                            <li><a href="service-details/dining-chair-shampooing.html">Dining Chair Shampooing </a></li>
                            <li><a href="service-details/office-chair-shampooing.html">Office Chair Shampooing</a></li>
                            <li><a href="service-details/home-deep-cleaning.html">Home Deep Cleaning </a></li>
                            <li><a href="service-details/carpet-shampooing.html">Carpet Shampooing </a></li>
                            <li><a href="service-details/fabric-sofa-shampooing.html">Fabric Sofa Shampooing</a></li>
                            <li><a href="service-details/bathroom-deep-cleaning.html">Bathroom Deep Cleaning</a></li>
                            <li><a href="service-details/floor-scrubbing-polishing.html">Floor Scrubbing &amp; Polishing
                                </a></li>
                            <li><a href="service-details/mattress-shampooing.html">Mattress Shampooing </a></li>
                            <li><a href="service-details/kitchen-deep-cleaning.html">Kitchen Deep Cleaning </a></li>
                        </ul>
                    </li>
                    <li> <a href="#">Special Services</a>
                        <ul class="drop-down one-column hover-fade">
                            <li><a href="servicesbycategory/16.html">Document Services</a></li>
                            <li><a href="servicesbycategory/15.html">Cars &amp; Bikes</a></li>
                            <li><a href="servicesbycategory/17.html">Movers &amp; Packers </a></li>
                            <li><a href="servicesbycategory/18.html">Home Automation</a></li>
                        </ul>
                    </li>
                    @if(Route::has('login'))
                        @auth
                            @if(Auth::user()->user_type === 'adm')
                                <li class="login-form"> <a href="#" title="Register">My account(admin)</a>
                                    <ul class="drop-down one-column hover-fade">
                                        <li><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                                        <li><a href="{{route('admin.service_categories')}}">Service Categories</a></li>
                                        <li><a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                                    </ul>
                                </li>
                            @elseif(Auth::user()->user_type === 'sp')
                                <li class="login-form"> <a href="#" title="Register">My account(Service provider)</a>
                                    <ul class="drop-down one-column hover-fade">
                                        <li><a href="{{route('sprovider.dashboard')}}">Dashboard</a></li>
                                        <li><a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                                    </ul>
                                </li>
                            @else
                                <li class="login-form"> <a href="#" title="Register">My account(Customer)</a>
                                    <ul class="drop-down one-column hover-fade">
                                        <li><a href="{{route('customer.dashboard')}}">Dashboard</a></li>
                                        <li><a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                                    </ul>
                                </li>
                            @endif 
                            <form id="logout-form" method="POST" action="{{route('logout')}}">
                                @csrf
                            </form>
                        
                        @else
                            <li class="login-form"> <a href="{{route('register')}}" title="Register">Register</a></li>
                            <li class="login-form"> <a href="{{route('login')}}" title="Login">Login</a></li>
                        @endif 
                    @endif
                    
                    <li class="search-bar">
                    </li>
                </ul>
            </nav>
        </header>
        {{$slot}}
        <footer id="footer" class="footer-v1">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="footer-section">
                            <h4 class="footer-title">Contact Us</h4>
                            <ul class="footer-contact-list">
                                <li>
                                    <i class="fa fa-map-marker"></i>
                                    <span>Dhaka, Bangladesh</span>
                                </li>
                                <li>
                                    <i class="fa fa-envelope"></i>
                                    <a href="mailto:contact@EaseLife.bd">contact@EaseLife.bd</a>
                                </li>
                                <li>
                                    <i class="fa fa-phone"></i>
                                    <a href="tel:+8801869326580">+8801869326580</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="footer-section">
                            <h4 class="footer-title">About EaseLife</h4>
                            <p class="footer-description">
                                Your trusted partner for comprehensive home services. We simplify your life by delivering professional, reliable solutions right to your doorstep.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="footer-section">
                            <h4 class="footer-title">Follow Us</h4>
                            <ul class="footer-social-links">
                                <li><a href="#" class="facebook"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#" class="twitter"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#" class="instagram"><i class="fa fa-instagram"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-down">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="nav-footer">
                                <li><a href="about-us.html">About Us</a></li>
                                <li><a href="contact-us.html">Contact</a></li>
                                <li><a href="faq.html">FAQ</a></li>
                                <li><a href="terms-of-use.html">Terms</a></li>
                                <li><a href="privacy.html">Privacy</a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <p class="text-xs-center crtext">&copy; 2024 EaseLife. All Rights Reserved.</p>
                        </div>
                    </div>
                </div>                
            </div>
            <style>
                .footer-v1 {
                    background-color: #0a2342;
                    padding: 40px 0 20px;
                    color: #ffffff;
                }
                .footer-section {
                    margin-bottom: 20px;
                }
                .footer-title {
                    color: #ffffff;
                    font-weight: 600;
                    margin-bottom: 15px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    border-bottom: 2px solid #1f487e;
                    padding-bottom: 10px;
                }
                .footer-contact-list {
                    list-style: none;
                    padding: 0;
                }
                .footer-contact-list li {
                    margin-bottom: 10px;
                    display: flex;
                    align-items: center;
                }
                .footer-contact-list i {
                    margin-right: 12px;
                    color: #1f487e;
                    width: 20px;
                    text-align: center;
                }
                .footer-contact-list a {
                    color: #d9e5ff;
                    text-decoration: none;
                    transition: color 0.3s ease;
                }
                .footer-contact-list a:hover,
                .footer-contact-list a:focus {
                    color: #ffffff;
                    text-decoration: underline;
                }
                .footer-contact-list span {
                    color: #d9e5ff;
                }
                .footer-description {
                    color: #d9e5ff;
                    line-height: 1.6;
                }
                .footer-social-links {
                    display: flex;
                    list-style: none;
                    padding: 0;
                    margin-top: 10px;
                }
                .footer-social-links li {
                    margin-right: 15px;
                }
                .footer-social-links a {
                    color: #1f487e;
                    font-size: 24px;
                    transition: all 0.3s ease;
                    background-color: #ffffff;
                    width: 40px;
                    height: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                }
                .footer-social-links a:hover {
                    color: #ffffff;
                    background-color: #1f487e;
                    transform: scale(1.1);
                }
                .footer-down {
                    background-color: #041f3d;
                    padding: 15px 0;
                }
                .nav-footer {
                    display: flex;
                    justify-content: flex-start;
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                .nav-footer li {
                    margin-right: 15px;
                }
                .nav-footer a {
                    color: #d9e5ff;
                    text-decoration: none;
                    transition: color 0.3s ease;
                }
                .nav-footer a:hover {
                    color: #ffffff;
                    text-decoration: underline;
                }
                .crtext {
                    color: #d9e5ff;
                    margin: 0;
                    text-align: right;
                }
                @media (max-width: 768px) {
                    .footer-social-links {
                        justify-content: center;
                    }
                    .crtext {
                        text-align: center;
                    }
                }
            </style>
        </footer>
    </div>
    <script type="text/javascript" src="{{asset('assets/js/nav/jquery.sticky.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/totop/jquery.ui.totop.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/accordion/accordion.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/rs-plugin/js/jquery.themepunch.tools.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/rs-plugin/js/jquery.themepunch.revolution.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/maps/gmap3.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/fancybox/jquery.fancybox.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/carousel/carousel.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/filters/jquery.isotope.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/twitter/jquery.tweet.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/flickr/jflickrfeed.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/theme-options/theme-options.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/theme-options/jquery.cookies.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/bootstrap/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/bootstrap/bootstrap-slider.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/dtb/jquery.dataTables.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/dtb/jquery.table2excel.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/dtb/script.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/select2.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/validation-rule.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/bootstrap3-typeahead.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/main.js')}}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('.tp-banner').show().revolution({
                dottedOverlay: "none",
                delay: 5000,
                startwidth: 1170,
                startheight: 480,
                minHeight: 250,
                navigationType: "none",
                navigationArrows: "solo",
                navigationStyle: "preview1"
            });
        });
    </script>
    @livewireScripts
</body>
</html>