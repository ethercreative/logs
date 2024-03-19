<?php

namespace ether\logs;

use Craft;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

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

	public static function iconPath (): null|string
	{
		return Craft::getAlias('@ether/logs/utility_icon.svg');
	}

	/**
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 * @throws Exception
	 */
	public static function contentHtml (): string
	{
		$logsDir = Craft::getAlias('@storage/logs');

		$logFiles = array_values(array_filter(
			scandir($logsDir),
			function ($var) {
				return $var[0] != '.' && strpos($var, '.log');
			}
		));

		if (!count($logFiles))
			return '<p>You don\'t have any log files.</p>';

		$currentLog = basename(Craft::$app->request->get('log', $logFiles[0]));

		if (strpos($currentLog, '.log') === false)
			return '<p>You can only access <code>.log</code> files!</p>';

		$url = explode('?log', Craft::$app->request->url)[0];

		$css = <<<CSS
#content {
	display: flex;
	flex-direction: column;
	padding-bottom: 0 !important;
}
CSS;

		$js = <<<JS
/** Mixin to extend the String type with a method to escape unsafe characters
 *  for use in HTML.  Uses OWASP guidelines for safe strings in HTML.
 * 
 *  Credit: http://benv.ca/2012/10/4/you-are-probably-misusing-DOM-text-methods/
 *          https://github.com/janl/mustache.js/blob/16ffa430a111dc293cd9ed899ecf9da3729f58bd/mustache.js#L62
 *
 *  Maintained by stevejansen_github@icloud.com
 *
 *  @license http://opensource.org/licenses/MIT
 *
 *  @version 1.0
 *
 *  @mixin
 */
(function(){
  "use strict";

  function escapeHtml() {
    return this.replace(/[&<>"'\/]/g, function (s) {
      var entityMap = {
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          '"': '&quot;',
          "'": '&#39;',
          "/": '&#x2F;'
        };

      return entityMap[s];
    });
  }

  if (typeof(String.prototype.escapeHtml) !== 'function') {
    String.prototype.escapeHtml = escapeHtml;
  }
})();

const logElem = document.getElementById("__log");

function streamLog (log) {
	logElem.innerHTML = "Loading...";
	
	history.pushState(null, document.title, "$url?log=" + log);
	
	fetch(Craft.getActionUrl("logs/logs/stream", { log: log }), {
		credentials: "include"
	}).then(data => data.text()).then(data => {
		let html = "";
		
		data.escapeHtml().split("\\n").forEach(line => {
			let m = /^(\d{4}(-\d{2}){2} (\d{2}:){2}\d{2}) (\[[^\]]+\]){3}\[([^\]]+)\]\[([^\]]+)\]/i.exec(line);
			if (m !== null) {
				let colour = "";
				switch (m[5].split(" ")[0]) {
					case "info":
						colour = "blue";
						break;
					case "warning":
						colour = "orange";
						break;
					case "error":
						colour = "red";
						break;
				}
				html += "<h4>" + m[1] + ' - <span style="' + (colour ? "color:" + colour : "") + '">' + m[5] + "</span> - " + m[6] + "</h4>";
				
				line = line.replace(m[0] + " ", "");
			}
			
			html += line + "\\r\\n";
		});
		
		logElem.innerHTML = html;
	}).catch(err => {
		logElem.innerHTML = err.message;
	});
}
		
document.getElementById("__logSwitch").addEventListener("change", function (e) {
	streamLog(e.target.value);
});

streamLog("$currentLog");
JS;

		Craft::$app->view->registerCss($css);
		Craft::$app->view->registerJs($js);

		return Craft::$app->view->renderTemplate(
			'logs/view',
			compact('currentLog', 'logFiles')
		);
	}

}
