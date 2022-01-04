<?php

class Player extends Data{
    public function __construct(string $tagPlayer){
        parent::__construct();
        $this->tagPlayer = $tagPlayer;
    }

    public function getPlayerInfo(){
        return parent::getData("players/" . urlencode($this->tagPlayer));
    }
}