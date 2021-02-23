<?php


namespace Source\Models;


use CoffeeCode\DataLayer\DataLayer;

class Team extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ost78_team", [], 'team_id', false);
    }
}
