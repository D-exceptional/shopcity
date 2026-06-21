<?php
// Define fallback image URLs
$fallbackImages = [
    'assets/img/products/product-1.png',
    'assets/img/products/product-6.png',
    'assets/img/products/product-7.png',
    'assets/img/products/product-8.png',
    'assets/img/products/product-9.png',
    'assets/img/products/product-10.png',
    'assets/img/products/product-11.png',
    'assets/img/products/product-12.png',
    'assets/img/products/product-14.png',
    'assets/img/products/product-15.png',
    'assets/img/products/product-16.png',
    'assets/img/products/product-17.png',
    'assets/img/products/product-18.png'
];

// Default product image
$defaultProductImage = 'assets/img/product-3.png';

// Default store image
$defaultStoreImage = 'assets/img/header-img.jpg';

// Function to generate the page URL
function pageUrl($page, $baseUrl) {
    return $baseUrl . '&page=' . $page;
}
