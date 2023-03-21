
<?php 

define('SOURCE_LIST', array(
  '1' => 'Facebook', 
  '2' => 'Website', 
  '3' => 'Adwords',
  '4' => 'Voltooide PDF',
  '5' => 'Configurator gestart',
  '6' => 'Afspraak verzoek',
  '7' => 'Telefoon'
));
//'', 'Facebook', 'Website', 'Adwords', 'Voltooide PDF', 'Configurator gestart', 'Afspraak verzoek', 'Telefoon'

define('CONTACT_TYPE', array(
  '1' => 'E-mail', 
  '2' => 'Telefoon', 
  '3' => 'Gesprek',
  '4' => 'Whatsapp'
));


define('LEAD_TYPE', array(
  '1' => 'Deal', 
  '2' => 'Geen deal', 
  '3' => 'Wijzigen offerte',
  '4' => 'Wacht op antwoord'
));

define('IMG_DIR_PATH', '../images/');

$root = "http://127.0.0.1:8000/";
define('ROOT', "http://127.0.0.1:8000/");
$key = "ORDER_WOW_VDZ";
$encryption_iv = '1234567891011121';
$decryption_iv = '1234567891011121';
define('KEY', $key);
define('ENC_IV', $encryption_iv);
define('DEC_IV', $decryption_iv);

function encrypt($string_data)
{
  
   $cipher = "AES-128-CTR";
   $ivlen = openssl_cipher_iv_length($cipher);
   
   $encryption = openssl_encrypt($string_data, $cipher, KEY, 0, ENC_IV);
   return  $encryption;
}

function decrypt($string_data)
{
   $cipher = "AES-128-CTR";
   $ivlen = openssl_cipher_iv_length($cipher);
   
   $decryption = openssl_decrypt($string_data, $cipher, KEY, 0, DEC_IV);
  return $decryption;
}

function getTimer($date) {

  if($date) {
    $difTime = dateDifferenceT($date);

    if ($difTime > 50) 
      return '<span class="new badge red" data-badge-caption="">'.round($difTime).'h</span>';
    else if ($difTime > 30) 
      return '<span class="new badge orange" data-badge-caption="">'.round($difTime).'h</span>';
    else if ($difTime > 10) 
      return '<span class="new badge green" data-badge-caption="">'.round($difTime).'h</span>';
    else if ($difTime > 1) 
      return '<span class="new badge green" data-badge-caption="">'.round($difTime).'h</span>';
    else {
      $difTime = dateDifferenceM($date);
      if($difTime < 0)
      $difTime = 0;
      return '<span class="new badge green" data-badge-caption="">'.round($difTime).'m</span>';
    }
  }

  return null;
}

function getProjectTimer($date, $mode) {

  if($mode == 'NO')
  {
    $difTime = dateDifferenceW($date, 1);
    if ($difTime >= 3) 
      return '<span class="new badge green" data-badge-caption="">'.floor($difTime).'w</span>';
    else if ($difTime > 1) 
      return '<span class="new badge orange" data-badge-caption="">'.floor($difTime).'w</span>';
    else {
      $difTime = dateDifferenceD($date, 1);
      if($difTime >= 1)
        return '<span class="new badge red" data-badge-caption="">'.round($difTime).'d</span>';
      else 
        return '<span class="new badge red" data-badge-caption="">0d</span>';
    }
  }
  else if($mode == 'YES')
  {
    return '<span class="new badge green" data-badge-caption=""><i class="material-icons">done</i></span>';
  }
  else
  return '<span class="new badge red" data-badge-caption="">0d</span>';

}

function getSaleTimer($date, $mode) {
  if($mode == 'NO')
  {
    $difTime = dateDifferenceW($date);
    if ($difTime >= 2) 
      return '<span class="new badge red" data-badge-caption="">'.floor($difTime).'w</span>';
    else if ($difTime > 1) 
      return '<span class="new badge orange" data-badge-caption="">'.floor($difTime).'w</span>';
    else {
      $difTime = dateDifferenceD($date);

      if($difTime >= 3)
        return '<span class="new badge orange" data-badge-caption="">'.round($difTime).'d</span>';
      else if($difTime >= 1)
        return '<span class="new badge green" data-badge-caption="">'.round($difTime).'d</span>';
      else 
        return '<span class="new badge green" data-badge-caption="">0d</span>';
    }
  }
  else if($mode == 'SKIP')
  {
    return '<span class="new badge grey" data-badge-caption=""></span>';
  }
  else if($mode == 'YES')
  {
    return '<span class="new badge green" data-badge-caption=""><i class="material-icons">done</i></span>';
  }
  else
   return null;
}

function getTicketTimer($date, $mode)
{
  if($mode == 'OPENED')
  {
    $difTime = dateDifferenceW($date);
    if ($difTime >= 2) 
      return '<span class="new badge red" data-badge-caption="">'.floor($difTime).'w</span>';
    else if ($difTime > 1) 
      return '<span class="new badge red" data-badge-caption="">'.floor($difTime).'w</span>';
    else {
      $difTime = dateDifferenceD($date);

      if($difTime >= 5)
        return '<span class="new badge red" data-badge-caption="">'.round($difTime).'d</span>';
      else  if($difTime >= 2)
        return '<span class="new badge orange" data-badge-caption="">'.round($difTime).'d</span>';
      else if($difTime >= 1)
        return '<span class="new badge green" data-badge-caption="">'.round($difTime).'d</span>';
      else 
      {
        return '<span class="new badge green" data-badge-caption="">0d</span>';
       
      }
    }
  }
  else if($mode == 'CLOSED')
  {
    return '<span class="new badge green" data-badge-caption=""><i class="material-icons">done</i></span>';
  }
  else
   return null;
}
function getInvoiceTimerWidget($date, $mode)
{
  if($mode == 0)
  {
    return '<span class="new badge green" data-badge-caption=""><i class="material-icons">done</i></span>';
  }
  if($mode == 1)
    return '<span class="new badge grey" data-badge-caption=""></span>';
  if($mode == 2)
  {
    $difTime = dateDifferenceD($date, 1);
    if($difTime > 0)
      return '<span class="new badge red" data-badge-caption="">' . round($difTime) . 'd</span>';
    else 
      return '<span class="new badge red anim-alert" data-badge-caption="">0d</span>';
  }
  if($mode == 3)
  {
    return '<span class="new badge orange" data-badge-caption=""></span>';
  }
}
function getTaskTimer($date, $hours, $mode)
{
  
  if($hours != 0)
  {
    if($hours < 0)
    $hours = 0;
      if($mode)
      {
        if($mode == 1) // mode = 1 -> processing
        {
            $difTime = dateDifferenceT2($date, $hours);
            if($difTime > 48)
              return '<span class="new badge green" data-badge-caption="">'.round($difTime / 24).'d</span>';
            else if ($difTime > 24) 
              return '<span class="new badge orange" data-badge-caption="">'.round($difTime / 24).'d</span>';
            else if (round($difTime) >= 1) 
              return '<span class="new badge red" data-badge-caption="">'.round($difTime).'h</span>';
            else 
              return '<span class="new badge red anim-alert" data-badge-caption="">0h</span>';
        }
        if($mode == 2) // mode = 2 -> completed
        {
            return '<span class="new badge green" data-badge-caption=""><i class="material-icons">done</i></span>';
        }
        if($mode == 3)
        {
          return '<span class="new badge orange" data-badge-caption=""></span>';
        }
      }
      else{ // mode = not yet started, mode = 0 -> skipped
        return '<span class="new badge grey" data-badge-caption=""></span>';
      }
  }
  else{
    
    if($mode)
    {
      if($mode == 1) // mode = 1 -> processing
      {
          $difDay = dateDifferenceD($date);

          if($difDay >= 0)
          {

            $days_after = date('Y-m-d', strtotime('+5 days', strtotime($date)));
            $days_after = date_create($days_after)->format("Y-m-d H:i:s");
            $difDay = dateDifferenceD($days_after, 1);

            if($difDay >= 3)
              return '<span class="new badge orange" data-badge-caption="">'.floor($difDay).'d</span>';
            else if($difDay >= 1)
              return '<span class="new badge red" data-badge-caption="">'.floor($difDay).'d</span>';
            else 
              return '<span class="new badge red anim-alert" data-badge-caption="">0d</span>';
          }
          else{
            return '<span class="new badge grey" data-badge-caption=""></span>';
          }
          
      }
     
      if($mode == 2) // mode = 2 -> completed
      {
          return '<span class="new badge green" data-badge-caption=""><i class="material-icons">done</i></span>';
      }
    }
    else{ // mode = not yet started, mode = 0 -> skipped
      return '<span class="new badge grey" data-badge-caption=""></span>';
    }
    
  }
  
}



function dateDifferenceT($date, $mode = 0)
{
    $last = date_create($date);
    $now = date_create();
    $interval = 0;
    if($mode == 0)
      $interval = strtotime($now->format("Y-m-d H:i:s")) - strtotime($last->format("Y-m-d H:i:s"));
    if($mode == 1)
      $interval = strtotime($last->format("Y-m-d H:i:s")) - strtotime($now->format("Y-m-d H:i:s"));
    return ($interval / 3600);
}

function dateDifferenceT2($date, $hours)
{
  $last = date_create($date);
  date_add($last, date_interval_create_from_date_string($hours . " hours"));
  $now = date_create();

    $interval = strtotime($last->format("Y-m-d H:i:s")) - strtotime($now->format("Y-m-d H:i:s"));

    return ($interval / 3600);
}

function dateDifferenceM($date, $mode = 0)
{
    $last = date_create($date);
    $now = date_create();
    $interval = 0;
    if($mode == 0)
      $interval = strtotime($now->format("Y-m-d H:i:s")) - strtotime($last->format("Y-m-d H:i:s"));
    if($mode == 1)
      $interval = strtotime($last->format("Y-m-d H:i:s")) - strtotime($now->format("Y-m-d H:i:s"));

    return ($interval / 60);
}

function dateDifferenceD($date, $mode = 0)
{
    $last = date_create($date);
    $now = date_create();
    $interval = 0;
    if($mode == 0)
      $interval = strtotime($now->format("Y-m-d H:i:s")) - strtotime($last->format("Y-m-d H:i:s"));
    if($mode == 1)
      $interval = strtotime($last->format("Y-m-d H:i:s")) - strtotime($now->format("Y-m-d 0:0:0"));

    return ($interval / (3600 * 24));
}

function dateDifferenceW($date, $mode = 0)
{
    $last = date_create($date);
    $now = date_create();
    $interval = 0;
    if($mode == 0)
      $interval = strtotime($now->format("Y-m-d H:i:s")) - strtotime($last->format("Y-m-d H:i:s"));
    if($mode == 1)
      $interval = strtotime($last->format("Y-m-d H:i:s")) - strtotime($now->format("Y-m-d 0:0:0"));

    return ($interval / (3600 * 24 * 7));
}


function getFDate($date, $format = 'd-m-Y')
{
    if ($date) {
      $fdate = date_create($date);
   
      return $fdate->format($format);
    }

    return null;
}

function getContactType($type)
{
  if($type)
    return CONTACT_TYPE[$type];

  return null;
}

function getSourceType($type)
{  
  if($type)
    return SOURCE_LIST[$type];

  return null;
}


function getRandomString($length = 10)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function dateFormat($date, $format='d-m-Y')
{
    $last = date_create($date);
    return $last -> format($format);
}

function getKlantenWidget($project_code)
{
  return "<span class='klanten-link'> - <a href='" . ROOT ."klanten?id=" . $project_code . "' target='_blank'><i class='material-icons'>assignment_ind</i> klantportaal</a></span>";
}

function cmpFiles($a, $b)
{
  return strcmp($b['uploaded_date'], $a['uploaded_date']);
}

function getWeekNumberFromDate($ddate)
{
  $date = new DateTime($ddate);
  $week = $date->format("W");
  return $week;
}
function getYearFromDate($ddate)
{
  $date = new DateTime($ddate);
  $week = $date->format("Y");
  return $week;
}
function getWeeksCountInYear($year) {
  $date = new DateTime;
  $date->setISODate($year, 53);
  return ($date->format("W") === "53" ? 53 : 52);
}
function convertNumberType($str)
{
  $str = str_replace(".", "", $str);
  return floatval(str_replace(",", ".", $str));
}

function addNewLog($contact_id, $user_id, $title, $text = '')
{

  if(!isset($con))
  {
    require( 'connection.php');
  }

  $stmt_new_log = $con -> prepare("SELECT * FROM contacts WHERE id = ?");
  $stmt_new_log -> bind_param("i", $contact_id);
  $stmt_new_log -> execute();
  
  $flag = 0;
  $result_new_log = $stmt_new_log -> get_result();

  while($row_new_log = $result_new_log -> fetch_assoc())
  {
    if($row_new_log['c_status'] == 3 && $row_new_log['l_status'] == 1)
    {
      $flag = 1;
    }
  }

  if($flag)
  {
    $date = date_create();
    $date_text = $date->format("Y-m-d H:i:s");
    $entry_type = 101;
    $stmt_new_log = $con -> prepare("INSERT INTO contact_log (contact_id, entry_type, entry_title, entry_date, entry_description, account_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_new_log -> bind_param("iisssi", $contact_id, $entry_type, $title, $date_text, $text, $user_id);
    $stmt_new_log -> execute();

  }
}
?>
