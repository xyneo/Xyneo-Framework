<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

class XyneoModel
{

    /**
     *
     * @var XyneoDataBase
     */
    protected $db;

    /**
     *
     * @var XyneoValidate
     */
    protected $validate;

    /**
     *
     * @var XyneoFile
     */
    protected $file;

    public function __construct()
    {
        if (DB_ALLOW == 'on') {
            $this->db = new XyneoDatabase();
        }
        
        $this->validate = new XyneoValidate();
        $this->file = new XyneoFile();
    }

    /**
     * Set message
     *
     * @param string $msg            
     * @return void
     */
    protected function xSetMessage($msg = false)
    {
        if ($msg) {
            $_SESSION['xyneomessage'] = $msg;
        } else {
            $_SESSION['xyneomessage'] = "MESSAGE";
        }
    }
}
