<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= $session->token() ?? null ?>">
<link type="image/x-icon" rel="icon" href="/assets/img/short-logo.png">
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="../../assets/plugins/fontawesome-free/css/all.min.css">
<!-- iCheck -->
<link rel="stylesheet" href="../../assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="../../assets/plugins/dist/css/adminlte.min.css">
<!-- Summernote -->
<link rel="stylesheet" href="../../assets/plugins/summernote/summernote-bs4.min.css">
<!-- Sweetalert 2.0.0 Stylesheet -->
<link href="../../assets/css/sweetalert-4.1.1.min.css" rel="stylesheet">
<!-- Custom CSS  -->
<link rel="stylesheet" href="<?php echo $cacheManager->parse('../../assets/css/dashboard.css'); ?>">
<link rel="stylesheet" href="<?php echo $cacheManager->parse('../../assets/css/overlay.css'); ?>">
<link rel="stylesheet" href="<?php echo $cacheManager->parse('../../assets/css/modal.css'); ?>">