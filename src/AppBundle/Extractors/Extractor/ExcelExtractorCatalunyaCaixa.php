<?php
/**
 * User: Joan Teixidó <joan@laiogurtera.com>
 * Date: 16/1/16
 * Time: 17:19
 */


namespace AppBundle\Extractors\Extractor;

use AppBundle\Extractors\Manager\EntryExcelManager;

/**
 *
 * Class ExcelExtractorCatalunyaCaixa
 * @package AppBundle\Extractors\Extractor
 * ServiceName excel.extractor.catalunyacaixa
 */
class ExcelExtractorCatalunyaCaixa implements FileExtractorInterface
{
    /** @var EntryExcelManager */
    protected $manager;

    public function __construct(EntryExcelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param \SplFileObject $file
     * @return array<AppBundle\Extractors\Entity\EntryExcel>
     */
    public function extract(\Symfony\Component\Finder\SplFileInfo $file)
    {
        $rows = [];
        $objPHPExcel = \PHPExcel_IOFactory::load($file);
        $worksheet = $objPHPExcel->getSheet(0);
        foreach ($worksheet->getRowIterator(13) as $row) {
            $rows[] = $this->manager->createFromRow($row);
        }

        return $rows;
    }

    /**
     * @param $path
     */
    public function extractFromPath(\Symfony\Component\Finder\Finder $finder)
    {
        $entityExcels = [];
        $entityExcels = [];
        foreach ($finder as $file) {
            if ($file->getExtension() == 'xls') {
                $entityExcels = array_merge($entityExcels, $this->extract($file));
            }
        }

        return $entityExcels;
    }
}