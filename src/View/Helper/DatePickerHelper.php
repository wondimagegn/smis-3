<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\Core\Configure;
class DatePickerHelper extends FormHelper
{
    public $helpers = ['Html'];

    private $format = 'yyyy-mm-dd';

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $format = Configure::read('DatePicker.format');
        if ($format) {
            $this->format = $format;
        }
    }

    public function picker($fieldName, array $options = [])
    {
        $this->initialize([]);

        $options += [
            'type' => 'text',
            'label' => false,
            'class' => 'datepicker form-control',
            'autocomplete' => 'off',
            'dateFormat' => 'DMY',
            'minYear' => date('Y') - 20,
            'maxYear' => date('Y') + 20
        ];

        $fieldId = $this->_domId($fieldName);

        $output = parent::control($fieldName, $options);
        $output .= $this->Html->scriptBlock("
            $(function() {
                $('#$fieldId').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '{$options['minYear']}:{$options['maxYear']}'
                });
            });
        ");
        return $output;
    }

    public function loadAssets()
    {
        return $this->Html->script([
                'https://code.jquery.com/jquery-3.6.0.min.js',
                'https://code.jquery.com/ui/1.12.1/jquery-ui.js'
            ]) .
            $this->Html->css('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
    }

}
?>
