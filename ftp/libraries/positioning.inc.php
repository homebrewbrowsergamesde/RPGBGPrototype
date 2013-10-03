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
 * @file $/libraries/positioning.inc.php
 * @brief Class handles the users position.
 * @author Stephan Kreutzer
 * @since 2013-10-03
 */



/**
 * @class Positioning
 * @todo Check is_numeric() === true for x and y.
 */
class Positioning
{
    public function __construct($userID)
    {
        $this->userID = $userID;
        $this->x = 0;
        $this->y = 0;
        $this->default = true;

        if (isset($_SESSION['positionX']) === true &&
            isset($_SESSION['positionY']) === true)
        {
            $this->default = false;

            $this->x = $_SESSION['positionX'];
            $this->y = $_SESSION['positionY'];
        }
        else
        {
            require_once(dirname(__FILE__)."/database.inc.php");

            if (Database::Get()->IsConnected() === true)
            {
                $result = Database::Get()->Query("SELECT `positionX`,\n".
                                                 "    `positionY`\n".
                                                 "FROM `".Database::Get()->GetPrefix()."user`\n".
                                                 "WHERE `id`=?\n",
                                                 array($this->userID),
                                                 array(Database::TYPE_INT));

                if (is_array($result) === true)
                {
                    if (count($result) >= 1)
                    {
                        $this->default = false;

                        $this->x = $result[0]['positionX'];
                        $this->y = $result[0]['positionY'];
                        $_SESSION['positionX'] = $this->x;
                        $_SESSION['positionY'] = $this->y;
                    }
                }
            }
        }
    }

    public function GetPosition()
    {
        return array('x' => $this->x, 'y' => $this->y);
    }

    public function SetPosition($x, $y)
    {
        require_once(dirname(__FILE__)."/database.inc.php");

        if (Database::Get()->IsConnected() !== true)
        {
            return -1;
        }

        if (Database::Get()->ExecuteUnsecure("UPDATE `".Database::Get()->GetPrefix()."user`\n".
                                             "SET `positionX`='".$x."',\n".
                                             "    `positionY`='".$y."'\n".
                                             "WHERE `id`=".$this->userID."\n") !== true)
        {
            return -2;
        }

        $this->default = false;
        $this->x = $x;
        $this->y = $y;

        $_SESSION['positionX'] = $this->x;
        $_SESSION['positionY'] = $this->y;

        return 0;
    }

    protected $x;
    protected $y;
    protected $default;
    protected $userID;
}



?>
