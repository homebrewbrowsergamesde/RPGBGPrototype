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



function VIEWHANDLER_MAIN()
{
    // Handling the change of view.

    if (isset($_POST['inventory']) === true)
    {
        $_SESSION['view'] = "inventory";
        unset($_POST['inventory']);
        return;
    }

    // Input handling.

    require_once(dirname(__FILE__)."/../../libraries/positioning.inc.php");

    $positioning = new Positioning($_SESSION['user_id']);
    $position = $positioning->GetPosition();

    if (isset($_POST['north']) === true ||
        isset($_POST['east']) === true ||
        isset($_POST['south']) === true ||
        isset($_POST['west']) === true)
    {
        if (isset($_POST['north']) === true)
        {
            $positioning->SetPosition($position['x'], $position['y'] - 1);
            unset($_POST['north']);
        }
        else if (isset($_POST['east']) === true)
        {
            $positioning->SetPosition($position['x'] + 1, $position['y']);
            unset($_POST['east']);
        }
        else if (isset($_POST['south']) === true)
        {
            $positioning->SetPosition($position['x'], $position['y'] + 1);
            unset($_POST['south']);
        }
        else if (isset($_POST['west']) === true)
        {
            $positioning->SetPosition($position['x'] - 1, $position['y']);
            unset($_POST['west']);
        }

        $position = $positioning->GetPosition();
    }




    $html = "";


    require_once(dirname(__FILE__)."/../components/positioncode.inc.php");

    $positionCode = new PositionCode;
    $positionHTML = $positionCode->GetHTML($position['x'], $position['y']);

    {
        $positionNew = $positioning->GetPosition();

        if ($positionNew['x'] !== $position['x'] ||
            $positionNew['y'] !== $position['y'])
        {
            // Position code has changed the position - get HTML for the new position.

            $position = $positionNew;
            $positionHTML = $positionCode->GetHTML($position['x'], $position['y']);
        }
    }

    require_once(dirname(__FILE__)."/../components/map.inc.php");

    $map = new Map(5, 5);
    $mapHTML = $map->GetHTML($position['x'], $position['y']);

    require_once(dirname(__FILE__)."/../components/navigation.inc.php");

    $navigation = new Navigation();
    $navigationHTML = $navigation->GetHTML();

    require_once(dirname(__FILE__)."/../../libraries/languagelib.inc.php");
    require_once(getLanguageFile("main", "gui/views"));

    $html .= "<div>\n".
             "  <form action=\"game.php\" method=\"post\">\n".
             "    <div>\n".
             "      <input type=\"submit\" name=\"inventory\" value=\"".LANG_MAIN_INVENTORY."\"/>\n".
             "    </div>\n".
             "  </form>\n".
             "</div>\n".
             "<hr/>\n".
             $mapHTML.
             "\n".
             $navigationHTML.
             "\n".
             $positionHTML.
             "<hr/>\n".
             "<form action=\"logout.php\" method=\"post\">\n".
             "  <div>\n".
             "    <input type=\"submit\" name=\"logout\" value=\"".LANG_MAIN_LOGOUT."\"/>\n".
             "  </div>\n".
             "</form>\n";

    return $html;
}

function CSSHANDLER_MAIN()
{
    return array();
}



?>
