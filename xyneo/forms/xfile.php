<?php

class XFile extends XInputtext
{

    /**
     *
     * @var string
     */
    private $destination;

    /**
     *
     * @var string
     */
    private $mimeTypes = "*";

    /**
     * Validate this form field
     *
     * @see XInputtext::validate()
     */
    public function validate()
    {
        if ($this->error) {
            return false;
        }
        
        if ($this->required && ! is_uploaded_file($_FILES[$this->id]["tmp_name"])) {
            $this->error = "required-to-upload";
            return false;
        }
        
        if (is_uploaded_file($_FILES[$this->id]["tmp_name"])) {
            if ($this->mimeTypes != "*" && ! in_array($this->getMimeType(), explode(",", $this->mimeTypes))) {
                $this->error = "format-not-allowed";
                return false;
            }
        }
        return true;
    }

    /**
     * Build field from parameters
     *
     * @see XInputtext::buildFromParameters()
     */
    public function buildFromParameters($parameters)
    {
        parent::buildFromParameters($parameters);
        
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "DESTINATION":
                    $this->setDestination($value);
                    break;
                case "MIMETYPES":
                    $this->setMimeTypes($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @return number
     */
    public function queryValue()
    {
        if (is_uploaded_file($_FILES[$this->id]["tmp_name"])) {
            return $this->getDestination();
        }
    }

    /**
     *
     * @param string $targetPath            
     * @return XFile
     */
    public function setDestination($targetPath)
    {
        $this->destination = $targetPath;
        return $this;
    }

    /**
     *
     * @param array $mimeTypes            
     * @return XFile
     */
    public function setMimeTypes($mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return string array
     */
    public function getMimeTypes()
    {
        return $this->mimeTypes;
    }

    /**
     *
     * @return string boolean
     */
    public function getOriginalName()
    {
        if (is_uploaded_file($_FILES[$this->id]["tmp_name"])) {
            return $_FILES[$this->id]["name"];
        } else {
            return false;
        }
    }

    /**
     *
     * @return string boolean
     */
    public function getTempName()
    {
        if (is_uploaded_file($_FILES[$this->id]["tmp_name"])) {
            return $_FILES[$this->id]["tmp_name"];
        } else {
            return false;
        }
    }

    /**
     *
     * @return integer boolean
     */
    public function getFileSize()
    {
        if (is_uploaded_file($_FILES[$this->id]["tmp_name"])) {
            return $_FILES[$this->id]["size"];
        } else {
            return false;
        }
    }

    /**
     *
     * @return string boolean
     */
    public function getMimeType()
    {
        if (is_uploaded_file($_FILES[$this->id]["tmp_name"])) {
            return $_FILES[$this->id]["type"];
        } else {
            return false;
        }
    }

    /**
     *
     * @param mixed $value            
     * @return mixed
     */
    public function transformDestinationPath($value)
    {
        $extension = end(explode(".", $this->getOriginalName()));
        return str_replace(array(
            "[id]",
            "[extension]"
        ), array(
            $value,
            $extension
        ), $this->getDestination());
    }

    /**
     *
     * @param mixed $value            
     * @return boolean
     */
    public function evaluate($value)
    {
        if ($this->getDestination()) {
            return move_uploaded_file($this->getTempName(), $this->transformDestinationPath($value));
        }
    }

    /**
     * Render field for the form
     *
     * @see XInputtext::renderContent()
     */
    public function renderContent()
    {
        $ret = "";
        if ($this->tooltip) {
            $this->className .= " tooltip";
        }
        if (isset($this->prefix)) {
            $ret .= $this->prefix . " ";
        }
        $ret .= "<input type=\"file\" name=\"" . $this->id . "\" id=\"" . $this->id . "\"" . " size=\"" . $this->size . "\"" . ($this->disabled ? " disabled" : "") . ($this->readonly ? " readonly" : "") . ($this->className ? " class=\"" . trim($this->className) . "\"" : "") . " />" . ($this->tooltip ? " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->tooltip . "</span>" : "");
        if (isset($this->suffix)) {
            $ret .= " " . $this->suffix;
        }
        return $ret;
    }
}
