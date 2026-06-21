<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $mailModel = $container->get(\App\Models\Mail::class);

  // Get counts
  $totalInbox = $mailModel->countInbox($email);
  $totalOutbox = $mailModel->countOutbox($fullName);

  // Get requested page from the URL, default to 1
  $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

  // Get combined sales stats
  $outboxMails = $mailModel->getOutbox($fullName, $page);

  // Get pagination values
  $totalPages = $outboxMails['total_pages'];
  $currentPage = $outboxMails['page'];

  // Base URL for pagination links — change this to your actual route or script
  $baseUrl = './mail-sent';
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <title> Admin | Outbox</title>
  <?php include_once 'includes/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini">
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
              <h1><b>Outbox (<?= $ratingManager->format($outboxMails['total']) ?>)</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Outbox</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
        <div class="row" style="height: 550px;">
          <div class="col-md-3">
            <a href="mail-compose" class="btn btn-primary btn-block mb-3">Compose</a>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Folders</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                  <li class="nav-item active">
                    <a href="./mailbox" class="nav-link">
                      <i class="fas fa-inbox"></i> Inbox
                      <span class="badge bg-primary float-right inbox-count">
                        <?= $ratingManager->format($totalInbox) ?>
                      </span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-envelope"></i> Sent
                      <span class="badge bg-primary float-right outbox-count">
                        <?= $ratingManager->format($totalOutbox) ?>
                      </span>
                    </a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Sent Mails</h3>

                <div class="card-tools">
                  <div class="input-group input-group-sm">
                    <input type="text" class="form-control mail-search" placeholder="Search Outbox">
                    <div class="input-group-append">
                      <div class="btn btn-primary">
                        <i class="fas fa-search"></i>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="mailbox-controls">
                  <!-- Check all button -->
                  <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
                  </button>
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="far fa-trash-alt"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-reply"></i>
                    </button>
                    <button type="button" class="btn btn-default btn-sm">
                      <i class="fas fa-share"></i>
                    </button>
                  </div>
                  <!-- /.btn-group -->
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fas fa-sync-alt"></i>
                  </button>
                  <div class="float-right">
                    <?= $outboxMails['display_range'] ?>
                    <div class="btn-group">
                     <button type="button" class="btn btn-default btn-sm">
                        <a href="<?= ($currentPage > 1) ? pageUrl($currentPage - 1, $baseUrl) : '#' ?>" class='outbox-previous view-link' data-total='<?= $totalPages ?>'>
                          <i class="fas fa-chevron-left"></i>
                        </a>
                      </button>
                      <button type="button" class="btn btn-default btn-sm">
                         <a href="<?= ($currentPage < $totalPages) ? pageUrl($currentPage + 1, $baseUrl) : '#' ?>" class='outbox-next view-link' data-total='<?= $totalPages ?>'>
                            <i class="fas fa-chevron-right"></i>
                          </a>
                      </button>
                    </div>
                   <!-- /.btn-group -->
                  </div>
                  <!-- /.float-right -->
                </div>
                <div class="table-responsive mailbox-messages">
                  <table class="table table-hover table-striped">
                    <tbody>
                      <?php if (!empty($outboxMails['mails'])): ?>
                        <!------ Dispplay Withdrawals ------>
                        <?php foreach ($outboxMails['mails'] as $mail): ?>
                          <?php
                            $long = $mail['mail_message'] ?? '';
                            $short = strlen($long) > 50 ? substr($long, 0, 50) . '...' : $long;
                            $media = isset($mail['mail_filename']) && $mail['mail_filename'] !== 'None' ? '<i class="fas fa-paperclip"></i>' : '';
                          ?>

                          <tr class='mail-list content-row' data-id='<?= $mail['mail_id'] ?>'>
                            <td>
                                <div class="icheck-primary">
                                    <input type="checkbox" value="">
                                    <label></label>
                                </div>
                            </td>
                            <td class="mailbox-star">
                                <a href="#"><i class="fas fa-star-o text-warning"></i></a>
                            </td>
                            <td class="mailbox-name">
                                <a href="./mail-read?id=<?= $mail['mail_id'] ?>"><?= $mail['mail_sender'] ?></a>
                            </td>
                            <td class="mailbox-subject"><?= $short ?></td>
                            <td class="mailbox-attachment"><?= $media ?></td>
                            <td class="mailbox-date"><?= $mail['mail_date'] ?> <?= $mail['mail_time'] ?></td>
                          </tr>

                        <?php endforeach; ?>

                        <?php else: ?>
                        <!-- No Withdrawal Available -->
                        <tr>
                          <td colspan="6" class='text-center'>No mails available</td>
                        </tr>

                      <?php endif; ?>
                    </tbody>  
                  </table>
                  <!-- /.table -->
                </div>
                <!-- /.mail-box-messages -->
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer section -->
    <?php include_once 'includes/footer.php'; ?>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('./scripts/mail.js'); ?>" type="module"></script>
</body>
</html>
