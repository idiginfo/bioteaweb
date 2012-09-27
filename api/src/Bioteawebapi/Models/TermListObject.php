<?php

class TermListObject
{
    private $fieldName;

    private $resumeTerm;

    private $termsList;

    // --------------------------------------------------------------

    public function __construct($fieldName, $resumeTerm, $termsList)
    {
        $this->fieldName = $fieldName;
        $this->resumeTerm = $resumeTerm;
        $this->termsList = $termsList;
    }

    // --------------------------------------------------------------

    /**
     * Get the field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    // --------------------------------------------------------------

    /**
     * Get the resume term
     *
     * @return string
     */
    public function getResumeTerm()
    {
        return $this->resumeTerm;
    }

    // --------------------------------------------------------------

    /**
     * Get terms list
     *
     * @param boolean $assoc  If true, keys are terms, values are frequencies
     * @return array
     */
    public function getTermsList($assoc = true)
    {
        return ($assoc) ? $this->termsList : array_values($this->termsList);
    }

}

/* EOF: TermListObject.php */