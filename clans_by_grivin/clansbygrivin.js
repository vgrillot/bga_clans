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

                // Save static material info
                this.colors = gamedatas.colors;

                // Setting up player boards
                for (const player_id in gamedatas.players) {
                    const player = gamedatas.players[player_id];
                    // Add secret color token to player area
                    this.addSecretColorOnPlayer(player_id);
                }

                // TODO: Set up your game interface here, according to "gamedatas"
                // Add huts to board
                console.log("board...");
                for (const hut of gamedatas.board) {
                    this.addHutOnBoard(hut.hut_id, hut.territory_id, hut.color_id);
                }

                // Display score
                this.displayScores(gamedatas.scores);

                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();

                this.updateSecretColorOnPlayer(
                    gamedatas._private.current_player_id,
                    gamedatas._private.secret_color_id);

                console.log("Ending game setup");
            },


            addSecretColorOnPlayer: function (player_id) {
                dojo.place(this.format_block('jstpl_playerSecretColor', {
                    player_id: player_id,
                }), 'player_board_' + player_id);
            },


            updateSecretColorOnPlayer: function (player_id, color_id) {
                //TODO:Need a review: How to use DOJO to change a css class
                // let player_secret_color = dojo.query('player_secret_color_' + player_id); -> doesn't return a single element
                // player_secret_color.removeClass('color_secret');
                // player_secret_color.addClass('color_' + color_id);

                // using jquery
                player_secret_color = $('player_secret_color_' + player_id);
                player_secret_color.classList.remove('color_secret');
                player_secret_color.classList.add('color_' + color_id);

                // using jquery
                player_name = $('player_name_' + player_id);
                // first (and only) elem is href
                player_name.firstElementChild.style.color = '#' + this.colors[color_id]['color'];
            },


            addHutOnBoard: function (hut_id, territory_id, color_id) {
                // console.log('addHutOnBoard(' + hut_id + ',' + territory_id + ',' + color_id + ')');
                hut = this.format_block('jstpl_hut', {
                    hut_id: hut_id,
                    hut_color: color_id
                });
                dojo.place(hut, 'huts');
                // this.placeOnObject('hut_' + hut_id, 'territory_' + territory_id);
                const [x, y] = this.get_new_hut_xy(i, false);
                this.placeOnObjectPos('hut_' + hut_id, 'territory_' + territory_id, x, y);
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

                    case 'secretColor':
                        this.updateSecretColorOnPlayer(args.player_id, args.args._private.secretColor);
                        break;

                    case 'playerTurn':
                        this.possibleMoves = args.args.getPossibleMoves;
                        if (this.isCurrentPlayerActive()) {
                            this.updateSourceTerritories(args.args.getPossibleMoves);
                        }
                        break;


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

            clearTerritoriesClass: function () {
                // Remove source

                // TODO: connect onclick to '' do nothing, and do not disconnect :(

                dojo.query('.territory').connect('onclick', this, '');
                dojo.query('.territory_source').connect('onclick', this, '');
                dojo.query('.territory_source').removeClass('territory_source');
                dojo.query('.territory_destination').connect('onclick', this, '');
                dojo.query('.territory_destination').removeClass('territory_destination');
            },

            /*
             * display all possible source of move
             */
            updateSourceTerritories: function (territories) {
                console.log("updateSourceTerritories");
                this.clearTerritoriesClass();
                // Set all possible sources
                for (var territory_id in this.possibleMoves) {
                    var destinations = territories[territory_id];
                    // console.log("territory_id=" + territory_id);
                    dojo.addClass('territory_' + territory_id, 'territory_source')
                }
                this.addTooltipToClass('territory_source', '', _('Select huts to move'));
                dojo.query('.territory_source').connect('onclick', this, 'onSelectSourceTerritory');
            },


            onSelectSourceTerritory: function (evt) {
                dojo.stopEvent(evt);
                //TODO:checkAction
                // if( !this.checkAction( "selectSourceTerritory" ) ) {
                //     return;
                // }
                if (!dojo.hasClass(evt.currentTarget.id, 'territory_source')) {
                    // this should not happend to still have the onclick() plugged here :(
                    return;
                }
                var territory = evt.currentTarget.id.split('_');
                var territory_id = territory[1];
                this.updateDestinationTerritories(territory_id);
            },


            onSelectDestinationTerritory: function (evt) {
                dojo.stopEvent(evt);
                var territory = evt.currentTarget.id.split('_');
                var dst_territory_id = territory[1];

                if (!dojo.hasClass(evt.currentTarget.id, 'territory_destination')) {
                    // this should not happend to still have the onclick() plugged here :(
                    return;
                }

                // avoid double clic...
                this.clearTerritoriesClass();

                // if (this.checkAction('playHuts'))    // Check that this action is possible at this moment
                // {
                this.ajaxcall("/clansbygrivin/clansbygrivin/playHuts.html", {
                    src_territory_id: this.src_territory_id,
                    dst_territory_id: dst_territory_id
                }, this, function (result) {
                });
                // }

            },


            updateDestinationTerritories: function (src_territory_id) {
                console.log("updateDestinationTerritories");
                this.src_territory_id = src_territory_id;
                this.clearTerritoriesClass();
                destinations = this.possibleMoves[src_territory_id];
                for (var i in destinations) {
                    dst_id = destinations[i];
                    // console.log("territory_id=" + territory_id);
                    dojo.addClass('territory_' + dst_id, 'territory_destination')
                }
                this.addTooltipToClass('territory_destination', '', _('Move huts here'));
                dojo.query('.territory_destination').connect('onclick', this, 'onSelectDestinationTerritory');
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

                dojo.subscribe('moveHuts', this, 'notif_moveHuts');
                // this.notifqueue.setSynchronous( 'moveHuts', 3000 );

                dojo.subscribe('villageDispute', this, 'notif_villageDispute');
                this.notifqueue.setSynchronous('villageDispute', 500);

                dojo.subscribe('villageBuilt', this, 'notif_villageBuilt');
                this.notifqueue.setSynchronous('villageBuilt', 3000);

                dojo.subscribe('villageDestroyed', this, 'notif_villageDestroyed');
                this.notifqueue.setSynchronous('villageDestroyed', 3000);

                dojo.subscribe('updateScore', this, 'notif_updateScore');

                dojo.subscribe('revealMySecretColor', this, 'notif_revealMySecretColor');
                dojo.subscribe('revealAllSecretColors', this, 'notif_revealAllSecretColors');

                //!!!for debug purposes:
                dojo.subscribe('debug', this, 'notif_debug');
            },


            /*
             * move huts...
             */
            notif_moveHuts(notif) {
                console.log('notif_moveHuts');
                console.log(notif);
                territory_id = "territory_" + notif.args.dst_territory_id;
                for (var i in notif.args.huts) {
                    hut_id = "hut_" + notif.args.huts[i].hut_id;
                    const [x, y] = this.get_new_hut_xy(i, true);
                    console.log("slideToObjectPos(" + hut_id + ", " + territory_id + ", " + x + ", " + y + ")");
                    this.slideToObjectPos(hut_id, territory_id, x, y).play();
                }
            },

            /*
             * notif_villageDispute
             * remote all single huts in this village
             */
            notif_villageDispute(notif) {
                console.log('notif_villageDispute');
                console.log(notif);
                territory_id = "territory_" + notif.args.src_territory_id;
                for (var i in notif.args.huts) {
                    hut_id = "hut_" + notif.args.huts[i];
                    this.remove_hut(hut_id);
                }
            },

            /*
             *
             */
            notif_villageBuilt(notif) {
                console.log('notif_villageBuilt');
                console.log(notif);

            },

            /*
             *
             */
            notif_villageDestroyed(notif) {
                console.log('notif_villageDestroyed');
                console.log(notif);

            },

            /*
             *
             */
            notif_updateScore(notif) {
                console.log('notif_udpateScore');
                console.log(notif);
                this.displayScores(notif.args.scores);
            },

            /*
             * notif_revealMySecretColor
             *
             * reveal all colors of all players
             */
            notif_revealMySecretColor(notif) {
                console.log('notif_revealMySecretColor');
                console.log(notif);
                this.updateSecretColorOnPlayer(notif.args.player_id, notif.args.color_id);
            },

            /*
             * notif_revealAllSecretColors
             *
             * reveal all colors of all players
             */
            notif_revealAllSecretColors(notif) {
                console.log('notif_revealAllSecretColors');
                console.log(notif);
                this.updateSecretColorOnPlayer(notif.args.player_id, notif.args.color_id);
            },

            /*
             * notif_debug()
             */
            notif_debug(notif) {
                console.log('notif_debug');
                console.log(notif);
            },

            /*
             * display score
             */
            displayScores(scores) {
                console.log('displayScore');
                console.log(scores);
                for (var i in scores) {
                    score_id = "hut_score_" + scores[i]["color_id"];
                    value = scores[i]["score"];
                    $(score_id).innerText = value;
                }
            },


            remove_hut(hut_id) {
                console.log('remove_hut');
                console.log(hut_id);
                //TODO:slide out of board #18
                // this.slideToObjectPos(hut_id, territory_id, x, y).play();
                this.fadeOutAndDestroy(hut_id);
            },


            /*
             * define new [x, y] offset for the hut to see them all
             * has_offset: slideTo...() need the center offset, placeTo() doesn't
             */
            get_new_hut_xy(n, has_offset) {
                const delta = 30;
                const offset = has_offset ? 50 : 0; // +50 : center of territory (100 diameter)
                const x = Math.random() * delta - delta / 2 + offset;
                const y = Math.random() * delta - delta / 2 + offset;
                return [x, y]
            }
        });
    });
