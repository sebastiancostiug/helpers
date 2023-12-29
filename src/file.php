<?php
/**
 * @package     Helpers package
 *
 * @subpackage  <File Helper functions>
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    helpers
 *
 * @since       2023.11.14
 */

if (!function_exists('remove_dirs')) {
/**
     * removeDirs
     *
     * @param string  $path   The path where to search for directories
     * @param array   $dirs   The directories to remove
     * @param boolean $dryrun Whether to run the command or not
     *
     * @return array
     */
    function remove_dirs($path, array $dirs, $dryrun = false)
    {
        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

        $filesRemoved = 0;
        $bytesRemoved = 0;
        foreach ($iterator as $info) {
            if ($info->isDir() && in_array($info->getFilename(), $dirs)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($info->getRealPath(), RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $filesRemoved++;
                        $bytesRemoved += $fileinfo->getSize();
                        $removeFunction = 'unlink';
                    } else {
                        $removeFunction = 'rmdir';
                    }
                    if ($dryrun) {
                        echo 'Would remove ' . $fileinfo->getRealPath() . PHP_EOL;
                    } else {
                        echo 'Removing ' . $fileinfo->getRealPath() . PHP_EOL;
                        $removeFunction($fileinfo->getRealPath());
                    }
                }

                // Remove the directory itself
                if ($dryrun) {
                    echo 'Would remove ' . $info->getRealPath() . PHP_EOL;
                } else {
                    echo 'Removing ' . $info->getRealPath() . PHP_EOL;
                    rmdir($info->getRealPath());
                }
            }
        }

        return [
            'files' => $filesRemoved,
            'bytes' => $bytesRemoved,
        ];
    }
}

if (!function_exists('remove_files')) {
    /**
     * removeFiles
     *
     * @param string  $path      The path where to search for files
     * @param array   $filenames The filenames to remove
     * @param boolean $dryrun    Whether to run the command or not
     *
     * @return array
     */
    function remove_files($path, array $filenames, $dryrun = false)
    {
        $files = [];

        $directory = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

        $filesRemoved = 0;
        $bytesRemoved = 0;

        foreach ($iterator as $info) {
            if ($info->isFile() && in_array($info->getFilename(), $filenames)) {
                $files[] = $info->getRealPath();
            }
        }

        foreach ($files as $file) {
            $filesRemoved++;
            $bytesRemoved += filesize($file);

            if ($dryrun) {
                echo 'Would remove ' . $file . PHP_EOL;
            } else {
                echo 'Removing ' . $file . PHP_EOL;
                unlink($file);
            }
        }

        return [
            'files' => $filesRemoved,
            'bytes' => $bytesRemoved,
        ];
    }
}

if (!function_exists('resize_image')) {
    /**
     * Resizes an image that is too large
     *
     * @param string  $file    The file name to be resized
     * @param integer $maxSize The maximum size of the image
     *
     * @return void
     */
    function resize_image($file, $maxSize)
    {
        list($width, $height, $type, $attr) = getimagesize($file);
        if ($width > $maxSize || $height > $maxSize) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $newWidth  = $maxSize;
                $newHeight = $maxSize / $ratio;
            } else {
                $newWidth  = $maxSize * $ratio;
                $newHeight = $maxSize;
            }
            $src = imagecreatefromstring(file_get_contents($file));
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);
            imagepng($dst, $file);
            imagedestroy($dst);
        }
    }
}

if (!function_exists('clear_folder')) {
    /**
     * clear_folder
     *
     * @param string $folder The folder to clear
     *
     * @return void
     */
    function clear_folder($folder)
    {
        if (file_exists($folder)) {
            $di = new \RecursiveDirectoryIterator($folder, \FilesystemIterator::SKIP_DOTS);
            $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($ri as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
        }
    }
}

if (!function_exists('csv_to_array')) {
    /**
     * Convert a CSV file to an array.
     *
     * @param string $filename  The path to the CSV file.
     * @param string $delimiter The delimiter used in the CSV file.
     *
     * @return array|false The array representation of the CSV file, or false if the file is not readable.
     */
    function csv_to_array($filename = '', $delimiter = ',')
    {
        // Check if the file exists and is readable
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];

        // Open the CSV file
        if (($handle = fopen($filename, 'r')) !== false) {
            // Read each row of the CSV file
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                // If the header row is not set, set it as the current row
                if (!$header) {
                    $header = $row;
                } else {
                    // Combine the header row with the current row and add it to the data array
                    $data[] = array_combine($header, $row);
                }
            }

            // Close the CSV file
            fclose($handle);
        }

        // Return the array representation of the CSV file
        return $data;
    }
}
