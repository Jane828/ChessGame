<?php

define('GAME_SERVER_PORT', getenv("GAME_SERVER_PORT"));

class Config{
    const GameServer_Port = GAME_SERVER_PORT; // "20061";
    const GameServer_Address =  "127.0.0.1";
    const Log_Dir = __DIR__ . '/../Logs/';

}
