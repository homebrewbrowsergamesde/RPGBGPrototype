<?php
/* Copyright (C) 2013  Stephan Kreutzer
 *
 * This file is part of RPGBGPrototype.
 *
 * RPGBGPrototype is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License version 3 or any later version,
 * as published by the Free Software Foundation.
 *
 * RPGBGPrototype is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License 3 for more details.
 *
 * You should have received a copy of the GNU Affero General Public License 3
 * along with RPGBGPrototype. If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * @file $/gui/views/main.inc.php
 * @details Main view of the game.
 * @author Stephan Kreutzer
 * @since 2013-10-03
 */



function VIEWHANDLER_MAIN($get, $post)
{
    require_once(dirname(__FILE__)."/../../libraries/positioning.inc.php");

    $positioning = new Positioning($_SESSION['user_id']);
    $position = $positioning->GetPosition();

    require_once(dirname(__FILE__)."/../components/map.inc.php");

    $map = new Map(5, 5);
    
    $html = $map->GetHTML($position['x'], $position['y']);

    return $html;
}

function CSSHANDLER_MAIN()
{
    return array();
}



?>
