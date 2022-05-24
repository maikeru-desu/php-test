<?php

class PlayerTotal
{
    public function getPlayerStats($where)
    {
        $sql = "
            SELECT roster.name, player_totals.*
            FROM player_totals
                INNER JOIN roster ON (roster.id = player_totals.player_id)
            WHERE $where";
        $data = query($sql) ?: [];

        
        return $data;
    }
}