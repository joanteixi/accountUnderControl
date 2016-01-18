<?php

/**
 * User: Joan Teixidó <joan@laiogurtera.com>
 * Date: 18/1/16
 * Time: 23:09
 */

namespace AppBundle\Extractors\Extractor;


interface FileExtractorInterface
{
    /**
     * Extract info from a file
     * @param \SplFileObject $file
     * @return mixed
     */
    public function extract(\Symfony\Component\Finder\SplFileInfo $file);

    public function extractFromPath(\Symfony\Component\Finder\Finder $finder);

}