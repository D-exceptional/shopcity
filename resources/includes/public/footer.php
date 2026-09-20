<!-- Footer Start -->
<div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
    <div class="container py-5">
        <div class="row g-4 rounded mb-5" style="background: rgba(255, 255, 255, .03);">
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="rounded p-4">
                    <div class="rounded-circle bg-teal d-flex align-items-center justify-content-center mb-4"
                        style="width: 70px; height: 70px;">
                        <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h4 class="text-white">Address</h4>
                        <p class="mb-2">Lagos Island, Lagos, Nigeria</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="rounded p-4">
                    <div class="rounded-circle bg-teal d-flex align-items-center justify-content-center mb-4"
                        style="width: 70px; height: 70px;">
                        <i class="fas fa-envelope fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h4 class="text-white">Mail Us</h4>
                        <p class="mb-2">support@<?= strtolower($appName) ?>.com</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="rounded p-4">
                    <div class="rounded-circle bg-teal d-flex align-items-center justify-content-center mb-4"
                        style="width: 70px; height: 70px;">
                        <i class="fa fa-phone-alt fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h4 class="text-white">Telephone</h4>
                        <p class="mb-2">(+234) 902 692 8911</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="rounded p-4">
                    <div class="rounded-circle bg-teal d-flex align-items-center justify-content-center mb-4"
                        style="width: 70px; height: 70px;">
                        <i class="fab fa-firefox-browser fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h4 class="text-white">Website</h4>
                        <p class="mb-2">https://<?= strtolower($appName) ?>.com</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-5">
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item d-flex flex-column">
                    <div class="footer-item">
                        <h4 class="text-primary mb-4">Newsletter</h4>
                        <p class="mb-3">Get notified on developments, feature releases, fantastic offers and announcements by subscribing.</p>
                        <div class="position-relative mx-auto rounded-pill">
                            <input class="form-control rounded-pill w-100 py-3 ps-4 pe-5" type="text"
                                placeholder="Enter your email">
                            <button type="button"
                                class="btn btn-primary rounded-pill position-absolute top-0 end-0 py-2 mt-2 me-2">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item d-flex flex-column">
                    <h4 class="text-primary mb-4">Customer Service</h4>
                    <a href="/contact" class=""><i class="fas fa-angle-right me-2"></i> Contact Us</a>
                    <a href="/returns" class=""><i class="fas fa-angle-right me-2"></i> Returns Policy</a>
                    <a href="/orders" class=""><i class="fas fa-angle-right me-2"></i> Order History</a>
                    <a href="/track" class=""><i class="fas fa-angle-right me-2"></i> Track Your Order</a>
                    <a href="/profile" class=""><i class="fas fa-angle-right me-2"></i> My Account</a>
                    <a href="/wishlist" class=""><i class="fas fa-angle-right me-2"></i> Wishlist</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item d-flex flex-column">
                    <h4 class="text-primary mb-4">Information</h4>
                    <a href="/about" class=""><i class="fas fa-angle-right me-2"></i> About Us</a>
                    <a href="/delivery" class=""><i class="fas fa-angle-right me-2"></i> Delivery Policy</a>
                    <a href="/privacy" class=""><i class="fas fa-angle-right me-2"></i> Privacy Policy</a>
                    <a href="/terms" class=""><i class="fas fa-angle-right me-2"></i> Terms & Conditions</a>
                    <a href="/warranty" class=""><i class="fas fa-angle-right me-2"></i> Warranty</a>
                    <a href="/faq" class=""><i class="fas fa-angle-right me-2"></i> FAQ</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="footer-item d-flex flex-column">
                    <h4 class="text-primary mb-4">Services</h4>
                    <a href="/brands" class=""><i class="fas fa-angle-right me-2"></i> Brands</a>
                    <a href="/auth/user/register?type=vendor" class=""><i class="fas fa-angle-right me-2"></i> Become A Seller</a>
                    <!--<a href="/auth/user/register?type=affiliate" class=""><i class="fas fa-angle-right me-2"></i> Become An Affiliate</a>-->
                    <a href="/contact" class=""><i class="fas fa-angle-right me-2"></i> Professional Enquiries</a>
                    <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Promote A Product</a>
                    <a href="#" class=""><i class="fas fa-angle-right me-2"></i> Testimonials</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

<!-- JavaScript Libraries -->
<script src="<?= asset('js/shared/jquery-3.6.4.min.js') ?>"></script>
<script src="<?= asset('js/shared/bootstrap-5.0.0.bundle.min.js') ?>"></script>
<script src="<?= asset('lib/wow/wow.min.js') ?>"></script>
<script src="<?= asset('lib/owlcarousel/owl.carousel.min.js') ?>"></script>

<!-- Template Javascript -->
<script src="<?= asset_versioned('js/public/main.js') ?>" type="module"></script>                                                                                 

<!-- Sweetalert 2.0.0 Script -->
<script src="<?= asset('js/shared/sweetalert-2.6.0.min.js') ?>"></script>

<!-- Functions Javascript -->
<script src="<?= asset_versioned('js/public/functions.js') ?>" type="module"></script>
