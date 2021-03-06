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
 * @file $/libraries/gamelib.inc.php
 * @brief All basic game functionality and some GUI helpers.
 * @author Stephan Kreutzer
 * @since 2012-06-02
 */



require_once(dirname(__FILE__)."/database.inc.php");



function generateHTMLHeader($title)
{
    $session = session_start();

    $html =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
             "<!DOCTYPE html\n".
             "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
             "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml-strict.dtd\">\n".
             "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
             "    <head>\n".
             "        <title>".$title."</title>\n".
             "        <meta http-equiv=\"expires\" content=\"1296000\" />\n".
             "        <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\" />\n".
             "    </head>\n".
             "    <body>\n".
             "        <div>\n";

    if ($session === true)
    {
        $session = isset($_SESSION['user_id']);
    }

    if ($session !== true)
    {
        $html .= "          Bitte erst einloggen.\n".
                 "        </div>\n".
                 "    </body>\n".
                 "</html>\n";

        echo $html;

        exit();
    }

    return $html;
}

function generateHTMLFooter()
{
    $html = "        </div>\n".
            "    </body>\n".
            "</html>\n".
            "\n";

    return $html;
}

/**
 * @param[in] $name Attribut muss zur direkten Verwendung in SQL-Anweisung
 *     vorbereitet worden sein!
 * @param[in] $passwort Attribut muss zur direkten Verwendung in SQL-Anweisung
 *     vorbereitet worden sein!
 */
function insertNewUser($name, $password)
{
    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    if (Database::Get()->BeginTransaction() !== true)
    {
        return -2;
    }

    $salt = md5(uniqid(rand(), true));
    $password = hash('sha512', $salt.$password);

    $id = Database::Get()->Insert("INSERT INTO `".Database::Get()->GetPrefix()."user` (`id`,\n".
                                 "    `name`,\n".
                                 "    `salt`,\n".
                                 "    `password`,\n".
                                 "    `positionX`,\n".
                                 "    `positionY`)\n".
                                 "VALUES (?, ?, ?, ?, ?, ?)\n",
                                 array(NULL, $name, $salt, $password, 0, 0),
                                 array(Database::TYPE_NULL, Database::TYPE_STRING, Database::TYPE_STRING, Database::TYPE_STRING, Database::TYPE_INT, Database::TYPE_INT));

    if ($id <= 0)
    {
        Database::Get()->RollbackTransaction();
        return -4;
    }


    // Initialize game data of the user.

    /**
     * @todo Inventory code based on old MySQL function calls.
     */
    /*
    require_once(dirname(__FILE__)."/inventorylib.inc.php");

    if (mysql_query("INSERT INTO `inventory` (`user_id`,\n".
                    "    `type`,\n".
                    "    `amount`)\n".
                    "VALUES\n".
                    "(".$id.", ".INVENTORY_TALER.", 10),\n".
                    "(".$id.", ".INVENTORY_BROETCHEN.", 0),\n".
                    "(".$id.", ".INVENTORY_SCHLUESSEL.", 0)\n",
                    $mysql_connection) !== true)
    {
        mysql_query("ROLLBACK", $mysql_connection);
        return -6;
    }
    */

    if (Database::Get()->CommitTransaction() === true)
    {
        return $id;
    }

    Database::Get()->RollbackTransaction();
    return -7;
}

function setPosition($userID, $positionX, $positionY)
{
    if (Database::Get()->IsConnected() !== true)
    {
        return -1;
    }

    if (Database::Get()->ExecuteUnsecure("UPDATE `".Database::Get()->GetPrefix()."user`\n".
                                         "SET `positionX`='".$positionX."',\n".
                                         "    `positionY`='".$positionY."'\n".
                                         "WHERE `id`=".$userID."\n") !== true)
    {
        return -2;
    }

    $_SESSION['positionX'] = $positionX;
    $_SESSION['positionY'] = $positionY;

    return 0;
}

function getUserVariables($userID, $names)
{
    global $mysql_connection;

    if ($mysql_connection == false)
    {
        return NULL;
    }

    if (is_array($names) !== true)
    {
        return NULL;
    }

    $sqlString = "SELECT `variable`,\n".
                 "    `value`\n".
                 "FROM `variables_user`\n".
                 "WHERE (";

    {
        $actualNames = 0;

        foreach ($names as $name)
        {
            if (is_string($name) === true)
            {
                $sqlString .= "`variable` LIKE '".$name."' OR ";
                $actualNames++;
            }
        }

        if ($actualNames < count($names))
        {
            echo "<p>\n".
                 "  Von den Benutzervariablen ";

            foreach ($names as $name)
            {
                echo "<code>".$name."</code> ";
            }

            echo "sind nur ".$actualNames." Variablennamen gültig.\n".
                 "</p>\n";

            return NULL;
        }

        if ($actualNames <= 0)
        {
            echo "<p>\n".
                 "  Keine Benutzervariablen zum Auslesen.\n".
                 "</p>\n";

            return NULL;
        }
    }

    $sqlString = substr($sqlString, 0, -4);
    $sqlString .= ") AND `user_id`=".$userID."\n";

    $variables = mysql_query($sqlString, $mysql_connection);

    if ($variables == false)
    {
        return NULL;
    }

    {
        $result = array();

        while ($temp = mysql_fetch_assoc($variables))
        {
            if (isset($result[$temp['variable']]) == true)
            {
                echo "<p>\n".
                     "  Benutzervariable <code>".$temp['variable']."</code> doppelt in der\n".
                     "  Datenbank vorhanden.\n".
                     "</p>\n";
            }
        
            $result[$temp['variable']] = $temp['value'];
        }

        mysql_free_result($variables);
        $variables = $result;
    }
    

    if (is_array($variables) !== true)
    {
        echo "<p>\n".
              "  Fehlgeschlagen, die Benutzervariablen ";
    
        foreach ($names as $name)
        {
            echo "<code>".$name."</code> ";
        }

        echo "auszulesen.\n".
             "</p>\n";
             
        return NULL;
    }
    
    $missing = array();
    
    foreach ($names as $name)
    {
        if (array_key_exists($name, $variables) == false)
        {
            $missing[] = $name;
        }
    }
    
    if (count($missing) > 0)
    {
        echo "<p>\n".
              "  Aus den Benutzervariablen ";
    
        foreach ($names as $name)
        {
            echo "<code>".$name."</code> ";
        }
        
        echo "konnten ";
        
        foreach ($missing as $name)
        {
            echo "<code>".$name."</code> ";
        }

        echo "nicht ausgelesen werden.\n".
             "</p>\n";

        return NULL;
    }

    return $variables;
}

function setUserVariables($userID, $variables)
{
    global $mysql_connection;

    if ($mysql_connection == false)
    {
        return -1;
    }

    if (is_array($variables) !== true)
    {
        return -2;
    }


    {
        $actualVariables = 0;

        foreach ($variables as $name => $value)
        {
            if (is_string($name) === true)
            {
                $actualVariables++;
            }
        }

        if ($actualVariables < count($variables))
        {
            echo "<p>\n".
                 "  Von den Benutzervariablen ";

            foreach ($variables as $name => $value)
            {
                echo "<code>".$name."</code> ";
            }

            echo "sind nur ".$actualVariables." Variablennamen gültig.\n".
                 "</p>\n";

            return -3;
        }

        if ($actualVariables <= 0)
        {
            echo "<p>\n".
                 "  Keine Benutzervariablen zum Schreiben.\n".
                 "</p>\n";

            return -4;
        }
    }


    if (mysql_query("BEGIN", $mysql_connection) !== true)
    {
        return -5;
    }

    foreach ($variables as $name => $value)
    {
        if (mysql_query("UPDATE `variables_user`\n".
                        "SET `value`='".((string)$value)."'\n".
                        "WHERE `variable` LIKE '".$name."' AND\n".
                        "    `user_id`=".$userID."\n",
                        $mysql_connection) !== true)
        {
            echo "<p>\n".
                 "  Setzen der Benutzervariable <code>".$name."</code> auf <tt>'".$value."'</tt>\n".
                 "  fehlgeschlagen.\n".
                 "</p>\n";

            mysql_query("ROLLBACK", $mysql_connection);
            return -6;
        }
    }

    if (mysql_query("COMMIT", $mysql_connection) !== true)
    {
        return -7;
    }

    return 0;
}

function getGlobalVariables($names)
{
    global $mysql_connection;

    if ($mysql_connection == false)
    {
        return NULL;
    }

    if (is_array($names) !== true)
    {
        return NULL;
    }

    $sqlString = "SELECT `variable`,\n".
                 "    `value`\n".
                 "FROM `variables_global`\n".
                 "WHERE (";

    {
        $actualNames = 0;

        foreach ($names as $name)
        {
            if (is_string($name) === true)
            {
                $sqlString .= "`variable` LIKE '".$name."' OR ";
                $actualNames++;
            }
        }

        if ($actualNames < count($names))
        {
            echo "<p>\n".
                 "  Von den globalen Variablen ";

            foreach ($names as $name)
            {
                echo "<code>".$name."</code> ";
            }

            echo "sind nur ".$actualNames." Variablennamen gültig.\n".
                 "</p>\n";

            return NULL;
        }

        if ($actualNames <= 0)
        {
            echo "<p>\n".
                 "  Keine globalen Variablen zum Auslesen.\n".
                 "</p>\n";

            return NULL;
        }
    }

    $sqlString = substr($sqlString, 0, -4);
    $sqlString .= ")\n";

    $variables = mysql_query($sqlString, $mysql_connection);

    if ($variables == false)
    {
        return NULL;
    }

    {
        $result = array();

        while ($temp = mysql_fetch_assoc($variables))
        {
            if (isset($result[$temp['variable']]) == true)
            {
                echo "<p>\n".
                     "  Globale Variable <code>".$temp['variable']."</code> doppelt in der\n".
                     "  Datenbank vorhanden.\n".
                     "</p>\n";
            }

            $result[$temp['variable']] = $temp['value'];
        }

        mysql_free_result($variables);
        $variables = $result;
    }
    

    if (is_array($variables) !== true)
    {
        echo "<p>\n".
              "  Fehlgeschlagen, die globalen Variablen ";
    
        foreach ($names as $name)
        {
            echo "<code>".$name."</code> ";
        }

        echo "auszulesen.\n".
             "</p>\n";
             
        return NULL;
    }
    
    $missing = array();
    
    foreach ($names as $name)
    {
        if (array_key_exists($name, $variables) == false)
        {
            $missing[] = $name;
        }
    }
    
    if (count($missing) > 0)
    {
        echo "<p>\n".
              "  Aus den globalen Variablen ";
    
        foreach ($names as $name)
        {
            echo "<code>".$name."</code> ";
        }
        
        echo "konnten ";
        
        foreach ($missing as $name)
        {
            echo "<code>".$name."</code> ";
        }

        echo "nicht ausgelesen werden.\n".
             "</p>\n";

        return NULL;
    }

    return $variables;
}

function setGlobalVariables($variables)
{
    global $mysql_connection;

    if ($mysql_connection == false)
    {
        return -1;
    }

    if (is_array($variables) !== true)
    {
        return -2;
    }


    {
        $actualVariables = 0;

        foreach ($variables as $name => $value)
        {
            if (is_string($name) === true)
            {
                $actualVariables++;
            }
        }

        if ($actualVariables < count($variables))
        {
            echo "<p>\n".
                 "  Von den globalen Variablen ";

            foreach ($variables as $name => $value)
            {
                echo "<code>".$name."</code> ";
            }

            echo "sind nur ".$actualVariables." Variablennamen gültig.\n".
                 "</p>\n";

            return -3;
        }

        if ($actualVariables <= 0)
        {
            echo "<p>\n".
                 "  Keine globalen Variablen zum Schreiben.\n".
                 "</p>\n";

            return -4;
        }
    }


    if (mysql_query("BEGIN", $mysql_connection) !== true)
    {
        return -5;
    }

    foreach ($variables as $name => $value)
    {
        if (mysql_query("UPDATE `variables_global`\n".
                        "SET `value`='".((string)$value)."'\n".
                        "WHERE `variable` LIKE '".$name."'\n",
                        $mysql_connection) !== true)
        {
            echo "<p>\n".
                 "  Setzen der globalen Variable <code>".$name."</code> auf <tt>'".$value."'</tt>\n".
                 "  fehlgeschlagen.\n".
                 "</p>\n";

            mysql_query("ROLLBACK", $mysql_connection);
            return -6;
        }
    }

    if (mysql_query("COMMIT", $mysql_connection) !== true)
    {
        return -7;
    }

    return 0;
}

/**
 * @param[in] $options Assoziatives Array, welches den CheckBox-Wert
 *     als Key und den Anzeigetext als Wert enthält.
 */
function generateHTMLChooseForm($name, $options)
{
    $form = "<form action=\"game.php\" method=\"post\">\n";

    if (is_array($options) === true)
    {
        foreach ($options as $option => $display)
        {
            $form .= "  <input type=\"radio\" name=\"".$name."\" value=\"".$option."\" />".$display."<br />\n";
        }
    }

    $form .= "  <input type=\"submit\" value=\"OK\" /><br />\n".
             "</form>\n";

    return $form;
}

/**
 * @param[in] $target Name der Detailseite, die von dem Formular angebrowst werden soll.
 *     Eine Datei mit dem übergebenen Namen und Endung *.php muss im Unterordner detailpages
 *     vorliegen.
 * @param[in] $name Assoziativer Name des Array-Elements der $_POST-Daten, unter welchem
 *     der übermittelte Wert (\p $value) des Formulars an die Zielseite (\p $target mit Endung
 *     .php im Unterordner detailpages) bereitgestellt wird.
 * @param[in] $value Wert, der bei Übermittlung des Formulars in den $_POST-Daten der Zielseite
 *     (\p $target mit Endung .php im Unterordner detailpages) gesendet wird.
 * @param[in] $display Beschriftung der Auswahl-Option.
 */
function generateHTMLDetailPageForm($target, $name, $value, $display)
{
    if (file_exists(dirname(__FILE__)."/../detailpages/".$target.".php") === true)
    {
        return "<form action=\"../detailpages/".$target.".php\" method=\"post\">\n".
               "  <input type=\"radio\" name=\"".$name."\" value=\"".$value."\"/>".$display."<br/>\n".
               "  <input type=\"submit\" value=\"OK\" /><br />\n".
               "</form>\n";
    }
    else
    {
        return "";
    }
}

function generateHTMLLeaveDetailPageForm($display)
{
    return "<form action=\"../gui/game.php\" method=\"post\">\n".
           "  <input type=\"radio\" name=\"leave_detail_page\" value=\"leave_detail_page\"/>".$display."<br/>\n".
           "  <input type=\"submit\" value=\"OK\" /><br />\n".
           "</form>\n";
}



?>
