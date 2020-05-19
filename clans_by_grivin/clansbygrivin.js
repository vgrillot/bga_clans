/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * ClansByGrivin implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * clansbygrivin.js
 *
 * ClansByGrivin user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
        "dojo", "dojo/_base/declare",
        "ebg/core/gamegui",
        "ebg/counter"
    ],
    function (dojo, declare) {
        return declare("bgagame.clansbygrivin", ebg.core.gamegui, {
            constructor: function () {
                console.log('clansbygrivin constructor');

                // Here, you can init the global variables of your user interface
                // Example:
                // this.myGlobalValue = 0;

            },

            /*
                setup:

                This method must set up the game user interface according to current game situation specified
                in parameters.

                The method is called each time the game interface is displayed to a player, ie:
                _ when the game starts
                _ when a player refreshes the game page (F5)

                "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
            */

            setup: function (gamedatas) {
                console.log("Starting game setup");

                // Setting up player boards
                // for( var player_id in gamedatas.players )
                // {
                //     var player = gamedatas.players[player_id];
                //
                //     // TODO: Setting up players boards if needed
                // }

                // TODO: Set up your game interface here, according to "gamedatas"
                // Add huts to board

                console.log("board...");
                for (const hut of gamedatas.board) {
                    this.addHutOnBoard(hut.hut_id, hut.territory_id, hut.color_id);
                }


                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();

                console.log("Ending game setup");
            },

            addHutOnBoard: function (hut_id, territory_id, color_id) {
                // console.log('addHutOnBoard(' + hut_id + ',' + territory_id + ',' + color_id + ')');
                hut = this.format_block('jstpl_hut', {
                    hut_id: hut_id,
                    hut_color: color_id
                });
                dojo.place(hut, 'huts');
                this.placeOnObject('hut_' + hut_id, 'territory_' + territory_id);
            },


            ///////////////////////////////////////////////////
            //// Game & client states

            // onEnteringState: this method is called each time we are entering into a new game state.
            //                  You can use this method to perform some user interface changes at this moment.
            //
            onEnteringState: function (stateName, args) {
                console.log('Entering state: ' + stateName);

                switch (stateName) {

                    /* Example:

                    case 'myGameState':

                        // Show some HTML block at this game state
                        dojo.style( 'my_html_block_id', 'display', 'block' );

                        break;
                   */
                    case 'playerTurn':
                        this.possibleMoves = args.args.getPossibleMoves;
                        if (this.isCurrentPlayerActive()) {
                            this.updateSourceTerritories(args.args.getPossibleMoves);
                        }


                    case 'dummmy':
                        break;
                }
            },

            // onLeavingState: this method is called each time we are leaving a game state.
            //                 You can use this method to perform some user interface changes at this moment.
            //
            onLeavingState: function (stateName) {
                console.log('Leaving state: ' + stateName);

                switch (stateName) {

                    /* Example:

                    case 'myGameState':

                        // Hide the HTML block we are displaying only during this game state
                        dojo.style( 'my_html_block_id', 'display', 'none' );

                        break;
                   */


                    case 'dummmy':
                        break;
                }
            },

            // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
            //                        action status bar (ie: the HTML links in the status bar).
            //
            onUpdateActionButtons: function (stateName, args) {
                console.log('onUpdateActionButtons: ' + stateName);

                if (this.isCurrentPlayerActive()) {
                    switch (stateName) {
                        /*
                                         Example:

                                         case 'myGameState':

                                            // Add 3 action buttons in the action status bar:

                                            this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' );
                                            this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' );
                                            this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' );
                                            break;
                        */
                    }
                }
            },

            ///////////////////////////////////////////////////
            //// Utility methods

            /*

                Here, you can defines some utility methods that you can use everywhere in your javascript
                script.

            */


            ///////////////////////////////////////////////////
            //// Player's action

            /*

                Here, you are defining methods to handle player's action (ex: results of mouse click on
                game objects).

                Most of the time, these methods:
                _ check the action is possible at this game state.
                _ make a call to the game server

            */

            /* Example:

            onMyMethodToCall1: function( evt )
            {
                console.log( 'onMyMethodToCall1' );

                // Preventing default browser reaction
                dojo.stopEvent( evt );

                // Check that this action is possible (see "possibleactions" in states.inc.php)
                if( ! this.checkAction( 'myAction' ) )
                {   return; }

                this.ajaxcall( "/clansbygrivin/clansbygrivin/myAction.html", {
                                                                        lock: true,
                                                                        myArgument1: arg1,
                                                                        myArgument2: arg2,
                                                                        ...
                                                                     },
                             this, function( result ) {

                                // What to do after the server call if it succeeded
                                // (most of the time: nothing)

                             }, function( is_error) {

                                // What to do after the server call in anyway (success or failure)
                                // (most of the time: nothing)

                             } );
            },

            */

            clearTerritoriesClass: function() {
                // Remove source
                dojo.query('.territory_source').removeClass('territory_source');
                dojo.query('.territory_destination').removeClass('territory_destination');

            },

            /*
             * display all possible source of move
             */
            updateSourceTerritories: function (territories) {
                console.log("updateSourceTerritories");
                this.clearTerritoriesClass();
                // Set all possible sources
                for(var territory_id in this.possibleMoves) {
                    var destinations = territories[territory_id];
                    // console.log("territory_id=" + territory_id);
                    dojo.addClass('territory_' + territory_id, 'territory_source')
                }
                this.addTooltipToClass('territory_source', '', _('Select huts to move'));
                dojo.query('.territory_source').connect('onclick', this, 'onSelectSourceTerritory');
            },


            onSelectSourceTerritory: function (evt) {
                dojo.stopEvent( evt );
                //TODO:checkAction
                // if( !this.checkAction( "selectSourceTerritory" ) ) {
                //     return;
                // }
                // debugger;
                var territory = evt.currentTarget.id.split('_');
                var territory_id = territory[1];
                this.updateDestinationTerritories(territory_id);
            },


            updateDestinationTerritories: function(src_territory_id) {
                console.log("updateDestinationTerritories");
                this.clearTerritoriesClass();
                // debugger;
                destinations = this.possibleMoves[src_territory_id];
                for(var i in destinations) {
                    dst_id = destinations[i];
                    // console.log("territory_id=" + territory_id);
                    dojo.addClass('territory_' + dst_id, 'territory_destination')
                }
                this.addTooltipToClass('territory_destination', '', _('Move huts here'));
                //TODO: dojo.query('.territory_destination').connect('onclick', this, 'onSelectDestinationTerritory');



            },



            ///////////////////////////////////////////////////
            //// Reaction to cometD notifications

            /*
                setupNotifications:

                In this method, you associate each of your game notifications with your local method to handle it.

                Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                      your clansbygrivin.game.php file.

            */
            setupNotifications: function () {
                console.log('notifications subscriptions setup');

                // TODO: here, associate your game notifications with local methods

                // Example 1: standard notification handling
                // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );

                // Example 2: standard notification handling + tell the user interface to wait
                //            during 3 seconds after calling the method in order to let the players
                //            see what is happening in the game.
                // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
                // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
                //
            },

            // TODO: from this point and below, you can write your game notifications handling methods

            /*
            Example:

            notif_cardPlayed: function( notif )
            {
                console.log( 'notif_cardPlayed' );
                console.log( notif );

                // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call

                // TODO: play the card in the user interface.
            },

            */
        });
    });
