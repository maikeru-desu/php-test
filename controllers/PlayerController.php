<?php
use Illuminate\Support;  // https://laravel.com/docs/5.8/collections - provides the collect methods & collections class
use LSS\Array2Xml;
require_once('traits/Formatter.php');
require_once('services/PlayerService.php');
require_once('enums/Type.php');

class PlayerController {

    use Formatter;

    private $playerService;

    public function __construct($args) {
        $this->args = $args;
        $this->playerService = new PlayerService();
    }

    public function export($type, $format) {
        $data = [];
        $searchArgs = ['player', 'playerId', 'team', 'position', 'country'];
        switch ($type) {
            case Type::PlayerStats:
                $search = $this->args->filter(function($value, $key) use ($searchArgs) {
                    return in_array($key, $searchArgs);
                });
                $data = $this->playerService->getPlayerStats($search);
                break;
            case Type::Player:
                $search = $this->args->filter(function($value, $key) use ($searchArgs) {
                    return in_array($key, $searchArgs);
                });
                $data = $this->playerService->getPlayers($search);
                break;
        }

        if (!$data) {
            exit("Error: No data found!");
        }
        return $this->format($data, $format);
    }
}