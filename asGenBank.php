<?php
  $oldType = array();
  $newType = array();
  
  //BELOW ARE DEFINED THE CONVERSIONS THAT THIS SCRIPT DOES, THE FIRST IS ANNOTATED
  
  $oldType[] = "tag"; //This is the feature type as it is annotated in the registry
  $newType[] = "misc_signal";//This is the feature key as it should be in the GenBank file, valid results are listed at http://www.ncbi.nlm.nih.gov/collab/FT/
  $newQualName[] = "label"; //This is the qualifier, valid ones are listed at the same URL
  $newLabelPrefix[] = "tag: ";  //Sometimes information is lost in going from the registry to GenBank, this information can be returned by adding it in the label.  So in this case if a feature is a tag with label "LLVA" it will become "tag: LLVA".
  
  $oldType[] = "cds";
  $newType[] = "CDS";
  $newQualName[] = "gene";
  $newLabelPrefix[] = "";
  
  $oldType[] = "rbs";
  $newType[] = "RBS";
  $newQualName[] = "label";
  $newLabelPrefix[] = "";
  
  
  
  $oldType[] = "polya";
  $newType[] = "polyA_signal";
  $newQualName[] = "label";
  $newLabelPrefix[] = "";
  
  $oldType[] = "operator";
  $newType[] = "protein_binding";
  $newQualName[] = "label";
  $newLabelPrefix[] = "operator: ";
  
  $oldType[] = "binding";
  $newType[] = "misc_binding";
  $newQualName[] = "label";
  $newLabelPrefix[] = "";
  
  $oldType[] = "protein";
  $newType[] = "mat_peptide";
  $newQualName[] = "product";
  $newLabelPrefix[] = "";
  
  $oldType[] = "dna";
  $newType[] = "misc_feature";
  $newQualName[] = "label";
  $newLabelPrefix[] = "DNA: ";
  
  $oldType[] = "promoter";
  $newType[] = "promoter";
  $newQualName[] = "label";
  $newLabelPrefix[] = "";
  
  $oldType[] = "conserved";
  $newType[] = "misc_feature";
  $newQualName[] = "label";
  $newLabelPrefix[] = "Conserved: ";
  
  $oldType[] = "stem_loop";
  $newType[] = "stem_loop";
  $newQualName[] = "label";
  $newLabelPrefix[] = "";
  
  
  $oldType[] = "mutation";
  $newType[] = "misc_difference";
  $newQualName[] = "label";
  $newLabelPrefix[] = "Mut: ";
  
  $oldType[] = "s_mutation";
  $newType[] = "misc_difference";
  $newQualName[] = "label";
  $newLabelPrefix[] = "Silent Mut: ";
  
  $oldType[] = "primer_binding";
  $newType[] = "primer_bind";
  $newQualName[] = "label";
  $newLabelPrefix[] = "";
  
  $oldType[] = "barcode";
  $newType[] = "misc_feature";
  $newQualName[] = "label";
  $newLabelPrefix[] = "barcode: ";
  
  $oldType[] = "BioBrick";
  $newType[] = "misc_feature";
  $newQualName[] = "label";
  $newLabelPrefix[] = "";
  
  $oldType[] = "start";
  $newType[] = "misc_feature";
  $newQualName[] = "label";
  $newLabelPrefix[] = "start: ";
  
  $oldType[] = "stop";
  $newType[] = "misc_feature";
  $newQualName[] = "label";
  $newLabelPrefix[] = "stop: ";
  //END OF DEFINITIONS
  //WHAT SHOULD THE FEATURE KEY BE FOR NON-LISTED FEATURES? ENTER IT BELOW
  $otherTitleName = "gene";
  
  //DON'T MESS WITH CODE BELOW UNLESS YOU KNOW WHAT YOU ARE DOING
  
  if (isset($_GET['text'])) {
      //Allows a direct download, if requested
      header("content-type: plain/text");
  }
  
  
  $getPart = trim($_GET['part']); //allow accidental spaces, etc.
  
  $dom = new DomDocument();
  //get part XML from Registry API
  $dom->load("http://parts.igem.org/cgi/xml/part.cgi?part=" . $getPart);
  if ($part = $dom->getElementsByTagName("part")->item(0)) {
      //See if we have indeed got a part, if so continue
      //Get the part name
      $partname = $part->getElementsByTagName("part_name")->item(0)->nodeValue;
      //Get the part's short description
      $partdesc = strip_tags($part->getElementsByTagName("part_short_desc")->item(0)->nodeValue);
      //Get the part's sequence
      $partseq = trim($part->getElementsByTagName("seq_data")->item(0)->nodeValue);
      //Remove non-bases which can confuse the count
      $partseq = preg_replace("/[^ATGCatgc]/", "", $partseq);
      
      
      
      
      if (!isset($_GET['text'])) {
          //Display the menu unless a plain-text download is called for
?>
<html>
<head>
<title>
<?= $getPart ?> in GenBank format
</title>
<link  href="//fonts.googleapis.com/css?family=Nobile:regular,italic,bold,bolditalic" rel="stylesheet" type="text/css" >
<style type="text/css">
#title {
  font-family: 'Nobile', serif;
  font-size: 28px;
  font-style: normal;
  font-weight: 700;
  text-shadow: none;
  text-decoration: none;
  text-transform: none;
  letter-spacing: 0.073em;
  word-spacing: 0em;
  line-height: 1em;
  margin-bottom:20px;
}
#container{width:800px; margin:0 auto; background:white; padding:20px; text-align:left;}

	body{font-family:Arial; sans-serif;
	background-color:#191f46;
	margin:0;
	padding:0;
	text-align:center;
	}
</style>
</head>
<body>
<div id="container">
<div style="float:right; width:250px; text-align:right;font-size:12px; color:gray;">This tool was created by <a href="http://theo.io">Theo Sanderson</a> as part of the Cambridge iGEM team for 2010. You can read a full description <a href="http://2010.igem.org/Team:Cambridge/Tools/GenBank">here</a>
</div>
<div id="title"><span style="color:#69ba01">BioBrick</span>&rarr;<span style="color:#ba2d02">GenBank</span></div>
<strong>Part processed: </strong><a href="/gbdownload/<?=$getPart?>.gb" style="font-weight:bold">download as .gb file</a><br />
<pre>
<?php
      }
      
      //BELOW: Display GenBank format
?>LOCUS       <?= $partname?>          <?= strlen($partseq)?> bp    DNA    linear
DEFINITION  <?= $partdesc?>

FEATURES             Location/Qualifiers<?php
      $features = $part->getElementsByTagName("feature");
      foreach ($features as $feature) {
          $feature_type = $feature->getElementsByTagName("type")->item(0)->nodeValue;
          $feature_dir = $feature->getElementsByTagName("direction")->item(0)->nodeValue;
          $feature_start = $feature->getElementsByTagName("startpos")->item(0)->nodeValue;
          $feature_end = $feature->getElementsByTagName("endpos")->item(0)->nodeValue;
          $feature_title = $feature->getElementsByTagName("title")->item(0)->nodeValue;
          
          
          
              
              if (in_array($feature_type, $oldType)) {
                  //If the feature type is listed in our list of registry types
                  //Find the key of the oldType
                  $key = array_search($feature_type, $oldType);
                  $feature_type = $newType[$key];
				  $feature_title = $newLabelPrefix[$key].$feature_title;
                  $QualName = $newQualName[$key];
              } else {
                  
                  $QualName = $otherTitleName;
              }
              echo "\n";
              //5 spaces to the left of the feature type
              echo str_repeat(" ", 5);
              echo $feature_type;
              //Enough space after the type to display the range at col 22
              echo str_repeat(" ", 16 - strlen($feature_type));
              //This is how ranges are displayed in GenBank
              $range = $feature_start . ".." . $feature_end;
              if ($feature_dir == "reverse") {
                  //This is how backwards sequences are shown in GenBank
                  echo "complement($range)";
              } else {
                  
                  //Nothing need be done for forwards sequences
                  echo $range;
              }
			  //No need to add a Feature Qualifier at all if there is no label listed
			  if($QualName!="label" || strpos($feature_title," ")!==false){
				  $feature_title='"'.$feature_title.'"'; //Add quotes unless, it is a LABEL with no spaces (weird GenBank quirk)
			  }
			  if ($feature_title != "") {
              echo "\n" . str_repeat(" ", 21) . "/" . $QualName . '=' . $feature_title ;
          }
      }
      echo "\n" . "ORIGIN" . "\n";
$i = 1;
$partseqsplit = str_split($partseq, 60);
foreach ($partseqsplit as $seqline){
       echo str_repeat(' ', 9-strlen(strval($i))) . $i . ' ' . chunk_split($seqline, 10, ' ') . "\n";
       $i = $i + 60;
}

echo "//";
      
      if (!isset($_GET['text'])) {
?>
</pre>
</div>
</body>
</html>
<?php
      }
  } else {
      //If BioBrick not found
?>Error - did you make a typo in the part entry? You can go <a href="http://2010.igem.org/Team:Cambridge/Tool/GenBank">back</a> to the form.<?php
  }
?>
<?php
  $myFile = "genbank/log.txt";
  $fh = fopen($myFile, 'a') or die("can't open file");
  $stringData = $getPart . "\n";
  fwrite($fh, $stringData);
  fclose($fh);
?>