<!-- Spinner Start -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<!-- Spinner End -->

<!-- Topbar Start -->
<div class="container-fluid px-5 d-none border-bottom d-lg-block top-bar <?= $isLoggedIn ? 'logged-in' : 'logged-out' ?>" data-uid="<?= $isLoggedIn ? $userId : null ?>">
    <div class="row gx-0 align-items-center">
        <div class="col-lg-4 text-center text-lg-start mb-lg-0">
            <div class="d-inline-flex align-items-center" style="height: 45px;">
                <a href="support" class="text-muted me-2"> Help</a><small> / </small>
                <a href="support" class="text-muted mx-2"> Support</a>
            </div>
        </div>
        <div class="col-lg-4 text-center d-flex align-items-center justify-content-center">
            <small class="text-dark pr-5">Mail Us: </small>
            <a href="#" class="text-muted"> support@<?= strtolower(SITE_NAME) ?>.com</a>
        </div>

        <div class="col-lg-4 text-center text-lg-end">
            <div class="d-inline-flex align-items-center" style="height: 45px;">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-muted me-2" data-bs-toggle="dropdown"><small>NGN</small></a>
                    <div class="dropdown-menu rounded">
                        <a href="#" class="dropdown-item">USD</a>
                        <a href="#" class="dropdown-item">EUR</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-muted mx-2" data-bs-toggle="dropdown"><small>English</small></a>
                    <div class="dropdown-menu rounded">
                        <a href="#" class="dropdown-item"> English</a>
                        <a href="#" class="dropdown-item"> Turkish</a>
                        <a href="#" class="dropdown-item"> Spanish</a>
                        <a href="#" class="dropdown-item"> Italian</a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-muted ms-2" data-bs-toggle="dropdown"><small><i class="fa fa-home me-2"></i> My Account</small></a>
                    <div class="dropdown-menu rounded">
                        <a href="profile" class="dropdown-item">Profile</a>
                        <a href="cart" class="dropdown-item">Cart</a>
                        <a href="orders" class="dropdown-item">Orders</a>
                        <a href="wishlist" class="dropdown-item">Wishlist</a>
                        <a href="track-order" class="dropdown-item">Track Order</a>
                        <a href="wallet" class="dropdown-item">Wallet</a> 
                        <a href="<?= $isLoggedIn ? '#' : 'login' ?>" class="dropdown-item header-log"><?= $isLoggedIn ? 'Log Out' : 'Login' ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid px-5 py-4 d-none d-lg-block bg-primary">
    <div class="row gx-0 align-items-center text-center">
        <div class="col-md-4 col-lg-3 text-center text-lg-start">
            <div class="d-inline-flex align-items-center">
                <a href="./" class="navbar-brand p-0">
                    <h1 class="display-5 text-primary m-0" style="color: #fff !important;"><?= SITE_NAME ?></h1>
                    <!-- <img src="assets/img/logo.png" alt="Logo"> -->
                </a>
            </div>
        </div>
        <div class="col-md-4 col-lg-6 text-center">
            <div class="position-relative ps-4">
                <div class="d-flex border rounded-pill">
                    <input class="form-control text-white border-0 rounded-pill w-100 py-3 bg-transparent main-search" type="text" data-bs-target="#dropdownToggle123" placeholder="Search electronics, clothes, jewelries, appliances...">
                    <select class="form-select text-dark border-0 border-start rounded-0 p-3 bg-transparent main-category" style="width: 200px;">
                        <option value="All Category">All Category</option>
                        <!-- Display All Categories -->
                        <?php foreach ($allCategories as $category): ?>
                            <option value="<?= $category['category_name'] ?>"><?= $category['category_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-primary rounded-pill py-3 px-5 start-search" style="border: 0;"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-3 text-center text-lg-end">
            <div class="d-inline-flex align-items-center">
                <a href="support" class="text-muted d-flex align-items-center justify-content-center me-3"><span class="rounded-circle btn-md-square border"><i class="fa fa-user-shield"></i></i></a>
                <a href="wishlist" class="text-muted d-flex align-items-center justify-content-center me-3"><span class="rounded-circle btn-md-square border"><i class="fas fa-heart"></i></a>
                <a href="cart" class="text-muted d-flex align-items-center justify-content-center">
                    <span class="rounded-circle btn-md-square border">
                        <i class="fas fa-shopping-cart"></i> 
                    </span>
                    <div class="cart-total d-flex align-items-center justify-content-center"><?= (int)$cartCount ?></div>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->

<!-- Navbar & Hero Start -->
<div class="container-fluid nav-bar p-0">
    <div class="row gx-0 bg-primary px-6 align-items-center gz-0">
        <div class="col-lg-3 d-none d-lg-block">
            <nav class="navbar navbar-light position-relative" style="width: 250px;">
                <button class="navbar-toggler border-0 fs-4 w-100 px-0 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#allCat">
                    <h4 class="m-0 m-6"><i class="fa fa-bars me-2"></i>All Categories</h4>
                </button>
                <div class="collapse navbar-collapse rounded-bottom" id="allCat">
                    <div class="navbar-nav ms-auto py-0">
                        <ul class="list-unstyled categories-bars">
                            <!-- Display Grouped Categories -->
                            <?php foreach ($groupedCategories as $category): ?>
                                <li>
                                    <div class="categories-bars-item">
                                        <a href="product-list?view=category&data=<?= $category['category_name'] ?>"><?= $category['category_name'] ?></a>
                                        <span>(<?= $category['product_count'] ?>)</span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <div class="col-12 col-lg-9">
            <nav class="navbar navbar-expand-lg navbar-light bg-primary ">
                <a href="./" class="navbar-brand d-block d-lg-none">
                    <h1 class="display-5 text-secondary m-0" style="color: #fff !important;"><?= SITE_NAME ?></h1>
                    <!-- <img src="assets/img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars fa-1x"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="./" class="nav-item nav-link nav-home active">Home</a>
                        <a href="brands" class="nav-item nav-link nav-brands">Brands</a>
                        <a href="about" class="nav-item nav-link nav-about">About</a>
                        <a href="contact" class="nav-item nav-link nav-contact me-2">Contact</a>
                        <a href="faq" class="nav-item nav-link nav-faq me-2">FAQ</a>
                        <?php if(!$isLoggedIn): ?>
                            <div class="nav-item dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Register</a>
                                <div class="dropdown-menu m-0">
                                    <a href="register?type=customer" class="dropdown-item">Customer</a>
                                    <a href="register?type=vendor" class="dropdown-item">Seller</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="nav-item dropdown d-block d-lg-none mb-3">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">All Category</a>
                            <div class="dropdown-menu m-0 dropdown-menu-height">
                                <ul class="list-unstyled categories-bars">
                                    <!-- Display Grouped Categories -->
                                    <?php foreach ($groupedCategories as $category): ?>
                                        <li>
                                            <div class="categories-bars-item">
                                                <a href="product-list?view=category&data=<?= $category['category_name'] ?>"><?= $category['category_name'] ?></a>
                                                <span>(<?= $category['product_count'] ?>)</span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <a href="<?= $isLoggedIn ? '#' : 'login' ?>" class="btn btn-secondary rounded-pill py-2 px-4 px-lg-3 mb-3 mb-md-3 mb-lg-0 btn-call" style="background-color: #34302f61 !important;border: 1px solid gray"><i class="fa fa-sign-in-alt me-2"></i> <?= $isLoggedIn ? 'Logout' : 'Login' ?></a>
                </div>
            </nav>
        </div>
    </div>
</div>
<!-- Navbar & Hero End -->