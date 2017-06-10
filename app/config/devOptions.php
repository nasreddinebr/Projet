<?php
/**********************************************************************************
 * This file include s configuring the connection options, and then setting Silex *
 * to dispalay detailed debugging information in the event of an error.			  *
 *********************************************************************************/

//include the configuration options
require __DIR__.'/configOptions.php';

//enable the debug mode
$app['debug'] = true;