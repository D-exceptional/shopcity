<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $walletModel = $container->get(\App\Models\Wallet::class);

  // Get view and page ID from URL
  $status = isset($_GET['status']) && is_string($_GET['status']) ? (string)$_GET['status'] : null;
  $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

  // Payments List
  $paymentList = $walletModel->getWithdrawalsByStatus($status, $page);

  // Pagination parameters
  $totalPages  = $paymentList['total_pages'];
  $currentPage = $paymentList['page'];
  $baseUrl     = "payouts?status={$status}";
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin | Payouts</title>
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
            <h1><b><?= $status ?> Payouts (<?= $ratingManager->format($paymentList['total']) ?>)</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
              <li class="breadcrumb-item active">Payouts</li>
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
          <h3 class="card-title">Payouts</h3>
          <div class="card-tools">
            <?php if ($status === 'Pending' && !empty($paymentList['payments'])): ?>
              <!-- Default button -->
              <button type="button" class="btn bg-primary color-white btn-sm btn-all">Pay All</button>
            <?php endif; ?>
            <?php if (!empty($paymentList['payments'])): ?>
              <!-- Default button -->
              <button type="button" class="btn bg-primary color-white btn-sm btn-load">Load More</button>
            <?php endif; ?>
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse" data-sum="<?= $paymentList['sum'] ?>">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0 overlow-x-auto white-space-normal">
          <table class="table table-striped projects">
            <thead>
              <tr>
                <th>S/N</th>
                <th>Fullname</th>
                <th>Email</th>
                <th>Country</th>
                <th>Amount</th>
                <th>Account</th>
                <th>Bank</th>
                <th>Code</th>
                <th>Currency</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id='transfer-lists'>
              <?php if (!empty($paymentList['payments'])): ?>
                <!-- Display All Payments -->
                <?php foreach ($paymentList['payments'] as $payment): ?>

                  <?php
                    // Set status maps
                    $statusMaps = [
                      'Pending'   => ['style' => 'btn-danger', 'text'  => 'Pending'],
                      'Completed' => ['style' => 'btn-success', 'text' => 'Completed'],
                    ];
                  ?>

                  <tr class='content-row' data-id='<?= $payment['withdrawal_id'] ?>'>
                    <td>#</td>
                    <td class='fullname'><?= $payment['firstname'] . ' ' . $payment['lastname'] ?></td>
                    <td class='email'><?= $payment['email'] ?></td>
                    <td class='country'><?= $payment['country'] ?></td>
                    <td class='amount'><?= $currencyManager->format((float)$payment['amount'] ?? 0); ?></td>
                    <td class='account'><?= $payment['account'] ?></td>
                    <td class='bank'><?= $payment['bank'] ?></td>
                    <td class='code'><?= $payment['bank_code'] ?></td>
                    <td class='currency'><?= $payment['currency_code'] ?></td>
                    <td class='reference'><?= $payment['reference'] ?></td>
                    <td class='time'><?= $payment['created_at'] ?></td>
                    <td class='status'>
                      <button class='btn <?= $statusMaps[$payment['withdrawal_status']]['style'] ?> btn-sm'>
                        <?= $payment['withdrawal_status'] ?>
                      </button> 
                    </td>
                    <td class='action'><button class='btn btn-info btn-sm'><?= $payment['withdrawal_status'] === 'Pending' ? 'Pay' : 'View' ?></button></td>
                  </tr>

                <?php endforeach; ?>

              <?php else: ?>
                <!-- No Product Available -->
                <td colspan="14" class='text-center'>No payments available</td>

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

  <!-- Footer section -->
  <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('scripts/payouts.js'); ?>" type="module"></script>

</body>
</html>
