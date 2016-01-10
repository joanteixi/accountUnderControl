<?php
/**
 * User: Joan Teixidó <joan@laiogurtera.com>
 * Date: 10/1/16
 * Time: 22:44
 */

namespace AppBundle\Manager;

use AppBundle\Entity\Entry;
use AppBundle\Entity\EntryExcel;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class EntryManager
 * Manage entry stuff
 * @package AppBundle\Manager
 */
class EntryManager
{

    /** @var ObjectManager */
    protected $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function existsEntryExcel(EntryExcel $entryExcel)
    {
        //check if exist with same name, same date and same value
        $oldEntry = $this->em->getRepository('AppBundle:Entry')->findOneBy(
            array(
                'concept' => $entryExcel->getConcept(),
                'value'   => $entryExcel->getValue(),
                'date'    => $entryExcel->getDate()
            )
        );

        return $oldEntry ? true : false;
    }

    public function createEntryEntity(EntryExcel $entryExcel)
    {
        $entry = new Entry();
        $entry->setConcept($entryExcel->getConcept());
        $entry->setDate($entryExcel->getDate());
        $entry->setValue($entryExcel->getValue());

        return $entry;
    }

    /**
     * @param \PHPExcel_Worksheet_Row $row
     * @return EntryExcel
     */
    public function createFromRow(\PHPExcel_Worksheet_Row $row)
    {
        $entryExcel = new EntryExcel();

        foreach ($row->getCellIterator('A', 'F') as $cell) {
            $setter = self::mapping($cell->getColumn());
            if ($setter) {
                $value = $cell->getValue();
                $entryExcel->$setter($value);

            }
        }

        return $entryExcel;
    }

    public function mapping($columnName)
    {
        $map = [
            'A' => 'concept',
            'C' => 'date',
            'E' => 'value'
        ];

        if (array_key_exists($columnName, $map)) {
            return 'set' . ucfirst($map[$columnName]);
        }
    }
}