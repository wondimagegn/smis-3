<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * CsvHelper for generating CSV output in CakePHP 3.7
 */
class CsvHelper extends Helper
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'delimiter' => ',',
        'enclosure' => '"',
        'filename' => 'Export.csv',
    ];

    /**
     * Buffer for storing CSV data
     *
     * @var resource
     */
    protected $buffer;

    /**
     * Current row being built
     *
     * @var array
     */
    protected $line = [];

    /**
     * Constructor
     *
     * @param View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
        $this->clear();
    }

    /**
     * Clears the current line and initializes the buffer
     *
     * @return void
     */
    public function clear()
    {
        $this->line = [];
        $this->buffer = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'r+');
    }

    /**
     * Adds a field to the current row
     *
     * @param mixed $value The value to add
     * @return void
     */
    public function addField($value)
    {
        $this->line[] = $value;
    }

    /**
     * Ends the current row and writes it to the buffer
     *
     * @return void
     */
    public function endRow()
    {
        $this->addRow($this->line);
        $this->line = [];
    }

    /**
     * Adds a row to the CSV buffer
     *
     * @param array $row The row data to add
     * @return void
     */
    public function addRow($row)
    {
        fputcsv($this->buffer, $row, $this->getConfig('delimiter'), $this->getConfig('enclosure'));
    }

    /**
     * Renders HTTP headers for CSV download
     *
     * @return void
     */
    public function renderHeaders()
    {
        header('Content-Type: text/csv');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $this->getConfig('filename') . '"');
    }

    /**
     * Sets the filename for the CSV output
     *
     * @param string $filename The desired filename
     * @return void
     */
    public function setFilename($filename)
    {
        $this->setConfig('filename', $filename);
        if (strtolower(substr($this->getConfig('filename'), -4)) !== '.csv') {
            $this->setConfig('filename', $this->getConfig('filename') . '.csv');
        }
    }

    /**
     * Renders the CSV content
     *
     * @param bool|string $outputHeaders Whether to output headers, or a filename if string
     * @param string|null $toEncoding Target encoding for output
     * @param string|null $fromEncoding Source encoding for conversion
     * @return string The CSV content
     */
    public function render($outputHeaders = true, $toEncoding = null, $fromEncoding = 'auto')
    {
        if ($outputHeaders) {
            if (is_string($outputHeaders)) {
                $this->setFilename($outputHeaders);
            }
            $this->renderHeaders();
        }

        rewind($this->buffer);
        $output = stream_get_contents($this->buffer);

        if ($toEncoding) {
            $output = mb_convert_encoding($output, $toEncoding, $fromEncoding);
        }

        return $this->output($output);
    }

    /**
     * Outputs the content (stub for compatibility)
     *
     * @param string $output The content to output
     * @return string
     */
    protected function output($output)
    {
        // In 3.x, this could directly return the string or handle output differently
        return $output;
    }
}
