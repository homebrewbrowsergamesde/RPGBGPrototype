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
 * @file $/gui/components/navigation.inc.php
 * @author Stephan Kreutzer
 * @since 2013-10-06
 */



class Navigation
{
    public function GetHTML()
    {
        require_once(dirname(__FILE__)."/../../libraries/languagelib.inc.php");
        require_once(getLanguageFile("navigation", "gui/components"));

        $html = "<div>\n".
                "  <form action=\"game.php\" method=\"post\">\n".
                "    <table border=\"0\">\n".
                "      <tr>\n".
                "        <td></td>\n".
                "        <td><input type=\"submit\" name=\"north\" value=\"".LANG_NAVIGATION_NORTH."\"/></td>\n".
                "        <td></td>\n".
                "      </tr>\n".
                "      <tr>\n".
                "        <td><input type=\"submit\" name=\"west\" value=\"".LANG_NAVIGATION_WEST."\"/></td>\n".
                "        <td></td>\n".
                "        <td><input type=\"submit\" name=\"east\" value=\"".LANG_NAVIGATION_EAST."\"/></td>\n".
                "      </tr>\n".
                "      <tr>\n".
                "        <td></td>\n".
                "        <td><input type=\"submit\" name=\"south\" value=\"".LANG_NAVIGATION_SOUTH."\"/></td>\n".
                "        <td></td>\n".
                "      </tr>\n".
                "    </table>\n".
                "  </form>\n".
                "</div>\n";

        return $html;
    }
}



?>
