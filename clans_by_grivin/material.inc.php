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

if (! defined('_FOREST')){
    define('_FOREST', 1);
    define('_MOUNTAIN', 2);
    define('_STEPPE', 3);
    define('_GRASSLAND', 4);
}


/*
 * colors code
 * (RVB)
 */
$this->colors = array(
    1 => array(
        "name" => "yellow",
        "color" => "00FFFF",
    ),
    2 => array(
        "name" => "red",
        "color" => "FF0000",
    ),
    3 => array(
        "name" => "green",
        "color" => "00FF00",
    ),
    4 => array(
        "name" => "blue",
        "color" => "0000FF",
    ),
    5 => array(
        "name" => "black",
        "color" => "000000",
    ),
);


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
    ),

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
        "land_type" => _STEPPE,
        "neighbor" => array(2, 3, 4),
        "region" => 1,
        "x" => 50,
        "y" => 50,
    ),
    2 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(3, 5, 6),
        "region" => 1,
        "x" => 202,
        "y" => 42,
    ),
    3 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(4, 5),
        "region" => 1,
        "x" => 117,
        "y" => 102,
    ),
    4 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(5, 32),
        "region" => 1,
        "x" => 53,
        "y" => 186,
    ),
    5 => array(
        "land_type" => _FOREST,
        "neighbor" => array(6, 29, 32),
        "region" => 1,
        "x" => 174,
        "y" => 178,
    ),

    6 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(7, 10, 29),
        "region" => 2,
        "x" => 305,
        "y" => 81,
    ),
    7 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(8, 10),
        "region" => 2,
        "x" => 441,
        "y" => 52,
    ),
    8 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(9, 10),
        "region" => 2,
        "x" => 576,
        "y" => 49,
    ),
    9 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(10, 11, 14, 22),
        "region" => 1,
        "x" => 700,
        "y" => 84,
    ),
    10 => array(
        "land_type" => _FOREST,
        "neighbor" => array(24, 27),
        "region" => 2,
        "x" => 495,
        "y" => 136,
    ),

    11 => array(
        "land_type" => _FOREST,
        "neighbor" => array(12, 13, 14),
        "region" => 3,
        "x" => 785,
        "y" => 37,
    ),
    12 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(13, 15),
        "region" => 3,
        "x" => 957,
        "y" => 42,
    ),
    13 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(14, 15),
        "region" => 3,
        "x" => 884,
        "y" => 94,
    ),
    14 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(15, 16, 22),
        "region" => 3,
        "x" => 825,
        "y" => 157,
    ),
    15 => array(
        "land_type" => _FOREST,
        "neighbor" => array(16),
        "region" => 1,
        "x" => 995,
        "y" => 172,
    ),

    16 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(17, 20),
        "region" => 4,
        "x" => 949,
        "y" => 266,
    ),
    17 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(18, 20),
        "region" => 4,
        "x" => 961,
        "y" => 371,
    ),
    18 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(19, 20),
        "region" => 4,
        "x" => 964,
        "y" => 452,
    ),
    19 => array(
        "land_type" => _FOREST,
        "neighbor" => array(20, 45, 46, 47),
        "region" => 4,
        "x" => 896,
        "y" => 518,
    ),
    20 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(21, 44),
        "region" => 4,
        "x" => 857,
        "y" => 386,
    ),

    21 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(22, 23, 44),
        "region" => 5,
        "x" => 751,
        "y" => 344,
    ),
    22 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(23, 24),
        "region" => 5,
        "x" => 754,
        "y" => 205,
    ),
    23 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(24, 25),
        "region" => 5,
        "x" => 669,
        "y" => 290,
    ),
    24 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(25, 27),
        "region" => 5,
        "x" => 585,
        "y" => 223,
    ),
    25 => array(
        "land_type" => _FOREST,
        "neighbor" => array(26, 38, 42),
        "region" => 5,
        "x" => 556,
        "y" => 323,
    ),


    26 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(27, 28, 30, 38),
        "region" => 6,
        "x" => 425,
        "y" => 342,
    ),
    27 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(28),
        "region" => 6,
        "x" => 463,
        "y" => 222,
    ),
    28 => array(
        "land_type" => _FOREST,
        "neighbor" => array(29, 30),
        "region" => 6,
        "x" => 363,
        "y" => 292,
    ),
    29 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(30, 32),
        "region" => 6,
        "x" => 272,
        "y" => 223,
    ),
    30 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(31, 37),
        "region" => 6,
        "x" => 288,
        "y" => 349,
    ),


    31 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(32, 33, 34, 35),
        "region" => 7,
        "x" => 171,
        "y" => 382,
    ),
    32 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(33),
        "region" => 7,
        "x" => 86,
        "y" => 271,
    ),
    33 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(34),
        "region" => 7,
        "x" => 60,
        "y" => 346,
    ),
    34 => array(
        "land_type" => _FOREST,
        "neighbor" => array(35),
        "region" => 7,
        "x" => 31,
        "y" => 440,
    ),
    35 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(36, 59, 60),
        "region" => 7,
        "x" => 72,
        "y" => 507,
    ),


    36 => array(
        "land_type" => _FOREST,
        "neighbor" => array(37, 39, 40, 55, 60),
        "region" => 8,
        "x" => 240,
        "y" => 570,
    ),
    37 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(38, 39),
        "region" => 8,
        "x" => 298,
        "y" => 478,
    ),
    38 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(39, 40, 42),
        "region" => 8,
        "x" => 429,
        "y" => 441,
    ),
    39 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(40),
        "region" => 8,
        "x" => 344,
        "y" => 541,
    ),
    40 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(41, 53),
        "region" => 8,
        "x" => 438,
        "y" => 570,
    ),


    41 => array(
        "land_type" => _FOREST,
        "neighbor" => array(42, 43, 45, 53),
        "region" => 9,
        "x" => 551,
        "y" => 538,
    ),
    42 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(43, 44),
        "region" => 9,
        "x" => 569,
        "y" => 441,
    ),
    43 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(44, 45),
        "region" => 9,
        "x" => 651,
        "y" => 497,
    ),
    44 => array(
        "land_type" => _FOREST,
        "neighbor" => array(45),
        "region" => 9,
        "x" => 737,
        "y" => 441,
    ),
    45 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(46, 51),
        "region" => 9,
        "x" => 706,
        "y" => 543,
    ),


    46 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(47, 49, 50, 51),
        "region" => 10,
        "x" => 780,
        "y" => 632,
    ),
    47 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(48, 49),
        "region" => 10,
        "x" => 936,
        "y" => 604,
    ),
    48 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(49, 50),
        "region" => 10,
        "x" => 965,
        "y" => 733,
    ),
    49 => array(
        "land_type" => _FOREST,
        "neighbor" => array(50),
        "region" => 10,
        "x" => 881,
        "y" => 691,
    ),
    50 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(51),
        "region" => 10,
        "x" => 798,
        "y" => 747,
    ),


    51 => array(
        "land_type" => _FOREST,
        "neighbor" => array(52, 53),
        "region" => 11,
        "x" => 678,
        "y" => 688,
    ),
    52 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(53, 54),
        "region" => 11,
        "x" => 586,
        "y" => 765,
    ),
    53 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(54, 55),
        "region" => 11,
        "x" => 484,
        "y" => 676,
    ),
    54 => array(
        "land_type" => _FOREST,
        "neighbor" => array(55),
        "region" => 11,
        "x" => 425,
        "y" => 766,
    ),
    55 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(56, 60),
        "region" => 11,
        "x" => 296,
        "y" => 714,
    ),


    56 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(57, 58, 60),
        "region" => 12,
        "x" => 187,
        "y" => 744,
    ),
    57 => array(
        "land_type" => _FOREST,
        "neighbor" => array(58, 59),
        "region" => 12,
        "x" => 46,
        "y" => 755,
    ),
    58 => array(
        "land_type" => _STEPPE,
        "neighbor" => array(59, 60),
        "region" => 12,
        "x" => 111,
        "y" => 676,
    ),
    59 => array(
        "land_type" => _GRASSLAND,
        "neighbor" => array(60),
        "region" => 12,
        "x" => 39,
        "y" => 619,
    ),
    60 => array(
        "land_type" => _MOUNTAIN,
        "neighbor" => array(),
        "region" => 12,
        "x" => 168,
        "y" => 597,
    ),


//    x => array(
//        "land_type" => _x,
//        "neighbor" => array(),
//        "region" => X,
//        "x" => 72,
//        "y" => 507,
//    ),
);



/*
 * back-propagate links between territories
 */

foreach ($this->territories as $to_territory_id => $territory) {
    foreach ($territory['neighbor'] as $from_territory_id) {
//        if (array_key_exists($from_territory_id, $this->territories)) {
        array_push($this->territories[$from_territory_id]['neighbor'], $to_territory_id);
//        }
    }
}

//dump all territories...
//print("\n\n\nT=");
//var_export($this->territories);
//die("debug");



