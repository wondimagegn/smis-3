<?php

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * ThickboxHelper for creating Thickbox modal links in CakePHP 3.7
 */
class ThickboxHelper extends Helper
{
    /**
     * Other helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html'];

    /**
     * Options for the Thickbox instance
     *
     * @var array
     */
    protected $options = [];

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
     * Sets properties for the Thickbox (DOM ID, height, width, type)
     *
     * @param array $options Options array
     * @return void
     */
    public function setProperties(array $options = [])
    {
        $options['type'] = $options['type'] ?? 'inline';
        $this->options = $options;
    }

    /**
     * Sets the preview content for the Thickbox link
     *
     * @param string $content Preview content
     * @return void
     */
    public function setPreviewContent($content)
    {
        $this->options['previewContent'] = $content;
    }

    /**
     * Sets the main content for the Thickbox modal
     *
     * @param string $content Main content
     * @return void
     */
    public function setMainContent($content)
    {
        $this->options['mainContent'] = $content;
    }

    /**
     * Resets the options array
     *
     * @return void
     */
    public function reset()
    {
        $this->options = [];
    }

    /**
     * Generates the Thickbox HTML output
     *
     * @return string HTML output
     */
    public function output()
    {
        $options = $this->options;
        $type = $options['type'] ?? 'inline';
        $id = $options['id'] ?? '';
        $height = $options['height'] ?? null;
        $width = $options['width'] ?? null;
        $previewContent = $options['previewContent'] ?? '';
        $mainContent = $options['mainContent'] ?? '';
        $ajaxUrl = $options['ajaxUrl'] ?? null;

        // Build the href based on type
        if ($type === 'inline') {
            $href = '#TB_inline?';
            $href .= 'inlineId=' . $id;
        } elseif ($type === 'ajax') {
            $href = $this->Html->url($ajaxUrl) . '?';
        }

        if ($height) {
            $href .= '&height=' . $height;
        }
        if ($width) {
            $href .= '&width=' . $width;
        }

        // Generate the link
        $output = $this->Html->link($previewContent, $href, ['class' => 'thickbox', 'escape' => false]);

        // Add inline content if applicable
        if ($type === 'inline') {
            $output .= $this->Html->div(null, $mainContent, ['id' => $id, 'style' => 'display:none;']);
        }

        // Clear options after rendering
        $this->reset();

        return $output;
    }

    /**
     * Adds Thickbox CSS and JS to the view before rendering
     *
     * @param string $viewFile The view file being rendered
     * @return void
     */
    public function beforeRender($viewFile)
    {
        $this->Html->css('/effects/css/thickbox.css', ['block' => 'css']);
        $this->Html->script('/effects/js/thickbox-compressed.js', ['block' => 'script']);
    }
}
