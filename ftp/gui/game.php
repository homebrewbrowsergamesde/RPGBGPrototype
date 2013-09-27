<?php
/* Copyright (C) 2012-2013  Stephan Kreutzer
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
 * @file $/gui/game.php
 * @brief Main view of the game - including the map, navigation buttons and 
 *     ways to access the menus.
 * @author Stephan Kreutzer
 * @since 2012-04-17
 */



require_once("../libraries/gamelib.inc.php");

echo generateHTMLHeader("RPGBGPrototype");


$position['x'] = 0;
$position['y'] = 0;

if (isset($_SESSION['positionX']) === true &&
    isset($_SESSION['positionY']) === true)
{
    $position['x'] = $_SESSION['positionX'];
    $position['y'] = $_SESSION['positionY'];
}
else
{
    require_once("../libraries/database_connect.inc.php");

    if ($mysql_connection != false)
    {
        $position = mysql_query("SELECT `positionX`,\n".
                                "    `positionY`\n".
                                "FROM `user`\n".
                                "WHERE `id`=".$_SESSION['user_id']."\n",
                                $mysql_connection);
    }

    if ($position != false)
    {
        $result = mysql_fetch_assoc($position);
        mysql_free_result($position);
        $position = array();
        $position['x'] = $result['positionX'];
        $position['y'] = $result['positionY'];
        $_SESSION['positionX'] = $position['x'];
        $_SESSION['positionY'] = $position['y'];
    }
}

if (is_numeric($position['x']) !== true ||
    is_numeric($position['y']) !== true)
{
    $position['x'] = 0;
    $position['y'] = 0;
    $_SESSION['positionX'] = $position['x'];
    $_SESSION['positionY'] = $position['y'];
}



if (isset($_POST['north']) === true ||
    isset($_POST['east']) === true ||
    isset($_POST['south']) === true ||
    isset($_POST['west']) === true)
{
    if (isset($_POST['north']) === true)
    {
        setPosition($_SESSION['user_id'], $position['x'], $position['y'] - 1);
        unset($_POST['north']);
    }
    else if (isset($_POST['east']) === true)
    {
        setPosition($_SESSION['user_id'], $position['x'] + 1, $position['y']);
        unset($_POST['east']);
    }
    else if (isset($_POST['south']) === true)
    {
        setPosition($_SESSION['user_id'], $position['x'], $position['y'] + 1);
        unset($_POST['south']);
    }
    else if (isset($_POST['west']) === true)
    {
        setPosition($_SESSION['user_id'], $position['x'] - 1, $position['y']);
        unset($_POST['west']);
    }

    $position['x'] = $_SESSION['positionX'];
    $position['y'] = $_SESSION['positionY'];
}



$positionText = "";

if (file_exists("./positioncode/".$position['x']."_".$position['y'].".inc.php") === true)
{
    $positionText .= require_once("../positioncode/".$position['x']."_".$position['y'].".inc.php");

    if ($position['x'] !== $_SESSION['positionX'] ||
        $position['y'] !== $_SESSION['positionY'])
    {
        // Position changed within the require_once() above (maybe as a result
        // of an "teleport" option on the previous position).

        $position['x'] = $_SESSION['positionX'];
        $position['y'] = $_SESSION['positionY'];

        if (file_exists("./positioncode/".$position['x']."_".$position['y'].".inc.php") === true)
        {
            $positionText .= require_once("../positioncode/".$position['x']."_".$position['y'].".inc.php");
        }
    }
}


//echo "position is: ".$position['x'].",".$position['y']."<hr>";

echo "        <div>\n".
     "          <a href=\"inventory.php\">Inventar</a>.\n".
     "          <hr />\n".
     "        </div>\n";

$map = "";
$images = false;

if (is_numeric($position['x']) === true &&
    is_numeric($position['y']) === true)
{
    require_once("../libraries/database_connect.inc.php");

    if ($mysql_connection != false)
    {
        $images = mysql_query("SELECT `map_images`.`image_name`,\n".
                              "    `map`.`x`,\n".
                              "    `map`.`y`\n".
                              "FROM `map`\n".
                              "INNER JOIN `map_images`\n".
                              "ON `map`.`map_images_id`=`map_images`.`id`\n".
                              "WHERE `map`.`x`>=".($position['x'] - 2)." AND\n".
                              "    `map`.`x`<=".($position['x'] + 2)." AND\n".
                              "    `map`.`y`>=".($position['y'] - 2)." AND\n".
                              "    `map`.`y`<=".($position['y'] + 2)."\n".
                              "ORDER BY `map`.`y` ASC,\n".
                              "    `map`.`x` ASC\n",
                              $mysql_connection);
    }

    if ($images != false)
    {
        $result = array();

        while ($image = mysql_fetch_assoc($images))
        {
            $result[] = $image;
        }

        mysql_free_result($images);
        $images = $result;
    }
}

if (is_array($images) === true)
{
    if (count($images) === 25)
    {
        $map .= "        <div>\n";

        for ($i = 0; $i < 25; $i++)
        {
            if (($i % 5) == 0 && $i > 0)
            {
                $map .= "<br/>\n";
            }

            //$map .= $images[$i]['x']."/".$images[$i]['y'].", ";
            $map .= "<img src=\"images/".$images[$i]['image_name']."\" style=\"border:0; padding:0; margin:0; vertical-align:bottom;\" width=\"40px\" height=\"40px\" alt=\"".$images[$i]['x']."/".$images[$i]['y']."\" title=\"".$images[$i]['x']."/".$images[$i]['y']."\"/>";
        }

        $map .= "        </div>\n";
    }
}


echo $map;

echo "        <div>\n".
     "          <form action=\"game.php\" method=\"post\">\n".
     "            <table border=\"0\">\n".
     "              <tr>\n".
     "                <td></td>\n".
     "                <td><input type=\"submit\" name=\"north\" value=\"N\"/></td>\n".
     "                <td></td>\n".
     "              </tr>\n".
     "              <tr>\n".
     "                <td><input type=\"submit\" name=\"west\" value=\"W\"/></td>\n".
     "                <td></td>\n".
     "                <td><input type=\"submit\" name=\"east\" value=\"O\"/></td>\n".
     "              </tr>\n".
     "              <tr>\n".
     "                <td></td>\n".
     "                <td><input type=\"submit\" name=\"south\" value=\"S\"/></td>\n".
     "                <td></td>\n".
     "              </tr>\n".
     "            </table>\n".
     "          </form>\n".
     "        </div>\n";

echo $positionText;

echo "        <div>\n".
     "          <hr />\n".
     "          <a href=\"logout.php\">Beenden</a>.\n".
     "        </div>\n";

echo generateHTMLFooter();



?>
