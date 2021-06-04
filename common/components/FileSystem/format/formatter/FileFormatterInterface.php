<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\FileSystem\format\formatter;

interface FileFormatterInterface
{
    /**
     * Format files and return result content
     * @param string $content
     * @param string $extension
     * @return string
     */
    public function format(string &$content, string &$extension): string;

    /**
     * Format files (from url) and return result content
     * @param string $path
     * @param string $extension
     * @return string
     */
    public function formatFromPath(string $path, string &$extension): string;

    /**
     * Format files (from local resourse) and return result content
     * @param string $pathToLocalResource
     * @param string $extension
     * @return string
     */
    public function formatFromLocalResource(string $pathToLocalResource, string &$extension): string;
}
