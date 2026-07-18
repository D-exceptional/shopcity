<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php'; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | About</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">About Us</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">About Us</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Contucts Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h5 class="text-primary wow fadeInUp" data-wow-delay="0.1s">Who We Are</h5>
                        <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">Get The Info</h1>
                        <p class="mb-4 wow fadeInUp" data-wow-delay="0.5s">
                           Get to know more about us, our brand and what we represent
                        </p>
                        <!--- Add More Info Below -->
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3 btn-doc" data-doc="">View About Document</button>
                        </div>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded">
                            <iframe class="rounded w-100" style="height: 100%;"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d78518.6344471355!2d5.6269505!3d6.3380223!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1042a0c4fd8c610d%3A0xc3a19ccbe636258c!2sBenin%20City%2C%20Edo%20State%2C%20Nigeria!5e0!3m2!1sen!2sng!4v1697520000000!5m2!1sen!2sng"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contuct End -->

    <!-- Include the footer section code file -->
    <?php include_once 'includes/footer.php'; ?>
    
    <!-- Docs View Overlay --->
    <div class='overlay'>
      <div class='close-overlay'>
        <i class='fa fa-times' aria-hidden='true'></i>
      </div>
    </div>

    <!-- Add user defined scripts here -->
</body>
</html>