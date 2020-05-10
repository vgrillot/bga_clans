<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ClansByGrivin implementation : © Vincent Grillot <grivin@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * ClansByGrivin game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*
 * land_types:
 *
 */

$this->land_types = array(
    0 => array(
        "name" => "Any"
    ),
    1 => array(
        "name" => "Forrest"
    ),
    2 => array(
        "name" => "Mountains"
    ),
    3 => array(
        "name" => "Steppe"
    ),
    4 => array(
        "name" => "Grassland"
    )
);


/*
 *  Seasons
 *
 *  There is 5 seasons,
 *  nb_turn : duration in round,
 *  favorite_land_type : describe which land type has a bonus to build a village
 *  hostile_land_type : describe which land type will destroy the build village
 *  bonus : victory point attributed in case of village build in the favorite land type
 *
 */

$this->seasons = array(
    1 => array(
        "nb_turn" => 4,
        "favorite_land_type" => 1, //forrest
        "hostile_land_type" => 2, //mountain
        "bonus" => 1,
    ),
    2 => array(
        "nb_turn" => 3,
        "favorite_land_type" => 2, //mountain
        "hostile_land_type" => 4, //grassland
        "bonus" => 2,
    ),
    3 => array(
        "nb_turn" => 2,
        "favorite_land_type" => 3, //steppe
        "hostile_land_type" => 1, //forrest
        "bonus" => 3,
    ),
    4 => array(
        "nb_turn" => 2,
        "favorite_land_type" => 4, //grassland
        "hostile_land_type" => 3, //steppe
        "bonus" => 4,
    ),
    5 => array(
        "nb_turn" => 1,
        "favorite_land_type" => 0, //none
        "hostile_land_type" => 0, //none
        "bonus" => 5,
    )
);


/*
 * describe the map
 *
 * Neighbor : Describe only one-way link, other way will be computed.
 * Region : Is used for random distribution on board initialization.
 * x, y : center of the area on a 1200x800 map (0,0 top left)
 *
 */
$this->territories = array(
    1 => array(
        "land_type" => 3,
        "neighbor" => array(2, 3, 4),
        "region" => 1,
        "x" => 50,
        "y" => 50,
    ),
    2 => array(
        "land_type" => 2,
        "neighbor" => array(3, 5, 6),
        "region" => 1,
        "x" => 202,
        "y" => 42,
    ),
    3 => array(
        "land_type" => 4,
        "neighbor" => array(4, 5),
        "region" => 1,
        "x" => 117,
        "y" => 102,
    ),
    4 => array(
        "land_type" => 2,
        "neighbor" => array(5, 32),
        "region" => 1,
        "x" => 53,
        "y" => 186,
    ),
    5 => array(
        "land_type" => 1,
        "neighbor" => array(6, 29, 32),
        "region" => 1,
        "x" => 174,
        "y" => 178,
    ),

    6 => array(
        "land_type" => 4,
        "neighbor" => array(7, 10, 29),
        "region" => 2,
        "x" => 305,
        "y" => 81,
    ),
    7 => array(
        "land_type" => 3,
        "neighbor" => array(8, 10),
        "region" => 2,
        "x" => 441,
        "y" => 52,
    ),
    8 => array(
        "land_type" => 4,
        "neighbor" => array(9, 10),
        "region" => 2,
        "x" => 576,
        "y" => 49,
    ),
    9 => array(
        "land_type" => 2,
        "neighbor" => array(10, 11, 14, 22),
        "region" => 1,
        "x" => 700,
        "y" => 84,
    ),
    10 => array(
        "land_type" => 1,
        "neighbor" => array(24, 27),
        "region" => 2,
        "x" => 495,
        "y" => 136,
    )


    /*x => array(
        "land_type" => _x,
        "neighbor" => array(),
        "region" => 1,
    ),*/
);

