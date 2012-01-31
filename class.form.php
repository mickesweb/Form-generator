<?php
/* ~class.form.php
 * 
 * @verson : 1.0
 * @contact : via mickesweb.se
 * @author :  Mikael Andersson <mikael@mickesweb.se>
 * @copyright (c) 2012, Mikael Andersson. All Rights Reserved.  
 * @license : http://creativecommons.org/licenses/by-nc-sa/3.0/
 * 
 * Last Updated: 2012-01-31
 * INFO: A class to create an html form. Some ready for HTML5 (see http://www.w3schools.com/html5/ )
 * NOTE: Need css: .errorLabel and .errorText
 *       If use addDBSelect you need: /class.dataaccess.php version 1.0 or newer
 *       If use valid_recaptchalib you need to include recaptchalib.php first. Get it from http://www.google.com/recaptcha
 */

define("DATABASE", false);

define("WEB_URL", "http://mickesweb.se/");
 
class Form {
    
    // @var bool
    private $error = false;
    // @var array ('label'=>"error text")
    private $errorArray = array();
    // @var int
    private $inputWidth = 200;
    // @var string
    private $formName = ""; 
    // @var string
    private $form = "";
    // @var Enum (never, afterInput or always)
    private $newLine = "always";

    /* Constructor, run when the new class is created.
     * Input:
     *      @param string $formName
     *      @param boolean $error
     *      @param array $errorArray (eg. array('label'=> "Error text"))
     *      @param int $inputWidth 
     */
    public function __construct($formName, $error=false, $errorArray=array(), $inputWidth=200) {
        $this->formName = trim($formName);
        $this->inputWidth = intval($inputWidth);
        $this->error = $error;
        $this->errorArray = $errorArray;
    }
    
    /* Settings when the new line will be inserted.
     * Input:
     *      @param Enum $newLineAt (never, afterInput or always)
     */
    public function newLineSetting($newLineAt) {
        $this->newLine = trim(strtolower($newLineAt));
    }
    
    /* Add a new line to html code */
    public function addNewLine() {
        $this->form .= '<br/>';
    }

    /* Generate the top of form.
     * Input:
     *      @param path $action 
     *      @param Enum $method (get, post, put or delete)
     *      @param Enum $target (_blank, _self, _parent or _top)
     *      @param Enum $autocomplete (on or off)
     *      @param Boolean $autovalidate
     * 
     * @return boolean
     */
    public function start($action, $method='post', $target='_self', $autocomplete='on', $autovalidate=false) {
        // Make sure it really is a new form.
        if($this->form == "") {
            $action = trim($action);
            $method = trim(strtolower($method));
            $target = trim(strtolower($target));

            $startForm = '<form name="'.$this->formName.'" id="'.$this->formName.'" method="'.$method.'" action="'.WEB_URL.$action.'" target="'.$target.'"';
            if(!($autovalidate)) {
                $startForm .= ' novalidate="novalidate"';
            }
            $startForm .= '>';
            
            $this->form = $startForm;
            
            return true;    
        } else {
            return false;
        }  
    }

    /* Generate an label.
     * Input:
     *      @param string $id
     *      @param string $text
     */
    public function addLabel($id, $text) {
        $labelId = trim($id);
        // Check if an error for this label.
        if ($this->error && array_key_exists($labelId, $this->errorArray)) {
            // Create the label and fix error text.
            $label = '<label for="'.$labelId.'"><span class="errorLabel">'.$text.' </span></label>';
            $label .= ' <span class="errorText">'.$this->errorArray[$labelId].'</span>';
        } else {
            // If no error, create the label.
            $label = '<label for="'.$labelId.'">'.$text.' </label>';
        }
        
        $this->form .= $label;
        
        if($this->newLine == "always") {
            self::addNewLine(); 
        }
    }

    /* Generate an input for text.
     * Input:
     *      @param string $id
     *      @param string $value 
     *      @param int $width 
     *      @param int $maxlength
     *      @param string $expand (add something self to input eg. placeholder="information" or autofocus)
     */
    public function addTextInput($id, $value='', $width=0, $maxlength=255, $expand='') {
        self::addInput($id, 'text', $value, $width, $maxlength, $expand);
    }
    
    /* Genereate an input for password.
     * Input:
     *      @param string $id
     *      @param int $widt
     *      @param int $maxlength
     *      @param string $expand (add something self to input eg. placeholder="information")
     */
    public function addPassword($id, $width=0, $maxlength=255, $expand='') {
        self::addInput($id, 'password', '', $width, $maxlength, $expand);
    }

    /* Generate an special input. eg, email, calendar, etc.
     * Input:
     *      @param string $id
     *      @param Enum $type (color, date, datetime, datetime-local, email, hidden, month, number, range, search, tel, time, url, week)
     *      @param string $value
     *      @param int $width
     *      @param int $maxlength
     *      @param int $expand (add something self to input eg. placeholder="information" or autofocus)
     */
    public function addInput($id, $type, $value='', $width=0, $maxlength=255, $expand='') {
        $inputId = trim($id);
        $inputType = trim(strtolower($type));
        // If no width is set, use standard width.
        if($width == 0) {
            $width = $this->inputWidth;
        } else {
            $width = intval($width);
        }
        $maxlength = intval($maxlength);
        // Create the input.
        $input = '<input type="'.$inputType.'" name="'.$inputId.'" id="'.$inputId.'" maxlength="'.strval($maxlength).'" style="width: '.strval($width).'px;" value="'.$value.'"';
        // Allows the user to expand with its own html code.
        if ($expand != '') {
            $input .= ' '.$expand.' ';
        }
        $input .= '/>';

        $this->form .= $input;
        
        if($this->newLine == "always" || $this->newLine == "afterinput") {
            self::addNewLine(); 
        }
    }
    
    /* Generate an file uploader.
     * Input:
     *      @param string $id
     *      @param string $expand (add something self to the input form)
     */
    public function addFileUploader($id, $expand='') {
        $inputId = trim($id);
        /* Checks if the form has already been converted, 
         * otherwise make it so that it can handle image upload. */
        if(!substr_count($this->form, 'multipart/form-data')) {
            $this->form = str_replace('<form ', '<form enctype="multipart/form-data" ', $this->form);
        }
        //Create the file input.
        $fileForm = '<input type="file" name="'.$inputId.'" id="'.$inputId.'"';
        // Allows the user to expand with its own html code.
        if ($expand != '') {
            $fileForm .= ' '.$expand.' ';
        }
        $fileForm .= ' />';
        
        $this->form .= $fileForm;
        
        if($this->newLine == "always" || $this->newLine == "afterinput") {
            self::addNewLine(); 
        }
    }

    /* Generate an textarea.
     * Input:
     *      @param string $id 
     *      @param text $value
     *      @param int $height
     *      @param int $width
     *      @param int $maxlength
     *      @param string $expand (add something self to input eg. placeholder="information" or readonly)
     */
    public function addTextarea($id, $value='', $height=0, $width=0, $maxlength=0, $expand='') {
        $textareaId = trim($id);
        // If no width is set, use standard width.
        if($width == 0) {
            $width = $this->inputWidth;
        } 
        // If no height is set, use half width.
        if($height == 0) {
            $height = round($width / 2);
        }
        //Create the textarea.
        $textareaForm = '<textarea name="'.$textareaId.'" id="'.$textareaId.'" style="width: '.strval($width).'px; height: '.strval($height).'px;"';
        if($maxlength != 0) {
            $textareaForm .= ' maxlength="'.strval($maxlength).'" ';
        }
        // Allows the user to expand with its own html code.
        if ($expand != '') {
            $textareaForm .= ' '.$expand.' ';
        }
        $textareaForm .= '>'.$value.'</textarea>';

        $this->form .= $textareaForm;
        
        if($this->newLine == "always" || $this->newLine == "afterinput") {
            self::addNewLine(); 
        }
    }

    /* Generate an checkbox or a radio.
     * Input:
     *      @param string $id
     *      @param string $value
     *      @param Enum $type (checkbox or radio)
     *      @param boolean $checked
     */
    public function addCheckbox($id, $value, $type='checkbox', $checked=false) {
        $boxName = trim($id);
        $checkbox = '<input type="'.$type.'" name="'.$boxName.'" value="'.$value.'"';
        if($checked) {
            $checkbox .= ' checked';
        }
        $checkbox .= '/>';
        
        $this->form .= $checkbox;
    }

    /* Generate an select from a list.
     * Input:
     *      @param string $id 
     *      @param string $text (an informational text first in the list.)
     *      @param array $list (eg. array('keyValue'=>"displayed text"))
     *      @param int $width
     *      @param string $selected (Key to election choices)
     *      @param string $expand
     */
    public function addSelect($id, $text, $list, $width=0, $selected='', $expand='') {
        $selectId = trim($id);
        $selected = trim($selected);
        // create the select.
        $select = '<select id="'.$selectId.'" name="'.$selectId.'"';
        // Add width only if it is set.
        if($width != 0) {
            $select .= 'style="width: '.$width.'px;"';
        }
        // Allows the user to expand with its own html code.
        if ($expand != '') {
            $select .= ' '.$expand.' ';
        }
        $select .= '>';
        $select .= '<option value="">-- '.trim($text).' --</option>';
        // Fix all select choice from the list.
        $keys = array_keys($list);
        foreach($keys as $keyvalue) {
            $select .= '<option value="'.$keyvalue.'"';
            if(strtolower($keyvalue) == strtolower($selected)) {
                $select .= ' selected ';
            }
            $select .= '>'.$list[$keyvalue].'</option>';
        }
        $select .= '</select>';
        
        $this->form .= $select;
        
        if($this->newLine == "always" || $this->newLine == "afterinput") {
            self::addNewLine(); 
        }
    }

    /* Generate an select from database value.
     * Input:
     *      @param string $id
     *      @param string $text (an informational text first in the list.)
     *      @param sql $sql (eg. "SELECT `id` as `key`, `firstname` as `value` FROM `names` WHERE `in`='yes' ORDER BY `firstname`" )
     *      @param int $width
     *      @param string $selected (Key to election choices)
     *      @param string $expand
     * 
     * @return boolean
     */
    public function addDBSelect($id, $text, $sql, $width=0, $selected='', $expand='') {
        if (DATABASE) {
            // Include only if database is active.
            include_once(dirname(__FILE__) . '/class.dataaccess.php');
            $data = new Dataaccess();
            // Get the values from database.
            $resultList = $data->query($sql);
            $data->close();
            // Generate a correct list, based on the SQL-query is properly constructed.
            // TODO: Do a better check that everything is right.
            $list = array();
            foreach($resultList as $item) {
                $list[$item['key']] = $item['value'];
            }
            self::addSelect($id, $text, $list, $width, $selected, $expand);
            
            return true;
        } else {
            return false;
        }
    }
    
    /* Generate a spamfilter function. 
     * Adds a hidden input with id="spamresult" and the correct answers as value.
     */
    public function addSpamFilter() {
        $digitA = rand(1,9);
        $digitB = rand(1,9);
        $result = $digitA + $digitB;
        // Add the hidden answer.
        self::addInput('spamresult', 'hidden', $result);
        // Add the issues and form to respond in.
        $this->form .= $digitA.' + '.$digitB.' = ';
        $this->form .= '<input type="text" name="spamfilter" id="spamfilter" style="width: 30px;" />';
        
        if($this->newLine == "always" || $this->newLine == "afterinput") {
            self::addNewLine(); 
        }
    }
    
    /* Generates a reCAPTCHA for validation
     * Input:
     *      @param string $publicKey (get it from http://www.google.com/recaptcha)
     */
    public function addValidRecaptchalib($publicKey) {
        $this->form .= recaptcha_get_html($publicKey);
        
        if($this->newLine == "always" || $this->newLine == "afterinput") {
            self::addNewLine(); 
        }
    }
    
    /* Ending the form with a submit button.
     * Input:
     *      @param string $value
     *      @param int $margin (Distances on the left side of the button)
     *      @param string $cssClass
     *      @param Enum $type (submit or button)
     *      @param string $javascript
     */
    public function addSubmitEnd($value='Send', $margin=0, $cssClass='', $type='submit', $javascript='') {
        $value = trim($value);
        $margin = trim($margin);
        $submitClass = trim($cssClass);
        $submitType = trim(strtolower($type));
        // Create the end of the form.
        $endForm = '<input name="'.$this->formName.'_send" type="'.$submitType.'" value="'.$value.'" style="margin-left: '.strval($margin).'px;"';
        // Add style class if it is need.
        if($submitClass != '') {
            $endForm .= ' class="'.$submitClass.'" ';
        }
        // Add onclick function if there is an javascript.
        if ($javascript != '') {
            $endForm .= ' onclick="'.$javascript.'" ';
        }
        $endForm .= '/>';
        $endForm .= '</form>';

        $this->form .= $endForm;
        
        if($this->newLine == "always" || $this->newLine == "afterinput") {
            self::addNewLine(); 
        }
    }
    
    /* Return the whole form.
     * 
     * @return html $form
     */
    public function get() {
        return $this->form;
    }
}
?>