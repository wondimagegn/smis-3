<?php

/**
 * Math Captcha Component class.
 * Generates a simple, plain text math equation as an alternative to image-based CAPTCHAs.
 *
 * @filesource
 * @author          Jamie Nay
 * @copyright       Jamie Nay
 * @license         http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link            http://jamienay.com/code/math-captcha-component
 */

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\Session;

class MathCaptchaComponent extends Component
{
    /**
     * Other components needed by this component
     *
     * @access public
     * @var array
     */
    protected $_defaultConfig = [
        'operand' => '+',
        'minNumber' => 1,
        'maxNumber' => 5,
        'numberOfVariables' => 2
    ];

    /**
     * Session instance
     *
     * @var \Cake\Http\Session
     */
    protected $session;

    /**
     * The variables used in the equation.
     *
     * @access public
     * @var array
     */
    public $variables = [];

    /*
     * The math equation.
     *
     * @access public
     * @var string
     */
    public $equation = null;

    /**
     * Constructor
     *
     * @param ComponentRegistry $registry ComponentRegistry object.
     * @param array $config Array of configuration settings.
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->session = new Session();
    }

    /**
     * Method that generates a math equation based on the component settings.
     * It also calls registerAnswer(), which determines the answer to the equation
     * and sets it as a session variable.
     *
     * @access public
     * @return string
     */
    public function generateEquation(): string
    {
        $this->variables = [];

        // Loop through our range of variables and set a random number for each one.
        foreach (range(1, $this->getConfig('numberOfVariables')) as $variable) {
            $this->variables[] = rand($this->getConfig('minNumber'), $this->getConfig('maxNumber'));
        }

        $this->equation = implode(' ' . $this->getConfig('operand') . ' ', $this->variables);

        // This function determines the answer to the equation and stores it as a session variable.
        $this->registerAnswer();

        return $this->equation;
    }

    /**
     * Determines the answer to the math question from the variables set in generateEquation()
     * and registers it as a session variable.
     *
     * @access public
     * @return int
     */
    public function registerAnswer(): int
    {
        $answer = 0;
        $expression = implode($this->getConfig('operand'), $this->variables);

        // Evaluate the math expression safely
        switch ($this->getConfig('operand')) {
            case '+':
                $answer = array_sum($this->variables);
                break;
            case '-':
                $answer = array_reduce($this->variables, function ($carry, $item) {
                    return ($carry === null) ? $item : $carry - $item;
                });
                break;
            case '*':
                $answer = array_product($this->variables);
                break;
            case '/':
                $answer = array_reduce($this->variables, function ($carry, $item) {
                    return ($carry === null) ? $item : $carry / $item;
                });
                break;
        }

        $this->session->write('MathCaptcha.answer', $answer);

        return $answer;
    }

    /**
     * Compares the given data to the registered equation answer.
     *
     * @access public
     * @param mixed $data The user input to validate.
     * @return bool
     */
    public function validates($data): bool
    {
        return $data == $this->session->read('MathCaptcha.answer');
    }
}
