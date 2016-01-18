<?php
/**
 * User: Joan Teixidó <joan@laiogurtera.com>
 * Date: 9/1/16
 * Time: 2:26
 */

namespace AppBundle\Extractors\Entity;


class EntryExcel
{
    protected $concept;

    protected $date;

    protected $value;


    /**
     * @return mixed
     */
    public function getConcept()
    {
        return $this->concept;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }


    public function setConcept($value)
    {
        $this->concept = $value;
    }

    public function setDate($value)
    {
        $this->date = \DateTime::createFromFormat('d-m-Y', $value);
    }

    public function setValue($value)
    {
        $this->value = $value;
    }


}