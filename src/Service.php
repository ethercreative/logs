<?php

namespace ether\logs;

use Craft;
use craft\base\Component;

class Service extends Component
{

	public function truncate ($log)
	{
		$logsDir = Craft::getAlias('@storage/logs');
		file_put_contents($logsDir . DIRECTORY_SEPARATOR . $log, '');
		Craft::$app->getSession()->setNotice($log . ' truncated.');
	}

	public function delete ($log)
	{
		$logsDir = Craft::getAlias('@storage/logs');
		unlink($logsDir . DIRECTORY_SEPARATOR . $log);
		Craft::$app->getSession()->setNotice($log . ' deleted.');
	}

}