  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 bg-primary overlow-y-auto overlow-x-hidden">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
       <img src="../assets/img/logo.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3 w-35 h-40 border-radius-50 opacity-9">
      <span class="brand-text font-weight-light">Mrsamase</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar h-630 overlow-x-hidden">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <?php if ($isLoggedIn): ?>
            <img src='<?= $profile ?>' class='img-circle elevation-2 wmg-70 hmg-70 border-radius-50 border-profile' alt='User Image'>
          <?php endif; ?>
        </div>
        <div class="info">
          <?php if ($isLoggedIn): ?>
            <a href='./profile' class='d-block overflow-none nowrap ellipsis color-white'><?= $fullName ?? 'Vendor' ?></a>
          <?php endif; ?>
          <p class="color-gray">Vendor</p>
        </div>
      </div>
     
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="." class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./store-list" class="nav-link">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
                Stores
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./notification" class="nav-link">
              <i class="nav-icon fas fa-bell"></i>
              <p>
                Notifications
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./wallet" class="nav-link">
              <i class="nav-icon fas fa-wallet"></i>
              <p>
                Wallet
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="./withdrawal" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Withdrawals
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-envelope-open"></i>
              <p>
                Mail
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./mailbox" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inbox</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./mail-compose" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Compose</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="./profile" class="nav-link">
              <i class="fas fa-user-cog nav-icon"></i>
              <p>Profile</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link logout">
              <i class="nav-icon fas fa-arrow-left"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>