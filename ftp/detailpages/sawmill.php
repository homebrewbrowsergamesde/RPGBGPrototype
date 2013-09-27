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
 * @file $/detailpages/sawmill.inc.php
 * @author Stephan Kreutzer
 * @since 2012-04-11
 */



require_once(dirname(__FILE__)."/../libraries/gamelib.inc.php");

echo generateHTMLHeader("Sägewerk");


echo "<p>\n".
     "  Hier kannst du nichts tun.\n".
     "</p>\n".
     generateHTMLLeaveDetailPageForm("Verlassen");


echo generateHTMLFooter();

?>
