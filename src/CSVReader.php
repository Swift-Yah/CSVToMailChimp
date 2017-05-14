<?php

/**
 * A thin CSV file reader.
 *
 * User: Rafael da Silva Ferreira
 * Date: 5/13/17
 * Time: 20:17
 */
final class CSVReader
{
    /**
     * File path.
     *
     * @var string
     */
    private $path;

    /**
     * CSVReader constructor.
     *
     * @param string $path
     */
    function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Return all lines from the CSV file as string array.
     *
     * @return array
     */
    function read()
    {
        $array = [];

        ini_set('auto_detect_line_endings', true);

        $resource = fopen($this->path, 'r');

        while (($row = fgetcsv($resource)) !== false) {
            array_push($array, $row[0]);
        }

        ini_set('auto_detect_line_endings', false);

        return $array;
    }
}