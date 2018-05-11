<!DOCTYPE html>
<html>
<?php
//WarWords.php
    include "./common_papers.inc";
	
?>	

	<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
	
    <title>WarWords</title>
		<style>
		/* Make the sample page fill the window. */
		 
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		} 
		</style>
		
<?php 
	global $url, $data, $WarWords, $WarWordsSort, $lenk, $ckrb, $link_id;
	global $InstType;
    $link_id = db_connect();
	if (!$link_id) die(sql_error());

	$url = $_GET["URL"];
	$lenk = $_GET["lenk"];
	$InstType = $_REQUEST["InstType"];
	//echo "InstType:$InstType<br>";
	if ($InstType == "Red") {
?>		
	<style>b {color: red; strong}</style>
<?php 
	} else {
?>		
	<style>b {strong}</style>
<?php 
	}	
	
	
	// echo "url:$url<br>";
	// https://stackoverflow.com/questions/8198386/how-can-i-import-html-with-php
	
	
	if ($url == "" || $lenk = 0 || $WarWords == "") {
		$WarWords = array();
		// read in the list of war words
		$qry = "SELECT Word FROM WarWords ORDER BY Sort DESC, Word";
		$rs = mysqli_query($link_id, $qry);
		$lenk = mysqli_num_rows($rs);
		//echo "i=$i qry=$qry<br>";
        for ($i = 0; $i< $lenk; $i++) {     
			mysqli_data_seek($rs, $i);
			$row = mysqli_fetch_row($rs);
			$WarWords[$i] = $row[0];
			//echo $WarWords[$i] .", ";
		}
		$qry = "SELECT Word FROM WarWords ORDER BY Word";
		$rs = mysqli_query($link_id, $qry);
		$lenk = mysqli_num_rows($rs);
		//echo "i=$i qry=$qry<br>";
        for ($i = 0; $i< $lenk; $i++) {     
			mysqli_data_seek($rs, $i);
			$row = mysqli_fetch_row($rs);
			$WarWordsSort[$i] = $row[0];
			//echo $WarWords[$i] .", ";
		}
		if ($url == "") {
			$url = "https://www.modelsw.com/wbw/WarWords.php";		
		}
	}
?>	

	</head>

    <body>
	<form>
	<input type="submit" name="Submit" value="Update" > 
    URL:<input type="text" name="URL" size="120" value="<?php echo $url ?>" />  
	<input type="checkbox" name="InstType" value="Red" 
               <?php if ($InstType=='Red') { echo "checked"; } ?>
               onclick="activeLink( this )">Red
	<input type="hidden" name="WarWords" value=<?php echo $WarWords ?>" />	
	<input type="hidden" name="WarWordsSort" value=<?php echo $WarWordsSort ?>" />	
	</form>	
    
	
<?php
	global $WarWords, $WarWordsSort, $lenk, $url, $case, $ckrb;
	$k = 0;
	//$WarWords = ["gun", "fight", "ammunition", "rifle"]; // test 
	$lenk = sizeof($WarWords);
	$pre = [' ', ',', '"', '(', '>', '“'];
	$post = [' ', '.', ',', '"', '?', ')', '”'];
	//echo "lenk=$lenk<br>";
 	if ($url == "https://www.modelsw.com/wbw/WarWords.php") {
		echo "WarWords is a subset of war and violence words, phrases, and metaphors found in:<br>";
		echo "https://myvocabulary.com/ (word-lists: War and Violence) -- used with permission<br>";
		echo "https://en.wikipedia.org/wiki/Category:Metaphors_referring_to_war_and_violence<br>";
		echo "And http://knowgramming.com/war_metaphors.htm -- used with permission*<br>";
		echo "Select a web page link and paste it into the URL above and click Update. The following words and phrases will be set to <b>bold</b>:<br><br>";
		while ($k<$lenk) {	
			echo $WarWordsSort[$k] . ", ";
			$k++;	
		}
		include "./footer.inc";
	} else {		
		$data = file_get_contents($url);
		$datalc = strtolower($data);		
		while ($k <$lenk) {	 // go through the list of war words backwards to get the long words first
			$ww = $WarWords[$k];          // gun 
			$l = strlen($ww);             // 3 
			$pl = 0;					  // is it plural
			$j = strpos($datalc, $ww);      // find it in data 
			//echo "new word: j=$j l=$l ww=$ww <br>";
			while ($j > 0) {			  // found
				if($datalc[$j+$l] == "s") {     // is it plural  -- it could be or not -- there may be letters after the s.
					$pl = 1;
				}
				//echo "j=$j l=$l ww=$ww pl=$pl<br>";
				$ck = 0;                      // check before and after $ww
				for ($n=0; $n<6; $n++) {        
					if($datalc[$j-1] == $pre[$n]) { // is the character ahead of $ww a space, comma, quote, or open paren
						$ck++;
						//echo "ahead is $pre[$n] n=$n <br>";
						break; 
					}
				}
				for ($n=0; $n<7; $n++) {        
					if($datalc[$j+$l+$pl] == $post[$n]) { // is the character after $ww a space, period, comma, quote, question, or close paren
						$ck++;
						//echo "after is $post[$n] n=$n <br>";
						break; 
					}
				}
				if ($ck == 2) { 	
					$datalc = substr_replace($datalc,"</b>", $j+$l+$pl, 0);  // insert the ending bold
					$datalc = substr_replace($datalc,"<b>", $j, 0);	// insert the leading bold
					$data = substr_replace($data,"</b>", $j+$l+$pl, 0);  // insert the ending bold
					$data = substr_replace($data,"<b>", $j, 0);	// insert the leading bold
					//echo "inserted bold at $j and end bold at $j+$l <br>";
				}
				$j = strpos($datalc, $ww, $j+$l+$pl+7); // continue looking for the same word further into data
				$pl=0;
			}
			$k++;
		}	
		echo $data;
	}
	
	
?>	

	</body>
</html>
