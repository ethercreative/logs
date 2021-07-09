<?php

namespace ether\logs;

use Craft;

class Controller extends \craft\web\Controller
{

	public function actionStream ()
	{
		$logsDir = Craft::getAlias('@storage/logs');
		$logFile = Craft::$app->request->getParam('log');
		$currentLog = basename(Craft::$app->request->get('log', $logFile));

		if (strpos($currentLog, '.log') === false)
			return '<p>You can only access <code>.log</code> files!</p>';

		$log = file_get_contents($logsDir . '/' . $currentLog);

		exit($log);
	}

}