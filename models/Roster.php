<?php

class Roster
{
    public function getPlayers($where)
    {
        $sql = "
            SELECT roster.*
            FROM roster
            WHERE $where
        ";

        $data = query($sql);
        
        return $data;
    }
}