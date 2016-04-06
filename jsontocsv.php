<?php

/**
 * Class JsonToCSV
 *
 * @author Axel Schelfhout
 */
class JsonToCSV
{

    public $columns;

    /**
     * JsonToCSV constructor.
     * @param $file
     * @param bool $skipFirstDimension
     */
    public function __construct($file, $skipFirstDimension = false)
    {
        $fileContent = file_get_contents($file);

        // Make it a readable array, so we can use it.
        $data = json_decode($fileContent, true);

        // If the first dimension of the data are entry ID's. We should skip the first, and go directly into the second.
        // This is where the data is.
        if ($skipFirstDimension) {
            $data = $data[0];
        }
        $this->columnSearch($data);

        return $this->getColumns();
    }

    /**
     * @param $data
     * @param string $parent
     */
    public function columnSearch($data, $parent = '')
    {
        foreach ($data as $key => $value) {
            if (!is_numeric($key)) {
                if (is_array($value)) {
                    if (strlen($parent) > 0) {
                        $key = $parent.'_'.$key;
                    }
                    $this->columnSearch($value, $key);
                }
                else {

                    if(strlen($parent)>0){
                        $newKey = $parent.'_'.$key;
                        $this->columns[] = $newKey;
                    }
                    else {
                        $this->columns[] = $key;
                    }
                }
            } else {
                $this->columnSearch($value, $parent);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        if (!empty($this->columns)) {
            return $this->columns;
        }
        return null;
    }

}

