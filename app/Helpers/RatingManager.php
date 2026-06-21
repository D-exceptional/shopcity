<?php

namespace App\Helpers;

class RatingManager
{
    /**
     * Generate star rating HTML markup.
     *
     * @param float|int $ratingAverage - Average rating value (e.g., 4.2)
     * @param int $maxStars - Maximum number of stars to display (default: 5)
     * @return string - Formatted HTML
     */
    public function render($ratingAverage, int $maxStars = 5): string
    {
        // Ensure rating is within bounds
        $rating = max(0, min($ratingAverage, $maxStars));

        $html = '<div class="d-flex">';
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

    /**
     * Generate formatted value in thousands.
     *
     * @param float|int $num - Numeric value (e.g., 1, 100, 1000)
     * @return string - Formatted number
     */
    public function format(float $num): string
    {
        $num = is_null($num) ? 0 : $num;
        // Automatically format with commas and up to 2 decimals if needed
        if (floor($num) == $num) {
            // It's an integer (no decimals)
            return number_format($num, 0, '.', ',');
        } else {
            // Has decimals
            return number_format($num, 2, '.', ',');
        }
    }
}
