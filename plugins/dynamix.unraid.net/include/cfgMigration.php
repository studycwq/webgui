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
function update($url, $payload) {
  $ch = curl_init($url);
  curl_setopt($ch,CURLOPT_POST, true);
  curl_setopt($ch,CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
  $result = curl_exec($ch);
  curl_close($ch);
}

$dyn_cfg = '/boot/config/plugins/dynamix/dynamix.cfg';
$mys_cfg = '/boot/config/plugins/Unraid.net/myservers.cfg';

if (file_exists($dyn_cfg) && !file_exists($mys_cfg)) {
  $orig = parse_ini_file($dyn_cfg,true);
  $var = (array)parse_ini_file('/usr/local/emhttp/state/var.ini',true);
  $url = "http://localhost:".$var['PORT']."/update.php";

  // write [remote] section to myservers.cfg
  if(!empty($orig['remote'])) {
    $fields_mys_remote = [
      'csrf_token' => $var['csrf_token'],
      '#file'      => $mys_cfg,
      '#section'   => 'remote'
    ];
    foreach($orig['remote'] as $key => $value) {
      $fields_mys_remote[$key] = $value;
    }
    update($url, http_build_query($fields_mys_remote));
  }
  /*
  // write [wizard] section to myservers.cfg
  if(!empty($orig['wizard'])) {
    $fields_mys_wizard = [
      'csrf_token' => $var['csrf_token'],
      '#file'      => $mys_cfg,
      '#section'   => 'wizard'
    ];
    foreach($orig['wizard'] as $key => $value) {
      $fields_mys_wizard[$key] = $value;
    }
    update($url, http_build_query($fields_mys_wizard));
  }
  */
  // remove [remote] section from dynamix.cfg
  if(!empty($orig['remote'])) {
    $fields_dyn_remote = [
      'csrf_token' => $var['csrf_token'],
      '#file'      => $dyn_cfg,
      '#section'   => 'remote',
      '#cleanup'   => 'true'
    ];
    foreach($orig['remote'] as $key => $value) {
      $fields_dyn_remote[$key] = '';
    }
    update($url, http_build_query($fields_dyn_remote));
  }
  // remove [wizard] section from dynamix.cfg
  if(!empty($orig['wizard'])) {
    $fields_dyn_wizard = [
      'csrf_token' => $var['csrf_token'],
      '#file'      => $dyn_cfg,
      '#section'   => 'wizard',
      '#cleanup'   => 'true'
    ];
    foreach($orig['wizard'] as $key => $value) {
      $fields_dyn_wizard[$key] = '';
    }
    update($url, http_build_query($fields_dyn_wizard));
  }
  // remove [remote] and [wizard] section headings from dyn_cfg file
  $oldtext = file_get_contents($dyn_cfg); 
  $newtext = preg_replace ('/\[(remote|wizard)\]\n/', '', $oldtext);
  if (strcmp($oldtext, $newtext) !== 0) {
    file_put_contents($dyn_cfg, $newtext);
  }
}
if (!file_exists($mys_cfg)) touch($mys_cfg);

?>
