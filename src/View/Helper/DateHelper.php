<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Core\Configure;
use Cake\I18n\Time;

/**
 * DateHelper for generating date input fields in CakePHP 3.7
 */
class DateHelper extends Helper
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'datePickerClasses' => 'highlight-days-67 split-date no-transparency',
        'fieldNames' => [
            'm' => '[month]', // Numeric month
            'M' => '[month]', // Word month
            'd' => '[day]',
            'y' => '[year]'
        ],
        'format' => 'd-m-y' // Default format
    ];

    /**
     * Constructor
     *
     * @param View $view The View this helper is being attached to
     * @param array $config Configuration settings for the helper
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
    }

    /**
     * Creates date input fields as dropdowns
     *
     * @param string|null $name Field name (e.g., "Model.field")
     * @param string|array|null $date Date string (YYYY-MM-DD) or array
     * @param array|null $options Options: format, label, disabled, class
     * @return string HTML output
     * @throws \Exception If name or date format is invalid
     */
    public function input($name = null, $date = null, $options = [])
    {
        if (empty($name)) {
            throw new \Exception('Missing name! Check DateHelper.');
        }

        // Convert name to data[Model][field] format
        $name = $this->_calculateName($name);

        // Prepare date
        if (is_array($date)) {
            $date = $this->_dateFromArray($date);
        }
        if (empty($date)) {
            $date = $this->_getDateFromData($name);
        }

        if (!preg_match('/^([0-9]{4}\-[0-9]{2}\-[0-9]{2})$/', $date)) {
            throw new \Exception('Wrong date format! Expected YYYY-MM-DD.');
        }

        // Parse options
        $format = $this->getConfig('format');
        $label = null;
        $disabled = false;
        $class = null;

        if (is_array($options)) {
            $format = $options['format'] ?? $format;
            $label = $options['label'] ?? null;
            $disabled = $options['disabled'] ?? false;
            $class = $options['class'] ?? null;
        }

        // Prepare format array
        $format = $this->_prepareFormat($format);
        $idBase = strtolower($format[0] . $format[0]);

        // Build output
        $output = '';
        if ($label) {
            $output .= sprintf('<label for="%s-%s">%s</label>', $name, $idBase, $label);
        }

        foreach ($format as $field) {
            $fieldId = $name . ($field !== 'y' ? '-' . strtolower($field . $field) : '');
            $fieldName = $name . $this->getConfig('fieldNames')[$field];

            $attributes = [
                'id' => $fieldId,
                'name' => $fieldName
            ];
            if ($disabled) {
                $attributes['disabled'] = 'disabled';
            }

            $classes = [];
            if ($class) {
                $classes[] = $class;
            }
            if ($field === 'y' && !$disabled) {
                $classes[] = $this->getConfig('datePickerClasses');
            }
            if (!empty($classes)) {
                $attributes['class'] = implode(' ', $classes);
            }

            $output .= '<select ' . $this->_attributesToString($attributes) . '>';
            $this->_createInputs($field, $date, $disabled, $output);
            $output .= '</select>' . "\n";
        }

        return $output;
    }

    /**
     * Displays a formatted date
     *
     * @param string $date Date string (YYYY-MM-DD)
     * @return string Formatted date
     */
    public function show($date)
    {
        $format = Configure::read('SMISdateFormat') ?? 'd-m-y';
        $format = str_replace('-', ' ', $format);
        $format = str_replace('M', 'F', $format);
        $format = str_replace('y', 'Y', $format);
        return (new Time($date))->format($format);
    }

    /**
     * Retrieves date from view data
     *
     * @param string $name Field name in data[Model][field] format
     * @return string Date in YYYY-MM-DD format
     */
    protected function _getDateFromData($name)
    {
        $viewVars = $this->getView()->getVars();
        $parts = explode('][', trim($name, '[]'));
        $data = $this->getView()->get($parts[1]); // Assumes simple Model.field structure

        if (!empty($data)) {
            if (is_array($data)) {
                return $this->_dateFromArray($data);
            }
            return $data;
        }

        return date('Y-m-d');
    }

    /**
     * Converts array date to string
     *
     * @param array $date Array with year, month, day keys
     * @return string YYYY-MM-DD
     */
    protected function _dateFromArray($date)
    {
        return sprintf('%04d-%02d-%02d', $date['year'], $date['month'], $date['day']);
    }

    /**
     * Calculates field name for form input
     *
     * @param string $str Model.field
     * @return string data[Model][field]
     */
    protected function _calculateName($str)
    {
        $result = str_replace('.', '][', $str);
        return 'data[' . $result . ']';
    }

    /**
     * Prepares date format into array
     *
     * @param string|null $formatStr e.g., 'd-m-y'
     * @return array Format parts
     */
    protected function _prepareFormat($formatStr)
    {
        if (empty($formatStr)) {
            $formatStr = Configure::read('AZAdateFormat') ?? $this->getConfig('format');
        }
        return explode('-', $formatStr);
    }

    /**
     * Generates dropdown options for date fields
     *
     * @param string $type Field type (m, M, d, y)
     * @param string $default Date in YYYY-MM-DD
     * @param bool $disabled Whether field is disabled
     * @param string &$str Output string reference
     * @return void
     */
    protected function _createInputs($type, $default, $disabled, &$str)
    {
        $defaultParts = $default ? explode('-', $default) : [date('Y'), '01', '01'];
        $months = [
            __('January'), __('February'), __('March'), __('April'), __('May'), __('June'),
            __('July'), __('August'), __('September'), __('October'), __('November'), __('December')
        ];

        if ($disabled) {
            switch ($type) {
                case 'm':
                    $str .= sprintf('<option value="%s" selected>%d</option>', $defaultParts[1], (int)$defaultParts[1]);
                    break;
                case 'M':
                    $str .= sprintf('<option value="%s" selected>%s</option>', $defaultParts[1], $months[$defaultParts[1] - 1]);
                    break;
                case 'd':
                    $str .= sprintf('<option value="%s" selected>%d</option>', $defaultParts[2], (int)$defaultParts[2]);
                    break;
                case 'y':
                    $str .= sprintf('<option value="%s" selected>%s</option>', $defaultParts[0], $defaultParts[0]);
                    break;
            }
        } else {
            switch ($type) {
                case 'm':
                    for ($i = 1; $i <= 12; $i++) {
                        $value = sprintf('%02d', $i);
                        $selected = ($defaultParts[1] == $value) ? ' selected' : '';
                        $str .= sprintf('<option value="%s"%s>%d</option>', $value, $selected, $i);
                    }
                    break;
                case 'M':
                    for ($i = 1; $i <= 12; $i++) {
                        $value = sprintf('%02d', $i);
                        $selected = ($defaultParts[1] == $value) ? ' selected' : '';
                        $str .= sprintf('<option value="%s"%s>%s</option>', $value, $selected, $months[$i - 1]);
                    }
                    break;
                case 'd':
                    for ($i = 1; $i <= 31; $i++) {
                        $value = sprintf('%02d', $i);
                        $selected = ($defaultParts[2] == $value) ? ' selected' : '';
                        $str .= sprintf('<option value="%s"%s>%d</option>', $value, $selected, $i);
                    }
                    break;
                case 'y':
                    $currentYear = (int)date('Y');
                    for ($i = $currentYear; $i <= $currentYear + 5; $i++) {
                        $selected = ($defaultParts[0] == $i) ? ' selected' : '';
                        $str .= sprintf('<option value="%d"%s>%d</option>', $i, $selected, $i);
                    }
                    break;
            }
        }
    }

    /**
     * Converts attributes array to HTML string
     *
     * @param array $attributes Key-value pairs
     * @return string HTML attributes
     */
    protected function _attributesToString(array $attributes)
    {
        $pairs = [];
        foreach ($attributes as $key => $value) {
            $pairs[] = sprintf('%s="%s"', $key, htmlspecialchars($value));
        }
        return implode(' ', $pairs);
    }
}
