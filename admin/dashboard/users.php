<?php 
  // Import Config File (Remove the demo images at launch)
  require_once __DIR__ . '/includes/config.php';

  // Import Initializer File
  require_once __DIR__ . '/includes/init.php';

  // Get role and page ID from URL
  $role = isset($_GET['role']) && is_string($_GET['role']) ? (string)$_GET['role'] : null;
  $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

  // All Users
  $userList = $userModel->getByRole($role, $page);

  // Pagination parameters
  $totalPages  = $userList['total_pages'];
  $currentPage = $userList['page'];
  $baseUrl     = "users?view=admin";
?> 

<!DOCTYPE html>
<html lang="en" style='overflow-x: hidden !important;width: 100vw;height: 100vh;'>
<head>
  <title>Admin | Users</title>
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
          <div class="col-sm-6 header-count">
            <h1><b><?= $role . 's' ?> (<?= $ratingManager->format($userList['total']) ?>)</b></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href=".">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
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
          <h3 class="card-title">All users</h3>
          <div class="card-tools">
            <!-- Previous button -->
            <button type="button" class="btn btn-info btn-tool btn-previous wb-50 hide">
              <a href="<?= ($currentPage > 1) ? pageUrl($currentPage - 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage <= 1) ? 'disabled' : '' ?>">&laquo;</a>
            </button>
            <!-- Next button -->
             <button type="button" class="btn btn-info btn-tool btn-next wb-50 hide">
              <a href="<?= ($currentPage < $totalPages) ? pageUrl($currentPage + 1, $baseUrl) : '#' ?>" class="rounded <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">&raquo;</a>
            </button>
            <button type="button" class="btn bg-primary color-white btn-sm btn-all">All</button>
            <button type="button" class="btn bg-primary color-white btn-sm btn-pending">Pending</button>
            <button type="button" class="btn bg-primary color-white btn-sm btn-active">Active</button>
            <?php if (!empty($userList['users'])): ?>
              <!-- Default button -->
              <button type="button" class="btn bg-primary color-white btn-sm btn-load">Load More</button>
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
                <th>S/N</th>
                <th>Profile</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Country</th>
                <th>State</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($userList['users'])): ?>
                <!-- Display All Products -->
                <?php foreach ($userList['users'] as $user): ?>

                  <tr class='content-row' data-id='<?=$user['user_id'] ?>'>
                    <td>#</td>
                    <td>
                      <img src='<?= ($user['avatar'] !== 'None') ? $user['avatar'] : '../../../assets/img/avatar.jpg' ?>' class='profile-user-img img-fluid img-circle' alt='User Image'>
                    </td>
                    <td class='first-name'><?= $user['firstname'] ?></td>
                    <td class='last-name'><?= $user['lastname'] ?></td>
                    <td class='email'><?= $user['email'] ?></td>
                    <td class='contact'><?= $user['contact'] ?></td>
                    <td class='country'><?= $user['country'] ?></td>
                    <td class='state'><?= $user['user_state'] ?></td>
                    <td class='role'><?= $user['user_role'] ?></td>
                    <td class='status'>
                      <button class='btn <?= ($user['user_status'] === 'Active') ? 'btn-success' : 'btn-danger' ?> btn-sm'>
                        <?= $user['user_status'] ?>
                      </button> 
                    </td>
                    <td><?= $user['created_at'] ?></td>
                    <td class='action'>
                      <div style="display: flex; gap: 10px;">
                        <?= ($role === 'Vendor') ? "<button class='btn bg-primary color-white btn-sm btn-doc wmg-70'>View ID</button>" : ""?>
                        <?= ($user['user_status'] === 'Active') 
                          ? "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Deactivate</button>" 
                          : "<button class='btn bg-primary color-white btn-sm btn-action w-150'>Activate</button>"
                        ?>
                        <button class='btn btn-danger btn-delete btn-sm wmg-70'>Delete</button> 
                      </div>
                    </td>
                  </tr>

                <?php endforeach; ?>

                <?php else: ?>
                  <!-- No Product Available -->
                  <td colspan="12" class='text-center'>No user available</td>

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
  
  <!-- Document Overlay --->
  <div class='overlay'>
    <div class='close-overlay'>
      <i class='fa fa-times' aria-hidden='true'></i>
    </div>
  </div>

  <!-- Custom Scripts -->
  <script src="<?php echo $cacheManager->parse('scripts/user-list.js'); ?>" type="module"></script>

</body>
</html>
