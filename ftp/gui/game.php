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



session_start();

require_once("../libraries/languagelib.inc.php");
require_once(getLanguageFile("game"));

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
     "    <head>\n".
     "        <title>".LANG_PAGETITLE."</title>\n".
     "        <link rel=\"stylesheet\" type=\"text/css\" href=\"../mainstyle.css\">\n".
     "        <meta http-equiv=\"expires\" content=\"1296000\" />\n".
     "        <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "    </head>\n".
     "    <body>\n";

if (isset($_SESSION['user_id']) !== true)
{
    echo "      <div class=\"mainbox\">\n".
         "        <div class=\"mainbox_body\">\n".
         "          <p class=\"error\">\n".
         "            ".LANG_INVALIDSESSION."\n".
         "          </p>\n".
         "        </div>\n".
         "      </div>\n".
         "    </body>\n".
         "</html>\n";

    exit();
}

if (is_numeric($_SESSION['user_id']) !== true)
{
    echo "      <div class=\"mainbox\">\n".
         "        <div class=\"mainbox_body\">\n".
         "          <p class=\"error\">\n".
         "            ".LANG_INVALIDSESSION."\n".
         "          </p>\n".
         "        </div>\n".
         "      </div>\n".
         "    </body>\n".
         "</html>\n";

    exit();
}

require_once("../libraries/database.inc.php");

if (Database::Get()->IsConnected() !== true)
{
    echo "      <div class=\"mainbox\">\n".
         "        <div class=\"mainbox_body\">\n".
         "          <p class=\"error\">\n".
         "            ".LANG_DBCONNECTFAILED."\n".
         "          </p>\n".
         "        </div>\n".
         "      </div>\n".
         "    </body>\n".
         "</html>\n";

    exit();
}



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
    $result = Database::Get()->Query("SELECT `positionX`,\n".
                                     "    `positionY`\n".
                                     "FROM `".Database::Get()->GetPrefix()."user`\n".
                                     "WHERE `id`=?\n",
                                     array($_SESSION['user_id']),
                                     array(Database::TYPE_INT));

    if (is_array($result) === true)
    {
        if (count($result) >= 1)
        {
            $position['x'] = $result[0]['positionX'];
            $position['y'] = $result[0]['positionY'];
            $_SESSION['positionX'] = $position['x'];
            $_SESSION['positionY'] = $position['y'];
        }
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
    require_once("../libraries/gamelib.inc.php");

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

if (file_exists("../positioncode/".$position['x']."_".$position['y'].".inc.php") === true)
{
    $positionText .= require_once("../positioncode/".$position['x']."_".$position['y'].".inc.php");

    if ($position['x'] !== $_SESSION['positionX'] ||
        $position['y'] !== $_SESSION['positionY'])
    {
        // Position changed within the require_once() above (maybe as a result
        // of an "teleport" option on the previous position).

        $position['x'] = $_SESSION['positionX'];
        $position['y'] = $_SESSION['positionY'];

        if (file_exists("../positioncode/".$position['x']."_".$position['y'].".inc.php") === true)
        {
            $positionText .= require_once("../positioncode/".$position['x']."_".$position['y'].".inc.php");
        }
    }
}


/*
//echo "position is: ".$position['x'].",".$position['y']."<hr>";

echo "        <div>\n".
     "          <a href=\"inventory.php\">Inventar</a>.\n".
     "          <hr />\n".
     "        </div>\n";
*/

$map = "";
$images = false;

if (is_numeric($position['x']) === true &&
    is_numeric($position['y']) === true)
{
    $images = Database::Get()->Query("SELECT `".Database::Get()->GetPrefix()."map_images`.`image_name`,\n".
                                     "    `".Database::Get()->GetPrefix()."map`.`x`,\n".
                                     "    `".Database::Get()->GetPrefix()."map`.`y`\n".
                                     "FROM `".Database::Get()->GetPrefix()."map`\n".
                                     "INNER JOIN `".Database::Get()->GetPrefix()."map_images`\n".
                                     "ON `".Database::Get()->GetPrefix()."map`.`map_images_id`=`".Database::Get()->GetPrefix()."map_images`.`id`\n".
                                     "WHERE `".Database::Get()->GetPrefix()."map`.`x`>=? AND\n".
                                     "    `".Database::Get()->GetPrefix()."map`.`x`<=? AND\n".
                                     "    `".Database::Get()->GetPrefix()."map`.`y`>=? AND\n".
                                     "    `".Database::Get()->GetPrefix()."map`.`y`<=?\n".
                                     "ORDER BY `".Database::Get()->GetPrefix()."map`.`y` ASC,\n".
                                     "    `".Database::Get()->GetPrefix()."map`.`x` ASC\n",
                                     array($position['x'] - 2, $position['x'] + 2, $position['y'] - 2, $position['y'] + 2),
                                     array(Database::TYPE_INT, Database::TYPE_INT, Database::TYPE_INT, Database::TYPE_INT));
}

echo "        <div class=\"mainbox\">\n".
     "          <div class=\"mainbox_body\">\n";

if (is_array($images) === true)
{
    if (count($images) === 25)
    {
        $map .= "            <div>\n";

        for ($i = 0; $i < 25; $i++)
        {
            if (($i % 5) == 0 && $i > 0)
            {
                $map .= "<br/>\n";
            }

            //$map .= $images[$i]['x']."/".$images[$i]['y'].", ";
            $map .= "<img src=\"../images/".$images[$i]['image_name']."\" style=\"border:0; padding:0; margin:0; vertical-align:bottom;\" width=\"40px\" height=\"40px\" alt=\"".$images[$i]['x']."/".$images[$i]['y']."\" title=\"".$images[$i]['x']."/".$images[$i]['y']."\"/>";
        }

        $map .= "            </div>\n";
    }
}

echo $map;

echo "            <div>\n".
     "              <form action=\"game.php\" method=\"post\">\n".
     "                <table border=\"0\">\n".
     "                  <tr>\n".
     "                    <td></td>\n".
     "                    <td><input type=\"submit\" name=\"north\" value=\"N\"/></td>\n".
     "                    <td></td>\n".
     "                  </tr>\n".
     "                  <tr>\n".
     "                    <td><input type=\"submit\" name=\"west\" value=\"W\"/></td>\n".
     "                    <td></td>\n".
     "                    <td><input type=\"submit\" name=\"east\" value=\"O\"/></td>\n".
     "                  </tr>\n".
     "                  <tr>\n".
     "                    <td></td>\n".
     "                    <td><input type=\"submit\" name=\"south\" value=\"S\"/></td>\n".
     "                    <td></td>\n".
     "                  </tr>\n".
     "                </table>\n".
     "              </form>\n".
     "            </div>\n";

echo $positionText;

echo "          </div>\n".
     "        </div>\n";


/*
echo "        <div>\n".
     "          <hr />\n".
     "          <a href=\"logout.php\">Beenden</a>.\n".
     "        </div>\n";
*/

echo "    </body>\n".
     "</html>\n";



?>
