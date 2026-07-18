<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $walletModel = $container->get(\App\Models\Wallet::class); 

  // Get combined sales stats
  $withdrawals   = $walletModel->getPaymentsByUser(WITHDRAWAL_TABLE, $userId);
  $walletBalance = $walletModel->getBalance(PAYOUT_WALLET, $userId);
  $bankDetails   = $walletModel->getBankDetails($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Vendor | Withdrawals</title>
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
              <h1><b>Withdrawal</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Withdrawals</li>
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
            <h3 class="card-title">Withdrawal</h3>
            <div class="card-tools"> 
              <?php if ($walletBalance > 0): ?>
                <button class='btn btn-sm btn-start bg-primary color-white'>Place Withdrawal</button> 
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
                    <th>Amount</th>
                    <th>Bank</th>
                    <th>Account</th>
                    <th>Reference</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($withdrawals)): ?>
                    <!------ Dispplay Withdrawals ------>
                    <?php foreach ($withdrawals as $withdrawal): ?>

                      <tr data-id='<?= $withdrawal['withdrawal_id'] ?>'>
                        <td>#</td>
                        <td><?= $ratingManager->format((float)$withdrawal['amount'] / BASE_CONVERSION_RATE ?? 0); ?> Coins</td>
                        <td><?= $withdrawal['bank'] ?></td>
                        <td><?= $withdrawal['account'] ?></td>
                        <td><?= $withdrawal['reference'] ?></td>
                        <td><?= $withdrawal['narration'] ?></td>
                        <td>
                          <button class='btn <?= ($withdrawal['withdrawal_status'] === 'Completed') ? 'btn-success' : 'btn-danger' ?> btn-sm'>
                            <?= $withdrawal['withdrawal_status'] ?>
                          </button> 
                        </td>
                        <td><?= $withdrawal['created_at'] ?></td>
                        <td>
                          <button class='btn btn-info btn-sm'>
                            View
                          </button> 
                        </td>
                      </tr>

                    <?php endforeach; ?>

                    <?php else: ?>
                    <!-- No Withdrawal Available -->
                    <tr>
                      <td colspan="9" class='text-center'>No withdrawals available</td>
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

  <!-- Load More --->
  <?php if (!empty($withdrawals)): ?>
    <button type="button" class="btn btn-sm load-more bg-primary color-white">Load More</button>
  <?php endif; ?>
  <!-- ./Load More -->
  
  <!-- Funds Withdraw Overlay --->
  <div class='overlay'>
    <form class="form-horizontal form-withdrawal">
        <div class="row align-items-center justify-content-center mt-15">
            <h3>Withdraw Funds</h3>
        </div>
      <div class="form-group">
          <div class='form-text'>
              <label for="facebook" class="col-sm-2 col-form-label">Available Coins</label>
          </div>
        <div class="col-sm-10">
          <input type="number" class="form-control available_amount" name="available_amount" value="<?= $walletBalance / BASE_CONVERSION_RATE ?>" disabled>
        </div>
      </div>
      <div class="form-group">
          <div class='form-text'>
              <label for="facebook" class="col-sm-2 col-form-label">Withdrawal Amount</label>
          </div>
        <div class="col-sm-10">
          <input type="number" step="any" class="form-control withdrawal_amount" name="withdrawal_amount" placeholder="Amount to withdraw">
        </div>
      </div>
      <div class="form-group">
        <div class="offset-sm-2 col-sm-10">
            <div class="row align-items-center justify-content-center mt-15">
              <?php if ($walletBalance > 0): ?>
                <button type='button' class='btn btn-success btn-withdraw' 
                  data-bank="<?= isset($bankDetails['bank_name']) && !is_null($bankDetails['bank_name']) ? $bankDetails['bank_name'] : 'None' ?>"
                  data-account="<?= isset($bankDetails['account_number']) && !is_null($bankDetails['account_number']) ? $bankDetails['account_number'] : 0 ?>"
                >
                Place Withdrawal
                </button>

                <?php else: ?>
                <button type='button' class='btn btn-danger' disabled>Insufficient Balance</button>

              <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="row align-items-center justify-content-center mt-15">
          <span class='info-span'></span>
      </div>
    </form>
    <div class='close-overlay'>
      <i class='fa fa-times' aria-hidden='true'></i>
    </div>
  </div>

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('./scripts/withdrawals.js'); ?>" type='module'></script>

</body>
</html>
