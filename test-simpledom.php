<?php

error_reporting(E_ALL);
require_once('simplehtmldom.php');

$data = file_get_contents('extra/console-magneto-error.txt');

/*
 * 
   <div class="table block">
              <div class="content-title">Des erreurs sont survenues :</div>
              <div class="tr">
                <table width="584">
                  <tr>
                    <td width="85"><strong><span style="color: #cc0000">Cette combinaison de chaîne / service n'est enregistrable que sur le disque interne</span></strong></td>
		  </tr>

*/
$html = new simple_html_dom($data);
$list = $html->find('div div[class=tr] strong span[style=]');

foreach($list as $elem){
	$name = utf8_decode($elem->plaintext);
	echo "name = $name\n";
}

?>
