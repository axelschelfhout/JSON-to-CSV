<?php

namespace jsontocsv;

/**
 * Class JsonToCSV
 *
 * @author Axel Schelfhout <axelschelfhout@gmail.com>
 */
class JsonToCSV
{

    private $columns;
    private $csv;
    private $rows;


    /**
     * Text
     *
     * @param $file
     * @param string $csvFileName
     * @return void
     */
    public function convert($file, $csvFileName = '')
    {
        $fileContent = $this->getFileContent($file);

        // Make it a readable array, so we can use it.
        $data = json_decode($fileContent, true);

        $this->columnSearch($data); // Find the column names.
        $this->fixRows(); // add all columns to all rows

        $this->createCSV($csvFileName);
    }

    /**
     * Text
     *
     * @param $csvFileName
     */
    private function createCSV($csvFileName)
    {
        if (strlen($csvFileName) < 1) {
            $csvFileName = 'jsontocsv.csv';
        }
        $this->csv = fopen($csvFileName, 'w');
        $this->addHeader();
        $this->addData();

        fclose($this->csv);
    }

    /**
     * Text
     *
     * @return void
     */
    private function addData()
    {
        foreach ($this->rows as $row) {
            fputcsv($this->csv, $row);
        }
    }


    /**
     * Text
     *
     * @return void
     */
    private function addHeader()
    {
        fputcsv($this->csv, $this->getColumns()); // First add the column names
    }

    /**
     * Return's the content of the provided file.
     *
     * @param $file
     * @return string
     */
    private function getFileContent($file)
    {
        $fileHandle = fopen($file, 'r');
        $fileContent = fread($fileHandle, filesize($file));
        fclose($fileHandle);
        return $fileContent;
    }

    /**
     * Text
     *
     * @param $data
     * @param string $parent
     * @param null $topKey
     */
    public function columnSearch($data, $parent = '', $topKey = null)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (!is_numeric($key)) {
                    if (is_array($value)) {
                        if (strlen($parent) > 0) {
                            $key = $parent.'_'.$key;
                        }
                        $this->columnSearch($value, $key, $topKey);
                    } else {
                        if (strlen($parent) > 0) {
                            $newKey = $parent.'_'.$key;
                            $this->columns[] = $newKey;

                            $this->rows[$topKey][$newKey] = strval($value); //
                        } else {
                            $this->columns[] = $key;

                            $this->rows[$topKey][$key] = strval($value); //
                        }
                    }
                } else {
                    $this->columnSearch($value, $parent, $key);
                }
            }
        }
    }

    /**
     * Text
     *
     * @return void
     */
    public function fixRows()
    {
        foreach ($this->rows as $key => $row) {
            foreach ($this->columns as $col) {
                if (!array_key_exists($col, $row)) {
                    $this->rows[$key][$col] = '';
                }
            }
            ksort($this->rows[$key]);
        }
    }

    /**
     * Text
     *
     * @return mixed
     */
    public function getColumns()
    {
        if (!empty($this->columns)) {
            $columns = array_unique($this->columns);
            sort($columns);
            return $columns;
        }
        return null;
    }
}
