<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

/**
 * This is the base form builder
 */
class XyneoForm extends XyneoHelper
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $action;

    /**
     *
     * @var string
     */
    protected $method = "post";

    /**
     *
     * @var array
     */
    protected $className = array();

    /**
     *
     * @var string
     */
    protected $callback = "back";

    /**
     *
     * @var string
     */
    protected $afterSaveCallback;

    /**
     *
     * @var array
     */
    protected $backAction = array();

    /**
     *
     * @var string
     */
    protected $title;

    /**
     *
     * @var string
     */
    protected $table;

    /**
     *
     * @var string
     */
    protected $submitValue = "::save::";

    /**
     *
     * @var string
     */
    protected $resetValue = "::reset::";

    /**
     *
     * @var string
     */
    protected $backValue = "::back::";

    /**
     *
     * @var array
     */
    protected $elements = array();

    /**
     *
     * @var array
     */
    protected $groups = array();

    /**
     *
     * @var array
     */
    protected $extraButtons = array();

    /**
     *
     * @var boolean
     */
    protected $isMultipart = false;

    /**
     *
     * @var boolean
     */
    protected $hasSubmitButton = true;

    /**
     *
     * @var boolean
     */
    protected $hasBackButton = true;

    /**
     *
     * @var boolean
     */
    protected $hasResetButton = false;

    /**
     *
     * @var boolean
     */
    protected $hasAutoFocus = true;

    /**
     *
     * @var string
     */
    protected $saveMethod = "insert";

    /**
     *
     * @var string
     */
    protected $requiredLabel = "";

    /**
     *
     * @var array
     */
    protected $requiredLabelClass = array(
        "required-label"
    );

    /**
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->action = $_SERVER["REQUEST_URI"];
    }

    /**
     *
     * @param string $id            
     * @return XyneoForm
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     *
     * @param string $action            
     * @return XyneoForm
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     *
     * @param string $method            
     * @return XyneoForm
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     *
     * @param string $className            
     * @return XyneoForm
     */
    public function addClassName($className)
    {
        $this->className[] = $className;
        return $this;
    }

    /**
     *
     * @param string $method            
     * @param array $action            
     * @return XyneoForm
     */
    public function setBackAction($method, $action = array())
    {
        $this->backAction = array(
            $method
        );
        foreach ($action as $item) {
            $this->backAction[] = $item;
        }
        return $this;
    }

    /**
     *
     * @param string $title            
     * @return XyneoForm
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     *
     * @param string $table            
     * @return XyneoForm
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     *
     * @param string $label            
     * @return XyneoForm
     */
    public function setRequiredLabel($label)
    {
        $this->requiredLabel = $label;
        return $this;
    }

    /**
     *
     * @param array $label            
     * @return XyneoForm
     */
    public function setRequiredLabelClass($classes)
    {
        $this->requiredLabelClass = $classes;
        return $this;
    }

    /**
     *
     * @param string $value            
     * @return XyneoForm
     */
    public function setSubmitValue($value)
    {
        $this->submitValue = $value;
        return $this;
    }

    /**
     *
     * @param string $value            
     * @return XyneoForm
     */
    public function setResetValue($value)
    {
        $this->resetValue = $value;
        return $this;
    }

    /**
     *
     * @param string $value            
     * @return XyneoForm
     */
    public function setBackValue($value)
    {
        $this->backValue = $value;
        return $this;
    }

    /**
     *
     * @param string $callback            
     * @return XyneoForm
     */
    public function setCallback($callback = null)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     *
     * @param string $callback            
     * @return XyneoForm
     */
    public function setAfterSaveCallback($callback = null)
    {
        $this->afterSaveCallback = $callback;
        return $this;
    }

    /**
     *
     * @param string $fieldId            
     * @param mixed $value            
     * @return XyneoForm
     */
    public function setValue($fieldId, $value)
    {
        $this->getField($fieldId)->setValue($value);
        return $this;
    }

    /**
     *
     * @param array $values            
     * @return XyneoForm
     */
    public function setValues($values)
    {
        if (is_array($values)) {
            foreach ($values as $fieldId => $value) {
                $field = $this->getField($fieldId);
                if ($field) {
                    $field->setValue($value);
                }
            }
        }
        return $this;
    }

    /**
     *
     * @param string $fieldId            
     * @param string $error            
     * @return XyneoForm
     */
    public function setError($fieldId, $error)
    {
        $this->getField($fieldId)->setError($error);
        return $this;
    }

    /**
     *
     * @param array $groupList            
     * @return XyneoForm
     */
    public function setGroups($groupList)
    {
        $this->groups = $groupList;
        return $this;
    }

    /**
     *
     * @param array $buttonList            
     * @return XyneoForm boolean
     */
    public function setExtraButtons($buttonList)
    {
        if (is_array($buttonList)) {
            $this->extraButtons = $buttonList;
            return $this;
        } else {
            return false;
        }
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoForm
     */
    public function setMultipart($state = true)
    {
        $this->isMultipart = (bool) $state;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isSent()
    {
        if ($this->method == "post") {
            return isset($_POST["sent"]) ? true : false;
        } else {
            return isset($_GET["sent"]) ? true : false;
        }
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *
     * @return array
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     *
     * @return array
     */
    public function getBackAction()
    {
        return $this->backAction;
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     *
     * @return string
     */
    public function getRequireLabel()
    {
        return $this->requiredLabel;
    }

    /**
     *
     * @return array
     */
    public function getRequireLabelClass()
    {
        return $this->requiredLabelClass;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     *
     * @param string $id            
     * @return boolean
     */
    public function hasField($id)
    {
        return isset($this->elements[$id]);
    }

    /**
     *
     * @param XyneoField $field            
     * @throws XyneoError
     * @return XyneoForm
     */
    public function addField(XyneoField $field)
    {
        if (! $field->getId()) {
            if (DEVELOPER_MODE == "on") {
                ob_start();
                var_dump($field);
                throw new XyneoError("Form error: adding field without ID, parameters:\n" . ob_get_clean());
            }
        }
        if ($field->getType() == "File" || is_subclass_of(get_class($field), "XyneoFile")) {
            $this->isMultipart = true;
        } else 
            if ($field->getType() == "Compose") {
                $this->hasCompose = true;
            }
        $this->elements[$field->getId()] = $field;
        return $this;
    }

    /**
     *
     * @param string $selectedFieldId            
     * @param XyneoField $newField            
     * @return XyneoForm
     */
    public function addFieldBefore($selectedFieldId, XyneoField $newField)
    {
        if ($newField->getType() == "File" || is_subclass_of(get_class($newField), "XFile")) {
            $this->isMultipart = true;
        } /*
           * else { if ($newField->getType() == "Compose") { $this->hasCompose = true; } }
           */
        
        $item = false;
        $num = 0;
        $Field = null;
        foreach ($this->elements as $fieldId => $field) {
            if ($fieldId == $selectedFieldId) {
                $item = $num;
                $Field = $field;
                break;
            }
            $num ++;
        }
        
        if ($item === false) {
            $item = count($this->elements);
        } else {
            $newField->setGroup($Field->getGroup());
        }
        
        $newFieldArr = array(
            $newField->getId() => $newField
        );
        
        $this->elements = array_merge(array_slice($this->elements, 0, $item), $newFieldArr, array_slice($this->elements, $item));
        
        return $this;
    }

    /**
     *
     * @param string $selectedFieldId            
     * @param XyneoField $newField            
     * @return XyneoForm
     */
    public function addFieldAfter($selectedFieldId, XyneoField $newField)
    {
        if ($newField->getType() == "File" || is_subclass_of(get_class($newField), "XFile")) {
            $this->isMultipart = true;
        } /*
           * else { if ($newField->getType() == "Compose") { $this->hasCompose = true; } }
           */
        $item = false;
        $num = 0;
        $Field = null;
        foreach ($this->elements as $fieldId => $field) {
            if ($fieldId == $selectedFieldId) {
                $item = $num;
                $Field = $field;
                break;
            }
            $num ++;
        }
        
        if ($item === false) {
            $item = count($this->elements);
        } else {
            $item ++;
            $newField->setGroup($Field->getGroup());
        }
        
        $newFieldArr = array(
            $newField->getId() => $newField
        );
        
        $this->elements = array_merge(array_slice($this->elements, 0, $item), $newFieldArr, array_slice($this->elements, $item));
        
        return $this;
    }

    /**
     *
     * @param
     *            XyneoField field1
     * @param
     *            XyneoField field2...
     * @return XyneoForm
     */
    public function addFields()
    {
        $a = func_get_args();
        foreach ($a as $v) {
            $this->addField($v);
        }
        return $this;
    }

    /**
     *
     * @param string $value            
     * @return XyneoForm
     */
    public function addExtraButton($value)
    {
        $this->extraButtons[] = $value;
        return $this;
    }

    /**
     *
     * @param string $groupId            
     * @param string $groupTitle            
     * @return XyneoForm
     */
    public function addGroup($groupId, $groupTitle)
    {
        if (! isset($this->groups[$groupId])) {
            $this->groups[$groupId] = $groupTitle;
        }
        return $this;
    }

    /**
     *
     * @return XyneoForm
     */
    public function removeField()
    {
        $fields = func_get_args();
        foreach ($fields as $fieldId) {
            if (isset($this->elements[$fieldId])) {
                unset($this->elements[$fieldId]);
            }
        }
        
        return $this;
    }

    /**
     *
     * @param string $groupId            
     * @return XyneoForm
     */
    public function removeGroup($groupId)
    {
        if (isset($this->groups[$groupId])) {
            unset($this->groups[$groupId]);
        }
        return $this;
    }

    /**
     *
     * @param string $buttonText            
     * @return XyneoForm
     */
    public function removeExtraButton($buttonText)
    {
        $key = array_search($buttonText, $this->extraButtons);
        if ($key !== false) {
            unset($this->extraButtons[$key]);
        }
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoForm
     */
    public function disableSubmit($state = true)
    {
        $this->hasSubmitButton = ! $state;
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoForm
     */
    public function disableBack($state = true)
    {
        $this->hasBackButton = ! $state;
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoForm
     */
    public function disableReset($state = true)
    {
        $this->hasResetButton = ! $state;
        return $this;
    }

    /**
     *
     * @param boolean $state            
     * @return XyneoForm
     */
    public function disableAutoFocus($state = true)
    {
        $this->hasAutoFocus = ! $state;
        return $this;
    }

    /**
     *
     * @param string $id            
     * @return XyneoField
     */
    public function getField($id)
    {
        if (isset($this->elements[$id])) {
            return $this->elements[$id];
        }
        return false;
    }

    /**
     *
     * @return string
     */
    public function getSubmitValue()
    {
        return $this->submitValue;
    }

    /**
     *
     * @return string
     */
    public function getResetValue()
    {
        return $this->resetValue;
    }

    /**
     *
     * @return string
     */
    public function getBackValue()
    {
        return $this->backValue;
    }

    /**
     *
     * @return string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     *
     * @param string $fieldId            
     * @return string
     */
    public function getValue($fieldId)
    {
        return $this->getField($fieldId)->getValue();
    }

    /**
     *
     * @return array
     */
    public function getValues()
    {
        return $this->invoke("getValue");
    }

    /**
     *
     * @param string $fieldId            
     * @return string
     */
    public function getError($fieldId)
    {
        return $this->getField($fieldId)->getError();
    }

    /**
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = array();
        foreach ($this->elements as $fieldId => $field) {
            $error = $field->getError();
            if ($error) {
                $errors[$fieldId] = $error;
            }
        }
        return $errors;
    }

    /**
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     *
     * @return array
     */
    public function getExtraButtons()
    {
        return $this->extraButtons;
    }

    /**
     *
     * @return boolean
     */
    public function isMultipart()
    {
        return $this->isMultipart;
    }

    /**
     *
     * @return boolean
     */
    public function hasSubmitButton()
    {
        return $this->hasSubmitButton;
    }

    /**
     *
     * @return boolean
     */
    public function hasBackButton()
    {
        return $this->hasBackButton;
    }

    /**
     *
     * @return boolean
     */
    public function hasResetButton()
    {
        return $this->hasResetButton;
    }

    /**
     *
     * @return boolean
     */
    public function hasAutoFocus()
    {
        return $this->hasAutoFocus;
    }

    /**
     *
     * @return boolean
     */
    public function hasErrors()
    {
        foreach ($this->getElements() as $field) {
            if ($field->getError()) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return XyneoForm
     */
    public function queryValues()
    {
        foreach ($this->elements as $fieldId => $field) {
            if (method_exists($field, "queryValue")) {
                $value = $field->queryValue($this->method);
                if ($value !== false) {
                    $field->setValue($value);
                }
            } else {
                if (strpos($fieldId, "[") !== false) {
                    $names = preg_split("/(\[|\])/", $fieldId, - 1, PREG_SPLIT_NO_EMPTY);
                    if ($this->method == "post") {
                        $varName = "\$_POST[\"" . $names[0] . "\"]";
                    } else {
                        $varName = "\$_GET[\"" . $names[0] . "\"]";
                    }
                    for ($i = 1; $i < count($names); $i ++) {
                        $varName .= "[\"" . $names[$i] . "\"]";
                    }
                    $value = @eval("return " . $varName . ";");
                } else {
                    if ($this->method == "post") {
                        $value = isset($_POST[$fieldId]) ? $_POST[$fieldId] : null;
                    } else {
                        $value = isset($_GET[$fieldId]) ? $_GET[$fieldId] : null;
                    }
                }
                if (get_magic_quotes_gpc() && is_string($value)) {
                    $value = stripslashes($value);
                }
                $field->setValue($value);
            }
        }
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function validateFields()
    {
        $isValid = true;
        foreach ($this->elements as $fieldId => $field) {
            if (! $field->validate()) {
                $isValid = false;
            }
        }
        return $isValid;
    }

    /**
     *
     * @param string $dbField
     *            Field's id
     * @param mixed $value
     *            Field's value
     * @param boolean $isKey
     *            The field is key or not
     * @return XyneoForm
     */
    public function addDbField($dbField, $value = null, $isKey = false)
    {
        $field = new XHidden($dbField);
        $field->setField($dbField)
            ->setValue($value)
            ->setKey($isKey)
            ->required(false);
        $this->addField($field);
        return $this;
    }

    /**
     *
     * @param string $methodName            
     * @param array $args            
     * @param array $fieldIdList            
     * @throws XyneoError
     * @return multitype:mixed
     */
    public function invoke($methodName, $args = array(), $fieldIdList = array())
    {
        $values = array();
        foreach ($this->elements as $fieldId => $field) {
            if (is_callable(array(
                $field,
                $methodName
            ))) {
                if (empty($fieldIdList) || in_array($fieldId, $fieldIdList)) {
                    $values[$fieldId] = call_user_func_array(array(
                        $field,
                        $methodName
                    ), $args);
                }
            } else {
                if (DEVELOPER_MODE == "on") {
                    throw new XyneoError("error calling method " . $methodName . " on field " . get_class($field));
                }
            }
        }
        return $values;
    }

    public function renderContent()
    {
        $ret = "";
        if ($this->isSent()) {
            $this->addClassName("sent");
        }
        if ($this->hasAutoFocus()) {
            $this->addClassName("autofocus");
            $script = new XScript();
            $script->setId("autofocus")->setValue("var f=document.getElementsByTagName('form');for(var i=f.length-1;i>=0;i--){if(f[i].className.indexOf('autofocus')!=-1){var e=f[i].elements;for(var j=0;j<e.length;j++){if(e[j].type!='hidden'){e[j].focus();break}}delete e;break}}delete f");
            $this->addField($script);
        }
        if ($this->hasBackButton() && count($this->getBackAction())) {
            $script = new XScript();
            $script->setId("backaction")->setValue("function b(){location.href='/" . trim(implode("/", $this->getBackAction()), "/") . "'}");
            $this->addField($script);
        }
        
        $sent = new XHidden();
        $sent->setId("sent")->setValue(1);
        $this->addField($sent);
        
        $ret .= "<form action=\"" . $this->getAction() . "\" method=\"" . $this->getMethod() . "\"" . ($this->getId() ? " id=\"" . $this->getId() . "\"" : "") . ($this->isMultipart() ? " enctype=\"multipart/form-data\"" : "") . (count($this->getClassName()) ? " class=\"" . trim(implode(" ", $this->getClassName())) . "\"" : "") . ">";
        if ($this->getTitle()) {
            $ret .= "<div class=\"form-title\">" . $this->getTitle() . "</div>";
        }
        $ret .= "<div class=\"form-content\">";
        $countGroups = count($this->getGroups());
        if ($countGroups) {
            $i = 0;
            foreach ($this->getGroups() as $groupId => $groupName) {
                $ret .= "<div class=\"form-group\" id=\"g_" . $groupId . "\">";
                $ret .= "<div class=\"form-group-title" . ($countGroups > 1 ? " form-group-" . ($i ? "open" : "close") : "") . "\">";
                $ret .= "<span>" . $groupName . "</span>";
                if ($this->getRequireLabel() && ! $i) {
                    $ret .= "<span class=\"" . implode(" ", $this->getRequireLabelClass()) . "\">" . $this->getRequireLabel() . "</span>";
                }
                $ret .= "<div class=\"clear clr clearfix\"></div>";
                $ret .= "</div>";
                $ret .= "<div class=\"form-group-elements" . ($i ? " closed" : "") . "\">";
                foreach ($this->getElements() as $field) {
                    if (($field->getGroup() && $field->getGroup() != $groupId) || (! $field->getGroup() && $i)) {
                        continue;
                    }
                    if (get_class($field) == "XHidden" || get_class($field) == "XScript") {
                        $ret .= $field->renderContent();
                    } elseif ($field->isRenderable()) {
                        $ret .= "<div class=\"form-field" . ($field->isHidden() ? " hidden" : "") . "\" id=\"f_" . $field->getId() . "\">";
                        $ret .= "<div class=\"field-label\">" . $field->renderLabel() . "</div>";
                        $ret .= "<div class=\"field-content\">" . $field->renderContent() . $field->renderError() . "</div>";
                        $ret .= "<div class=\"clear clr clearfix\"></div>";
                        $ret .= "</div>";
                    }
                }
                $ret .= "</div>"; // end form-group-elements
                $ret .= "</div>"; // end form-group
                $i ++;
            }
        } else {
            foreach ($this->getElements() as $field) {
                if (get_class($field) == "XHidden" || get_class($field) == "XScript") {
                    $ret .= $field->renderContent();
                } elseif ($field->isRenderable()) {
                    $ret .= "<div class=\"form-field" . ($field->isHidden() ? " hidden" : "") . "\" id=\"f_" . $field->getId() . "\">";
                    $ret .= "<div class=\"field-label\">" . $field->renderLabel() . "</div>";
                    $ret .= "<div class=\"field-content\">" . $field->renderContent() . $field->renderError() . "</div>";
                    $ret .= "<div class=\"clear clr clearfix\"></div>";
                    $ret .= "</div>";
                }
            }
            if ($this->getRequireLabel()) {
                $ret .= "<span class=\"" . implode(" ", $this->getRequireLabelClass()) . "\">" . $this->getRequireLabel() . "</span>";
            }
            $ret .= "<div class=\"clear clr clearfix\"></div>";
        }
        $ret .= "<div class=\"form-buttons\">";
        if ($this->hasSubmitButton()) {
            $ret .= "<button type=\"submit\"" . ($this->getId() ? " id=\"" . $this->getId() . "-submit\"" : "") . ">" . $this->getSubmitValue() . "</button>";
        }
        foreach ($this->getExtraButtons() as $num => $button) {
            $ret .= "<button type=\"button\" id=\"" . ($this->getId() ? $this->getId() . "-" : "") . "button-" . $num . "\">" . $button . "</button>";
        }
        if ($this->hasResetButton()) {
            $ret .= "<button type=\"reset\" class=\"cancel\">" . $this->getResetValue() . "</button>";
        }
        if ($this->hasBackButton() && count($this->getBackAction())) {
            $ret .= "<button type=\"button\" class=\"cancel\" id=\"" . ($this->getId() ? $this->getId() . "-" : "") . "button-back\" onclick=\"b()\">" . $this->getBackValue() . "</button>";
        }
        $ret .= "<div class=\"clear clr clearfix\"></div>";
        $ret .= "</div>"; // end form-buttons
        $ret .= "</div>"; // end form-content
        $ret .= "</form>";
        return $ret;
    }

    /**
     * Load values from the database by specified condition.
     *
     * @param array $condition            
     * @throws XyneoError
     * @return XyneoForm
     */
    public function loadValues($condition)
    {
        if (! $this->table || (! is_array($condition) || ! count($condition)) || ! count($this->elements)) {
            throw new XyneoError("Form error: cannot load form values, missing required conditions");
        }
        
        $fields = array();
        foreach ($this->elements as $field) {
            if ($field->getField()) {
                $fields[$field->getField()] = $field;
            }
        }
        
        if (! count($fields)) {
            throw new XyneoError("Form error: cannot load form values, missing database fields");
        }
        
        $this->db->xSelect(array_keys($fields))
            ->xFrom($this->table)
            ->xLimit(1);
        foreach ($condition as $key => $value) {
            $this->db->xWhere($key, $value, "=");
        }
        $row = $this->db->xGet();
        
        if (! $row->rowCount()) {
            throw new XyneoError("Form error: cannot find record to load data from");
        }
        $rs = $row->fetch(PDO::FETCH_ASSOC);
        
        foreach ($fields as $dbField => $field) {
            $field->setValue($rs[$dbField]);
        }
        
        return $this;
    }

    /**
     *
     * @param string $type            
     * @throws XyneoError
     * @return boolean integer
     */
    public function save($type = null)
    {
        if (is_null($type)) {
            $type = $this->saveMethod;
        }
        $type = strtolower($type);
        if (! $this->validateFields()) {
            return false;
        }
        if ($type != "insert" && $type != "update") {
            throw new XyneoError("Form error: cannot save form, no valid save type set");
        }
        
        if ($this->table) {
            $values = array();
            $keys = array();
            foreach ($this->elements as $field) {
                if ($field->getField() && $field->getType() != "Html" && ! is_subclass_of(get_class($field), "XHtml")) {
                    if (strtolower($field->getValue()) == "null") {
                        $values[$field->getField()] = "NULL";
                    } else {
                        $values[$field->getField()] = $field->getValue();
                    }
                    if ($type == "update" && $field->isKey()) {
                        $keys[$field->getField()] = $field->getValue();
                    }
                }
            }
            
            if (! count($values)) {
                throw new XyneoError("From error: cannot save form, no fields to save");
            }
            
            if ($type == "update" && ! count($keys)) {
                throw new XyneoError("Form error: cannot save form, keys not present");
            }
            
            foreach ($this->elements as $field) {
                if ($field->isUnique() && $field->getField()) {
                    $this->db->xSelect(array(
                        "COUNT(*)"
                    ))
                        ->xFrom($this->table)
                        ->xWhere($field->getField(), $field->getValue(), "=");
                    foreach ($keys as $key => $value) {
                        $this->db->xWhere($key, $value, "<>");
                    }
                    $rs = $this->db->xGet();
                    
                    if ($rs->fetchColumn(0)) {
                        $field->setError("already-exists-in-the-database");
                    }
                }
                if ($field->getMatchTo() && $this->getField($field->getMatchTo()) && $field->getValue() != $this->getValue($field->getMatchTo())) {
                    $field->setError("field-contents-are-not-the-same");
                }
            }
            
            if (count($this->getErrors())) {
                return false;
            }
            
            $options = array(
                "table" => $this->table,
                "fields" => $values,
                "condition" => $keys
            );
            $rs = $this->db->xSet($type, $options);
            
            $insertId = ($type == "insert" ? $this->db->lastInsertId($this->table . "." . current(array_keys($keys))) : current(array_values($keys)));
        } else {
            $insertId = "";
        }
        
        foreach ($this->getElements() as $field) {
            if (method_exists($field, "evaluate")) {
                $field->evaluate($insertId, $type);
            }
        }
        
        $this->triggerCallback($insertId, $type);
        
        return $insertId;
    }

    /**
     *
     * @param string $insertId            
     * @param string $type            
     */
    public function triggerCallback($insertId = "", $type = null)
    {
        if (is_null($type)) {
            $type = $this->saveMethod;
        }
        $type = strtolower($type);
        
        if (is_callable($this->afterSaveCallback)) {
            call_user_func($this->afterSaveCallback, $insertId, $this, $type);
        }
        
        if ($this->callback) {
            if ($this->callback == "back") {
                if (! count($this->backAction)) {
                    $redirect = $_SERVER["REQUEST_URI"];
                } else {
                    $redirect = implode("/", $this->getBackAction());
                }
                
                header("Location: /" . trim($redirect, "/"));
                exit();
            } else {
                if (is_callable($this->callback)) {
                    call_user_func($this->callback, $insertId, $this);
                }
            }
        }
    }
}

?>