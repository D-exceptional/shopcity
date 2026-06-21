  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 bg-primary overlow-y-auto overlow-x-hidden">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
       <img src="../../assets/img/logo.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3 w-35 h-40 border-radius-50 opacity-9">
      <span class="brand-text font-weight-light">Mrsamase</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar h-630 overlow-x-hidden">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <?php if ($isLoggedIn): ?>
            <img src='<?= $storeAvatar ?? '../../assets/img/header-bg.jpg' ?>' class='img-circle elevation-2 wmg-70 hmg-70 border-radius-50 border-profile' alt='Store Image'>
          <?php endif; ?>
        </div>
        <div class="info">
          <?php if ($isLoggedIn): ?>
            <a href='./settings?id=<?= $storeId ?>' class='d-block overflow-none nowrap ellipsis'><?= $storeName ?? 'Store' ?></a>
          <?php endif; ?>
          <p class="color-gray">Store</p>
        </div>
      </div>
     
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href=".?id=<?= $storeId ?>" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>
               Products
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./product-list?id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./product-create?id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Orders
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./order-list?status=Pending&id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pending</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./order-list?status=Shipped&id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Shipped</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./order-list?status=Delivered&id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Delivered</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
               Customers
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./customers?type=unique&id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>All</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./customers?type=loyal&id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Loyal</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tag"></i>
              <p>
               Copons
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./coupon-list?id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./coupon-create?id=<?= $storeId ?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="./settings?id=<?= $storeId ?>" class="nav-link">
              <i class="fas fa-cog nav-icon"></i>
              <p>Settings</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link store-logout">
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