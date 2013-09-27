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
 * @file $/install/1_0.inc.php
 * @details This file might get copied to $/positioncode/1_0.inc.php by
 *     the installation routine.
 * @author Stephan Kreutzer
 * @since 2012-04-14
 */



require_once(dirname(__FILE__)."/../gamelib.inc.php");



return "<p>\n".
       "  Sägewerk &bdquo;Hutmannsweiler&ldquo;.\n".
       "</p>\n".
       generateHTMLDetailPageForm("saegewerk", "init", "init", "Sägewerk betreten.");


?>
