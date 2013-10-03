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
 * @file $/gui/components/map.inc.php
 * @brief Class generates the map (as generic images) from the database.
 * @author Stephan Kreutzer
 * @since 2013-10-03
 */



class Map
{
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;

        if (($this->width % 2) == 0)
        {
            $this->width += 1;
        }

        if (($this->height % 2) == 0)
        {
            $this->height += 1;
        }

        if ($this->width < 1)
        {
            $this->width = 1;
        }

        if ($this->height < 1)
        {
            $this->height = 1;
        }
    }

    public function GetHTML($x, $y)
    {
        require_once(dirname(__FILE__)."/../../libraries/database.inc.php");

        if (Database::Get()->IsConnected() !== true)
        {
            return "";
        }

        $offsetX = ($this->width - 1) / 2;
        $offsetY = ($this->height - 1) / 2;

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
                                         array($x - $offsetX, $x + $offsetX, $y - $offsetY, $y + $offsetY),
                                         array(Database::TYPE_INT, Database::TYPE_INT, Database::TYPE_INT, Database::TYPE_INT));

        if (is_array($images) !== true)
        {
            return "";
        }

        $fields = $this->width * $this->height;

        if (count($images) !== $fields)
        {
            return "";
        }

        $html = "<div id=\"map\">";

        for ($i = 0; $i < $fields; $i++)
        {
            if (($i % $this->width) == 0 && $i > 0)
            {
                $html .= "<br/>";
            }

            $html .= "<img src=\"images/".$images[$i]['image_name']."\" style=\"border:0; padding:0; margin:0; vertical-align:bottom;\" width=\"40px\" height=\"40px\" alt=\"".$images[$i]['x']."/".$images[$i]['y']."\" title=\"".$images[$i]['x']."/".$images[$i]['y']."\"/>";
        }

        $html .= "</div>";

        return $html;
    }

    protected $width;
    protected $height;
}



?>
