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
 * @file $/index.php
 * @brief Start page.
 * @author Stephan Kreutzer
 * @since 2012-06-01
 */



session_start();

require_once("./libraries/languagelib.inc.php");
require_once(getLanguageFile("index"));

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
     "    <head>\n".
     "        <title>".LANG_PAGETITLE."</title>\n".
     "        <link rel=\"stylesheet\" type=\"text/css\" href=\"mainstyle.css\">\n".
     "        <meta http-equiv=\"expires\" content=\"1296000\" />\n".
     "        <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "    </head>\n".
     "    <body>\n";

if (isset($_POST['name']) !== true ||
    isset($_POST['passwort']) !== true)
{
    require_once("./gui/language_selector.inc.php");
    echo getHTMLLanguageSelector("index.php");

    echo "      <div class=\"mainbox\">\n".
         "        <div class=\"mainbox_header\">\n".
         "          <h1 class=\"mainbox_header_h1\">".LANG_HEADER."</h1>\n".
         "        </div>\n".
         "        <div class=\"mainbox_body\">\n";

    if (isset($_POST['install_done']) == true)
    {
        if (@unlink(dirname(__FILE__)."/install/install.php") === true)
        {
            clearstatcache();
        }
        else
        {
            echo "          <p class=\"error\">\n".
                 "            ".LANG_INSTALLDELETEFAILED."\n".
                 "          </p>\n";
        }
    }

    if (file_exists("./install/install.php") === true &&
        isset($_GET['skipinstall']) != true)
    {
        echo "          <form action=\"install/install.php\" method=\"post\">\n".
             "            <input type=\"submit\" value=\"".LANG_INSTALLBUTTON."\"/><br/>\n".
             "          </form>\n";
    }
    else
    {
        echo "          <form action=\"index.php\" method=\"post\">\n".
             "            <input name=\"name\" type=\"text\" size=\"20\" maxlength=\"40\" /> ".LANG_NAMEFIELD_CAPTION."<br />\n".
             "            <input name=\"passwort\" type=\"password\" size=\"20\" maxlength=\"40\" /> ".LANG_PASSWORDFIELD_CAPTION."<br />\n".
             "            <input type=\"submit\" value=\"".LANG_SUBMITBUTTON."\"/><br/>\n".
             "          </form>\n";
    }

    require_once("./gui/license.inc.php");
    echo getHTMLLicenseNotification("license");
}
else
{
    require_once("./libraries/database.inc.php");
    
    $result = Database::Get()->Query("SELECT `id`,\n".
                                     "    `salt`,\n".
                                     "    `password`\n".
                                     "FROM `".Database::Get()->GetPrefix()."user`\n".
                                     "WHERE `name` LIKE ?\n",
                                     array($_POST['name']),
                                     array(Database::TYPE_STRING));

    if (is_array($result) === true)
    {
        if (count($result) === 0)
        {
            // The user doesn't exist, so insert him.

            require_once("./libraries/gamelib.inc.php");

            $id = insertNewUser($_POST['name'], $_POST['passwort']);

/*
            if ($id > 0)
            {
                $user = array("id" => $id);
            }
            else
            {
                $user = NULL;
            }
            */
        }
        else
        {
            // The user does already exist, he wants to login.

            if ($password === hash('sha512', $salt.$_POST['passwort']))
            {
                $user = array("id" => $id);
            }
            else
            {
                /**
                 * @todo Security can be improved by not telling that the user
                 *     basically exists and just the password was incorrect.
                 */

                echo "        <p>\n".
                     "          Wrong password. <a href=\"index.php\">Try again</a>.\n".
                     "        </p>\n".
                     "    </body>\n".
                     "</html>\n".
                     "\n";

                exit();
            }
        }
    }
    else
    {
        echo '      <div class="mainbox">'.
             '        <div class="mainbox_body">'.
             '          <p class="error">'.
             '            '.LANG_DBCONNECTFAILED.
             '          </p>'.
             '        </div>'.
             '      </div>';
    }

    if (is_array($user) === true)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $name;

        echo "        <div>\n".
             "          Enter <a href=\"gui/game.php\">game</a>.\n".
             "        </div>\n";
    }
    else
    {
        echo "        <p>\n".
             "          DB error.\n".
             "        </p>\n";
    }
}

echo "    </body>\n".
     "</html>\n".
     "\n";



?>
