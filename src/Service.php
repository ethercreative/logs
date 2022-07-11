<?php

namespace ether\logs;

use Craft;
use craft\base\Component;

class Service extends Component
{

	public function truncate ($log)
	{
		$logsDir = Craft::getAlias('@storage/logs');
		$success = file_put_contents($logsDir . DIRECTORY_SEPARATOR . $log, '');

		if ($success !== false) Craft::$app->getSession()->setNotice($log . ' truncated.');
		else Craft::$app->getSession()->setError('Failed to truncate ' . $log . ', check file permissions');
	}

	public function delete ($log)
	{
		$logsDir = Craft::getAlias('@storage/logs');
		$success = unlink($logsDir . DIRECTORY_SEPARATOR . $log);

		if ($success !== false) Craft::$app->getSession()->setNotice($log . ' deleted.');
		else Craft::$app->getSession()->setError('Failed to delete ' . $log . ', check file permissions');
	}

}