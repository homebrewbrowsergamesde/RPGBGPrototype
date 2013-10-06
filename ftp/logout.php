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
 * @file $/logout.php
 * @author Stephan Kreutzer
 * @since 2012-04-09
 */



session_start();

$_SESSION = array();

if (isset($_COOKIE[session_name()]) == true)
{
    setcookie(session_name(), '', time()-42000, '/');
}


require_once("./libraries/languagelib.inc.php");
require_once(getLanguageFile("logout"));



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
     "    <body>\n".
     "        <div class=\"mainbox\">\n".
     "          <div class=\"mainbox_body\">\n".
     "            <p class=\"success\">\n".
     "              ".LANG_LOGOUTSUCCESS."\n".
     "            </p>\n".
     "            <form action=\"index.php\" method=\"post\">\n".
     "              <div>\n".
     "                <input type=\"submit\" value=\"".LANG_MAINPAGEBUTTON."\"/><br/>\n".
     "              </div>\n".
     "            </form>\n".
     "          </div>\n".
     "        </div>\n".
     "    </body>\n".
     "</html>\n";



?>
