-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- ClansByGrivin implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

-- CREATE TABLE IF NOT EXISTS `card` (
--   `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `card_type` varchar(16) NOT NULL,
--   `card_type_arg` int(11) NOT NULL,
--   `card_location` varchar(16) NOT NULL,
--   `card_location_arg` int(11) NOT NULL,
--   PRIMARY KEY (`card_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Example 2: add a custom field to the standard "player" table
-- ALTER TABLE `player` ADD `player_my_custom_field` INT UNSIGNED NOT NULL DEFAULT '0';


--
--  Store hidden color played
--
--  secret_color : the color assigned to the player 1..5 (yellow, green, ...)
--
ALTER TABLE player
    ADD player_secret_color_id SMALLINT NULL;


--
-- HUT
--
-- color_id : 1..5 (yellow, green, ...)
-- territory_id : id of territory in material.inc,
--                NULL if the village has been removed from the map
--
CREATE TABLE IF NOT EXISTS hut
(
    hut_id       SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    color_id     SMALLINT          NOT NULL,
    territory_id TINYINT UNSIGNED  NULL,
    PRIMARY KEY (hut_id)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1;


--
-- VILLAGE
--
-- player_id : who created the current village
-- resolved : True if the bonus token has been attributed, huts destroyed if needed, village destroyed if needed, ...
-- removed : To keep track of removed villages on unfavorable epoch
-- epoch_id : For later statistics... (if any)
-- token_id : village token, can it can be in different order of village_id on multiple village creation
--
--
CREATE TABLE IF NOT EXISTS village
(
    village_id   SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    player_id    SMALLINT          NOT NULL,
    territory_id TINYINT UNSIGNED  NULL,
    epoch_id     TINYINT UNSIGNED  NULL,
    token_id     TINYINT UNSIGNED  NULL,
    resolved     BOOL              NOT NULL DEFAULT FALSE,
    destroyed    BOOL              NOT NULL DEFAULT FALSE,
    PRIMARY KEY (village_id)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1;


--
-- SCORE
--
CREATE TABLE IF NOT EXISTS score
(
    color_id SMALLINT NOT NULL,
    score    SMALLINT NOT NULL DEFAULT 0,
    PRIMARY KEY (color_id)
) ENGINE = InnoDB;

