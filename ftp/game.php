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
 * @details All paths within the HTML output have to be relative to this file.
 * @author Stephan Kreutzer
 * @since 2012-04-17
 */



session_start();

// Avoid collision of defines for game.php and for the view.
require_once("libraries/languagelib.inc.php");
require_once(getLanguageFile("game"));


$html = "";
$viewHTML = "";
$viewCSS = "";
$error = "";

if (strlen($error) == 0)
{
    if (isset($_SESSION['user_id']) !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_INVALIDSESSION."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

if (strlen($error) == 0)
{
    if (is_numeric($_SESSION['user_id']) !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_INVALIDSESSION."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

require_once("libraries/database.inc.php");

if (strlen($error) == 0)
{
    if (Database::Get()->IsConnected() !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_DBCONNECTFAILED."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

$view = "main";

if (strlen($error) == 0)
{
    if (isset($_SESSION['view']) === true)
    {
        $view = $_SESSION['view'];
    }
    else
    {
        // Set view to default.
        $_SESSION['view'] = $view;
    }

    if (file_exists("./gui/views/".$view.".inc.php") !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_NOVIEW_BEFORE.$view.LANG_NOVIEW_AFTER."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}


include_once("./gui/views/".$view.".inc.php");


$handler = "";

if (strlen($error) == 0)
{
    $handler = "VIEWHANDLER_".strtoupper($view);

    if (function_exists($handler) !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_NOVIEW_BEFORE.$view.LANG_NOVIEW_AFTER."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

if (strlen($error) == 0)
{
    if (is_callable($handler) !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_NOVIEW_BEFORE.$view.LANG_NOVIEW_AFTER."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

if (strlen($error) == 0)
{
    try
    {
        $result = $handler();

        if (is_string($result) === true)
        {
            $viewHTML .= "        <div class=\"mainbox\">\n".
                         "          <div class=\"mainbox_body\">\n".
                         $result."\n".
                         "          </div>\n".
                         "        </div>\n";
        }
    }
    catch (Exception $ex)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_ERROR."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

$viewChanged = false;

if (strlen($error) == 0)
{
    // Check if view was changed due to $_GET or $_POST input
    // to the previous, old view - if so, load the new view.

    if ($view != $_SESSION['view'])
    {
        // View was changed.
        $view = $_SESSION['view'];
        $viewChanged = true;

        if (file_exists("./gui/views/".$view.".inc.php") !== true)
        {
            $error = "        <div class=\"mainbox\">\n".
                     "          <div class=\"mainbox_body\">\n".
                     "            <p class=\"error\">\n".
                     "              ".LANG_NOVIEW_BEFORE.$view.LANG_NOVIEW_AFTER."\n".
                     "            </p>\n".
                     "          </div>\n".
                     "        </div>\n";
        }
    }
}


// If view wasn't changed, the "once" will ensure that the file won't get loaded
// a second time.
include_once("./gui/views/".$view.".inc.php");


if (strlen($error) == 0 &&
    $viewChanged == true)
{
    if ($handler == "VIEWHANDLER_".strtoupper($view))
    {
        // The new handler has the same name as the old handler. This isn't
        // supposed to happen and should be impossible for game.php, too.
    }

    $handler = "VIEWHANDLER_".strtoupper($view);

    if (function_exists($handler) !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_NOVIEW_BEFORE.$view.LANG_NOVIEW_AFTER."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

if (strlen($error) == 0 &&
    $viewChanged == true)
{
    if (is_callable($handler) !== true)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_NOVIEW_BEFORE.$view.LANG_NOVIEW_AFTER."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}

if (strlen($error) == 0 &&
    $viewChanged == true)
{
    try
    {
        $result = $handler();

        if (is_string($result) === true)
        {
            $viewHTML .= "        <div class=\"mainbox\">\n".
                         "          <div class=\"mainbox_body\">\n".
                         $result."\n".
                         "          </div>\n".
                         "        </div>\n";
        }
    }
    catch (Exception $ex)
    {
        $error = "        <div class=\"mainbox\">\n".
                 "          <div class=\"mainbox_body\">\n".
                 "            <p class=\"error\">\n".
                 "              ".LANG_ERROR."\n".
                 "            </p>\n".
                 "          </div>\n".
                 "        </div>\n";
    }
}


if (strlen($error) == 0)
{
    // If the view has changed, only the CSS handler of the new view
    // gets called.

    $handler = "CSSHANDLER_".strtoupper($view);

    if (function_exists($handler) === true)
    {
        if (is_callable($handler) === true)
        {
            $cssList = $handler();

            if (is_array($cssList) === true)
            {
                if (count($cssList) > 0)
                {
                    foreach ($cssList as $css)
                    {
                        if (is_string($css) === true)
                        {
                            if (file_exists("./".$css) === true)
                            {
                                $viewCSS .= "        <link rel=\"stylesheet\" type=\"text/css\" href=\"./".$css."\"/>\n";
                            }
                            else
                            {
                                // CSS link configured, but no corresponding file present.
                            }
                        }
                    }
                }
            }
        }
    }
}


$html =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
         "<!DOCTYPE html\n".
         "    PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n".
         "    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n".
         "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n".
         "    <head>\n".
         "        <title>".LANG_PAGETITLE."</title>\n".
         "        <link rel=\"stylesheet\" type=\"text/css\" href=\"mainstyle.css\"/>\n";

if (strlen($error) == 0)
{
    $html .= $viewCSS;
}

$html .= "        <meta http-equiv=\"expires\" content=\"1296000\"/>\n".
         "        <meta http-equiv=\"content-type\" content=\"application/xhtml+xml; charset=UTF-8\"/>\n".
         "    </head>\n".
         "    <body>\n";

if (strlen($error) == 0)
{
    $html .= $viewHTML;
}
else
{
    $html .= $error;
}

$html .= "    </body>\n".
         "</html>\n";


echo $html;



?>
