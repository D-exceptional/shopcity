<?php
    // Import Initializer File
    require_once __DIR__ . '/includes/init.php'; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= SITE_NAME ?> | Terms Of Service</title>
    <!-- Include the head section code file -->
    <?php include_once 'includes/head.php'; ?>
</head>

<body>

    <!-- Include the header section code file -->
    <?php include_once 'includes/header.php'; ?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Terms Of Service</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Terms Of Service</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Contucts Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h5 class="text-primary wow fadeInUp" data-wow-delay="0.1s">Our Terms Of Service</h5>
                        <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">Get The Info</h1>
                        <p class="mb-4 wow fadeInUp" data-wow-delay="0.5s">
                            Get to know more about us, our brand and what we represent
                        </p>
                        <!--- Add More Info Below -->
                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3 btn-doc" data-doc="terms-of-service.pdf">View Policy Document</button>
                        </div>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded">
                            <img class="rounded w-100 h-100" style="height: 100%;" src="assets/img/policy-1.jpg" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
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