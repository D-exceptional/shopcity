<?php

declare(strict_types=1);

namespace App\Support;

class RateManager
{
    /**
     * Generate star rating HTML markup.
     *
     * @param float|int $ratingAverage - Average rating value (e.g., 4.2)
     * @param int $maxStars - Maximum number of stars to display (default: 5)
     * @return string - Formatted HTML
     */
    public function render(
        float $ratingAverage, 
        int $maxStars = 5
    ): string {

        // Ensure rating is within bounds
        $rating = max(0, min($ratingAverage, $maxStars));

        $html       = '<div class="d-flex">';
        $fullStars  = floor($rating);                // number of full stars
        $halfStar   = ($rating - $fullStars) >= 0.5; // whether to show a half star
        $emptyStars = $maxStars - $fullStars - ($halfStar ? 1 : 0);

            // Add full stars
            for ($i = 0; $i < $fullStars; $i++) {
                $html .= '<i class="fas fa-star text-primary"></i>';
            }

            // Add half star (if applicable)
            if ($halfStar) {
                $html .= '<i class="fas fa-star-half text-primary"></i>';
            }

            // Add empty stars
            for ($i = 0; $i < $emptyStars; $i++) {
                $html .= '<i class="far fa-star"></i>'; // Change to `fas` if you want gray stars for 0.0 ratings
            }

        $html .= '</div>';

        return $html;
    }
}
