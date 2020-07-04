<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ClansByGrivin implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * clansbygrivin.action.php
 *
 * ClansByGrivin main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/clansbygrivin/clansbygrivin/myAction.html", ...)
 *
 */


class action_clansbygrivin extends APP_GameAction
{
    // Constructor: please do not modify
    public function __default()
    {
        if (self::isArg('notifwindow')) {
            $this->view = "common_notifwindow";
            $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
        } else {
            $this->view = "clansbygrivin_clansbygrivin";
            self::trace("Complete reinitialization of board game");
        }
    }


    /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */

    public function playHuts()
    {
        self::setAjaxMode();

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $src_territory_id = self::getArg("src_territory_id", AT_posint, true);
        $dst_territory_id = self::getArg("dst_territory_id", AT_posint, true);

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->moveHuts($src_territory_id, $dst_territory_id);

        self::ajaxResponse();
    }

}
  

