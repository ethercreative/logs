<?php

namespace ether\logs;

use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use yii\base\Event;

/**
 * @property Service $service
 */
class Logs extends Plugin
{

	public $controllerMap = [
		'logs' => Controller::class,
	];

	public function init ()
	{
		parent::init();

		$this->setComponents([
			'service' => Service::class,
		]);

		Event::on(
			Utilities::class,
			Utilities::EVENT_REGISTER_UTILITY_TYPES,
			function (RegisterComponentTypesEvent $event) {
				$event->types[] = Utility::class;
			}
		);
	}

}