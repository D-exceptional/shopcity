<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $walletModel = $container->get(\App\Models\Wallet::class); 

  // Get combined sales stats
  $walletStats = $walletModel->getVendorWalletStats($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Vendor | Earnings</title>
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
              <h1><b>Earnings</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Earnings</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content content-view">
        <!-- Default box -->
        <div class="card h-550">
          <div class="card-header">
            <h3 class="card-title">Earnings</h3>
            <div class="card-tools">
              <button class='btn btn-sm bg-primary color-white btn-calculator'>
                Open Calculator
              </button> 
              <?php if ($walletStats['current_balance'] > 0): ?>
                <button class='btn btn-sm bg-primary color-white'>
                  <a href="./withdrawal" class="decoration-none color-white">Place Withdrawal</a>
                </button> 
              <?php endif; ?>
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-0 overlow-x-auto white-space-normal">
            <table class="table table-striped projects">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Current Balance</th>
                        <th>Total Balance</th>
                        <th>Savings Balance</th>
                        <th>Pending Payout</th>
                        <th>Total Payout</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                  <?php if ($isLoggedIn): ?>
                    <!------ Dispplay earnings ------>
                      <tr>
                          <td>#</td>
                          <td><?= $ratingManager->format((float)$walletStats['current_balance'] / BASE_CONVERSION_RATE ?? 0); ?> Coins</td>
                          <td><?= $ratingManager->format((float)$walletStats['total_balance'] / BASE_CONVERSION_RATE ?? 0); ?> Coins</td>
                          <td><?= $ratingManager->format((float)$walletStats['savings_balance'] / BASE_CONVERSION_RATE ?? 0); ?> Coins</td>
                          <td><?= $ratingManager->format((float)$walletStats['pending_payout'] / BASE_CONVERSION_RATE ?? 0); ?> Coins</td>
                          <td><?= $ratingManager->format((float)$walletStats['total_payout'] / BASE_CONVERSION_RATE ?? 0); ?> Coins</td>
                          <td>
                              <button class='btn btn-info btn-sm'>
                              View
                              </button> 
                          </td>
                      </tr>

                    <?php else: ?>
                    <!-- No Login Detected -->
                    <tr>
                      <td colspan="8" class='text-center'>Login to view earning details</td>
                    </tr>

                  <?php endif; ?>
    
                </tbody>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->

      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>

  <div class='overlay'>
    <div class="calculator-modal">
      <div class="row align-items-center justify-content-center mt-15">
        <h3>Understand The Coin System</h3>
        <span>
          Mrsamase leverages a proprietary coin-based financial system to power all reward mechanisms, payouts, and internal transactions across the platform.
          This innovative approach serves as the backbone of Mrsamase’s economic ecosystem, ensuring a streamlined, transparent, and secure method of value exchange between users and the platform.
          By adopting a unified digital coin framework, Mrsamase simplifies complex financial operations such as earnings management, commissions, and in-platform purchases, while providing users with real-time visibility into their financial activities. 
        </span>
      </div>
      <div class="row align-items-center justify-content-center mt-15">
        <h3>Usage Example</h3>
        <span>
          Use the input boxes below to see how it works in real time
        </span>
      </div>
      <div class="form-group">
        <div class='form-text'>
          <label for="facebook" class="col-sm-2 col-form-label">Fixed Rate</label>
          </div>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="amount" value="1 Coin = ₦100.00" disabled>
        </div>
      </div>
      <div class="form-group">
        <div class='form-text'>
          <label for="facebook" class="col-sm-2 col-form-label">Amount (Coins)</label>
          </div>
        <div class="col-sm-10">
          <input type="text" class="form-control input_amount" name="amount" placeholder="Enter amount to convert">
        </div>
      </div>
      <div class="form-group">
          <div class='form-text'>
              <label for="facebook" class="col-sm-2 col-form-label">Amount (₦aira)</label>
          </div>
        <div class="col-sm-10">
          <input type="text" step="any" class="form-control output_amount" name="amount" disabled>
        </div>
      </div>
      <div class="row align-items-center justify-content-center mt-15">
          <span class='info-span'></span>
      </div>
    </div>
    <div class='close-overlay'>
      <i class='fa fa-times' aria-hidden='true'></i>
    </div>
  </div>

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('./scripts/calculator.js'); ?>" type='module'></script>

</body>
</html>
