<!-- Preloader -->
<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?= asset('plugins/dist/img/AdminLTELogo.png') ?>" alt="AdminLTELogo" height="60" width="60">
</div>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">

            <?php if ($isLoggedIn): ?>

                <p class='d-block' style='overflow: hidden !important;white-space: nowrap !important;text-overflow: ellipsis !important;'>

                    <?= '<b>' . $timeManager->greet() . ', ' .  $firstName . '</b>' ?>
                </p>

            <?php endif; ?>

        </a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Refresh -->
      <li class="nav-item">
        <a class="nav-link page-refresh" data-widget="navbar-refresh" href="#" role="button">
          <i class="fas fa-random"></i>
        </a>
      </li>
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar page-search" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link notification-trigger" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>

            <?php if ($isLoggedIn): ?>

                <?php
                    $unreadNotifications = $notifications['unread'] ?? 0;
                ?>

                <?php if ($unreadNotifications > 0): ?>

                    <span class='badge badge-danger navbar-badge r-25'>
                        <?= $unreadNotifications ?>
                    </span>

                <?php endif; ?>

            <?php endif; ?>

        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">

            <?php if ($isLoggedIn): ?>

                <?php
                    $unreadNotifications = $notifications['unread'] ?? 0;
                    $counterText = ($unreadNotifications > 0) ? "{$unreadNotifications} New notifications" : 'No new notifications';
                ?>

                <span class='dropdown-item dropdown-header'>
                    <?= $counterText ?>
                </span>

            <?php endif; ?>

        </span>
        <div class="dropdown-divider"></div>

        <!---------- NEW ORDER ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $newOrders = $notifications['newOrders'] ?? ['count' => 0, 'last_date' => null];
                $count = $newOrders['count'];
                $date  = $newOrders['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-shopping-cart mr-2'></i>
                    <?= $count ?> New orders
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- ORDER COMPLETION ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $orderCompletions = $notifications['orderCompletions'] ?? ['count' => 0, 'last_date' => null];
                $count = $orderCompletions['count'];
                $date  = $orderCompletions['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-check mr-2'></i>
                    <?= $count ?> New order completions
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- ORDER CANCELLATION ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $orderCancellations = $notifications['orderCancellations'] ?? ['count' => 0, 'last_date' => null];
                $count = $orderCancellations['count'];
                $date  = $orderCancellations['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-ban mr-2'></i>
                    <?= $count ?> New order cancellations
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- ITEM UPDATE ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $itemUpdates = $notifications['itemUpdates'] ?? ['count' => 0, 'last_date' => null];
                $count = $itemUpdates['count'];
                $date  = $itemUpdates['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-rss mr-2'></i>
                    <?= $count ?> New item status updates
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- PRODUCT REVIEW ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $productReviews = $notifications['productReviews'] ?? ['count' => 0, 'last_date' => null];
                $count = $productReviews['count'];
                $date  = $productReviews['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-thumbs-up mr-2'></i>
                    <?= $count ?> New product reviews
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- NEW MESSAGE ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $newMessages = $notifications['newMessages'] ?? ['count' => 0, 'last_date' => null];
                $count = $newMessages['count'];
                $date  = $newMessages['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-envelope mr-2'></i>
                    <?= $count ?> New messages
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>
        
        <!---------- PRODUCT APPROVAL ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $productApprovals = $notifications['productApprovals'] ?? ['count' => 0, 'last_date' => null];
                $count = $productApprovals['count'];
                $date  = $productApprovals['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-check mr-2'></i>
                    <?= $count ?> New product approvals
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- FUND REDEEM ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $fundRedeems = $notifications['fundRedeems'] ?? ['count' => 0, 'last_date' => null];
                $count = $fundRedeems['count'];
                $date  = $fundRedeems['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-retweet mr-2'></i>
                    <?= $count ?> New fund redeems
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- FUND REQUEST ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $fundRequests = $notifications['fundRequests'] ?? ['count' => 0, 'last_date' => null];
                $count = $fundRequests['count'];
                $date  = $fundRequests['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-wallet mr-2'></i>
                    <?= $count ?> New fund requests
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>

            <?php endif; ?>

        <?php endif; ?>

        <!---------- FUND PAYOUT ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $fundPayouts = $notifications['fundPayouts'] ?? ['count' => 0, 'last_date' => null];
                $count = $fundPayouts['count'];
                $date  = $fundPayouts['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-university mr-2'></i>
                    <?= $count ?> New fund payouts
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <div class='dropdown-divider'></div>
                
            <?php endif; ?>

        <?php endif; ?>

        <!---------- ACCOUNT UPDATE ----------->
        <?php if ($isLoggedIn): ?>

            <?php
                $accountUpdates = $notifications['accountUpdates'] ?? ['count' => 0, 'last_date' => null];
                $count = $accountUpdates['count'];
                $date  = $accountUpdates['last_date'];
            ?>

            <?php if ($count > 0): ?>

                <a href='#' class='dropdown-item'>
                    <i class='fas fa-refresh mr-2'></i>
                    <?= $count ?> New account updates
                    <span class='float-right text-muted text-sm'><?= $count ?></span>
                </a>
                <!--<div class='dropdown-divider'></div>-->

            <?php endif; ?>
            
        <?php endif; ?>

        <div class="dropdown-divider"></div>
        <a href="/admin/notification" class="dropdown-item dropdown-footer">View all</a>
      </div>
      </li>
    </ul>
  </nav>