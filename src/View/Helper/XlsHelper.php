<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * XlsHelper for generating Excel-compatible XML files in CakePHP 3.7
 *
 * Based on http://bakery.cakephp.org/articles/view/excel-xls-helper
 * Enhanced to create XML openable in Microsoft Excel by Yuen Ying Kit
 */
class XlsHelper extends Helper
{
    /**
     * Constructor
     *
     * @param \Cake\View\View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
    }

    /**
     * Sets the HTTP headers for an Excel file download
     *
     * @param string $filename The name of the file (without .xls extension)
     * @return void
     */
    public function setHeader($filename)
    {
        $response = $this->getView()->getResponse();
        $response = $response
            ->withHeader('Pragma', 'public')
            ->withHeader('Expires', '0')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->withHeader('Content-Type', 'application/force-download')
            ->withHeader('Content-Type', 'application/download')
            ->withHeader('Content-Disposition', "inline; filename=\"{$filename}.xls\"");
        $this->getView()->setResponse($response);
    }

    /**
     * Adds the XML header for the .xls file
     *
     * @return void
     */
    public function addXmlHeader()
    {
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
        echo "          xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n";
        echo "          xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
        echo "          xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
    }

    /**
     * Sets the worksheet name in the .xls file
     *
     * @param string $workSheetName The name of the worksheet
     * @return void
     */
    public function setWorkSheetName($workSheetName)
    {
        echo "\t<Worksheet ss:Name=\"" . htmlspecialchars($workSheetName) . "\">\n";
        echo "\t\t<Table>\n";
    }

    /**
     * Adds the XML footer to the .xls file
     *
     * @return void
     */
    public function addXmlFooter()
    {
        echo "\t\t</Table>\n";
        echo "\t</Worksheet>\n";
        echo "</Workbook>\n";
    }

    /**
     * Opens a new row in the .xls file
     *
     * @return void
     */
    public function openRow()
    {
        echo "\t\t\t<Row>\n";
    }

    /**
     * Closes the current row in the .xls file
     *
     * @return void
     */
    public function closeRow()
    {
        echo "\t\t\t</Row>\n";
    }

    /**
     * Writes a cell with a number value
     *
     * @param mixed $value The number value (or null)
     * @return void
     */
    public function writeNumber($value)
    {
        if (is_null($value)) {
            echo "\t\t\t\t<Cell><Data ss:Type=\"String\"> </Data></Cell>\n";
        } else {
            echo "\t\t\t\t<Cell><Data ss:Type=\"Number\">" . (float)$value . "</Data></Cell>\n";
        }
    }

    /**
     * Writes a cell with a string value
     *
     * @param string $value The string value
     * @return void
     */
    public function writeString($value)
    {
        echo "\t\t\t\t<Cell><Data ss:Type=\"String\">" . htmlspecialchars($value) . "</Data></Cell>\n";
    }

    /**
     * Writes the beginning of a binary Excel file (BOF record)
     *
     * @return void
     */
    public function bof()
    {
        echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    }

    /**
     * Writes the end of a binary Excel file (EOF record)
     *
     * @return void
     */
    public function eof()
    {
        echo pack("ss", 0x0A, 0x00);
    }
}
