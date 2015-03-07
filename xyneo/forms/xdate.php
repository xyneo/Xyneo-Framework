<?php

class XDate extends XSelect
{

    /**
     * The first year in interval
     *
     * @var string
     */
    private $firstYear = "-10";

    /**
     * The last year in interval
     *
     * @var string
     */
    private $lastYear = "+10";

    /**
     * Year from the field value
     *
     * @var integer
     */
    private $year;

    /**
     * Month from the field value
     *
     * @var integer
     */
    private $month;

    /**
     * Day from the field value
     *
     * @var integer
     */
    private $day;

    /**
     * Date format for render
     *
     * @var string
     */
    private $dateformat = "Year: %Y Month: %m Day: %j";

    /**
     * Original id wrapper
     *
     * @var string
     */
    private $originalId;

    /**
     * Original tooltip wrapper
     *
     * @var string
     */
    private $originalTooltip;

    /**
     * Original prefix wrapper
     *
     * @var string
     */
    private $originalPrefix;

    /**
     * Original suffix wrapper
     *
     * @var string
     */
    private $originalSuffix;

    /**
     * Original classname wrapper
     *
     * @var string
     */
    private $originalClassname;

    /**
     * Build field from parameters
     *
     * @see XSelect::buildFromParameters()
     */
    public function buildFromParameters($parameters)
    {
        parent::buildFromParameters($parameters);
        
        foreach ($parameters as $key => $value) {
            switch (strtoupper($key)) {
                case "FIRSTYEAR":
                    $this->setFirstYear($value);
                    break;
                case "LASTYEAR":
                    $this->setLastYear($value);
                    break;
                case "DATEFORMAT":
                    $this->setDateformat($value);
                    break;
            }
        }
        
        return $this;
    }

    /**
     * Set first year interval
     *
     * @param string $year
     *            E.g.: -10
     * @return XDate
     */
    public function setFirstYear($year)
    {
        $this->firstYear = $year;
        return $this;
    }

    /**
     * Set last year interval
     *
     * @param string $year
     *            E.g.: +10
     * @return XDate
     */
    public function setLastYear($year)
    {
        $this->lastYear = $year;
        return $this;
    }

    /**
     * Set date format
     *
     * @param string $format
     *            Date format may only contain: <ul>
     *            <li>%y - A two digit representation of a year</li>
     *            <li>%Y - A full numeric representation of a year, 4 digits</li>
     *            <li>%F - A full textual representation of a month, such as January or March</li>
     *            <li>%m - Numeric representation of a month, with leading zeros</li>
     *            <li>%M - A short textual representation of a month, three letters</li>
     *            <li>%n - Numeric representation of a month, without leading zeros</li>
     *            <li>%d - Day of the month, 2 digits with leading zeros</li>
     *            <li>%j - Day of the month without leading zeros</li>
     *            </ul>
     * @return XDate
     */
    public function setDateformat($format)
    {
        $this->dateformat = $format;
        return $this;
    }

    /**
     * Get first year interval
     *
     * @return string
     */
    public function getFirstYear()
    {
        return $this->firstYear;
    }

    /**
     * Get last year interval
     *
     * @return string
     */
    public function getLastYear()
    {
        return $this->lastYear;
    }

    /**
     * Get date format
     *
     * @return string
     */
    public function getDateformat()
    {
        return $this->dateformat;
    }

    /**
     * Set year, month and day from the field value
     *
     * @see XyneoField::setValue()
     */
    public function setValue($value)
    {
        if (! preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $value)) {
            return false;
        }
        list ($this->year, $this->month, $this->day) = explode("-", $value);
        return $this;
    }

    /**
     * Get the value from the request method
     *
     * @param string $method
     *            Request method: <ul>
     *            <li>get</li>
     *            <li>post</li>
     *            </ul>
     * @return string
     */
    public function queryValue($method)
    {
        $value = "";
        if ($method == "post") {
            if (isset($_POST[$this->id . "-year"]) && isset($_POST[$this->id . "-month"]) && isset($_POST[$this->id . "-day"])) {
                $value = $_POST[$this->id . "-year"] . "-" . $_POST[$this->id . "-month"] . "-" . $_POST[$this->id . "-day"];
            }
        } else {
            if (isset($_GET[$this->id . "-year"]) && isset($_GET[$this->id . "-month"]) && isset($_GET[$this->id . "-day"])) {
                $value = $_GET[$this->id . "-year"] . "-" . $_GET[$this->id . "-month"] . "-" . $_GET[$this->id . "-day"];
            }
        }
        return $value;
    }

    /**
     * Get the value from year, month and day
     *
     * @see XyneoField::getValue()
     */
    public function getValue()
    {
        if ($this->year && $this->month && $this->day) {
            $this->value = $this->year . "-" . str_pad($this->month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($this->day, 2, "0", STR_PAD_LEFT);
        }
        return parent::getValue();
    }

    /**
     * Validate this form field
     *
     * @see XSelect::validate()
     */
    public function validate()
    {
        if (! $this->error && (! $this->required || strtotime($this->getValue()))) {
            $year = date("Y", strtotime($this->getValue()));
            if ($year < $this->calculateFirst() || $year > $this->calculateLast()) {
                $this->error = "date-is-out-of-range";
                return false;
            } else {
                return true;
            }
        } else {
            if (! $this->error) {
                $this->error = "date-format-error";
            }
            return false;
        }
    }

    /**
     * Render field for the form
     *
     * @see XSelect::renderContent()
     */
    public function renderContent()
    {
        $this->originalId = $this->id;
        $this->originalTooltip = $this->tooltip;
        $this->setTooltip("");
        $this->originalPrefix = $this->prefix;
        $this->setPrefix("");
        $this->originalSuffix = $this->suffix;
        $this->setSuffix("");
        $this->originalClassname = $this->className;
        $this->setClassName("");
        $ret = "";
        if ($this->originalTooltip) {
            $this->originalClassname .= " tooltip";
        }
        if ($this->originalPrefix) {
            $ret .= $this->originalPrefix . " ";
        }
        $dateFormat = $this->dateformat;
        $dateFormat = str_ireplace(array(
            "%Y",
            "%y",
            "%F",
            "%m",
            "%M",
            "%n",
            "%d",
            "%j"
        ), array(
            $this->showYear("Y"),
            $this->showYear("y"),
            $this->showMonth("F"),
            $this->showMonth("m"),
            $this->showMonth("M"),
            $this->showMonth("n"),
            $this->showDay("d"),
            $this->showDay("j")
        ), $dateFormat);
        $ret .= "<span" . ($this->originalClassname ? " class=\"" . trim($this->originalClassname) . "\"" : "") . ">" . $dateFormat . "</span>";
        if ($this->originalTooltip) {
            $ret .= " <span id=\"tt_" . $this->id . "\" class=\"tooltip\">" . $this->originalTooltip . "</span>";
        }
        if ($this->originalSuffix) {
            $ret .= " " . $this->originalSuffix;
        }
        return $ret;
    }

    /**
     * Build year select field
     *
     * @param string $format
     *            Year format from $dateformat
     * @return string
     */
    protected function showYear($format)
    {
        $this->setId($this->originalId . "-year");
        $options = array();
        for ($i = $this->calculateLast(); $i >= $this->calculateFirst(); $i --) {
            $time = $i . "-01-01";
            $options[$i] = date($format, strtotime($time));
        }
        $this->setOptions($options);
        $this->setUserValue((int) $this->year);
        return parent::renderContent();
    }

    /**
     * Build month select field
     *
     * @param string $format
     *            Month format from $dateformat
     * @return string
     */
    protected function showMonth($format)
    {
        $this->setId($this->originalId . "-month");
        $options = array();
        for ($i = 1; $i <= 12; $i ++) {
            $time = date("Y") . "-" . ($i < 10 ? "0" . $i : $i) . "-01";
            $value = date($format, strtotime($time));
            $options[$i] = is_numeric($value) ? $value : "::" . $value . "::";
        }
        $this->setOptions($options);
        $this->setUserValue((int) $this->month);
        return parent::renderContent();
    }

    /**
     * Build day select field
     *
     * @param string $format
     *            Day format from $dateformat
     * @return string
     */
    protected function showDay($format)
    {
        $this->setId($this->originalId . "-day");
        $options = array();
        for ($i = 1; $i <= 31; $i ++) {
            $time = date("Y") . "-01-" . ($i < 10 ? "0" . $i : $i);
            $options[$i] = date($format, strtotime($time));
        }
        $this->setOptions($options);
        $this->setUserValue((int) $this->day);
        return parent::renderContent();
    }

    /**
     * Calculate first year by interval
     *
     * @return integer
     */
    protected function calculateFirst()
    {
        switch (substr($this->firstYear, 0, 1)) {
            case "+":
                return (int) (date("Y") + substr($this->firstYear, 1));
                break;
            case "-":
                return (int) (date("Y") - substr($this->firstYear, 1));
                break;
            case "":
                return (int) date("Y");
                break;
            default:
                return (int) $this->firstYear;
                break;
        }
    }

    /**
     * Calculate last year by interval
     *
     * @return integer
     */
    protected function calculateLast()
    {
        switch (substr($this->lastYear, 0, 1)) {
            case "+":
                return (int) (date("Y") + substr($this->lastYear, 1));
                break;
            case "-":
                return (int) (date("Y") - substr($this->lastYear, 1));
                break;
            case "":
                return (int) date("Y");
                break;
            default:
                return (int) $this->lastYear;
                break;
        }
    }
}
