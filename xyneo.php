<?php

/*
 * Checking the basic directory structure.
 *------------------------------------------------------------------------------
 *
 * Checks the myapp, public and xyneo folders. It also checks the default config
 * file location.
 * Loads the config file if exists on the default location.
 *
 */


// define xyneo
    define("XYNEO",true);

/*
 * Load and instantiate the bootstrap if exists.
 *------------------------------------------------------------------------------
 *
 */



// Set the root directory

//chdir(__DIR__);

// Check the myapp folder

if(!is_dir('myapp'))
{
    
    die('The myapp folder doesnt exist. Check your directory structure.');
    
}

// Check the xyneo folder

if(!is_dir('xyneo'))
{
    
    die('The xyneo folder doesnt exist. Check your directory structure.');
    
}

// Check the public folder

if(!is_dir('xyneo'))
{
    
    die('The public folder doesnt exist. Check your directory structure.');
    
}

// Check and load the config file

if(file_exists('myapp/config/config.php'))

    require_once 'myapp/config/config.php';

else
    
    die("Could not load config file.");

/*
 * Set error reporting.
 *------------------------------------------------------------------------------
 *
 * Sets the error reporting level accordig to the value defined for developement
 * mode.
 *
 */

// Check DEVELOPER_MODE value

if (defined('DEVELOPER_MODE'))
{
	switch (DEVELOPER_MODE)
	{
		case 'on':
			error_reporting(E_ALL);
		break;
	
		case 'off':
			error_reporting(0);
		break;

		default:
			die('Bad value for DEVELOPER_MODE. Please check your 
                            config file!');
	}
}

else 
{
    
     die('Your config file might have been damaged. Couldnt find const: 
         DEVELOPER_MODE');
    
}


/*
 * Define XYNEO to prevent files from direct access.
 *------------------------------------------------------------------------------
 *
 */


if(file_exists('xyneo/xyneoappinit.php'))
        
    require_once 'xyneo/xyneoappinit.php';

else
    
    die('The xyneoappinit.php file doesnt exist. The core files might have been 
        damaged.');



$xyneo_app = new Bootstrap();




