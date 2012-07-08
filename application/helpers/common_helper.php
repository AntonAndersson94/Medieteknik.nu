<?php

function encrypt_password($password) {
	$salt = 'MT_sillystring_as_sal';
	if(CRYPT_SHA512 == 1){
		return substr(crypt($password, '$6$rounds=10000$'.$salt.'$'),33);
	}
	if (CRYPT_SHA256 == 1) {
	    return substr(crypt($password, '$5$rounds=10000$'.$salt.'$'),33);
	}
	if (CRYPT_MD5 == 1) {
	    return substr(crypt($password, '$1$'.$salt.'$'),12);
	}
	
	return false;
}

function get_full_name($arr) {
	$fullname = '';
	if(isset($arr->first_name)) $fullname .= $arr->first_name;
	if(isset($arr->last_name)) $fullname .= " ". $arr->last_name;
	return trim($fullname);
}

function readable_date($date, &$lang) {
	if($lang == '') {
		$lang = array('date_today' => 'Idag', 'date_yesterday' => 'Igår');
	}
		
	$theDate = new DateTime($date);
	$today = new DateTime(date("Y-m-d 24:00:00"));
	$interval = $theDate->diff($today);
	$n = $interval->format("%d"); // get total number of days that differ (always positive number)
	
	$string = '';
	if($n == 0) {
		$string = $lang['date_today'] . " " . $theDate->format('H:i');
	} else if ($n == 1) {
		$string = $lang['date_yesterday'] . " " . $theDate->format('H:i');
	} else {
		$string = $theDate->format('Y-m-d H:i');
	}
	return $string;
}

function compact_name($name) {
	$string = strtolower($name);
	$string = preg_replace("/(å|ä)/","a",$string);
	$string = preg_replace("/(ö)/","o",$string);
	return preg_replace("/[^A-Za-z0-9_]/","_",strtolower($string));
}

function uncompact_name($name) {
	$string = preg_replace("/^_*/", "", $name);
	$string = preg_replace("/_*$/", "", $string);
	$string = preg_replace("/[_]+/i", ".{1}", $string);
	$string = preg_replace("/a/i", "(a|å|ä){1}", $string);
	return preg_replace("/o/i", "(o|ö){1}", $string);
}

function text_format($input, $p = 'p') {
	//\r\n, \n\r, \n and \r
	$patterns = array('/\r\n/', '/\n\r/', '/\r/', '/\n/');
	$replacements = array('<br/>','<br/>','<br/>','<br/>');
	$text = preg_replace($patterns,$replacements, $input);
	
	// more than one (2-30) line break is converted to a paragraph
	if($p != '') {
		return '<'.$p.'>'.preg_replace('/(<br\/>){2,30}/','</'.$p.'><'.$p.'>', $text).'</'.$p.'>';
	} else {
		return $text;
	}
	
	/*
	$patterns = array('/Hej/', '/undrar/', '/(\n){2}/');
	$replacements = array('Hello', 'wonder', '</p><p>');
	return preg_replace($patterns,$replacements, $input);
	*/
}

function news_size_to_class($size) {
	switch($size) {
		case 1:
			return "oneThird";
		case 2:
			return "oneHalf";
		case 3:
			return "twoThirds";
		default:
			return "";
		
	}
}
function news_size_to_class_invert($size) {
	switch($size) {
		case 1:
			return "twoThirds";
		case 2:
			return "oneHalf";
		case 3:
			return "oneThird";
		default:
			return "";
		
	}
}
function news_size_to_px($size) {
	switch($size) {
		case 1:
			return 250;
		case 2:
			return 375;
		case 3:
			return 500;
		case 4:
			return 750;
		default:
			return "";
		
	}
}

/**
 * Better GI than print_r or var_dump -- but, unlike var_dump, you can only dump one variable.  
 * Added htmlentities on the var content before echo, so you see what is really there, and not the mark-up.
 * 
 * Also, now the output is encased within a div block that sets the background color, font style, and left-justifies it
 * so it is not at the mercy of ambient styles.
 *
 * Inspired from:     PHP.net Contributions
 * Stolen from:       [highstrike at gmail dot com]
 * Modified by:       stlawson *AT* JoyfulEarthTech *DOT* com 
 *
 * @param mixed $var  -- variable to dump
 * @param string $var_name  -- name of variable (optional) -- displayed in printout making it easier to sort out what variable is what in a complex output
 * @param string $indent -- used by internal recursive call (no known external value)
 * @param unknown_type $reference -- used by internal recursive call (no known external value)
 */
function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
{
    $do_dump_indent = "<span style='color:#666666;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';
    
    // So this is always visible and always left justified and readable
    echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

    if (is_array($var) && isset($var[$keyvar]))
    {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    }
    else
    {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if($type == "String") $type_color = "<span style='color:green'>";
        elseif($type == "Integer") $type_color = "<span style='color:red'>";
        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif($type == "NULL") $type_color = "<span style='color:black'>";

        if(is_array($avar))
        {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#666666'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach($keys as $name)
            {
                $value = &$avar[$name];
                do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        }
        elseif(is_object($avar))
        {
            echo "$indent$var_name <span style='color:#666666'>$type</span><br>$indent(<br>";
            foreach($avar as $name=>$value) do_dump($value, "$name", $indent.$do_dump_indent, $reference);
            echo "$indent)<br>";
        }
        elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br>";
        elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color\"".htmlentities($avar)."\"</span><br>";
        elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br>";
        elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
        elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> ".htmlentities($avar)."<br>";

        $var = $var[$keyvar];
    }
    
    echo "</div>";
}
