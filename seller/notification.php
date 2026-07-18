<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $notificationModel = $container->get(\App\Models\Notification::class); 

  // Get combined sales stats
  $notifications = $notificationModel->getAllById($userId);
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Vendor | Notifications</title>
  <?php include_once 'includes/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">
    <!-- Navbar -->
    <?php include_once 'includes/header.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include_once 'includes/sidebar.php'; ?>
    <!-- / Main Sidebar Container -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><b>Notifications</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Notifications</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content content-view">
        <div class="container-fluid">

          <!-- Timelime example  -->
          <div class="row h-500">
            <div class="col-md-12">
              <!-- The time line -->
              <div class="timeline notification-list">
                <?php if (!empty($notifications)): ?>
                  <?php foreach ($notifications as $notification): ?>
                    <?php
                      $type = $notification['notification_type'];
                      $details = $notification['notification_details'];
                      $timestamp = $timeManager->format($notification['notification_date']);

                      // Map notification type to icon + color
                      $iconMap = [
                        'New Order'          => ['fas fa-shopping-cart', 'bg-yellow'],
                        'Order Completion'   => ['fas fa-shopping-cart', 'bg-green'],
                        'Order Cancellation' => ['fas fa-shopping-cart', 'bg-red'],
                        'Item Update'        => ['fas fa-rss', 'bg-blue'],
                        'Product Review'     => ['fas fa-thumbs-up', 'bg-pink'],
                        'New Message'        => ['fas fa-envelope', 'bg-blue'],
                        'Product Approval'   => ['fas fa-check', 'bg-green'],
                        'Fund Redeem'        => ['fas fa-retweet', 'bg-blue'],
                        'Fund Request'       => ['fas fa-wallet', 'bg-yellow'],
                        'Fund Payout'        => ['fas fa-university', 'bg-yellow']
                      ];

                      // Default icon if not found
                      [$icon, $bg] = $iconMap[$type] ?? ['fas fa-refresh', 'bg-pink'];
                    ?>

                    <!-- Timeline Item -->
                    <div>
                      <i class="<?= $icon . ' ' . $bg ?>"></i>
                      <div class="timeline-item">
                        <span class="time">
                          <i class="fas fa-clock"></i> <?= $timestamp ?>
                        </span>
                        <h3 class="timeline-header">
                          <a href="#"></a> <?= $type ?>
                        </h3>
                        <div class="timeline-body">
                          <?= $details ?>
                        </div>
                        <div class="timeline-footer">
                          <!--<a href="#" class="btn btn-primary btn-sm">View</a>-->
                        </div>
                      </div>
                    </div>
                    <!-- END Timeline Item -->

                  <?php endforeach; ?>

                <?php else: ?>
                  <!-- No Notifications -->
                  <div class="d-flex justify-content-center align-items-center">
                    <p>No notification available</p>
                  </div>
                <?php endif; ?>
                <!--- ./ End of notification -->
              </div>
            </div>
            <!-- /.col -->
          </div>
        </div>
        <!-- /.timeline -->

      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>

  <!-- Load More --->
  <?php if (!empty($notifications)): ?>
    <button type="button" class="btn btn-sm load-more bg-primary color-white">Load More</button>
  <?php endif; ?>
  <!-- ./Load More -->

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <script src="<?php echo $cacheManager->parse('./scripts/notifications.js'); ?>" type="module"></script>
  
</body>
</html>
