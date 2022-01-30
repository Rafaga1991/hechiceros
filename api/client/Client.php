<?php

class Client{

    public static function getClan($clanTag=null):Clan{

        return new Clan($clanTag);

    }

    public static function getPlayer(string $tag):Player{

        return new Player($tag);

    }

}