<?php
/**
 * @link
 * http://worcesterwideweb.com/2008/03/17/php-5-and-imagecreatefromjpeg-recoverable-error-premature-end-of-jpeg-file/
 */
ini_set('display_errors', '0');
# don't show any errors...

error_reporting(E_ALL | E_STRICT);
# ...but do log them

// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// ini_set('error_reporting', -1);

ob_start();

function handleShutdown()
{
	if (function_exists('error_get_last'))
	{
		if (is_array($error = error_get_last()) && $error['type'] == 1)
		{
			$i = ob_get_level();
			while ($i > 0)
			{
				ob_clean();
				$i--;
			}
			echo json_encode($error);
			die ;
		}
	}
}

register_shutdown_function('handleShutdown');

try
{
	$application -> getBootstrap() -> bootstrap('translate');
	$application -> getBootstrap() -> bootstrap('locale');
	$application -> getBootstrap() -> bootstrap('hooks');
	
	$view = Zend_Registry::get('Zend_View');
	
	/**
	 * following step to speed up & beat performance
	 * 1. check album limit
	 * 2. check quota limit
	 * 3. get nodes of this schedulers
	 * 4. get all items of current schedulers.
	 * 5. process each node
	 * 5.1 check required quota
	 * 5.2 fetch data to pubic file
	 * 5.3 store to file model
	 * 6. check status of schedulers, if scheduler is completed == (remaining == 0)
	 * 6.1 udpate feed and message.
	*/
	
	/**
	 * Unlimited time.
	*/
	set_time_limit(0);
	
	$tableScheduler = Engine_Api::_() -> getDbTable('Schedulers', 'Ynmediaimporter');
	$schedulerId = (string)$_REQUEST['scheduler_id'];
	$scheduler = $tableScheduler -> find($schedulerId) -> current();
	
	/**
	 * check Ynmediaimporter::processScheduler for futher information.
	 *
	*/
	$result = Ynmediaimporter::processScheduler($scheduler, 0, 10, 1, 1);
	
	/**
	 * get remain
	 * @see Ynmediaimporter::processScheduler
	*/
	$remain = $result['remain'];
	
	if ($remain == 0)
	{
		$result['message'] = $view -> translate('Your import request has been completed.');
	}
	else
	{
		$result['message'] = $view -> translate('Your import request has been added to the queue.');
	}
	
	echo json_encode($result);
	
	exit(0);
}
catch (Exception $e)
{
	if(APPLICATION_ENV == 'DEVELOPMENT')
	{
		echo json_encode(array(
				'error' => 1,
				'message' => $e->getMessage()
		));
	}	
	else 
	{
		$view = Zend_Registry::get('Zend_View');
		echo json_encode(array(
				'error' => 1,
				'message' => $view -> translate('Your import request has been added to the queue.')
		));
	}
}


