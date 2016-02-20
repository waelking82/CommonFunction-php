<?php

class shared
{
    function trim_value(&$value)
{
    $value = trim($value);    // this removes whitespace and related characters from the beginning and end of the string
}
    function curPageURL() 
    {
             $pageURL = 'http';
             //if ($_SERVER["HTTPS"] == "on") 
             //{
                     //$pageURL .= "s";
                     //}

             $pageURL .= "://";
             if ($_SERVER["SERVER_PORT"] != "80") 
             {
              $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
             } 
             else 
             {
              $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
             }
             return $pageURL;
    }
    //########################################################################################################3

    function curPageName() 
    {
            return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
    }

    function isemptyisset($value)
    {
            if(isset($value) && !empty($value))
            {
                    return $value;
            }
            return false;
    }

    function isValidURL($url)
    {
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }

    function convertPOSTquerystring($convertPOST)
    {
    $query_string = "";

    if ($convertPOST) 
    {
            $kv = array();
            foreach ($convertPOST as $key => $value)
            {
                    $kv[] = "$key=$value";
            }
             $query_string = join("&", $kv);
            }
            else 
            {
              $query_string = $_SERVER['QUERY_STRING'];
            }
            echo $query_string;
    }

    function get_page_title($url)
    {

            if( !($data = file_get_contents($url)) ) return false;

            if( preg_match("#<title>(.+)<\/title>#iU", $data, $t))  
            {
                    return trim($t[1]);
            } else 
            {
                    return false;
            }
    }
    function getMetaData($url)
    {
            // get meta tags
            $metaArray=get_meta_tags($url);
            // store page
            $page=file_get_contents($url);
            // find where the title CONTENT begins
            $titleStart=strpos($page,'<title>')+7;
            // find how long the title is
            $titleLength=strpos($page,'</title>')-$titleStart;
            // extract title from $page
            $metaArray['title']=substr($page,$titleStart,$titleLength);
            // return array of data
            return $metaArray;
    }

    function real_escape_string($txt)
    {
            $phrase  = "You should eat fruits, vegetables, and fiber every day.";
            $old = array('"');
            $new   = array('\"');

            return str_replace($old, $new, $txt);
    }

    function destroy($var) 
    {
            global $var;
            unset($var);
    }
    function source_code ($url) 
    {
            if (function_exists ('curl_init')) {
                    $curl = @curl_init ($url);

                    @curl_setopt ($curl, CURLOPT_HEADER, FALSE);
                    @curl_setopt ($curl, CURLOPT_RETURNTRANSFER, TRUE);
                    @curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, TRUE);
                    @curl_setopt ($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

                    $source = @curl_exec ($curl);
                    @curl_close ($curl);
                    return $source;

            } 
            else 
            {
                    return @file_get_contents ($url);
            }
    }

    function replaceText($extract,$replace,$text)
    {
            $text=str_replace($extract,$replace,$text);
            return $text;
    }
    function cleanData($data)
    {
            return trim($data);
    }
    # Get all dates between two dates using php code:
    function getAllDatesBetweenTwoDates($strDateFrom,$strDateTo)
    {
        $aryRange=array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>$iDateFrom)
        {
            //array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                array_push($aryRange,date('Y-m-d',$iDateFrom));
                $iDateFrom+=86400; // add 24 hours 
            }
        }
        return $aryRange;
    }
     # Get all Months with years between two dates using php code:

    function getAllMonths($date1, $date2)
    {
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        $my = date('mY', $time2);

        $months = array(date('F Y', $time1));

        while($time1 < $time2) {
            $time1 = strtotime(date('Y-m-d', $time1).' +1 month');
            if(date('mY', $time1) != $my && ($time1 < $time2))
            $months[] = date('F Y', $time1);
        }

        $months[] = date('F Y', $time2);
        $months = array_unique($months);
        return $months;
    }

    function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
        $first_of_month = gmmktime(0,0,0,$month,1,$year);

        #remember that mktime will automatically correct if invalid dates are entered
        # for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
        # this provides a built in "rounding" feature to generate_calendar()

        $day_names = array(); #generate all the day names according to the current locale
        for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
            $day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

        list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
        $weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
        $title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

        #Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
        @list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
        if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
        if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
        $calendar = '<table class="calendar">'."\n".
            '<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

        if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
            #if day_name_length is >3, the full name of the day will be printed
            foreach($day_names as $d)
                $calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
            $calendar .= "</tr>\n<tr>";
        }

        if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
        for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
            if($weekday == 7){
                $weekday   = 0; #start a new week
                $calendar .= "</tr>\n<tr>";
            }
            if(isset($days[$day]) and is_array($days[$day])){
                @list($link, $classes, $content) = $days[$day];
                if(is_null($content))  $content  = $day;
                $calendar .= '<td'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
                    ($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>';
            }
            else $calendar .= "<td>$day</td>";
        }
        if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

        return $calendar."</tr>\n</table>\n";
    }
    
    function daysLeftForBirthday($devabirthdate)
{
    /* input birthday date format -> Y-m-d */
    list($y, $m, $d) = explode('-',$devabirthdate);
    $nowdate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $nextbirthday = mktime(0,0,0,$m, $d, date("Y"));

    if ($nextbirthday<$nowdate)
        $nextbirthday=$nextbirthday+(60*60*24*365);

    $daycount=intval(($nextbirthday-$nowdate)/(60*60*24));

    return $daycount;
}

    function get_ArrayOfMonth($year, $month){
        $num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $day_names = array();
        for($i=0;$i<$num_days_in_month;$i++)
        {
            $B=$i+1;
            //echo $year.'-'.$month.'-'.$B;
            $timestamp = strtotime($year.'-'.$month.'-'.$B);

            $day_names[$i] = date('D', $timestamp);
        }
        return $day_names;

    }
    
    function Array_depth_report($CompanyBooking)
    {
        foreach ($CompanyBooking as $key => $value)
                        {
                            //echo $CompanyBooking[$key][0].'<br />';
                            $keyget =array_search(array($CompanyBooking[$key][0],$CompanyBooking[$key][1],$CompanyBooking[$key][2]),$CompanyBooking);
                                if($keyget===FALSE)
                                {
                                    
                                }
                                else
                                {
                                   unset($CompanyBooking[$key]);
                                }
                        }
    }
    
    // Function to get the client IP address
    function get_client_ip() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    function getImagesDirectories($pathDirImages) {
        // image extensions
        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');

        $results = array();

        // directory to scan
        $directory = new DirectoryIterator($pathDirImages);

        // iterate
        foreach ($directory as $fileinfo) {
            // must be a file
            if ($fileinfo->isFile()) {
                // file extension
                $extension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                // check if extension match
                if (in_array($extension, $extensions)) {
                    // add to result
                    $results[] =$fileinfo->getFilename();
                }
            }
        }
        return $results;
    }
    public function get_folder_images() {
        return 'http://'.$_SERVER['SERVER_NAME'].'/test/images/';
    }
    public function convertDateToYearMonthDay($date)
    {
        $format = DateTime::createFromFormat('d/m/Y',$date);
        return $format->format('Y-m-d');
    }
    public function convertDateToDayMonthYear($date)
    {
        $format = DateTime::createFromFormat('Y-m-d',$date);
        return $format->format('d/m/Y');
    }
    public function convertDateTimeToYearMonthDay($date)
    {
        $format = DateTime::createFromFormat('d/m/Y H:i:s',$date);
        return $format->format('Y-m-d H:i:s');
    }
    public function convertDateTimeToDayMonthYear($date)
    {
        $format = DateTime::createFromFormat('Y-m-d H:i:s',$date);
        return $format->format('d/m/Y');
    }
    public function convertDateTimeToDayMonthYearHIS($date)
    {
        $format = DateTime::createFromFormat('Y-m-d H:i:s',$date);
        return $format->format('d/m/Y H:i:s');
    }
    public function checkAmPm($period)
    {
        $text=($period==0?'AM':'PM');
        return $text;
    }
    public function checkStateOpenClose($state)
    {
        $text=($state==0?'Close':'Open');
        return $text;
    }
    public function checkStateActiveInactive($state)
    {
        $text=($state==0?'Active':'Inactive');
        return $text;
    }
    public function convertDateTimeToTime($date)
    {
        $format = DateTime::createFromFormat('Y-m-d H:i:s',$date);
        return (int)$format->format('H');
    }
    public function getDateTimeByDBFormat() {
        date_default_timezone_set('UTC');
      return date("Y-m-d H:i:s");
    }
    public function getDateByDBFormat() {
        date_default_timezone_set('UTC');
      return date("Y-m-d");
    }
    //PHP multi dimensional array search
    public function searcharray($value, $key, $array) {
        if(is_array($array))
        {
            foreach ($array as $k => $val) {
                    if ($val[$key] == $value) {
                        return $k;
                    }
                }
        }
                
    return FALSE;
    }
    public function multidimensional_search($parents, $searched) { 
        if (empty($searched) || empty($parents)) { 
          return false; 
        } 

        foreach ($parents as $key => $value) { 
          $exists = true; 
          foreach ($searched as $skey => $svalue) { 
            $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
          } 
          if($exists){ return $key; } 
        } 

        return false; 
      } 
    //get age
    public function GetAge($Date) {
        $from = new DateTime($Date);
        $to   = new DateTime('today');
        return $from->diff($to)->y;

        # procedural
        //echo date_diff(date_create('1970-02-01'), date_create('today'))->y;
        //        $tz  = new DateTimeZone('Europe/Brussels');
        //$age = DateTime::createFromFormat('d/m/Y', '12/02/1973', $tz)
        //     ->diff(new DateTime('now', $tz))
        //     ->y;
        
        //SELECT DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(`DOB`, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(`DOB`, '00-%m-%d')) AS age from users
    }
    public function getRatioImage($width,$height,$size) {
    if($size>$width && $size>$height)
    {
        return FALSE;
    }
    $biggest=($width>$height?$biggest=$width:$biggest=$height);
    return $size/$biggest;
        
    }
    public function SaveFileByBinary($data,$extention,$path,$filename=NULL) {
    $data = base64_decode($data);
    $im = imagecreatefromstring($data);
    // assign new width/height for resize purpose
    $newwidth = $newheight = 50;
    // Create a new image from the image stream in the string
    $thumb = imagecreatetruecolor($newwidth, $newheight); 

    if ($im !== false) {

        // Select the HTTP-Header for the selected filetype 
        #header('Content-Type: image/png'); // uncomment this code to display image in browser

        // alter or save the image  
        
        $fileName = ($filename==NULL?$path.date('ymdhis'):$path.$filename);
        imagealphablending($im, false); // setting alpha blending on
        imagesavealpha($im, true); // save alphablending setting (important)

        // Generate image and print it
        $resp = imagepng($im, $fileName.'.'.$extention);

        // resizing png file
        imagealphablending($thumb, false); // setting alpha blending on
        imagesavealpha($thumb, true); // save alphablending setting (important)

        $source = imagecreatefrompng($fileName.'.'.$extention); // open image
        imagealphablending($source, true); // setting alpha blending on

        list($width, $height, $type, $attr) = getimagesize($fileName.'.'.$extention);
        #echo '<br>' . $width . '-' . $height . '-' . $type . '-' . $attr . '<br>';

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        $resp = imagepng($thumb,$fileName.'_thumb.'.$extention);

        // frees image from memory
        imagedestroy($im);
        imagedestroy($thumb);

        }
        else {
            echo 'An error occurred.';
        }
    }
    public function SaveIamgesByBinary($data,$extention,$path,$WideImagePath,$filename=NULL) {
        $response=  array();
        require_once $WideImagePath;
        require_once dirname(__FILE__) . '/Enums.php';
        require_once dirname(__FILE__) . '/shared.class.php';
        $shared=new shared();
        $dataAfterDecode = base64_decode($data);
        $imageName=date('ymdhis');
        $fileName = ($filename==NULL?$path.$imageName:$path.$filename);   
        //var_dump(WideImage::load($dataAfterDecode)->saveToFile($fileName.$extention));
        try
        {
          $lowresolution=  Enums::lowresolution;
          $thumbnailreslution=  Enums::thumbnailreslution;
          $image = WideImage::load($dataAfterDecode);
          $image->saveToFile($fileName.'.'.$extention);
          if($shared->getRatioImage($image->getWidth(),$image->getHeight(),$lowresolution)!==FALSE)
          {
              $image->resize($lowresolution, $lowresolution)->saveToFile($fileName.'_low.'.$extention);
          }
          else
          {
              $image->saveToFile($fileName.'_low.'.$extention);
          }

          if($shared->getRatioImage($image->getWidth(),$image->getHeight(),$thumbnailreslution)!==FALSE)
          {
              $image->resize($thumbnailreslution, $thumbnailreslution)->saveToFile($fileName.'_thumb.'.$extention);
          }
          else
          {
               $image->saveToFile($fileName.'_thumb.'.$extention);
          }
            $image->destroy();
          $response["error"] = false;
          $response['message'] = "SUCCESSFULLY";
          $response['imageName'] = $imageName.'.'.$extention;
        }
        catch (Exception $e)
        {
          $response['error'] = true;
          $response['message'] = "An error occurred. Please try again";
        }
        return $response;
    
    }
    
    
}