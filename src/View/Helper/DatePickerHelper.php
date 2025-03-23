<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\Core\Configure;
use Cake\View\View;

/**
 * DatePickerHelper for generating date picker input fields in CakePHP 3.7
 */
class DatePickerHelper extends FormHelper
{

    /**
     * Other helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'format' => '%Y-%m-%d'
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
        $this->_setup();
    }

    /**
     * Sets up the helper by reading configuration
     *
     * @return void
     */
    protected function _setup()
    {
        $format = Configure::read('DatePicker.format');
        if ($format !== null) {
            $this->setConfig('format', $format);
        }
    }

    /**
     * Generates a date picker input field
     *
     * @param string $fieldName Field name (e.g., "Model.field")
     * @param array $options Options for the input
     * @return string HTML and JavaScript output
     */
    public function picker($fieldName, array $options = [])
    {

        $this->_setup();

        // Set the entity for the field
        $this->setEntity($fieldName);

        // Generate DOM ID
        $htmlAttributes = $this->domId($options);

        // Default options
        $options = array_merge([
            'type' => 'date', // Use CakePHP's date input type
            'div' => ['class' => 'date'],
            'minYear' => date('Y') - 20,
            'maxYear' => date('Y') + 20,
            'dateFormat' => 'DMY' // Kept for reference, though unused directly
        ], $options);

        // Map minYear and maxYear to CakePHP's year range
        $options['year'] = [
            'start' => $options['minYear'],
            'end' => $options['maxYear']
        ];

        // Remove dateFormat since it's not directly used by FormHelper
        unset($options['dateFormat']);

        // Uncomment to restore calendar icon functionality
        /*
        $options['templates'] = [
            'inputContainer' => '<div class="{{class}}">{{content}} ' .
                $this->Html->image('calendar.png', ['id' => $htmlAttributes['id'], 'style' => 'cursor:pointer']) .
                '</div>'
        ];
        if (!empty($options['empty'])) {
            $options['templates']['inputContainer'] .= $this->Html->image('b_drop.png', [
                'id' => $htmlAttributes['id'] . '_drop',
                'style' => 'cursor:pointer'
            ]);
        }
        */

        // Generate the input field
        $output = parent::control($fieldName, $options);

        // Add JavaScript for datepicker (assuming external JS function `datepick`)
        $script = sprintf(
            "datepick('%s', '01/01/%s', '31/12/%s');",
            $htmlAttributes['id'],
            $options['minYear'],
            $options['maxYear']
        );
        $output .= $this->Html->scriptBlock($script);

        return $output;
    }
}
