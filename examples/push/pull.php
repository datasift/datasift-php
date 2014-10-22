<?php
/**
 * DataSift client
 *
 * This software is the intellectual property of MediaSift Ltd., and is covered
 * by retained intellectual property rights, including copyright.
 *
 * @category  DataSift
 * @package   PHP-client
 * @author    Paul Mozo <paul.mozo@datasift.com>
 * @copyright 2011 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */

/**
 * This script creates a pull subscription and requests data from it periodically.
 *
 */

// Include the DataSift library
require dirname(__FILE__) . '/../../lib/datasift.php';

// Include the configuration - put your username and API key in this file
require dirname(__FILE__) . '/../../config.php';

// Authenticate
echo "Creating user...\n";
$user = new DataSift_User(USERNAME, API_KEY);

//Define some CSDL
$csdl = 'interaction.content contains "coffee"';

// Create the stream definition
$stream_definition = $user->createDefinition($csdl);

//Create the push definition
$push_definition = $user->createPushDefinition();
$push_definition->setOutputType('pull');

$push_sub = $push_definition->subscribeDefinition($stream_definition, 'My PHP pull subscription');

echo "Pull subscription created, ID: ".$push_sub->getId()."\n";

//Pull 10 times every 2 seconds
for ($i=0; $i < 10; $i++) { 
	try {
		sleep(2);
		var_dump($push_sub->pull());
	}
	catch (Exception $e){
		echo $e;
	}
}

$push_sub->delete();

?>