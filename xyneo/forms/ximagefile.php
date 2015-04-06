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
     *
     * @var string
     */
    private $forcedMime = "";

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
                $this->error = "image-format-error";
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
                    $this->setMaxWidth((int) $value);
                    break;
                case "MAXHEIGHT":
                    $this->setMaxHeight((int) $value);
                    break;
                case "FORCEDMIME":
                    $this->setMaxHeight($value);
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
     * @param string $mime
     * @return XImagefile
     */
    public function setForcedMime($mime)
    {
        $this->forcedMime = $mime;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getForcedMime()
    {
        return $this->forcedMime;
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
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     *
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
            $savePath = $this->transformDestinationPath($value, $this->forcedMime);
            $image->resize($this->getMaxWidth(), $this->getMaxHeight());
            $image->save($savePath);
            return $savePath;
        } else {
            return parent::evaluate($value, $this->forcedMime);
        }
    }
}
