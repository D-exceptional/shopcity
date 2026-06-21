<!--- Core Meta Tags --->
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="" name="keywords">
<meta content="" name="description">

<!--- CSRF Token --->
<meta name="csrf-token" content="<?= $session->token() ?? null ?>">

<!-- Google Web Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<!-- Icon Font Stylesheet -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Libraries Stylesheet -->
<link href="assets/lib/animate/animate.min.css" rel="stylesheet">
<link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

<!-- Customized Bootstrap Stylesheet -->
<link href="<?php echo $cacheManager->parse('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">

<!-- Template Stylesheet -->
<link href="<?php echo $cacheManager->parse('assets/css/style.css'); ?>" rel="stylesheet">
<link href="<?php echo $cacheManager->parse('assets/css/overlay.css'); ?>" rel="stylesheet">

<!-- Sweetalert 2.0.0 Stylesheet -->
<link href="assets/css/sweetalert-4.1.1.min.css" rel="stylesheet">