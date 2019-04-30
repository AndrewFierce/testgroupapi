<?php 
require_once __DIR__.'/../vendor/autoload.php'; 

//Подключение класса для работы с mysql
include_once('ClassVKApi.php');

use \VK\Client\VKApiClient;
use \VK\OAuth\VKOAuth;
use \VK\OAuth\VKOAuthDisplay;
use \VK\OAuth\Scopes\VKOAuthUserScope;
use \VK\OAuth\VKOAuthResponseType;

use VK\CallbackApi\Server\VKCallbackApiServerHandler; 
use VK\CallbackApi\VKCallbackApiHandler;
use VK\CallbackApi\LongPoll\VKCallbackApiLongPollExecutor;

//Функция для запуска сессии
session_start();


// Имя клиента
if (empty($_POST["date"])) {
    $errorMSG = "Поле с именем должно быть заполнено!";
} else {
    $date = $_POST["date"];
}

//проверяет соответствие коду CAPTCHA
if ($_SESSION["code"] != $_POST["captcha"]) {
  //сообщаем строку true, если код соответствует
  $errorMSG .= "Код с картинки введен не верно! Попробуйте еще раз.";
}

//Работа в API VK
$vk = new VKApiClient(); 
$access_token = 'string_of_access_tocken';

$vkObj = new VKApi($vk, $access_token);
$timeAnswer = $vkObj->average_response_time($_POST["date"]);
if (count($timeAnswer) > 0) {
	for ($i = 0; $i < count($timeAnswer); $i++){
		$n = $i + 1;
		echo "<br>Диалог №$n <br>";
		$vkObj->get_messages($timeAnswer)[$i];
	}
}

?>
