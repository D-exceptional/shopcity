<!-- Total Revenue -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info amount-view" data-amount="<?= $storeOrderStats['total_revenue'] ?? 0 ?>">
                <h3 class="text-center"><?= $currencyManager->format((float)$storeOrderStats['total_revenue'] ?? 0); ?></h3>
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
                <h3 class="text-center"><?= $ratingManager->format($storeOrderStats['pending_orders'] ?? 0); ?></h3>
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
                <h3 class="text-center"><?= $ratingManager->format($storeOrderStats['shipped_orders'] ?? 0); ?></h3>
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
                <h3 class="text-center"><?= $ratingManager->format($storeOrderStats['delivered_orders'] ?? 0); ?></h3>
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
                <h3 class="text-center"><?= $ratingManager->format($storeOrderStats['loyal_customers'] ?? 0); ?></h3>
                <p>Total Loyal Customers</p>
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
                <h3 class="text-center"><?= $ratingManager->format($activeProducts ?? 0); ?></h3>
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
                <h3 class="text-center"><?= $ratingManager->format($pendingProducts ?? 0); ?></h3>
                <p>Total Pending Products</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Coupons -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-tag"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($storeCouponStats['total'] ?? 0); ?></h3>
                <p>Total Coupons</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Active Coupons -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-tag"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($storeCouponStats['active'] ?? 0); ?></h3>
                <p>Total Active Coupons</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Pending Coupons -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-tag"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($storeCouponStats['inactive'] ?? 0); ?></h3>
                <p>Total Pending Coupons</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>

<!-- Total Reviews -->
<div class="col-lg-3 col-6 dashboard-item">
    <div class="small-box bg-info bg-transparent border-primary color-primary">
        <div class="inner d-flex">
            <div class="icon-div w-20 d-flex align-items-center justify-items-center content-top">
                <i class="fas fa-comment"></i>
            </div>
            <div class="info-div w-80 d-flex flex-direction-column align-items-end justify-content-end wrap p-info">
                <h3 class="text-center"><?= $ratingManager->format($storeReviewsStats ?? 0); ?></h3>
                <p>Total Reviews</p>
            </div>
        </div>
        <a href="#" class="small-box-footer bg-primary color-primary color-white"></a>
    </div>
</div>