<?php

namespace nstcactus\craftcms\diskUsageWidget\helpers;

class FilesizeHelper
{
    /**
     * Convert a filesize in bytes to a human-readable filesize (using one of the k,M,G,T,P,E,Z,Y suffixes)
     * @note 1000 b = 1 kb as per the International System of Units (https://en.wikipedia.org/wiki/Metric_prefix)
     * @param int $filesizeInBytes
     * @param int $precision
     * @return string
     */
    public static function toHumanReadable(int $filesizeInBytes, int $precision = 2): string
    {
        $units = [ 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];

        /**
         * Le for vide, c'est normal, on a juste besoin de biniouter la variable $i qui nous permettra de sélectionner le bon suffixe.
         * @noinspection PhpStatementHasEmptyBodyInspection
         * @noinspection LoopWhichDoesNotLoopInspection
         * @noinspection MissingOrEmptyGroupStatementInspection
         */
        for($i = 0; $filesizeInBytes >= 999  && $i < ( count( $units ) -1); $filesizeInBytes /= 1000, $i++) {}

        return(round( $filesizeInBytes, $precision, PHP_ROUND_HALF_DOWN) . ' ' . $units[$i] );
    }


    /**
     * Convert a human-readable filesize to a number of bytes
     * @note 1000 b = 1 kb as per the International System of Units (https://en.wikipedia.org/wiki/Metric_prefix)
     * @param string $humanReadableFilesize
     * @return float
     */
    public static function toMachineReadable(string $humanReadableFilesize): float
    {
        $suffix = 'bkmgtp';
        preg_match("/^(\\d+(?:\.\d+)?)\s*([$suffix])?/", strtolower($humanReadableFilesize), $matches);

        if (!isset($matches[2])) {
            $matches[2] = 'b';
        }

        if (!isset($matches[1])) {
            $matches[1] = 2000000; // Default to 2Mo
        }

        $multiplier = strpos($suffix, $matches[2]);

        /** @noinspection PowerOperatorCanBeUsedInspection */
        return $matches[1] * pow(1000, $multiplier);
    }
}
