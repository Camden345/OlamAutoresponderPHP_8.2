<?php
// This file is part of Olam Autoresponder.
// Copyright (c) 2004-2007 Aaron Colman and Adaptive Business Design.
// Copyright (c) 2016 Anna Burdette, Benjamin Jobson, and David Reed.
//
// Olam Autoresponder is free software: you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation, version 2.
//
// Olam Autoresponder is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
//     along with Olam Autoresponder.  If not, see <http://www.gnu.org/licenses/>.

// PHP 8.1 deprecated passing null to string parameters. This function is used in makeSafe() and makeSemiSafe().
/*
function nullToString($thing) {
    if (is_null($thing)) {
        $thing = "";
    }
}
*/

/**
 * Allow these tags
 */
$allowedTags = '<center><u><table><td><tr><font><br><em><strong><h1><h2><h3><h4><h5><h6><b><i><a><ul><ol><li><pre><hr><blockquote><p><div><span><img>';

/**
 * Disallow these attributes/prefix within a tag
 */
$stripAttrib = 'javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|' .
    'onmousemove|onmouseout|onkeypress|onkeydown|onkeyup';

# Strips forbidden tags and attributes
function removeEvilTags($source)
{
    global $allowedTags;
    $source = strip_tags($source, $allowedTags);

    return preg_replace_callback('/<(.*?)>/i', function ($matches) {
        return '<' . removeEvilAttributes($matches[1]) . '>';
    }, $source);
}

# Strips forbidden attributes from a tag
function removeEvilAttributes($tagSource)
{
    global $stripAttrib;
    return stripslashes(preg_replace("/$stripAttrib/i", 'forbidden', $tagSource));
}

// Since PHP 5.4, get_magic_quotes_pgc has always returned "false".
// With that in mind, "myaddslashes()" had no practical difference from simply "addslashes()",
// so all instances of "myaddslashes($st)" are replaced with "addslashes($st)".

function makeSafe($UnsafeSource)
{
    return addslashes(htmlspecialchars(removeEvilTags(trim(nullToString($UnsafeSource))), ENT_QUOTES));
}

function makeSemiSafe($UnsafeSource)
{
    return addslashes(removeEvilTags(trim(nullToString($UnsafeSource))));
}

# Will output: <a href="forbiddenalert(1);" target="_blank" forbidden =" alert(1)">test</a>
# echo removeEvilTags('<a href="javascript:alert(1);" target="_blank" OnPheasantOver="alert(1)">test</a>');
?>
