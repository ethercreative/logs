<?php

namespace ether\logs;

use Craft;

class Utility extends \craft\base\Utility
{

	public static function displayName (): string
	{
		return Craft::t('logs', 'Logs');
	}

	public static function id (): string
	{
		return 'logs';
	}

	public static function iconPath ()
	{
		return Craft::getAlias('@ether/logs/utility_icon.svg');
	}

	/**
	 * @return string
	 * @throws \Twig_Error_Loader
	 * @throws \yii\base\Exception
	 */
	public static function contentHtml (): string
	{
		$logsDir = Craft::getAlias('@storage/logs');

		$logFiles = array_values(array_filter(
			scandir($logsDir),
			function ($var) {
				return $var[0] != '.';
			}
		));

		if (!count($logFiles))
			return '<p>You don\'t have any log files.</p>';

		$currentLog = Craft::$app->request->get('log', $logFiles[0]);

		$log = file_get_contents($logsDir . '/' . $currentLog);

		return Craft::$app->view->renderTemplate(
			'logs/view',
			compact('currentLog', 'logFiles', 'log')
		);
	}

}