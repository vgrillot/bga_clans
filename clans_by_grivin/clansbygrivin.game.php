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

        // TODO: reset scores


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
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

    /*
     * get all active territories (with huts)
     */
    function getTerritories()
    {
        $sql = "SELECT territory_id, count(hut_id) AS huts FROM hut GROUP BY territory_id ORDER BY territory_id";
        $territories = self::getObjectListFromDB($sql);
//        var_dump($territories);
        return $territories;
    }

    // Get the list of possible moves (x => y => true)
    function getSourceTerritories()
    {
        $result = array();
        $territories = self::getTerritories();
        foreach ($territories as $i => $territory) {
            if ($territory['huts'] > 0) {
                $result[] .= $territory['territory_id'];
            }
        }
        return $result;
    }


    /*
     * list all neighbor of a territory
     */
    function getNeihborTerritories($territory_id)
    {
        return $this->territories[$territory_id]['neighbor'];
    }


    /*
     * list all possible target territories for one source territory
     */
    function getDestinationTerritories($src_territory_id)
    {
        // take all neighbor
        $neighbor = $this->getNeihborTerritories($src_territory_id);
        //TODO: check if they are not empty...
        $result = array();
    }

    /*
     * list all possible moves:
     * - list all sources
     * - and add all possible destinations
     */
    function getPossibleMoves()
    {
        $moves = array();
        $territories = $this->getTerritories();
        foreach ($territories as $i => $territory) {
            if ($territory['huts'] > 0) {
                $src_id = $territory['territory_id'];
//                echo("<br><br>");
//                var_dump($src_id);
                $destinations = array();
                foreach ($this->territories[$src_id]['neighbor'] as $dst_id) {
//                    echo("<br>dst_id=$dst_id");
                    # TODO : check village size...
                    array_push($destinations, $dst_id);
                }
//                echo("<br>destination=");
//                var_dump($destinations);
//                $destinations = "aaa";
                $moves[$src_id] = $destinations;
//                array_push($moves, [$src_id => $destinations]);
            }
        }
//        echo("<br><br><br><br>moves=");
//        var_dump($moves);
//        die();
        return $moves;
    }


    /*
     *  check if a move create a village (or more)
     *
     *   to list all new villages, for all source neighbor, check if they stil have other neighbor
     */
    function listNewVillage($territories, $src_territory_id, $dst_territory_id)
    {
        $villages = array();
        foreach ($this->getNeihborTerritories($src_territory_id) as $neihbor_territory_id) {
            $has_neighbor = False;
            foreach ($this->getNeihborTerritories($neihbor_territory_id) as $neihbor_neihbor_territory_id) {
                if (key_exists($neihbor_neihbor_territory_id, $territories)) {
                    $has_neighbor = True;
                }
            }
            if (!$has_neighbor) {
                array_push($villages, $neihbor_territory_id);
            }
        }
        return $villages;
    }


    /*
     * apply village destruction (due to epoch/region malus)
     */
    function updateKillVillage($territory_id)
    {
        $sql = "UPDATE hut SET territory_id = NULL WHERE territory_id = $territory_id";
        self::DbQuery( $sql );
    }

    /*
     * apply village construction
     */
    function updateVillageConstruction($territory_id) {

    }


    /*
     *  apply huts move
     */
    function updateMoveHuts($src_territory_id, $dst_territory_id)
    {
        $sql = "UPDATE hut SET territory_id = $dst_territory_id WHERE territory_id = $src_territory_id";
        self::DbQuery( $sql );
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
        //TODO: self::checkAction( 'moveHuts' );
        //TODO: check movement is possible...
        //TODO: listNewVillage

        $this->updateMoveHuts($src_territory_id, $dst_territory_id);

        $player_id = self::getActivePlayerId();

        // Add your game logic to play a card there

        // Notify all players about the card played
        self::notifyAllPlayers("moveHuts", clienttranslate('${player_name} plays '), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'src_territory_id' => $src_territory_id,
            'dst_territory_id' => $dst_territory_id,
        ));

        self::notifyAllPlayers("moveHuts", clienttranslate('${player_name} plays '), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'src_territory_id' => $src_territory_id,
            'dst_territory_id' => $dst_territory_id,
        ));

        // Then, go to the next state
        $this->gamestate->nextState( 'computeVillage' );

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
