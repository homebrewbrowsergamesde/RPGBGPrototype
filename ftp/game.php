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
 * @file $/game.php
 * @brief Main page of the game that will load views to display dynamically.
 * @author Stephan Kreutzer
 * @since 2012-04-17
 */



session_start();

require_once("libraries/languagelib.inc.php");
require_once(getLanguageFile("game"));

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
     "    <head>\n".
     "        <title>".LANG_PAGETITLE."</title>\n".
     "        <link rel=\"stylesheet\" type=\"text/css\" href=\"mainstyle.css\"/>\n".
     "        <meta http-equiv=\"expires\" content=\"1296000\"/>\n".
     "        <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "    </head>\n".
     "    <body>\n";

if (isset($_SESSION['user_id']) !== true)
{
    echo "        <div class=\"mainbox\">\n".
         "          <div class=\"mainbox_body\">\n".
         "            <p class=\"error\">\n".
         "              ".LANG_INVALIDSESSION."\n".
         "            </p>\n".
         "          </div>\n".
         "        </div>\n".
         "    </body>\n".
         "</html>\n";

    exit();
}

if (is_numeric($_SESSION['user_id']) !== true)
{
    echo "        <div class=\"mainbox\">\n".
         "          <div class=\"mainbox_body\">\n".
         "            <p class=\"error\">\n".
         "              ".LANG_INVALIDSESSION."\n".
         "            </p>\n".
         "          </div>\n".
         "        </div>\n".
         "    </body>\n".
         "</html>\n";

    exit();
}

require_once("libraries/database.inc.php");

if (Database::Get()->IsConnected() !== true)
{
    echo "        <div class=\"mainbox\">\n".
         "          <div class=\"mainbox_body\">\n".
         "            <p class=\"error\">\n".
         "              ".LANG_DBCONNECTFAILED."\n".
         "            </p>\n".
         "          </div>\n".
         "        </div>\n".
         "    </body>\n".
         "</html>\n";

    exit();
}

/*
$view = "main";

if (isset($_SESSION['view']) === true)
{
    $view = $_SESSION['view'];
}

if (file_exists("./gui/views/".$view.".inc.php") !== true)
{
    echo "        <div class=\"mainbox\">\n".
         "          <div class=\"mainbox_body\">\n".
         "            <p class=\"error\">\n".
         "              ".LANG_DBCONNECTFAILED."\n".
         "            </p>\n".
         "          </div>\n".
         "        </div>\n".
         "    </body>\n".
         "</html>\n";

    exit();
}

require_once("./gui/views/".$view.".inc.php");
*/












echo "    </body>\n".
     "</html>\n";



?>
