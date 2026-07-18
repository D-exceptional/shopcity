<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Initialize Models
  $mailModel = $container->get(\App\Models\Mail::class); 

  // Get counts
  $totalInbox  = $mailModel->countInbox($email);
  $totalOutbox = $mailModel->countOutbox($fullName);

  // Get mail ID from URL
  $mailId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 1;

  // Get mail details
  $mailDetails = $mailModel->getMail($mailId);

  // Prepare attachment data
  $filename  = $mailDetails['mail_filename'] ?? 'None';
  $extension = strtolower($mailDetails['mail_extension'] ?? 'None');
  $filePath  = "../../attachments/$filename";
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Vendor | Read Mail</title>
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

    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><b>Read Mail</b></h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href=".">Home</a></li>
                <li class="breadcrumb-item active">Read Mail</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content" style="overflow-y: auto !important; box-sizing: border-box;">
        <div class="container-fluid">
          <div class="row" style="height: 550px;">

            <!-- Sidebar -->
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
                        <span class="badge bg-primary float-right"><?= $totalInbox ?></span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="./mail-sent" class="nav-link">
                        <i class="far fa-envelope"></i> Sent
                        <span class="badge bg-primary float-right"><?= $totalOutbox ?></span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Mail Content -->
            <div class="col-md-9">
              <div class="card card-primary card-outline">
                <div class="card-header">
                  <h3 class="card-title">Read Mail</h3>
                  <div class="card-tools">
                    <a href="#" class="btn btn-tool" title="Previous"><i class="fas fa-chevron-left"></i></a>
                    <a href="#" class="btn btn-tool" title="Next"><i class="fas fa-chevron-right"></i></a>
                  </div>
                </div>

                <div class="card-body p-0">
                  <div class="mailbox-read-info">
                    <h6>
                      From: <b><?= $mailDetails['mail_sender'] ?></b>
                      <span class="mailbox-read-time float-right">
                        <?= $mailDetails['mail_date'] . ' ' . $mailDetails['mail_time'] ?>
                      </span>
                    </h6>
                  </div>

                  <div class="mailbox-read-message">
                    <p><?= nl2br($mailDetails['mail_message']) ?></p>
                  </div>
                </div>

                <div class="card-footer bg-white">
                  <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                    <?php
                      // Ensure these variables exist (single attachment model)
                      $filename  = $mailDetails['mail_filename'] ?? 'None';
                      $extension = strtolower($mailDetails['mail_extension'] ?? 'None');
                      $filePath  = $filename ? "../../attachments/" . $filename : '';
                    ?>
                      
                    <div class="card-footer bg-white">
                      <ul class="mailbox-attachments d-flex align-items-stretch clearfix">
                        <?php if ($filename !== 'None'): 
                            // choose rendering based on extension
                            if ($extension === 'pdf'): ?>
                              <li>
                                <span class="mailbox-attachment-icon"><i class="far fa-file-pdf"></i></span>
                                <div class="mailbox-attachment-info">
                                  <a href="<?= $filePath ?>" class="mailbox-attachment-name">
                                    <i class="fas fa-paperclip"></i> <?= $filename ?>
                                  </a>
                                  <span class="mailbox-attachment-size clearfix mt-1">
                                    <a href="<?= $filePath ?>" class="btn btn-default btn-sm float-right" download>
                                      <i class="fas fa-cloud-download-alt"></i>
                                    </a>
                                  </span>
                                </div>
                              </li>

                            <?php elseif ($extension === 'docx'): ?>
                              <li>
                                <span class="mailbox-attachment-icon"><i class="far fa-file-word"></i></span>
                                <div class="mailbox-attachment-info">
                                  <a href="<?= $filePath ?>" class="mailbox-attachment-name">
                                    <i class="fas fa-paperclip"></i> <?= $filename ?>
                                  </a>
                                  <span class="mailbox-attachment-size clearfix mt-1">
                                    <a href="<?= $filePath ?>" class="btn btn-default btn-sm float-right" download>
                                      <i class="fas fa-cloud-download-alt"></i>
                                    </a>
                                  </span>
                                </div>
                              </li>

                            <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png'])): ?>
                              <li>
                                <span class="mailbox-attachment-icon has-img">
                                  <img src="<?= $filePath ?>" alt="Image">
                                </span>
                                <div class="mailbox-attachment-info">
                                  <a href="#" class="mailbox-attachment-name"><i class="fas fa-camera"></i> <?= $filename ?></a>
                                  <span class="mailbox-attachment-size clearfix mt-1">
                                    <a href="<?= $filePath ?>" class="btn btn-default btn-sm float-right" download>
                                      <i class="fas fa-cloud-download-alt"></i>
                                    </a>
                                  </span>
                                </div>
                              </li>

                            <?php elseif ($extension === 'mp4'): ?>
                              <li>
                                <span class="mailbox-attachment-icon has-img">
                                  <video src="<?= $filePath ?>" controls style="max-height: 133px;"></video>
                                </span>
                                <div class="mailbox-attachment-info">
                                  <a href="#" class="mailbox-attachment-name"><i class="fas fa-video"></i> <?= $filename ?></a>
                                  <span class="mailbox-attachment-size clearfix mt-1">
                                    <a href="<?= $filePath ?>" class="btn btn-default btn-sm float-right" download>
                                      <i class="fas fa-cloud-download-alt"></i>
                                    </a>
                                  </span>
                                </div>
                              </li>

                            <?php else: // unknown extension ?>
                              <li>
                                <span class="mailbox-attachment-icon"><i class="far fa-file"></i></span>
                                <div class="mailbox-attachment-info">
                                  <a href="<?= $filePath ?>" class="mailbox-attachment-name"><?= $filename ?></a>
                                  <span class="mailbox-attachment-size clearfix mt-1">
                                    <a href="<?= $filePath ?>" class="btn btn-default btn-sm float-right" download>
                                      <i class="fas fa-cloud-download-alt"></i>
                                    </a>
                                  </span>
                                </div>
                              </li>
                            <?php endif; ?>
                        <?php else: ?>
                          <li><h6>No attachment available for this email</h6></li>
                        <?php endif; ?>
                      </ul>
                    </div>

                  </ul>
                </div>

                <div class="card-footer">
                  <div class="float-right">
                    <button type="button" class="btn btn-default"><i class="fas fa-reply"></i> Reply</button>
                    <button type="button" class="btn btn-default"><i class="fas fa-share"></i> Forward</button>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include_once 'includes/footer.php'; ?>

</body>
</html>
