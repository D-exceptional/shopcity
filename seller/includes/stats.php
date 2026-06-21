<!-- Total Revenue -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info amount-view" data-amount="<?= $orderStats['total_revenue'] ?? 0 ?>">
                <h3 class="text-center"><?= $currencyManager->format((float)$orderStats['total_revenue'] ?? 0); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Pending Orders -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($orderStats['pending_orders'] ?? 0); ?></h3>
                <p>Total Pending Orders</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>


<!-- Total Shipped Orders -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($orderStats['shipped_orders'] ?? 0); ?></h3>
                <p>Total Shipped Orders</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Delivered Orders -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($orderStats['delivered_orders'] ?? 0); ?></h3>
                <p>Total Delivered Orders</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Loyal Customers -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-users"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($orderStats['unique_customers'] ?? 0); ?></h3>
                <p>Total Buying Customers</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Active Products -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($productStats['active']  ?? 0); ?></h3>
                <p>Total Active Products</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Pending Products -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($productStats['pending'] ?? 0); ?></h3>
                <p>Total Pending Products</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Active Stores -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-university"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format((float)$storeStats['active'] ?? 0); ?></h3>
                <p>Total Active Stores</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Current Balance -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info amount-view" data-amount="<?= $walletStats['current_balance'] ?? 0 ?>">
                <h3 class="text-center"><?= $currencyManager->format((float)$walletStats['current_balance'] ?? 0); ?></h3>
                <p>Current Balance</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Balance -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info amount-view" data-amount="<?= $walletStats['total_balance'] ?? 0 ?>">
                <h3 class="text-center"><?= $currencyManager->format((float)$walletStats['total_balance'] ?? 0); ?></h3>
                <p>Total Balance</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Savings -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info amount-view" data-amount="<?= $walletStats['savings_balance'] ?? 0 ?>">
                <h3 class="text-center"><?= $currencyManager->format((float)$walletStats['savings_balance'] ?? 0); ?></h3>
                <p>Savings Balance</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Payouts -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info amount-view" data-amount="<?= $walletStats['total_payout'] ?? 0 ?>">
                <h3 class="text-center"><?= $currencyManager->format((float)$walletStats['total_payout'] ?? 0); ?></h3>
                <p>Total Payout</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Pending Payouts -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info amount-view" data-amount="<?= $walletStats['pending_payout'] ?? 0 ?>">
                <h3 class="text-center"><?= $currencyManager->format((float)$walletStats['pending_payout'] ?? 0); ?></h3>
                <p>Pending Payout</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Inbox -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format((float)$mailStats['inbox'] ?? 0); ?></h3>
                <p>Total Inbox</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Outbox -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format((float)$mailStats['outbox'] ?? 0); ?></h3>
                <p>Total Outbox</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Notifications -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-bell"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format((float)$notificationStats['all'] ?? 0); ?></h3>
                <p>Total Notifications</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>