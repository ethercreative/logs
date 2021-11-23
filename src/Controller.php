<?php

namespace ether\logs;

use Craft;
use craft\helpers\UrlHelper;
use Exception;
use yii\web\HttpException;

class Controller extends \craft\web\Controller
{

	public function actionStream ()
	{
		try {
			$logsDir = Craft::getAlias('@storage/logs');
			$currentLog = $this->_getLogFile();
			$log = file_get_contents($logsDir . DIRECTORY_SEPARATOR . $currentLog);

			exit($log);
		} catch (Exception $e) {
			if (strpos($e->getMessage(), 'failed to open stream') !== false)
				return '<p>Unable to find log file</p>';

			return '<p>You can only access .log files!</p>';
		}
	}

	public function actionTruncate ()
	{
		$this->requireAdmin(false);
		$logFile = $this->_getLogFile();
		Logs::getInstance()->service->truncate($logFile);
	}

	public function actionDelete ()
	{
		$this->requireAdmin(false);
		$logFile = $this->_getLogFile();
		Logs::getInstance()->service->delete($logFile);

		return $this->redirect(UrlHelper::cpUrl('utilities/logs'));
	}

	private function _getLogFile (): string
	{
		$logFile = Craft::$app->request->getParam('log');
		$currentLog = basename(Craft::$app->request->get('log', $logFile));

		if (strpos($currentLog, '.log') === false)
			throw new HttpException(403);

		return $currentLog;
	}

}