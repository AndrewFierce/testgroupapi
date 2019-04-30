<?php 

/*Подключаем классы и методы для работы с API*/
require_once __DIR__.'/../vendor/autoload.php'; 


use \VK\Client\VKApiClient;
use \VK\OAuth\VKOAuth;
use \VK\OAuth\VKOAuthDisplay;
use \VK\OAuth\Scopes\VKOAuthUserScope;
use \VK\OAuth\VKOAuthResponseType;

use VK\CallbackApi\Server\VKCallbackApiServerHandler; 
use VK\CallbackApi\VKCallbackApiHandler;
use VK\CallbackApi\LongPoll\VKCallbackApiLongPollExecutor;

class VKApi {
	protected $vk;
	protected $access_token;

	public function __construct(VKApiClient $vk, $access_token) {
        $this->vk = $vk;
        $this->access_token = $access_token;
    }

    //Функция вывода статистики по диалогам
    public function average_response_time($date = 900) {
    	$allMessages = $this->vk->messages()->getConversations($this->access_token, array());
    	$data = array();
    	$averageSpeed = array();
    	$k = 0;
    	for ($i = 0; $i < $allMessages['count']; $i++) {
			$peer_id = $allMessages['items'][$i]['conversation']['peer']['id'];
			$messageWithUser = $this->vk->messages()->search($this->access_token, array( 
				'peer_id' => $peer_id,
				'date' => $date,
			));
			for($j = 0; $j < $messageWithUser['count']; $j++) {
				$idsender = $messageWithUser['items'][$j]['from_id'];
				if ($j < $messageWithUser['count'] - 1) {
					if ($idsender != $messageWithUser['items'][$j+1]['from_id'] && $idsender < 0) {
						$k++;
						$speed = $messageWithUser['items'][$j]['date'] - $messageWithUser['items'][$j+1]['date'];
						array_push($averageSpeed, $speed);
						if ($speed > 900) {
							array_push($data, $peer_id);
						}
					}
				}
			}
		}
		if (count($averageSpeed) > 0) {
			echo 'Среднее время ответа на сообщения: ' . gmdate('H:i:s', array_sum($averageSpeed)/$k) . '<br>';
			$counter = array_count_values($averageSpeed);
			arsort($counter);
			echo "Наиболее встречающееся время ответа: " . gmdate('H:i:s', array_slice(array_keys($counter), 0, 1, true)[0]) . "<br>";
		}
		else {
			echo 'Ответов на данную дату еще пока не было.';
		}
		return $data;
    }

    //Функция вывода диалогов
    public function get_messages ($peer_id) {
    	$messageWithUser = $this->vk->messages()->getHistory($this->access_token, array( 
			'peer_id' => $peer_id,
		));
		for($j=0; $j < $messageWithUser['count']; $j++) {
			$idsender = $messageWithUser['items'][$j]['from_id'];
			echo '<br>' . gmdate('Y-m-d H:i:s', $messageWithUser['items'][$j]['date']) . '<br>';
			if ($idsender < 0) {
				$senderName = $this->vk->groups()->getById($this->access_token, array( 
					'group_ids' => $idsender,
				));
				echo $senderName[0]['name'] . '<br>';
			}
			else {
				$senderName = $this->vk->users()->get($this->access_token, array( 
					'user_ids' => $idsender,
				));
				echo $senderName[0]['first_name'] . ' '  . $senderName[0]['last_name'] . '<br>';
			}
			echo $messageWithUser['items'][$j]['text'];
			echo "<br><br>";
		}
    }
}

?>