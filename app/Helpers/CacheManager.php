<?php

namespace App\Helpers;

class CacheManager
{
    /**
     * Generate a cache busted version of the given url
     *
     * @param string $filePath - File url
     * @return string - Formatted url
     */
    public function parse($filePath) {
        // Check if the file exists
        if (file_exists($filePath)) {
            // Get the last modified time of the file
            $fileModificationTime = filemtime($filePath);
            // Return the URL with a query parameter for cache busting
            return $filePath . '?v=' . $fileModificationTime;
        }
        return $filePath; // Return original path if file doesn't exist
    }
}
