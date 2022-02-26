<?php

namespace api\clan;

use api\data\Data;

class Clan extends Data{
    public function __construct($tag=null){
        parent::__construct();
        $this->tagClan = ($tag != null) ? $tag : $this->tagClan;
    }

    public function getClanInfo():array{
        return parent::getData("clans/".urlencode($this->tagClan));
    }

    public function getMembers():array{
        return parent::getData("clans/".urlencode($this->tagClan)."/members");
    }

    public function getWarLog():array{
        return parent::getData("clans/".urlencode($this->tagClan)."/warlog");
    }

    public function getCurrentWar():array{
        return parent::getData("clans/".urlencode($this->tagClan)."/currentwar");
    }

    public function getCurrentWarLeagueGroup():array{
        return parent::getData("clans/".urlencode($this->tagClan)."/currentwar/leaguegroup");
    }

    public function getCurrentWarLeague(string $tag):array{
        return parent::getData('clanwarleagues/wars/' . urlencode($tag));
    }
}

