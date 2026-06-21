<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Contact</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Contact Us</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Contact</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Contucts Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h5 class="text-primary wow fadeInUp" data-wow-delay="0.1s">Get in touch</h5>
                        <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">Connect With Us</h1>
                        <p class="mb-4 wow fadeInUp" data-wow-delay="0.5s">
                           We're open to hear your enquiries, experience, suggestions and constructive criticisms.
                        </p>
                        <form class="form-contact">
                            <div class="row g-4 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating form-text">
                                        <input type="text" class="form-control form-name" data-type="name" placeholder="Your Name">
                                        <label for="name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating form-text">
                                        <input type="email" class="form-control form-email" data-type="email" placeholder="Your Email">
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating form-text">
                                        <input type="phone" class="form-control form-phone" data-type="phone" placeholder="Phone">
                                        <label for="phone">Your Phone</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <select name="country" class="form-control bg-white py-3 form-country">
                                        <option value="">Select Your Country</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating form-text">
                                        <input type="text" class="form-control form-subject" data-type="text" placeholder="Subject">
                                        <label for="subject">Subject</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating form-text">
                                        <textarea class="form-control form-message" data-type="text" placeholder="Leave a message here" style="height: 160px"></textarea>
                                        <label for="message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3 btn-contact" type="submit">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded">
                            <iframe class="rounded w-100" style="height: 100%;"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d78518.6344471355!2d5.6269505!3d6.3380223!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1042a0c4fd8c610d%3A0xc3a19ccbe636258c!2sBenin%20City%2C%20Edo%20State%2C%20Nigeria!5e0!3m2!1sen!2sng!4v1697520000000!5m2!1sen!2sng"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="row g-4 align-items-center justify-content-center">
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-view d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fas fa-map-marker-alt fa-2x text-primary text-white"></i>
                                    </div>
                                    <div>
                                        <h4>Address</h4>
                                        <p class="mb-2">Lagos Island, Lagos, Nigeria</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-view d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fas fa-envelope fa-2x text-primary text-white"></i>
                                    </div>
                                    <div>
                                        <h4>Mail Us</h4>
                                        <p class="mb-2">support@<?= strtolower(SITE_NAME) ?>.com</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.5s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-view d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fa fa-phone-alt fa-2x text-primary text-white"></i>
                                    </div>
                                    <div>
                                        <h4>Telephone</h4>
                                        <p class="mb-2">(+234) 902 692 8911</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.7s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-view d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fab fa-firefox-browser fa-2x text-primary text-white"></i>
                                    </div>
                                    <div>
                                        <h4>Website</h4>
                                        <p class="mb-2">https://shopcity.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contuct End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>

    <!-- Add user defined scripts here -->
    <script src="<?php echo $cacheManager->parse('assets/js/contact.js'); ?>" type="module"></script>
</body>

</html>