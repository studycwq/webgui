<?PHP
/* Copyright 2005-2021, Lime Technology
 * Copyright 2012-2021, Bergware International.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
?>
<?
// remove malicious code appended after variable assignment
function unscript($text) {
  return trim(preg_split('/[;|&\?=]/',untangle($text))[0]);
}
// remove malicious HTML elements
function untangle($text) {
  return preg_replace('#<.+?>(.*?)</.+?>#','',html_entity_decode($text));
}
// remove malicious code appended after string variable
function unbundle($text) {
  return trim(preg_split('/[;|\?=]/',preg_replace(["#['\"](.*?)['\"];?.+$#","#[()\[\]/\\&`]#"],'',html_entity_decode($text)))[0]);
}
?>
