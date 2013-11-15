<?php

function defaultVal($val,$default = null,$forceNull = false) {
        // Experiment to see if we can easily remove the strict standards
        // errors with a small function      
        if (empty($default)){
            if (empty($val)){
                return null;
            } else {
                return $val;
            }
        } else {
            return $default;
        }

        // The above more of less emulates below but using empty we can test
        // for isset, which below doesn't and is the cause of most of the strict
        // standards errors
	if ($forceNull) {
		return $val === null ? $default : $val;
	}
	return $val ? $val : $default;
}

/**
 * turn a title into a slug for meaningful urls,
 * eg. "This is my long Title" => "this-is-my-long-title"
 * 
 * @param string $title
 */
function toSlug($title) {
	$slug = str_replace(array(" ","_",",",".","/"), "-", $title);
	return strtolower($slug);
}

/**
 * arranges an array in pages of equal size
 *
 * (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11)
 *
 * with size 3:
 *
 * ((1,2,3),(4,5,6),(7,8,9),(10,11))
 */
function paginate($array, $pageSize) {
	return array_chunk($array, $pageSize);
}

/**
 * takes an array of the form
 *
 * ( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11)
 *
 * and arranges it to
 *
 * ((1,2,3,4,5,6),(7,8,9,10,11))
 *
 * if $noOfColumns was 2
 *
 * always tries to have columns of equal length
 * but the last column can be shorter
 */
function columnize($array, $noOfColumns) {
	return array_chunk($array, sizeof($array) / $noOfColumns);
}

/**
 * 
 * Function to rotate an image if GD is *not* compiled into PHP.
 * This is from beau@dragonflydevelopment.com from the comments at:
 * 
 * http://www.php.net/manual/en/function.imagerotate.php
 * 
 * @param $img
 * @param $rotation (90, 180, 270, 0, 360)
 */
function rotateImage($img, $rotation) {
  $width = imagesx($img);
  $height = imagesy($img);
  switch($rotation) {
    case 90: $newimg= @imagecreatetruecolor($height , $width );break;
    case 180: $newimg= @imagecreatetruecolor($width , $height );break;
    case 270: $newimg= @imagecreatetruecolor($height , $width );break;
    case 0: return $img;break;
    case 360: return $img;break;
  }
  if($newimg) {
    for($i = 0;$i < $width ; $i++) {
      for($j = 0;$j < $height ; $j++) {
        $reference = imagecolorat($img,$i,$j);
        switch($rotation) {
          case 90: if(!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference )){return false;}break;
          case 180: if(!@imagesetpixel($newimg, $width - $i, ($height - 1) - $j, $reference )){return false;}break;
          case 270: if(!@imagesetpixel($newimg, $j, $width - $i, $reference )){return false;}break;
        }
      }
    } return $newimg;
  }
  return false;
}

function lookupForSelect(&$w,$type) {
    $select = array();
    $rows = $w->db->select("code,title")->from("lookup")->where("type",$type)->fetch_all();
    if ($rows) {
	    foreach ($rows as $row) {
	        $select[]=array($row['title'],$row['code']);
	    }
    }
    return $select;
}



function getStateSelectArray() {
    return array(
            array("ACT", "ACT"),
            array("NSW", "NSW"),
            array("NT", "NT"),
            array("QLD", "QLD"),
            array("SA", "SA"),
            array("TAS", "TAS"),
            array("VIC", "VIC"),
            array("WA", "WA"));
}

/**
 * Iterates over $needle_array and
 * applies $func to $haystack and $current_needle.
 * Returns 
 * @param unknown_type $haystack
 * @param unknown_type $needles
 */
function strcontains($haystack, $needle_array) {
	foreach ($needle_array as $needle) {
		if (stripos($haystack, $needle) !== false)
			return true;
	}
	return false;
}

/**
 * Checks for either a scalar prefix ($needle) in the $haystack
 * or checks whether any of an array of needles is in the $haystack.
 * 
 * Returns true when a needle is found at the start of the haystack, false otherwise.
 * 
 * @param unknown $haystack
 * @param unknown $needle
 * @return boolean
 */
function startsWith($haystack,$needle) {
	if (is_scalar($needle)) {
    	return strpos($haystack, $needle) === 0;
	} else if (is_array($needle) && sizeof($needle) > 0) {
		foreach ($needle as $pref) {
			if (strpos($haystack, $pref) === 0) return true;
		}
	}
	return false;
}


function str_whitelist($dirty_data, $limit=0) {
    if ($limit > 0) {
        $dirty_data = substr($dirty_data, 0, $limit);
    }
    $dirty_array = str_split($dirty_data);
    $clean_data = "";
    foreach($dirty_array as $char) {
        $clean_char = preg_replace("/[^a-zA-Z0-9 ,.'\"-\/]/", "", $char);
        $clean_data = $clean_data.$clean_char;
    }
    return $clean_data;
}

function phone_whitelist($dirty_data) {
    $dirty_array = str_split($dirty_data);
    $clean_data = "";
    foreach($dirty_array as $char) {
        $clean_char = preg_replace("/[^0-9 ()+-]/", "", $char);
        $clean_data = $clean_data.$clean_char;
    }
    return $clean_data;
}

function int_whitelist($dirty_data, $limit) {
    $dirty_data = substr($dirty_data, 0, $limit);
    $dirty_array = str_split($dirty_data);
    $clean_data = "";
    foreach($dirty_array as $char) {
        $clean_char = preg_replace("/[^0-9]/", "", $char);
        $clean_data = $clean_data.$clean_char;
    }
    return $clean_data;
}

function getTimeSelect($start=8, $end=19) {
    for($i=$start;$i<=$end;$i++) {
        $m = " am";
        $t = $i;
        if ($i>=12) {
            $m = " pm";
            if ($i>12){
                $t = $i-12;
            }
        }
        $t = sprintf("%02d", $t);
        $select[]=array($t.":00".$m,$i.":00");
        $select[]=array($t.":30".$m,$i.":30");
    }
    return $select;
}

function formatDate($date,$format="d/m/Y",$usetimezone = true) {
    if (!$date) return null;
    if (!is_long($date)) {
    	$date = strtotime(str_replace("/","-",$date)); 
    }
	/*
    $timezone = new DateTimeZone( "Australia/Sydney" );
	$d = new DateTime();
	$d->setTimestamp( $date);
	$d->setTimezone( $timezone );
    return $d->format($format);
	*/
    return date($format,$date);     
}

function formatDateTime($date,$format="d/m/Y h:i a",$usetimezone = true) {
    return formatDate($date,$format);
}

/*
That it is an implementation of the function money_format for the
platforms that do not it bear.

The function accepts to same string of format accepts for the
original function of the PHP.

(Sorry. my writing in English is very bad)

The function is tested using PHP 5.1.4 in Windows XP
and Apache WebServer.
*/
function formatMoney($format, $number)
{
    if (function_exists('money_format')){
        return money_format($format, $number);
    }
    $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
              '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
    if (setlocale(LC_MONETARY, 0) == 'C') {
        setlocale(LC_MONETARY, '');
    }
    $locale = localeconv();
    preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
    foreach ($matches as $fmatch) {
        $value = floatval($number);
        $flags = array(
            'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                           $match[1] : ' ',
            'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                           $match[0] : '+',
            'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
            'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
        );
        $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
        $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
        $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
        $conversion = $fmatch[5];

        $positive = true;
        if ($value < 0) {
            $positive = false;
            $value  *= -1;
        }
        $letter = $positive ? 'p' : 'n';

        $prefix = $suffix = $cprefix = $csuffix = $signal = '';

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
        switch (true) {
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                $prefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                $suffix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                $cprefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                $csuffix = $signal;
                break;
            case $flags['usesignal'] == '(':
            case $locale["{$letter}_sign_posn"] == 0:
                $prefix = '(';
                $suffix = ')';
                break;
        }
        if (!$flags['nosimbol']) {
            $currency = $cprefix .
                        ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                        $csuffix;
        } else {
            $currency = '';
        }
        $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

        $value = number_format($value, $right, $locale['mon_decimal_point'],
                 $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
        $value = @explode($locale['mon_decimal_point'], $value);

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
        if ($left > 0 && $left > $n) {
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
        }
        $value = implode($locale['mon_decimal_point'], $value);
        if ($locale["{$letter}_cs_precedes"]) {
            $value = $prefix . $currency . $space . $value . $suffix;
        } else {
            $value = $prefix . $value . $space . $currency . $suffix;
        }
        if ($width > 0) {
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                     STR_PAD_RIGHT : STR_PAD_LEFT);
        }

        $format = str_replace($fmatch[0], $value, $format);
    }
    return $format;
}

function recursiveArraySearch($haystack, $needle, $index = null)
{
    $aIt     = new RecursiveArrayIterator($haystack);
    $it    = new RecursiveIteratorIterator($aIt);

    while($it->valid())
    {
        if (((isset($index) AND ($it->key() == $index)) OR (!isset($index))) AND ($it->current() == $needle)) {
            return $aIt->key();
        }

        $it->next();
    }

    return false;
}

/**
 * 
 * This function will return the correct dates using date picker or a custom month picker.
 * If you have a from date and to date criteria, this function changes the to date based on
 * the dm_var. parse in 'm' for month selection and 'd' for day selection.
 * With date pickers, they always return the beginning of that day. i.e. 00:00:00 10/11/2010
 * this will change the to date to: 23:59:59 10/11/2010.
 * 
 *  To use this function, use the list function, calling this function.
 *  i.e. list($from_date, $to_date) = returncorrectdates($w,$dm_var,$from_date,$to_date);
 * @param obj $w
 * @param string $dm_var
 * @param string $from_date
 * @param string $to_date
 */

function returncorrectdates(Web &$w, $dm_var, $from_date, $to_date){
	if ($dm_var == 'm'){
		$from_date = strtotime(str_replace("/","-",$from_date));
		$to_date = strtotime(str_replace("/","-",$to_date));
		$accepted_date = getDate($to_date);
		$month_number = $accepted_date['mon'];
		$accepted_year = $accepted_date['year'];
		if (date('I')){
			$minus_var = "3601";
		} else {
			$minus_var = '1';
		}
		if ($to_date){
			$to_date = $to_date + ((60 * 60 * 24 * cal_days_in_month(CAL_GREGORIAN, $month_number, $accepted_year))-$minus_var);
		}	
		return array($from_date, $to_date);
	}
	if ($dm_var == 'd'){
		$from_date = strtotime(str_replace("/","-",$_GET['from_date']));
		$to_date = strtotime(str_replace("/","-",$_GET['to_date']));
		$accepted_date = getDate($to_date);
		$month_number = $accepted_date['mon'];
		$accepted_year = $accepted_date['year'];
		if ($to_date){
			$to_date = $to_date + 86399;
		}		
		return array($from_date, $to_date);
	}
}

// find a value in a multidimension array
function in_multiarray($value, $array) {
	$top = sizeof($array) - 1;
	$bottom = 0;
	
	while($bottom <= $top) {
		if($array[$bottom] == $value) {
			return true;
		}
		else {
			if(is_array($array[$bottom])) {
				if(in_multiarray($value, ($array[$bottom])))
					return true;
			}
		}
		$bottom++;
	}
	return false;
}
