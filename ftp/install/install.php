<?php
/* Copyright (C) 2013-2017  Christian Huke, Stephan Kreutzer
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
 * @file $/install/install.php
 * @brief Installation routine to set up the game.
 * @author Christian Huke, Stephan Kreutzer
 * @since 2013-09-13
 */



require_once("../libraries/languagelib.inc.php");
require_once(getLanguageFile("install"));



echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
     "<!DOCTYPE html\n".
     "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
     "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
     "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
     "  <head>\n".
     "    <title>".LANG_PAGETITLE."</title>\n".
     "    <link rel=\"stylesheet\" type=\"text/css\" href=\"../mainstyle.css\">\n".
     "    <link rel=\"stylesheet\" type=\"text/css\" href=\"install.css\">\n".
     "    <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
     "  </head>\n".
     "  <body>\n";


$step = 0;

if (isset($_POST['step']) === true)
{
    if (is_numeric($_POST['step']) === true)
    {
        $step = (int)$_POST['step'];

/*
        if ($step == 3 &&
            isset($_POST['retry']) === true)
        {
            // Special handling for step 2 (retry other database connection
            // settings after one connection was already established successfully).
            $step = 2;
        }
*/

        if ($step == 4 &&
            isset($_POST['init']) === true)
        {
            // Special handling for step 3 (redo database initialization after
            // initialization was already completed successfully).
            $step = 3;
        }
    }
}

if (isset($_GET['stepjump']) === true)
{
    if (is_numeric($_GET['stepjump']) === true)
    {
        $step = (int)$_GET['stepjump'];
    }
}


if ($step == 0)
{
    // Language selection only for the first step.
    require_once("../gui/components/language_selector.inc.php");
    echo getHTMLLanguageSelector("install.php");

    echo "    <div class=\"mainbox\">\n".
         "      <div class=\"mainbox_header\">\n".
         "        <h1 class=\"mainbox_header_h1\">".LANG_STEP0_HEADER."</h1>\n".
         "      </div>\n".
         "      <div class=\"mainbox_body\">\n";

    require_once("../gui/license.inc.php");
    echo getHTMLLicenseNotification("license");

    echo "        <p>\n".
         "          ".LANG_STEP0_WELCOMETEXT."\n".
         "        </p>\n".
         "        <div>\n".
         "          <form action=\"install.php\" method=\"post\">\n".
         "            <input type=\"hidden\" name=\"step\" value=\"1\"/>\n".
         "            <input type=\"submit\" value=\"".LANG_STEP0_PROCEEDTEXT."\" class=\"mainbox_proceed\"/>\n".
         "          </form>\n".
         "        </div>\n".
         "      </div>\n".
         "    </div>\n";
}
else if ($step == 1)
{
    echo "    <div class=\"mainbox\">\n".
         "      <div class=\"mainbox_header\">\n".
         "        <h1 class=\"mainbox_header_h1\">".LANG_STEP1_HEADER."</h1>\n".
         "      </div>\n".
         "      <div class=\"mainbox_body\">\n";

    require_once("../gui/license.inc.php");
    echo getHTMLLicenseFull("license");

    echo "        <div>\n".
         "          <form action=\"install.php\" method=\"post\">\n".
         "            <input type=\"hidden\" name=\"step\" value=\"2\"/>\n".
         "            <input type=\"submit\" value=\"".LANG_STEP1_PROCEEDTEXT."\" class=\"mainbox_proceed\"/>\n".
         "          </form>\n".
         "        </div>\n".
         "      </div>\n".
         "    </div>\n";
}
else if ($step == 2)
{
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "rpgbgprototype";
    $prefix = "";

    if (isset($_POST['host']) === true)
    {
        $host = $_POST['host'];
    }

    if (isset($_POST['username']) === true)
    {
        $username = $_POST['username'];
    }

    if (isset($_POST['password']) === true)
    {
        $password = $_POST['password'];
    }

    if (isset($_POST['database']) === true)
    {
        $database = $_POST['database'];
    }

    if (isset($_POST['prefix']) === true)
    {
        $prefix = $_POST['prefix'];
    }

    echo "    <div class=\"mainbox\">\n".
         "      <div class=\"mainbox_header\">\n".
         "        <h1 class=\"mainbox_header_h1\">".LANG_STEP2_HEADER."</h1>\n".
         "      </div>\n".
         "      <div class=\"mainbox_body\">\n".
         "        <p>\n".
         "          ".LANG_STEP2_REQUIREMENTS."\n".
         "        </p>\n";

    if (file_exists("../libraries/database_connect.inc.php") !== true)
    {
        $file = @fopen("../libraries/database_connect.inc.php", "w");

        if ($file != false)
        {
            @fclose($file);
        }
        else
        {
            echo "        <p>\n".
                 "          <span class=\"error\">".LANG_STEP2_DATABASECONNECTFILECREATEFAILED."</span>\n".
                 "        </p>\n";
        }
    }

    if (is_writable("../libraries/database_connect.inc.php") === true)
    {
        echo "        <p>\n".
             "          <span class=\"success\">".LANG_STEP2_DATABASECONNECTFILEISWRITABLE."</span>\n".
             "        </p>\n";

        $php_code = "<?php\n".
                    "// This file was automatically generated by the installation routine.\n".
                    "\n".
                    "\$pdo = false;\n".
                    "\$db_table_prefix = \"$prefix\"; // Prefix for database tables.\n".
                    "\$exceptionConnectFailure = NULL;\n".
                    "\n".
                    "\n".
                    "try\n".
                    "{\n".
                    "    \$pdo = @new PDO('mysql:host=".$host.";dbname=".$database.";charset=utf8', \"".$username."\", \"".$password."\", array(PDO::MYSQL_ATTR_INIT_COMMAND => \"SET NAMES utf8\"));\n".
                    "}\n".
                    "catch (PDOException \$ex)\n".
                    "{\n".
                    "    \$pdo = false;\n".
                    "    \$exceptionConnectFailure = \$ex;\n".
                    "}\n".
                    "\n".
                    "?>\n";

        $file = @fopen("../libraries/database_connect.inc.php", "wb");

        if ($file != false)
        {
            if (@fwrite($file, $php_code) != false)
            {
                echo "        <p>\n".
                     "          <span class=\"success\">".LANG_STEP2_DATABASECONNECTFILEWRITESUCCEEDED."</span>\n".
                     "        </p>\n";
            }
            else
            {
                echo "        <p>\n".
                     "          <span class=\"error\">".LANG_STEP2_DATABASECONNECTFILEWRITEFAILED."</span>\n".
                     "        </p>\n";
            }

            @fclose($file);
        }
        else
        {
            echo "        <p>\n".
                 "          <span class=\"error\">".LANG_STEP2_DATABASECONNECTFILEWRITABLEOPENFAILED."</span>\n".
                 "        </p>\n";
        }
    }
    else
    {
        echo "        <p>\n".
             "          <span class=\"error\">".LANG_STEP2_DATABASECONNECTFILEISNTWRITABLE."</span>\n".
             "        </p>\n";
    }


    $successConnect = false;

    clearstatcache();

    if (file_exists("../libraries/database_connect.inc.php") === true)
    {
        if (is_readable("../libraries/database_connect.inc.php") === true)
        {
            echo "        <p>\n".
                 "          <span class=\"success\">".LANG_STEP2_DATABASECONNECTFILEISREADABLE."</span>\n".
                 "        </p>\n";

            require_once("../libraries/database.inc.php");

            if (Database::Get()->IsConnected() === true)
            {
                $successConnect = true;

                echo "            <p>\n".
                     "              <span class=\"success\">".LANG_STEP2_DBCONNECTSUCCEEDED."</span>\n".
                     "            </p>\n";
            }
            else
            {
                if (strlen(Database::Get()->GetLastErrorMessage()) > 0)
                {
                    echo "        <p>\n".
                         "          <span class=\"error\">".LANG_STEP2_DBCONNECTFAILED." ".Database::Get()->GetLastErrorMessage()."</span>\n".
                         "        </p>\n";
                }
                else
                {
                    echo "        <p>\n".
                         "          <span class=\"error\">".LANG_STEP2_DBCONNECTFAILED." ".LANG_STEP2_DBCONNECTFAILEDNOERRORINFO."</span>\n".
                         "        </p>\n";
                }
            }
        }
        else
        {
            echo "        <p>\n".
                 "          <span class=\"error\">".LANG_STEP2_DATABASECONNECTFILEISNTREADABLE."</span>\n".
                 "        </p>\n";
        }
    }
    else
    {
        echo "        <p>\n".
             "          <span class=\"error\">".LANG_STEP2_DATABASECONNECTFILEDOESNTEXIST."</span>\n".
             "        </p>\n";
    }

    if (isset($_POST['save']) == false ||
        $successConnect == false)
    {
        echo "        <div>\n".
             "          <form action=\"install.php\" method=\"post\">\n".
             "            <input type=\"hidden\" name=\"step\" value=\"2\"/>\n".
             "            <input type=\"text\" name=\"host\" value=\"".$host."\"/> ".LANG_STEP2_HOSTDESCRIPTION."<br/>\n".
             "            <input type=\"text\" name=\"username\" value=\"".$username."\"/> ".LANG_STEP2_USERNAMEDESCRIPTION."<br/>\n".
             "            <input type=\"text\" name=\"password\" value=\"".$password."\"/> ".LANG_STEP2_PASSWORDDESCRIPTION."<br/>\n".
             "            <input type=\"text\" name=\"database\" value=\"".$database."\"/> ".LANG_STEP2_DATABASENAMEDESCRIPTION."<br/>\n".
             "            <input type=\"text\" name=\"prefix\" value=\"".$prefix."\"/> ".LANG_STEP2_TABLEPREFIXDESCRIPTION."<br/>\n".
             "            <input type=\"submit\" name=\"save\" value=\"".LANG_STEP2_SAVETEXT."\" class=\"mainbox_proceed\"/>\n".
             "          </form>\n".
             "        </div>\n";
    }
    else
    {
        echo "        <div>\n".
             "          <form action=\"install.php\" method=\"post\">\n".
             "            <input type=\"hidden\" name=\"step\" value=\"2\"/>\n".
             "            <input type=\"hidden\" name=\"host\" value=\"".$host."\"/>\n".
             "            <input type=\"hidden\" name=\"username\" value=\"".$username."\"/>\n".
             "            <input type=\"hidden\" name=\"password\" value=\"".$password."\"/>\n".
             "            <input type=\"hidden\" name=\"database\" value=\"".$database."\"/>\n".
             "            <input type=\"hidden\" name=\"prefix\" value=\"".$prefix."\"/>\n".
             "            <input type=\"submit\" value=\"".LANG_STEP2_EDITTEXT."\" class=\"mainbox_proceed\"/>\n".
             "          </form>\n".
             "        </div>\n".
             "        <div>\n".
             "          <form action=\"install.php\" method=\"post\">\n".
             "            <input type=\"hidden\" name=\"step\" value=\"3\"/>\n".
             "            <input type=\"submit\" value=\"".LANG_STEP2_PROCEEDTEXT."\" class=\"mainbox_proceed\"/>\n".
             "          </form>\n".
             "        </div>\n";
    }

    echo "      </div>\n".
         "    </div>\n";
}
else if ($step == 3)
{
    $dropExistingTables = false;
    $keepExistingTables = false;

    if (isset($_POST['drop_existing_tables']) === true)
    {
        $dropExistingTables = true;
    }

    if (isset($_POST['keep_existing_tables']) === true)
    {
        $keepExistingTables = true;
    }


    echo "    <div class=\"mainbox\">\n".
         "      <div class=\"mainbox_header\">\n".
         "        <h1 class=\"mainbox_header_h1\">".LANG_STEP3_HEADER."</h1>\n".
         "      </div>\n".
         "      <div class=\"mainbox_body\">\n".
         "        <p>\n".
         "          ".LANG_STEP3_INITIALIZATIONDESCRIPTION."\n".
         "        </p>\n";


    $successInit = false;

    if (isset($_POST['init']) === true)
    {
        require_once("../libraries/database.inc.php");

        if (Database::Get()->IsConnected() === true)
        {
            $success = Database::Get()->BeginTransaction();

            /**
             * @todo No preparation of SQL query strings, because no user input is
             *     involved. If user input gets inserted in the future, update
             *     the method calls!
             */

            // Table user

            if ($success === true)
            {
                if ($dropExistingTables === true)
                {
                    if (Database::Get()->ExecuteUnsecure("DROP TABLE IF EXISTS ".Database::Get()->GetPrefix()."user") !== true)
                    {
                        $success = false;
                    }
                }
            }

            if ($success === true)
            {
                $sql = "CREATE TABLE ";

                if ($keepExistingTables === true)
                {
                    $sql .= "IF NOT EXISTS ";
                }
                
                $sql .= "`".Database::Get()->GetPrefix()."user` (".
                        "  `id` int(11) NOT NULL AUTO_INCREMENT,".
                        "  `name` varchar(40) COLLATE utf8_bin NOT NULL,".
                        "  `salt` varchar(255) COLLATE utf8_bin NOT NULL,".
                        "  `password` varchar(255) COLLATE utf8_bin NOT NULL,".
                        "  `positionX` int(11) NOT NULL,".
                        "  `positionY` int(11) NOT NULL,".
                        "  PRIMARY KEY (`id`),".
                        "  UNIQUE KEY `name` (`name`)".
                        ") ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

                if (Database::Get()->ExecuteUnsecure($sql) !== true)
                {
                    $success = false;
                }
            }

            // Table map_images

            if ($success === true)
            {
                if ($dropExistingTables === true)
                {
                    if (Database::Get()->ExecuteUnsecure("DROP TABLE IF EXISTS ".Database::Get()->GetPrefix()."map_images") !== true)
                    {
                        $success = false;
                    }
                }
            }

            if ($success === true)
            {
                $sql = "CREATE TABLE ";

                if ($keepExistingTables === true)
                {
                    $sql .= "IF NOT EXISTS ";
                }
                
                $sql .= "`".Database::Get()->GetPrefix()."map_images` (".
                        "  `id` int(11) NOT NULL AUTO_INCREMENT,".
                        "  `image_name` varchar(255) NOT NULL,".
                        "  PRIMARY KEY (`id`),".
                        "  UNIQUE KEY `image_name` (`image_name`)".
                        ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

                if (Database::Get()->ExecuteUnsecure($sql) !== true)
                {
                    $success = false;
                }
            }

            // Table map

            if ($success === true)
            {
                if ($dropExistingTables === true)
                {
                    if (Database::Get()->ExecuteUnsecure("DROP TABLE IF EXISTS ".Database::Get()->GetPrefix()."map") !== true)
                    {
                        $success = false;
                    }
                }
            }

            if ($success === true)
            {
                $sql = "CREATE TABLE ";

                if ($keepExistingTables === true)
                {
                    $sql .= "IF NOT EXISTS ";
                }
                
                $sql .= "`".Database::Get()->GetPrefix()."map` (".
                        "  `x` int(11) NOT NULL,".
                        "  `y` int(11) NOT NULL,".
                        "  `map_images_id` int(11) NOT NULL".
                        ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

                if (Database::Get()->ExecuteUnsecure($sql) !== true)
                {
                    $success = false;
                }
            }

            // Table variables_global

            if ($success === true)
            {
                if ($dropExistingTables === true)
                {
                    if (Database::Get()->ExecuteUnsecure("DROP TABLE IF EXISTS ".Database::Get()->GetPrefix()."variables_global") !== true)
                    {
                        $success = false;
                    }
                }
            }

            if ($success === true)
            {
                $sql = "CREATE TABLE ";

                if ($keepExistingTables === true)
                {
                    $sql .= "IF NOT EXISTS ";
                }
                
                $sql .= "`".Database::Get()->GetPrefix()."variables_global` (".
                        "  `variable` varchar(255) COLLATE utf8_bin NOT NULL,".
                        "  `value` varchar(255) COLLATE utf8_bin NOT NULL,".
                        "  UNIQUE KEY `name` (`variable`)".
                        ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

                if (Database::Get()->ExecuteUnsecure($sql) !== true)
                {
                    $success = false;
                }
            }

            // Table variables_user

            if ($success === true)
            {
                if ($dropExistingTables === true)
                {
                    if (Database::Get()->ExecuteUnsecure("DROP TABLE IF EXISTS ".Database::Get()->GetPrefix()."variables_user") !== true)
                    {
                        $success = false;
                    }
                }
            }

            if ($success === true)
            {
                $sql = "CREATE TABLE ";

                if ($keepExistingTables === true)
                {
                    $sql .= "IF NOT EXISTS ";
                }
                
                $sql .= "`".Database::Get()->GetPrefix()."variables_user` (".
                        "  `variable` varchar(255) COLLATE utf8_bin NOT NULL,".
                        "  `user_id` int(11) NOT NULL,".
                        "  `value` varchar(255) COLLATE utf8_bin NOT NULL,".
                        "  INDEX (`user_id`)".
                        ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

                if (Database::Get()->ExecuteUnsecure($sql) !== true)
                {
                    $success = false;
                }
            }

            // Table inventory

            if ($success === true)
            {
                if ($dropExistingTables === true)
                {
                    if (Database::Get()->ExecuteUnsecure("DROP TABLE IF EXISTS ".Database::Get()->GetPrefix()."inventory") !== true)
                    {
                        $success = false;
                    }
                }
            }

            if ($success === true)
            {
                $sql = "CREATE TABLE ";

                if ($keepExistingTables === true)
                {
                    $sql .= "IF NOT EXISTS ";
                }
                
                $sql .= "`".Database::Get()->GetPrefix()."inventory` (".
                        "  `user_id` int(11) NOT NULL,".
                        "  `type` int(11) NOT NULL,".
                        "  `amount` int(11) NOT NULL DEFAULT 0".
                        ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";

                if (Database::Get()->ExecuteUnsecure($sql) !== true)
                {
                    $success = false;
                }
            }


            // Data of map example

            if ($success === true)
            {
                $sql = "INSERT INTO `".Database::Get()->GetPrefix()."map_images` (`id`, `image_name`) VALUES".
                       "(NULL, 'forest.png'),".
                       "(NULL, 'crossway.png'),".
                       "(NULL, 'sawmill.png'),".
                       "(NULL, 'village.png'),".
                       "(NULL, 'ruin.png')";

                if (Database::Get()->InsertUnsecure($sql) < 0)
                {
                    $success = false;
                }
            }

            if ($success === true)
            {
                $sql = "INSERT INTO `".Database::Get()->GetPrefix()."map` (`x`, `y`, `map_images_id`) VALUES".
                       "(-3, -3, 1), (-2, -3, 1), (-1, -3, 1), (0, -3, 1), (1, -3, 1), (2, -3, 1), (3, -3, 1),".
                       "(-3, -2, 1), (-2, -2, 1), (-1, -2, 1), (0, -2, 1), (1, -2, 1), (2, -2, 1), (3, -2, 1),".
                       "(-3, -1, 1), (-2, -1, 1), (-1, -1, 1), (0, -1, 4), (1, -1, 1), (2, -1, 1), (3, -1, 1),".
                       "(-3,  0, 1), (-2,  0, 1), (-1,  0, 5), (0,  0, 2), (1,  0, 3), (2,  0, 1), (3,  0, 1),".
                       "(-3,  1, 1), (-2,  1, 1), (-1,  1, 1), (0,  1, 1), (1,  1, 1), (2,  1, 1), (3,  1, 1),".
                       "(-3,  2, 1), (-2,  2, 1), (-1,  2, 1), (0,  2, 1), (1,  2, 1), (2,  2, 1), (3,  2, 1),".
                       "(-3,  3, 1), (-2,  3, 1), (-1,  3, 1), (0,  3, 1), (1,  3, 1), (2,  3, 1), (3,  3, 1)";

                if (Database::Get()->InsertUnsecure($sql) < 0)
                {
                    $success = false;
                }
            }


            if ($success === true)
            {
                if (Database::Get()->commitTransaction() === true)
                {
                    echo "        <p>\n".
                         "          <span class=\"success\">".LANG_STEP3_DBOPERATIONSUCCEEDED."</span>\n".
                         "        </p>\n";

                    $successInit = true;
                }
                else
                {
                    echo "        <p>\n".
                         "          <span class=\"error\">".LANG_STEP3_DBCOMMITFAILED."</span>\n".
                         "        </p>\n";
                }
            }
            else
            {
                if (strlen(Database::Get()->GetLastErrorMessage()) > 0)
                {
                    echo "        <p>\n".
                         "          <span class=\"error\">".LANG_STEP3_DBOPERATIONFAILED." ".Database::Get()->GetLastErrorMessage()."</span>\n".
                         "        </p>\n";
                }
                else
                {
                    echo "        <p>\n".
                         "          <span class=\"error\">".LANG_STEP3_DBOPERATIONFAILED." ".LANG_STEP3_DBOPERATIONFAILEDNOERRORINFO."</span>\n".
                         "        </p>\n";
                }

                Database::Get()->RollbackTransaction();
            }
        }
        else
        {
            if (strlen(Database::Get()->GetLastErrorMessage()) > 0)
            {
                echo "        <p>\n".
                     "          <span class=\"error\">".LANG_STEP3_DBCONNECTFAILED." ".Database::Get()->GetLastErrorMessage()."</span>\n".
                     "        </p>\n";
            }
            else
            {
                echo "        <p>\n".
                     "          <span class=\"error\">".LANG_STEP3_DBCONNECTFAILED." ".LANG_STEP3_DBCONNECTFAILEDNOERRORINFO."</span>\n".
                     "        </p>\n";
            }
        }
    }

    echo "        <div>\n".
         "          <form action=\"install.php\" method=\"post\">\n";

    if ($successInit === true)
    {
        echo "            <input type=\"hidden\" name=\"step\" value=\"4\"/>\n";
    }
    else
    {
        echo "            <input type=\"hidden\" name=\"step\" value=\"3\"/>\n";
    }

    if ($dropExistingTables === true)
    {
        echo "            <input type=\"checkbox\" name=\"drop_existing_tables\" value=\"drop\" checked=\"checked\"/> ".LANG_STEP3_CHECKBOXDESCRIPTIONDROPEXISTINGTABLES."<br/>\n";
    }
    else
    {
        echo "            <input type=\"checkbox\" name=\"drop_existing_tables\" value=\"drop\"/> ".LANG_STEP3_CHECKBOXDESCRIPTIONDROPEXISTINGTABLES."<br/>\n";
    }

    if ($keepExistingTables === true)
    {
        echo "            <input type=\"checkbox\" name=\"keep_existing_tables\" value=\"keep\" checked=\"checked\"/> ".LANG_STEP3_CHECKBOXDESCRIPTIONKEEPEXISTINGTABLES."<br/>\n";
    }
    else
    {
        echo "            <input type=\"checkbox\" name=\"keep_existing_tables\" value=\"keep\"/> ".LANG_STEP3_CHECKBOXDESCRIPTIONKEEPEXISTINGTABLES."<br/>\n";
    }

    echo "            <input type=\"submit\" name=\"init\" value=\"".LANG_STEP3_INITIALIZETEXT."\" class=\"mainbox_proceed\"/>\n";

    if ($successInit === true)
    {
        echo "            <input type=\"submit\" value=\"".LANG_STEP3_COMPLETETEXT."\" class=\"mainbox_proceed\"/>\n";
    }

    echo "          </form>\n".
         "        </div>\n".
         "      </div>\n".
         "    </div>\n";
}
else if ($step == 4)
{
    echo "    <div class=\"mainbox\">\n".
         "      <div class=\"mainbox_header\">\n".
         "        <h1 class=\"mainbox_header_h1\">".LANG_STEP4_HEADER."</h1>\n".
         "      </div>\n".
         "      <div class=\"mainbox_body\">\n".
         "        <p>\n".
         "          ".LANG_STEP4_COMPLETETEXT."\n".
         "        </p>\n".
         "        <div>\n".
         "          <form action=\"../index.php\" method=\"post\">\n".
         "            <input type=\"hidden\" name=\"install_done\" value=\"install_done\"/>\n".
         "            <input type=\"submit\" value=\"".LANG_STEP4_EXITTEXT."\" class=\"mainbox_proceed\"/>\n".
         "          </form>\n".
         "        </div>\n".
         "      </div>\n".
         "    </div>\n";
}

echo "  </body>\n".
     "</html>\n";



?>
