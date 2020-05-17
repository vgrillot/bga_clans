{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- ClansByGrivin implementation : © Vincent Grillot <grivin@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    clansbygrivin_clansbygrivin.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->


<div id="board">
    <!-- BEGIN territory -->
    <div id="territory_{id}" class="territory" style="left: {left}px; top: {top}px;"></div>
    <!-- END territory -->

    <div id="huts">
    </div>

    <div id="seasons">
    </div>

    <div id="scores">
    </div>
</div>


<script type="text/javascript">

// Javascript HTML templates

//never use something like ${id}... there is a global conflict somewhere.

var jstpl_hut='<div class="hut color_${hut_color}" id="hut_${hut_id}"></div>';


</script>  

{OVERALL_GAME_FOOTER}
