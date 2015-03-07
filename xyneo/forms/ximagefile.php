<?php

class XImagefile extends XFile
{

    /**
     *
     * @var integer
     */
    private $maxWidth = 800;

    /**
     *
     * @var integer
     */
    private $maxHeight = 600;

    /**
     *
     * @var XyneoImage
     */
    private $image;

    /**
     * Validate this form field
     *
     * @see XFile::validate()
     */
    public function validate()
    {
        if (parent::validate() && (! $this->required || in_array($this->getMimeType(), array(
            "image/gif",
            "image/jpeg",
            "image/png"
        )))) {
            return true;
        } else {
            if (! $this->error) {
                $this->error = "image-format-error" . ": " . $this->getMimeType();
            }
            return false;
        }
    }

    /**
     *
     * @see XFile::buildFromParameters()
     */
    public function buildFromParameters($parameters)
    {
        parent::buildFromParameters($parameters);
        
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "MAXWIDTH":
                    $this->setMaxWidth(intval($value));
                    break;
                case "MAXHEIGHT":
                    $this->setMaxHeight(intval($value));
                    break;
            }
        }
        
        return $this;
    }

    /**
     *
     * @param integer $maxWidth            
     * @return XImageFile
     */
    public function setMaxWidth($maxWidth)
    {
        $this->maxWidth = $maxWidth;
        return $this;
    }

    /**
     *
     * @param integer $maxHeight            
     * @return XImageFile
     */
    public function setMaxHeight($maxHeight)
    {
        $this->maxHeight = $maxHeight;
        return $this;
    }

    /**
     *
     * @return integer
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     *
     * @return integer
     */
    public function getMaxHeigth()
    {
        return $this->maxHeight;
    }

    /**
     * @return boolean XyneoImage
     */
    public function getImage()
    {
        if (is_null($this->image)) {
            $this->image = new XyneoImage($this->getTempName());
        }
        return $this->image->exists() ? $this->image : false;
    }

    /**
     *
     * @see XFile::evaluate()
     */
    public function evaluate($value = null)
    {
        $image = $this->getImage();
        if ($image) {
            $image->resample($this->getMaxWidth(), $this->getMaxHeigth());
            $savePath = $this->transformDestinationPath($value);
            $image->save($savePath);
        } else {
            parent::evaluate($value);
        }
    }
}
