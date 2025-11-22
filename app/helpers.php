<?php

declare(strict_types=1);

if (!function_exists('label_with_bisaya')) {
    /**
     * Format label with Bisaya translation in "English (Bisaya)" format
     *
     * @param string $englishLabel
     * @param string|null $translationKey
     * @return string
     */
    function label_with_bisaya(string $englishLabel, ?string $translationKey = null): string
    {
        $translationKey = $translationKey ?? strtolower(str_replace(' ', '_', $englishLabel));
        $bisaya = trans("fields.{$translationKey}", [], 'ceb');

        // If translation doesn't exist, return just English
        if ($bisaya === "fields.{$translationKey}") {
            return $englishLabel;
        }

        return "{$englishLabel} ({$bisaya})";
    }
}

