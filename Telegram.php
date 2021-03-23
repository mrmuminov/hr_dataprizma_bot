<?php

class Telegram {

    public $botToken = null;
    public $update = null;

    public function __construct($botToken)
    {
        $this->botToken = $botToken;
        $this->update = json_decode(file_get_contents("php://input"));
    }

    public function onMessage($text, $function) {
        if ($this->update->message->text == $text || $text === -1) {
            return $function($this, $this->update->message->from, $this->update->message->text);
        }
        return false;
    }


    public function sendMessage($options)
    {
        $this->request("sendMessage", $options);
    }

    public function request($method, $options, $botToken = null){
        $botToken = is_null($botToken) ? $this->botToken : $botToken;
        $url = "https://api.telegram.org/bot" . $botToken . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $options);
        $res = curl_exec($ch);
        if (!empty(curl_error($ch))) {
            $result = curl_error($ch);
        }
        $result = json_decode($res, true);

        return $result;
    }
}