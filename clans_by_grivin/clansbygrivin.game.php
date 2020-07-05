<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ClansByGrivin implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * clansbygrivin.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');


class ClansByGrivin extends Table
{
    function __construct()
    {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();

        self::initGameStateLabels(array(
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ));
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "clansbygrivin";
    }

    function traceExportVar($varToExport, $varName, $functionStr)
    {
        self::trace("###### $functionStr(): $varName is " . var_export($varToExport, true) . " ");
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        $gameinfos = self::getGameinfos();

        // Create players

        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $public_color = "FFFFFF"; //white for all

        // Prepare a random secret color for each player
        $secret_colors = $this->shuffleColors();

        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, player_secret_color_id) VALUES ";
        $values = array();
        foreach ($players as $player_id => $player) {
            // Pick a random color secret color for the player
            $secret_color = array_shift($secret_colors);
            $values[] = sprintf("('%s','%s','%s','%s','%s', %d)",
                $player_id,
                $public_color,
                $player['player_canal'],
                addslashes($player['player_name']),
                addslashes($player['player_avatar']),
                $secret_color
            );
        }
        $sql .= implode($values, ',');
        self::DbQuery($sql);
        // no reattribution : self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();

        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );

        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        /*
         * setup the initial game situation here
         */

        // for all region, shuffle 5 colors, create some huts and assign it to 5 territories...
        $this->setupHuts();

        // TODO: reset seasons, put all village tokens

        // reset scores for all colors
        $this->setupScores();


        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
     * shuffleColors
     *
     * return an array of shuffled colors
     * cannot use $this->game as it is not yet created.
     *
     */
    protected function shuffleColors()
    {
        $shuffled_colors = range(1, 5);
        shuffle($shuffled_colors);
        return $shuffled_colors;
    }


    /*
     * setupHuts()
     *
     * There is 5 territories for each region
     * Set one hut of each color randomly
     *
     */
    protected function setupHuts()
    {
        $huts = array();
//        $this->traceExportVar("test", "test", "setupHuts");
//        $this->traceExportVar($this->territories[1], "territories", "setupHuts");

        $sql = "INSERT INTO hut(color_id, territory_id) VALUES";
        $values = array();

        // parse all regions
        foreach (range(0, 11) as $region) {
            // pick random colors
            $colors = $this->shuffleColors();
            foreach (range(0, 4) as $hut) {
                // place a hut
                $territory_id = $region * 5 + $hut + 1;
                $color = array_shift($colors);
                $values[] = sprintf("(%d, %d)", $color, $territory_id);
            }
        }

        // to the database and beyond !
        $sql .= implode($values, ',');
        self::DbQuery($sql);
    }


    /*
     * setupScores()
     */
    private function setupScores()
    {
        $sql = "INSERT INTO score (color_id, score) VALUES ";
        foreach ($this->colors as $color_id => $color) {
            $values[] = sprintf("(%d, 0)", $color_id);
        }
        $sql .= implode($values, ',');
        self::DbQuery($sql);
    }


    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();

        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!

        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb($sql);

        // Get all huts position, if they have not been removed
        $sql = "SELECT hut_id, color_id, territory_id FROM hut WHERE territory_id IS NOT NULL ORDER BY territory_id, color_id";
        $result['board'] = self::getObjectListFromDB($sql);
        $result['scores'] = $this->getScores();
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression  #15

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

    /*
     * update map with current huts count
     */
    function updateTerritoriesHutCount()
    {
        foreach ($this->territories as $id => &$t)
            $t['huts'] = "0";

        $sql = "SELECT territory_id, count(hut_id) AS huts FROM hut GROUP BY territory_id ORDER BY territory_id";
        $qry_huts = self::getObjectListFromDB($sql);

        // update the map
        foreach ($qry_huts as &$h) {
            $id = $h['territory_id'];
            $t = &$this->territories[$id];
            $t['huts'] = $h['huts'];
        }
    }


    /*
     * insert a list of a new village, assign them to the current player and flag them unresolved yet.
     */
    function insertVillages($new_villages)
    {
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return information visible by this player !!
        $sql = "INSERT INTO village(player_id, territory_id) VALUES";
        $values = array();
        foreach ($new_villages as $territory_id) {
            $values[] = sprintf("(%d, %d)", $current_player_id, $territory_id);
            $sql .= implode($values, ',');
            self::DbQuery($sql);
        }
    }


    /*
     * mark a village as done
     *
     * return id of token_id
     */
    function updateVillage($territory_id, $destruction, $epoch_id)
    {
        // calc the token_id,
        $sql = "SELECT COUNT(*) + 1 as new_token_id FROM village WHERE resolved = TRUE";
        $village_token_id = self::getUniqueValueFromDB($sql);

        $current_player_id = self::getCurrentPlayerId();
        $sql =
            "UPDATE village " .
            "SET resolved = TRUE " .
            ",   epoch_id = $epoch_id " .
            ",   token_id = $village_token_id ";
        if ($destruction)
            $sql .= ", destroyed = TRUE ";
        $sql .= "WHERE territory_id = $territory_id";
        self::DbQuery($sql);
        return $village_token_id;
    }


    /*
     * getVillageCount
     *
     * return count of village, already resolved only, destroyed or not
     * can be use asalready  distributed village token count
     *
     */
    function getVillageCount()
    {
        $sql = "SELECT COUNT(*) FROM village WHERE resolved = TRUE";
        return self::getUniqueValueFromDB($sql);
    }


    // Get the list of possible moves (x => y => true)
    function getSourceTerritories()
    {
        $result = array();
        $this->updateTerritoriesHutCount();
        foreach ($this->territories as $territory) {
            if ($territory['huts'] > 0)
                $result[] .= $territory['territory_id'];
        }
        return $result;
    }


    /*
     * list all neighbor of a territory
     */
    function getNeighborTerritories($territory_id)
    {
        return $this->territories[$territory_id]['neighbor'];
    }


    /*
     * list all possible moves:
     * - list all sources
     * - and add all possible destinations
     */
    function getPossibleMoves()
    {
        $moves = array();
        $this->updateTerritoriesHutCount();

        foreach ($this->territories as $src_id => $territory) {
            $src_huts = $territory['huts'];

            if ($src_huts > 0) {
                $destinations = array();
                foreach ($territory['neighbor'] as $dst_id) {
                    $dst_huts = $this->territories[$dst_id]['huts'];

                    // Allow only to move into non empty territory
                    if ($dst_huts == 0)
                        continue;

                    // Big village cannot move, but if they are moving to a bigger
                    if (($src_huts >= 7) && ($src_huts > $dst_huts))
                        continue;

                    array_push($destinations, $dst_id);
                }

                // add the possible move only if it has some destination
                if (count($destinations) > 0)
                    $moves[$src_id] = $destinations;
            }
        }
        return $moves;
    }

    /*
     * getVillages()
     * - list pending village creation if there is more than one on the same turn
     */
    function getVillages()
    {
        $villages = array();
        //TODO: getVillages not implemented #6
        return $villages;
    }


    /*
     *  check if a move create a village (or more)
     *
     *   to list all new villages, for all source neighbor, check if they still have other neighbor
     *
     *   return array of territory_id
     */
    function listNewVillage($src_territory_id)
    {
        $villages = array();
        foreach ($this->getNeighborTerritories($src_territory_id) as $neighbor_territory_id) {
            if ($this->territories[$neighbor_territory_id]['huts'] > 0) {
                $has_neighbor = False;
                foreach ($this->getNeighborTerritories($neighbor_territory_id) as $neighbor_neighbor_territory_id) {
                    if ($this->territories[$neighbor_neighbor_territory_id]['huts'] > 0) {
                        $has_neighbor = True;
                    }
                }
                if (!$has_neighbor) {
                    array_push($villages, $neighbor_territory_id);
                }
            }
        }
        return $villages;
    }


    /*
     * listSingleHuts()
     * return the list of single hut of this village, if any
     */
    function listSingleHuts($territory_id)
    {
        $huts = array();
        $sql = "SELECT hut_id " .
            "FROM hut " .
            "WHERE territory_id = $territory_id " .
            "AND color_id in ( " .
            "  SELECT color_id " .
            "  FROM hut " .
            "  WHERE territory_id = $territory_id " .
            "  GROUP BY color_id " .
            "  HAVING COUNT(*) = 1 " .
            ")";
        $qry = self::getObjectListFromDB($sql);
        foreach ($qry as &$h) {
            $huts[] = $h['hut_id'];
        }
        return $huts;
    }

    /*
     * hasAllColors()
     * return True if all colors are present in the same territory, do prepare the dispute...
     */
    private function hasAllColors($territory_id)
    {
//        echo ("hasAllColors($territory_id)");
        $sql = "SELECT COUNT(DISTINCT color_id) AS colors " .
            "FROM hut " .
            "WHERE territory_id = $territory_id ";

        $qry = self::getUniqueValueFromDB($sql);
        return $qry == 5;
    }


    /*
     * removeHut()
     */
    function removeHut($hut_id)
    {
        $sql = "DELETE FROM hut WHERE hut_id = $hut_id";
        self::DbQuery($sql);
    }


    /*
     * apply village destruction (due to epoch/region malus)
     */
    function updateKillVillage($territory_id)
    {
        $sql = "UPDATE hut SET territory_id = NULL WHERE territory_id = $territory_id";
        self::DbQuery($sql);
    }


    /*
     *  apply huts move
     *  return the list of updated huts
     */
    function updateMoveHuts($src_territory_id, $dst_territory_id)
    {
        // save the list of update huts
        $sql = "SELECT * FROM hut WHERE territory_id = $src_territory_id";
        $huts = self::getObjectListFromDB($sql);
        // apply the move to db
        $sql = "UPDATE hut SET territory_id = $dst_territory_id WHERE territory_id = $src_territory_id";
        self::DbQuery($sql);

        // reload the territories from DB... (could be calculated...)
        $this->updateTerritoriesHutCount();
        return $huts;
    }


    /*
     *
     * updateScores()
     *
     * compute the score of the current territory and update db
     *
     * all present colors scores the count of hut in the new village + eqpoch bonus
     *
     */
    private function updateScores($territory_id, $bonus)
    {
        $village_score = $this->territories[$territory_id]['huts'] + $bonus;
        $sql =
            "UPDATE score s " .
            "SET s.score = s.score + $village_score " .
            "WHERE s.color_id IN ( " .
            "  SELECT DISTINCT color_id " .
            "  FROM hut " .
            "  WHERE territory_id = $territory_id " .
            ")";
        self::DbQuery($sql);
        return $village_score;
    }


    /*
     * getScores
     */
    private function getScores()
    {
        $sql = "SELECT * FROM score ORDER BY color_id";
        $qry = self::getObjectListFromDB($sql);
        return $qry;
    }



//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in clansbygrivin.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */


    /*
     * moveHuts
     */
    function moveHuts($src_territory_id, $dst_territory_id)
    {

        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        //TODO: self::checkAction( 'moveHuts' ); #16
        //TODO: check movement is possible...

        $huts = $this->updateMoveHuts($src_territory_id, $dst_territory_id);

        $player_id = self::getActivePlayerId();

        // Add your game logic to play a card there

        // Notify all players one or more huts have been moved
        self::notifyAllPlayers("moveHuts", clienttranslate('${player_name} plays '), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'src_territory_id' => $src_territory_id,
            'dst_territory_id' => $dst_territory_id,
            'huts' => $huts,
        ));

        $new_villages = $this->listNewVillage($src_territory_id);
//        $new_villages = [$dst_territory_id]; //!!!TEMP:force a village. ..

        if (count($new_villages) > 1) {
            // There is more than one village, a decision should be taken
            // TODO:can ignore village selection for creation if there is no impact from epoch bonus.

            //!!!TEMP : make an arbitrary selection
            foreach ($new_villages as $territory_id) {
                $this->makeVillage($territory_id);
            }

            // TODO:let the user select the village creation order #6
            // $this->gamestate->nextState('selectVillage');
            // return;
        } elseif (count($new_villages) == 1) {
            // There is only one new village, can be created directly *
            // Before moving to next player...
            $territory_id = $new_villages[0];
            $this->makeVillage($territory_id);
        }

        // Then, go to the next state
        $this->gamestate->nextState('nextPlayer');
    }


    /*
     * makeVillage()
     */
    function makeVillage($territory_id)
    {
        $player_id = self::getActivePlayerId();

        //TODO: self::checkAction( 'makeVillage' ); #16
        //TODO: check movement is possible...

        // Village dispute if there is all color present in the same territory:
        //TODO:rule to check: what if we create a village with 5 huts of 5 colors : destruction or nothing ? #17
        if ($this->hasAllColors($territory_id)) {
            $single_huts = $this->listSingleHuts($territory_id);
            if (count($single_huts) > 0) {
                self::notifyAllPlayers("villageDispute", clienttranslate('There is a village dispute !'), array(
                    //                'player_id' => $player_id,
                    //                'player_name' => self::getActivePlayerName(),
                    'src_territory_id' => $territory_id,
                    'huts' => $single_huts,
                ));
                foreach ($single_huts as $hut_id) {
                    $this->removeHut($hut_id);
                }
            }
            // need to refresh in memory-map
            $this->updateTerritoriesHutCount();
        }

        //TODO: manage season (bonus or destruction) #
        $bonus = 0; // no bonus
        $destruction = False; // no multiplier

        //TODO: notify bonus token


        if ($destruction) {
            //TODO: notify village destruction
        } else {
            // Compute new score
            $village_score = $this->updateScores($territory_id, $bonus);
            $scores = $this->getScores();
            self::notifyAllPlayers("villageBuilt", clienttranslate('${player_name} build a village (${village_score} points) at ${src_territory_id}'), array(
                'player_id' => $player_id,
                'player_name' => self::getActivePlayerName(),
                'src_territory_id' => $territory_id,
                'village_score' => $village_score,
                'scores' => $scores,
            ));
            self::notifyAllPlayers("updateScore", '', array(
                'scores' => $scores,
            ));
        }

    }


//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

    function argPlayerTurn()
    {
        return array(
            'getPossibleMoves' => self::getPossibleMoves()
        );
    }

    function argSelectVillage()
    {
        return array(
            'getVillages' => self::getVillages()
        );
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */


    function stNextPlayer()
    {
        // Active next player
        $player_id = self::activeNextPlayer();

        // TODO : check if there is still some epoch to play and some move possible... #4

        $remainingVillages = 12 -  $this->getVillageCount();

        if ($remainingVillages <= 0) {

            $this->gamestate->nextState('endGame');
            return;
        }

        // This player can play. Give him some extra time
        self::giveExtraTime($player_id);
        $this->gamestate->nextState('playerTurn');

    }

    function stGameEnd()
    {
        // TODO: reveal secret colors #5
        // TODO: attribute scores #5
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn($state, $active_player)
    {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: " . $statename);
    }

///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }


}
