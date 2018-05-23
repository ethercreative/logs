<?php

namespace ether\logs;

class Controller extends \craft\web\Controller
{

	public function actionStream ()
	{
		$logsDir = \Craft::getAlias('@storage/logs');
		$logFile = \Craft::$app->request->getParam('log');
		$currentLog = \Craft::$app->request->get('log', $logFile);
		$log = file_get_contents($logsDir . '/' . $currentLog);

		exit($log);
	}

}