<?php

class Data{
    protected $tagClan;

    public function __construct(){
        $data = file_get_contents(__DIR__ . '/Data.json');
        $data = json_decode($data, true);
        $this->token = $data['key'];
        $this->url = $data['url'];
        $this->tagClan = $data['tagClan'];
//        $this->tagClan = (Session::getClanTag() != '') ? Session::getClanTag() : $data['tagClan'];
    }

    protected function getData(string $param):array{
        @header('Content-Type: text/html; charset=UTF-8');
        $this->url .= $param;

        $ch = curl_init($this->url);

        $headr = array();
        $headr[] = "Accept: application/json";
        $headr[] = "Authorization: Bearer " . $this->token;

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($ch);
        $data = json_decode($res, true);
        curl_close($ch);
        return $data;
    }
}