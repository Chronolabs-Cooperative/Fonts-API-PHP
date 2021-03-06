<?php
/**
 * API form element of button
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2017 API Project (www.api.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          form
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.api.org/
 */
defined('API_ROOT_PATH') || exit('API root path not defined');

api_load('APIFormElement');

/**
 *
 *
 * @package             kernel
 * @subpackage          form
 *
 * @author              Kazumi Ono    <onokazu@api.org>
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 */

/**
 * A button
 *
 * @author              Kazumi Ono    <onokazu@api.org>
 * @copyright       (c) 2000-2016 API Project (www.api.org)
 *
 * @package             kernel
 * @subpackage          form
 */
class APIFormButton extends APIFormElement
{
    /**
     * Value
     * @var string
     * @access    private
     */
    public $_value;

    /**
     * Type of the button. This could be either "button", "submit", or "reset"
     * @var string
     * @access    private
     */
    public $_type;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name
     * @param string $value
     * @param string $type    Type of the button. Potential values: "button", "submit", or "reset"
     */
    public function __construct($caption, $name, $value = '', $type = 'button')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_type = $type;
        $this->setValue($value);
    }

    /**
     * Get the initial value
     *
     * @param  bool $encode To sanitizer the text?
     * @return string
     */
    public function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }

    /**
     * Set the initial value
     *
     * @param $value
     *
     * @return string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function getType()
    {
        return in_array(strtolower($this->_type), array('button', 'submit', 'reset')) ? $this->_type : 'button';
    }

    /**
     * prepare HTML for output
     *
     * @return string
     */
    public function render()
    {
        return APIFormRenderer::getInstance()->get()->renderFormButton($this);
    }
}
