<?php
// Define fallback image URLs
$fallbackImages = [
    'assets/img/product-3.png',
    'assets/img/product-4.png',
    'assets/img/product-5.png',
    'assets/img/product-6.png',
    'assets/img/product-7.png',
    'assets/img/product-8.png',
    'assets/img/product-9.png',
    'assets/img/product-10.png',
    'assets/img/product-11.png',
    'assets/img/product-12.png',
    'assets/img/product-14.png',
    'assets/img/product-15.png',
    'assets/img/product-16.png',
    'assets/img/product-17.png'
];

// Function to generate the page URL
function pageUrl($page, $baseUrl) {
    return $baseUrl . '&page=' . $page;
}

// -------------------------------------------------
// Define Views
// -------------------------------------------------
$currentView = 'Dashboard';
