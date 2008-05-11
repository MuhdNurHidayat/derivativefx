<?php
/*
Copyright Luxo 2008

This file is part of derivativeFX.

    derivativeFX is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    derivativeFX is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with derivativeFX.  If not, see <http://www.gnu.org/licenses/>.
    
    */
include("functions.php");

if($_SERVER["REQUEST_METHOD"] != "POST")
{
header('Location: http://'. $_SERVER['SERVER_NAME'] .'/~luxo/derivativeFX/deri1.php');
die();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html style="direction: ltr;" lang="en">
<head>
  <meta content="text/html; charset=UTF-8"
 http-equiv="content-type">
  <title>derivativeFX</title>
  <meta content="Luxo" name="author">
  <meta content="Derivative Upload" name="description">
  <script type="text/javascript" src="js/prototype.js"></script>
  <script type="text/javascript">
  
  function showhide(id)
  {
  
    if($(id).style.display == 'none')
    {
    $(id).style.display = 'inline';
    }
    else
    {
    $(id).style.display = 'none';
    }
  
  }
  
  </script>
      <link rel="stylesheet" type="text/css" href="style/style.css">
</head>
<body style="direction: ltr;" class="bodynorm" onload="$('bodyload').show();$('loading').hide()">
<table
 style="background-color: rgb(210, 211, 210); width: 100%; height: 100%; text-align: left; margin-left: auto; margin-right: auto;"
 border="0" cellpadding="0" cellspacing="0" id="loading">
  <tbody>
    <tr>
      <td style="text-align: center; vertical-align: middle;"><pre><h1 style="color:red">Please wait...</h1><br />
<?php


echo"Start...<br />";
/*Funktion: Empfangen der Originale, Lizenz-"merge"
            Wenn OK: Generieren der Beschreibung
            Beschreibung(en) zusammensetzen (schwierig)
            Ausgeben
*/     
$images = array();
foreach($_POST as $key => $data)
{
$key  = trim($key);
$data = trim($data);
  //Bildnamen
  if(substr($key,0,9) == "original_")
  {
    if($data != "Image:" AND $data != "")
    {
      $data = bigfirst($data);
      $imagenr = substr($key,9);
      $licences = explode("|",trim($_POST["origliz_".$imagenr]));
      foreach($licences as $lic)
      {
        if(trim($lic) != "")
        {
          $images[trim($data)][] = trim($lic);
        } 
      }
    }
  }
}

function bigfirst($name)
{
 $name = str_replace(" ","_",$name);
  $a1 = substr($name,6,1);
  $a2 = substr($name,7);
  return "Image:".ucwords($a1).$a2;
}


$countimage = count($images);
if($countimage == 0){ die("no images received!"); }
echo"$countimage Image(s) received...<br />";
//Lizenzen und Bilder im Array $images gespeichert.
//Nun in verschiedene Kategorien einteilen
$categorys = array(
"PD" => array(), //Falls nur ein Bild übermittelt wird, kann diese Kat verwendet werden.
"GFDL" => array(),
"CC-by-sa" => array(),
"CC-by" => array(),
"CC-sa" => array(),
"FWL" => array(),
"MPL" => array(),
"FAL" => array(),
"GPL" => array(),
"LGPL" => array(),
"CeCILL" => array()
);

//Lizenzen in Kats aufteilen
foreach($images as $imagename => $licarray)
{
$imagelizok[$imagename] = false;
  foreach($licarray as $licence)
  {
  $vergeben = false;
    //GFDL
    if(substr($licence,0,4) == "GFDL" OR $licence == "Picswiss" OR $licence == "PolishSenateCopyright" OR $licence == "PolishPresidentCopyright" OR $licence == "Pressefotos Die Gruenen")
    {
      $categorys['GFDL'][$imagename] = $licence;
      $vergeben = true;
    }
    
    //GPL
    if($licence == "GPL")
    {
      $categorys['GPL'][$imagename] = $licence;
      $vergeben = true;
    }
    
    //LGPL
    if($licence == "LGPL")
    {
      $categorys['LGPL'][$imagename] = $licence;
      $vergeben = true;
    }
    
    //CeCILL
    if($licence == "CeCILL")
    {
      $categorys['CeCILL'][$imagename] = $licence;
      $vergeben = true;
    }
    
    //CC-Lizenzen****
    if(strtolower(substr($licence,0,3)) == "cc-")
    {
      //CC-by
      if(stristr($licence,"-by") AND !stristr($licence,"-sa"))
      {
        $categorys['CC-by'][$imagename] = $licence;
        $vergeben = true;
      }
      
      //CC-by-sa
      if(stristr($licence,"-by") AND stristr($licence,"-sa"))
      {
        $categorys['CC-by-sa'][$imagename] = $licence;
        $vergeben = true;
      }
      
      //CC-sa
      if(!stristr($licence,"-by") AND stristr($licence,"-sa"))
      {
        $categorys['CC-sa'][$imagename] = $licence;
        $vergeben = true;
      }
    
    }
    
    if($licence == "FAL")
    {
      $categorys['FAL'][$imagename] = $licence;
      $vergeben = true;
    }
    
    if($licence == "FWL")
    {
      $categorys['FWL'][$imagename] = $licence;
      $vergeben = true;
    }
    
    if($licence == "MPL")
    {
      $categorys['MPL'][$imagename] = $licence;
      $vergeben = true;
    }
    
    
    if(strtolower(substr($licence,0,3)) == "pd-")
    {
    $vergeben = true;
          $categorys['PD'][$imagename] = $licence;
      foreach($categorys as $catname => $notnotused)
      {
        $categorys[$catname][$imagename] = $licence; 
      }
    
    }
    
    $foroutstat[$licence] = $vergeben;
    if($vergeben == true)
    {
      $imagelizok[$imagename] = true;
    }
    else
    {
      echo"<span class='notexist'>unknown template {{".$licence."}} ... </span><br />";
    }
  }

  if($imagelizok[$imagename] == false)
  {
    echo"<span class='delete'>No known licence found in $imagename!</span><br />";
  }
  else
  {
    echo"<b>$imagename</b> is ok...<br />";
  }
}
//Fertig in Kats eingeteilt

//print_r($categorys);

$lizenzauswahl = array();

$isaccord = false;
foreach($categorys as $katname => $contarray)
{
  if($countimage == count($contarray))
  {
    // $contarray =  [Image:Horse.jpg] => GFDL
    echo"accordance in '$katname' ...<br />";
    $lizenzauswahl[] = $contarray;
    $liclic[] = $katname;
    $isaccord = true;
  }
}

if($isaccord == true)
{
  //Bildbeschreibung vorbereiten
  echo"loading description pages...<br />";
  $categorys = array();//vorbereiten
foreach($images as $imagename => $licarray)
{
    echo $imagename."...<br />";
    //query laden
    //http://commons.wikimedia.org/w/query.php?what=content|imageinfo&iihistory&format=txt&titles=Image:Beispiel.jpg|Image:Hund.jpg
    $url = "http://commons.wikimedia.org/w/api.php?action=query&prop=imageinfo&iilimit=50&iiprop=timestamp|user|comment|url|size|sha1|metadata&format=php&titles=".$imagename;
    $tempcache = file_get_contents($url) or die("<div class='notexist'>ERROR - connection to wikimedia server lost!</div><br />");
    $tempcache = unserialize($tempcache);
    $arkey = array_keys($tempcache["query"]["pages"]);
    if($tempcache["query"]["pages"][$arkey[0]] == "-1") { die("<div class='notexist'>ERROR - $imagename not found!</div>"); }
    
    $tempcache["query"]["pages"][$arkey[0]]["content"]["*"] = file_get_contents("http://commons.wikimedia.org/w/index.php?action=raw&title=".$imagename);
    $imagedatas[$imagename] = $tempcache["query"]["pages"][$arkey[0]];
    //Array mit Bildinfos erstellt, nun Beschreibung daraus parsen
    
    //Lizenzen auch in dieses Array um auf nächster Seite zu haben
    $imagedatas[$imagename]["licenses"] = $images[$imagename];
    
    
    //Nach {{information}} suchen
       
    $start = stripos($imagedatas[$imagename]["content"]["*"], "|Description");
    $end   = stripos($imagedatas[$imagename]["content"]["*"], "|Source");
    if($start AND $end)
    {
      $desc = substr($imagedatas[$imagename]["content"]["*"],$start,$end-$start );
      $desc = trim(substr(strstr($desc,"="),1));
    }
    else
    {
    //offenbar kein Information verwenet
    $desc = trim(substr($imagedatas[$imagename]["content"]["*"],0,stripos($imagedatas[$imagename]["content"]["*"],"{")));
    }
    
    $order   = array("\r\n", "\n", "\r");
    $replace = ' ';
    $desc = str_replace($order, $replace, $desc);
    
    $outputdescription .= " ".$desc;
    //Kategorien hinzufügen
    
  
  echo"Search categories with CommonSense...<br />";
  $tempcatar = catscan(substr($imagename,6));
  echo count($tempcatar)." categories found for ".substr($imagename,6)."...";
  $categorys = array_merge($categorys, $tempcatar);
    

}//Beschreibung fertig

//Lizenzen splitten
/*  <select name='license'>
  <option>GFDL</option>
  <optgroup label='CC-Licenses'>
  <option>cc-by</option>
  <option>cc-sa</option>
  <option>cc-by-sa</option>
  </optgroup>
  <option>GPL</option>
  <option>Public Domain</option>
  </select> 
  
  $categorys = array(
"FWL" => array(),
"MPL" => array(),
"FAL" => array(),
"GPL" => array(),
"LGPL" => array(),
"CeCILL" => array(),
"GFDL" => array(),
"CC-by" => array(),
"CC-sa" => array(),
"CC-by-sa" => array(),
"PD" => array() //Falls nur ein Bild übermittelt wird, kann diese Kat verwendet werden.
); */

//Templates
$licensesar = array(
"CC-by-sa" => "Cc-by-sa-3.0",
"CC-by" => "Cc-by-3.0",
"CC-sa" => "cc-sa-1.0",
"GFDL" => "GFDL",
"PD" => "PD-self",
"FWL" => "FWL",
"MPL" => "MPL",
"FAL" =>"FAL",
"GPL" => "GPL",
"LGPL" => "LGPL",
"CeCILL" => "CeCILL" );

foreach($liclic as $licgroup)
{
  $licenseausw .= "<option>".$licensesar[$licgroup]."</option>\n";
}




}
//loading-mitteilungen bis hier!
?>
        </pre>
      </td>
    </tr>
  </tbody>
</table>
<span id="bodyload" style="display:none">
<?php 
if($isaccord == false)
{
  
  //Keine Lizenzübereinstimmungen gefunden!
  echo"<h1>Sorry,</h1> your images can't be merged!<br /><br />The <b>licenses are incompatible</b> or the <b>licenses can't be detected</b>.<br />
  See above for all license tags the tool found for this images:<br /><br />\n<ul>";
  foreach($images as $imagename => $licarray)
  {
    //$imagelizok
    $starset1 = "";
    if($imagelizok[$imagename] == false) { $starset1 = "<sup class='marker'>2</sup>"; }
    echo"<li>$imagename $starset1</li><ul>";
    foreach($licarray as $liz)
    {
      $starset = "";
      if($foroutstat[$liz] == false) { $starset = "<sup class='marker'>1</sup>"; }
      echo "<li>{{".$liz."}} $starset</li>";
    }
    echo"</ul>";
  }
  echo"</ul>\n<br /><sup class='marker'>1.)</sup>Unknown templates<br />";
  echo"<sup class='marker'>2.)</sup>no known license found for this image!";
}
else
{
echo"<img src='derivativeFX_small.png' />";


echo"<form enctype='multipart/form-data' method='post' action='deri3.php' name='sendform'>Description:".helpcontent("description")." <br />

  <font style='font-style: italic;' size='-1'>for
the
  <code>|description=</code> -parameter in {{Information}}</font><br />
  <textarea name='data' style='display:none'>".base64_encode(serialize($imagedatas))."</textarea>
  <textarea cols='70' rows='10' name='description'>".htmlspecialchars($outputdescription)."</textarea><br />

  <br />
  <hr
 style=\"height: 1px; width: 50%; margin-left: 0px; margin-right: auto;\">

<div class='tempinc'>
  <input checked='checked' onClick=\"showhide('addreto')\" name='addtempret' value='true' type='checkbox'> Add Template
{{RetouchedPicture}} to the description".helpcontent("templateretouched")."
<span id='addreto'><br /><br />
  Changes: <input size='50' name='changestemp'><br />
  Editor: <input size='50' name='editor'> <small>Without wikisyntax, e.g. \"Harry\"</small>
</span></div>
<br /><a href=\"Javascript:showhide('othertemplates')\">Other Templates...</a><br />
<span id='othertemplates' style='display:none'>
<div class='tempinc'>
<input onClick=\"showhide('bwstemp')\" name='addbwstemp' value='true' type='checkbox'> Add Template {{Bilderwerkstatt}} to the description
<br/><span id='bwstemp'style='display:none'>
  Changes: <input size='50' name='changesbws'><br />
  </span>
</div><br />
<div class='tempinc'>
<input name='addfrbws' value='true' type='checkbox'> Add Template {{Atelier graphique}} to the description
</div>
<br />
<div class='tempinc'>
<input name='addfrkws' value='true' type='checkbox'> Add Template {{Atelier graphique carte}} to the description
</div></span>
  <br />
<hr
 style=\"height: 1px; width: 50%; margin-left: 0px; margin-right: auto;\">
Categories:<br />
<small><small>powered by <a href='/~daniel/WikiSense/CommonSense.php' target='_blank'>CommonSense</a></small></small><br />

<ul>";
$n = 1;
foreach($categorys as $cat)
{
  if(trim($cat) != "")
  {
    echo "<li><input type='checkbox' name='Category$n' value='$cat' /> ".$cat."</li>\n";
    $n = $n +1;
  }
}
if(count($categorys) == 0)
{  echo"<li><i>No categories found...</i></li>\n"; }
echo"</ul>
<hr
 style=\"height: 1px; width: 50%; margin-left: 0px; margin-right: auto;\">
License:".helpcontent("license")."<br />

  <select name='license'>
  $licenseausw
  </select>

  <br />
  <br />
  <hr
 style=\"height: 1px; width: 50%; margin-left: 0px; margin-right: auto;\">
You can change the whole summary on the next page.<br />
  <br />
  <input value='OK - Next' type='submit'><br />
  <br />

  <br />

</form>";


}
?>
<hr style="height: 2px; width: 60%;">
<div style="text-align: center;">by <a href="/%7Eluxo/">Luxo</a>
| <a href="http://commons.wikimedia.org/wiki/User_talk:Luxo">contact</a>
| <a
 href="http://meta.wikimedia.org/wiki/User:Luxo/Licenses#derivativeFX">license</a><br />
<br />
<a href="http://wiki.ts.wikimedia.org/view/Main_Page"><img
 style="border: 0px solid ; width: 88px; height: 31px;"
 alt="powered by Wikimedia Toolserver"
 title="powered by Wikimedia Toolserver"
 src="http://tools.wikimedia.de/images/wikimedia-toolserver-button.png"></a>&nbsp;</div>

</span>
</body>
</html>