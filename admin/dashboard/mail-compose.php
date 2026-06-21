<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Necessary Models
  $mailModel = $container->get(\App\Models\Mail::class);

  // Get counts
  $totalInbox  = $mailModel->countInbox($email);
  $totalOutbox = $mailModel->countOutbox($fullName);

  // Get users
  $allAdmins    = $userModel->allByRole('Admin');
  $allCustomers = $userModel->allByRole('Customer');
  $allVendors   = $userModel->allByRole('Vendor');
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <title> Admin | Compose Message</title>
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
              <h1><b>Compose</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Compose</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content" style='overflow-y: auto !important;box-sizing: border-box;'>
        <div class="container-fluid">
          <div class="row" style="height: 550px;">
            <div class="col-md-3">
              <a href="mailbox" class="btn btn-primary btn-block mb-3">Back to Inbox</a>

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
                      <a href="mailbox" class="nav-link">
                        <i class="fas fa-inbox"></i> Inbox
                        <span class="badge bg-primary float-right">
                          <?= $ratingManager->format($totalInbox) ?>
                        </span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./mail-sent" class="nav-link">
                        <i class="far fa-envelope"></i> Sent
                        <span class="badge bg-primary float-right">
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
                  <h3 class="card-title">Compose New Message</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                    <input class="form-control form-recipient" value="Choose recipients">
                  </div>
                  <div class="form-group">
                    <input class="form-control form-subject" placeholder="Subject:">
                  </div>
                  <div class="form-group">
                      <textarea class="form-control form-message" style="height: 300px"></textarea>
                  </div>
                  <div class="form-group">
                    <div class="btn btn-default btn-file">
                      <i class="fas fa-paperclip"></i> Attachment
                      <input type="file" class="form-attachment">
                    </div>
                    <p class="help-block">Max. 32MB</p>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <div class="float-right">
                    <!--<button type="button" class="btn btn-default"><i class="fas fa-pencil-alt"></i> Draft</button>-->
                    <button type="button" class="btn btn-primary btn-send" data-name="<?= $fullName ?>"><i class="far fa-envelope"></i> Send</button>
                  </div>
                  <button type="button" class="btn btn-default btn-discard"><i class="fas fa-times"></i> Discard</button>
                </div>
                <!-- /.card-footer -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer section -->
    <?php include_once 'includes/footer.php'; ?>

    <div class='mail-overlay'>
      <div class='main-content'>
        <div class='main-content-select'>
            <div id='view-admins'>Admins</div>
            <div id='view-customers'>Customers</div>
            <div id='view-vendors'>Vendors</div>
            <div class='main-content-close' id='close-modal'><i class='fa fa-times'></i></div>
        </div>
        <div class='main-content-header'>
          <div class='main-content-header-text'>
            Send to everyone
          </div>
          <div class='main-content-header-icon'>
            <input type='checkbox' id='send-to-all'>
          </div>
        </div>
          <div class='main-content-view'>
            <div class='main-content-inner'>
              <div class='user-view' id='admin-section-view'>
                  <div class='user-view-header'>
                    <div class='user-view-header-text'>Admins Only</div>
                    <div class='user-view-header-icon'>
                      <input type='text' class="user-search" placeholder="Search admins...">
                      <input type='checkbox' id='send-all-admin'>
                    </div>
                  </div>
                <div class='user-scroll' id='admin-section'>
                  <?php if (!empty($allAdmins)): ?>
                    <!------ Dispplay recipients ------>
                    <?php foreach ($allAdmins as $recipient): ?>
                      <?php
                        $recipientFullname = $recipient['firstname'] . ' ' . $recipient['lastname'];
                        $displayAvatar = "../../assets/img/avatar.jpg";
                      ?>

                      <div class='user-card'>
                        <div class='user-card-image' title='<?= $recipient['email'] ?>'>
                          <img src='<?= $displayAvatar ?>' alt='admin-image'>
                        </div>
                        <div class='user-card-name'><?= $recipientFullname ?></div>
                        <div class='user-card-select'>
                          <input type='checkbox' class='add-user' id='<?= $recipient['user_id'] ?>' data-member='Admin'>
                          <p class='email-address'><?= $recipient['email'] ?></p>
                        </div>
                      </div>

                    <?php endforeach; ?>

                    <?php else: ?>
                    <!-- No admin Available -->
                    <span class='text-center'>No admin available</span>

                  <?php endif; ?>
                </div>
              </div>

              <div class='user-view' id='affiliate-section-view'>
                <div class='user-view-header'>
                    <div class='user-view-header-text'>Customers Only</div>
                    <div class='user-view-header-icon'>
                        <input type='text' class="user-search" placeholder="Search customers...">
                        <input type='checkbox' id='send-all-affiliate'>
                    </div>
                  </div>
                <div class='user-scroll' id='customer-section'>
                  <?php if (!empty($allCustomers)): ?>
                    <!------ Dispplay recipients ------>
                    <?php foreach ($allCustomers as $recipient): ?>
                      <?php
                        $recipientFullname = $recipient['firstname'] . ' ' . $recipient['lastname'];
                        $displayAvatar = "../../assets/img/avatar.jpg";
                      ?>

                      <div class='user-card'>
                        <div class='user-card-image' title='<?= $recipient['email'] ?>'>
                          <img src='<?= $displayAvatar ?>' alt='customer-image'>
                        </div>
                        <div class='user-card-name'><?= $recipientFullname ?></div>
                        <div class='user-card-select'>
                          <input type='checkbox' class='add-user' id='<?= $recipient['user_id'] ?>' data-member='Customer'>
                          <p class='email-address'><?= $recipient['email'] ?></p>
                        </div>
                      </div>

                    <?php endforeach; ?>

                    <?php else: ?>
                    <!-- No customer Available -->
                    <span class='text-center'>No customer available</span>

                  <?php endif; ?>
                </div>
              </div>

              <div class='user-view' id='vendor-section-view'>
                <div class='user-view-header'>
                  <div class='user-view-header-text'>Vendors Only</div>
                  <div class='user-view-header-icon'>
                      <input type='text' class="user-search" placeholder="Search vendors...">
                      <input type='checkbox' id='send-all-vendor'>
                  </div>
                </div>
                <div class='user-scroll' id='vendor-section'>
                  <?php if (!empty($allVendors)): ?>
                    <!------ Dispplay recipients ------>
                    <?php foreach ($allVendors as $recipient): ?>
                      <?php
                        $recipientFullname = $recipient['firstname'] . ' ' . $recipient['lastname'];
                        $displayAvatar = "../../assets/img/avatar.jpg";
                      ?>

                      <div class='user-card'>
                        <div class='user-card-image' title='<?= $recipient['email'] ?>'>
                          <img src='<?= $displayAvatar ?>' alt='vendor-image'>
                        </div>
                        <div class='user-card-name'><?= $recipientFullname ?></div>
                        <div class='user-card-select'>
                          <input type='checkbox' class='add-user' id='<?= $recipient['user_id'] ?>' data-member='Vendor'>
                          <p class='email-address'><?= $recipient['email'] ?></p>
                        </div>
                      </div>

                    <?php endforeach; ?>

                    <?php else: ?>
                    <!-- No vendor Available -->
                    <span class='text-center'>No vendor available</span>

                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
      </div>
  </div>
    
    <!-- Summernote -->
    <script src="../../assets/plugins/summernote/summernote-bs4.min.js"></script>

  <!-- Page specific script -->
  <script>
    $(function () {
      //Add text editor
      $('.form-message').summernote()
    })
  </script>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('./scripts/mail-compose.js'); ?>" type='module'></script>
</body>
</html>
