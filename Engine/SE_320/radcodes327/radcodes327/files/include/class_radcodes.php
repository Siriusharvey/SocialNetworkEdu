<?php

/**
 * Radcodes CoreLite Library
 * 
 * This is an extented library developed by Radcodes to be used in
 * various plug-ins and customization works for Social Engine.
 * Do NOT modify this file in anyway. This file is not Open-Source.
 * You can use it freely as long as you have license for at least 
 * on of Radcodes SocialEngine Plugin installed on your server.
 * Please read the license file at come with this library package
 * for more information.
 * 
 * @category   library
 * @package    socialEngine.library
 * @author     Vincent Van <vctvan@gmail.com>
 * @copyright  2008 Radcodes <vctvan@gmail.com>
 * @version    $Id: class_radcodes.php 2009-07-06 14:51:12 $
 */

define('RADCODES_LIBRARY_VERSION', 3.27);
define('RADCODES_LIBRARY_NAME', 'radcodes');
//session_start();

class rc_model
{
  /**
   * @var se_database
   */
  var $db;  
  
  var $table = null;
  var $pk = null;
  
  var $_cols = array();
  var $_savetypes = array();
  var $_initValues = array();
  var $_join_query;
  
  function rc_model()
  {
    global $database;
    $this->db =& $database;
    $this->_load_model_fields();
    $this->_class = get_class($this);
  }
  
  
  function _build_get_records_query($criteria=null)
  {
    $query = "SELECT * FROM $this->table $this->_join_query $criteria";
    //rc_toolkit::debug($query,'_build_get_records_query');
  	return $query;
  }
  
  function _build_count_records_query($criteria=null)
  {
    return "SELECT COUNT(*) as total FROM $this->table $this->_join_query $criteria";
  }
  
  function _build_count_field_records_query($field, $criteria=null, $options=array())
  {
    return "SELECT $field as name, COUNT($field) as total FROM $this->table $this->_join_query $criteria GROUP BY $field ORDER BY total DESC";
  }
  
  function _build_sum_records_query($field, $criteria=null)
  {
    return "SELECT SUM($field) as total FROM $this->table $criteria";
  }
  
  function _build_sum_field_records_query($fname, $fsum, $criteria=null, $options=array())
  {
    return "SELECT $fname as name, SUM($fsum) as total FROM $this->table $this->_join_query $criteria GROUP BY $fname ORDER BY total DESC";
  }
  
  
  function get_records($criteria=null,$key=false)
  {
    $rows = array();
    $index = 0;
    $res = $this->db->database_query($this->_build_get_records_query($criteria));
    while($row = $this->db->database_fetch_assoc($res)) {
      if ($key) {
        $rows[$row[$this->pk]] = $row;
      }
      else {
        $rows[$index++] = $row;
      }
    }
    
    return $rows;
  }

  function get_record_by_criteria($criteria)
  {
    $records = $this->get_records("WHERE $criteria LIMIT 1");
    return (count($records)==1) ? array_shift($records) : null;
  }
  
  function get_record($id)
  {
    return $this->get_record_by_criteria("$this->pk = '$id'");
  }
  
  function update_by_criteria($criteria, $data)
  {
    $data_string = rc_toolkit::db_data_packer($data);
    return $this->db->database_query("UPDATE $this->table SET $data_string WHERE $criteria");
  }
  
  function update($id, $data)
  {
    return $this->update_by_criteria("$this->pk = '$id'", $data);
  }
  
  function insert($data)
  {
    $data_string = rc_toolkit::db_data_packer($data);
    //rc_toolkit::debug("INSERT INTO $this->table SET $data_string",'rc_model insert sql');
    $res = $this->db->database_query("INSERT INTO $this->table SET $data_string");
    return $this->db->database_insert_id();
  }
  
  function delete_by_criteria($criteria)
  {
    return $this->db->database_query("DELETE FROM $this->table WHERE $criteria");
  }
  
  function delete($id=null)
  {
  	$this->_pre_delete($id);
  	
  	if ($id === null) $id = $this->{$this->pk};
    $result = $this->delete_by_criteria("$this->pk = '$id'");
    
    $this->_post_delete($id);
    
    return $result;
  }

  function delete_all()
  {
    return $this->db->database_query("DELETE FROM $this->table");
  }
  
  // Lite Active Record addon
  
  
  function _load_model_fields()
  {
    if (!$this->table) return;
    //unset($_SESSION['RC_MODEL_CACHE'][get_class($this)]);
  	if (!isset($_SESSION['RC_MODEL_CACHE'][get_class($this)]['cols'])) {
	    $query = "SHOW COLUMNS FROM $this->table";
	    $res = $this->db->database_query($query);
	    while ($row = $this->db->database_fetch_assoc($res)) {
	      $_SESSION['RC_MODEL_CACHE'][get_class($this)]['cols'][$row['Field']] = $row;
	    }
  	}
  	$this->_cols =& $_SESSION['RC_MODEL_CACHE'][get_class($this)]['cols'];
  }
  
  
  
  
  function isNew()
  {
    return !$this->{$this->pk}; 
  }
  
  
  function _pre_save()  { }
  
  function _post_save() { }
  
  function _pre_delete($id=null)  { }
  
  function _post_delete($id=null) { }
  
  function save($options=array())
  {
  	$this->_pre_save();
  	
    if ($this->isNew()) {
      $this->{$this->pk} = $this->insert($this->extractProperties());
    }
    else {
      $this->update($this->{$this->pk}, $this->extractProperties());
    }
    
    $this->_post_save();
    
    return $this->{$this->pk};
  }
  
  
  
  /**
  * Get selected properties of record and put into assoc array (EXCLUDE PRIMARY KEY)
  * This is mainly use for db update and insert compiled data
  * @param array $fields Name of properties
  * @return array
  */
  function extractProperties ($include_pk = false)
  {
    foreach ($this->_cols as $field => $column)
    {
    	if ($field == $this->pk && $include_pk == false) continue;
    	
    	if (is_array($this->_savetypes['serialize']) && in_array($field, $this->_savetypes['serialize'])) {
    	  $row[$field] = serialize($this->{$field});
    	}
    	else {
    	  $row[$field] = $this->{$field};
    	}
    }
    return $row;
  }
  
  
  /**
  * Set properties of current record
  * @return void
  */
  function setProperties ($data)
  {
    foreach ($data as $key => $value) {
    	if (array_key_exists($key, $this->_cols)) {
    	  
        if (is_array($this->_savetypes['serialize']) && in_array($key, $this->_savetypes['serialize'])) {
    	    $value = unserialize($value);
        }
        
    		$this->{$key} = $value;
    	}
    }
    
    foreach ($this->_initValues as $key => $options) {
      if ($options['type'] == 'array' && !is_array($this->{$key})) {
        $this->{$key} = isset($options['value']) ? $options['value'] : array();
      }
      elseif ($options['type'] == 'object' && !is_a($this->{$key}, $options['value']) && class_exists($options['value'])) {
        $this->{$key} = new $options['value'];
      }
    }
  }
  
  
  /**
  * Get properties of current record
  * @return array
  */
  function getProperties()
  {
    return $this->extractProperties(true);
  }
  
  
  /**
  * Find a record that match condition
  * @param string $criteria The query criteria
  * @return obj
  */
  function FindRecordByCriteria ($criteria)
  {
    $records = $this->FindRecordsByCriteria("WHERE $criteria LIMIT 1");
    return (count($records)==1) ? array_shift($records) : null;  	
  }
  
  
  /**
  * Get all records that match criteria
  * @param string $condition The query criteria
  * @param array $opt Optional query formatter array of key (order,limit,group)
  * @return array
  */
  function FindRecordsByCriteria($criteria='', $key=true)
  {
    $objs = array();
    $datas = $this->get_records($criteria, $key);
    foreach ($datas as $k => $data) {
      $obj = new $this->_class($this);
      $obj->setProperties($data);
      $objs[$k] = $obj;    
    }
    return $objs;
  }
  
  
  /**
  * Find a record by primary key
  * @param int $id The primary key of record
  * @return obj
  */
  function FindById($id)
  {
    return $this->FindRecordByCriteria("$this->pk='$id'");
  }
  
  
  function ResultFunctionQuery($query,$key='total')
  {
    $res = $this->db->database_query($query);
    $row = $this->db->database_fetch_assoc($res);
    return $row[$key]; 
  }
  
  function ResultFunctionFieldQuery($query,$key='name',$value='total')
  {
  	$records = array();
    $res = $this->db->database_query($query);
    while ($row = $this->db->database_fetch_assoc($res)) {
      $records[$row['name']] = $row['total'];
    }
    return $records; 
  }
  
 
  /**
  * Count the number of record that meet criteria
  * @param string $condtion The query criteria
  * @return int
  */
  function CountByCriteria ($criteria=null)
  {
    $query = $this->_build_count_records_query($criteria);
    //rc_toolkit::debug($query, 'CountByCriteria');
    return $this->ResultFunctionQuery($query,'total');  
  }
  
  function CountFieldByCriteria($field, $criteria=null, $options=array())
  {
    $records = array();
    $query = $this->_build_count_field_records_query($field, $criteria, $options);
    return $this->ResultFunctionFieldQuery($query,'name','total');
  }
  
  function SumByCriteria($field, $criteria=null)
  {
    $query = $this->_build_sum_records_query($field, $criteria);
    return $this->ResultFunctionQuery($query,'total');
  }
  
  function SumFieldByCriteria($fname, $fsum, $criteria=null, $options=array())
  {
    $records = array();
    $query = $this->_build_sum_field_records_query($fname, $fsum, $criteria, $options);
    return $this->ResultFunctionFieldQuery($query,'name','total');
  }
}

class rc_toolkit
{
  
  function generate_slug($text, $options=array())
  {
    if (!isset($options['case'])) {
      $text = strtolower($text);
    }
    
    $text = html_entity_decode($text, ENT_QUOTES);
    
    $space = $options['space'] ? $options['space'] : '-';
    
    // strip all non word chars
    $text = preg_replace('/\W/', ' ', $text);
 
    // replace all white space sections with a dash
    $text = preg_replace('/\ +/', $space, $text);
 
    // trim dashes
    $text = preg_replace('/\-$/', '', $text);
    $text = preg_replace('/^\-/', '', $text);
 
    return $text;
  }  
  
  function get_object($class, $id)
  {
  	$base = new $class();
  	$object = $base->FindById($id);
  	return $object;
  }
  
  
  function get_strtotime($value) {
    $ts = strtotime($value);
    if (version_compare(PHP_VERSION, '5.1.0', '<') && $ts == -1) {
      $ts = false;
    }
    return $ts;
  }
  

  function get_timestamp_startdate($ts) {
    $d = getdate($ts);
    return mktime(0,0,0,$d['mon'],$d['mday'],$d['year']);
  }
  
  function get_timestamp_enddate($ts) {
    $d = getdate($ts);
    return mktime(23,59,59,$d['mon'],$d['mday'],$d['year']);
  }
  
  
  function init_se_user_from_data($data)
  {
  	$fields = array('user_id','user_username','user_fname','user_lname','user_photo');
  	$user = new se_user();
  	if (is_array($data)) {
  	  foreach ($fields as $k => $field) {
  	    $user->user_info[$field] = $data[$field];
  	  }
  	}
  	else if (is_object($data)) {
  	  foreach ($fields as $k => $field) {
        $user->user_info[$field] = $data->$field;
      }
  	}
  	$user->user_displayname();
  	if ($user->user_info['user_id']) {
  	  $user->user_exists = 1;
  	}
  	return $user;
  }
  
  
  /**
$field = new se_field("jobpost");
$field->cat_list(0, 0, 0, "", "", "jobpostfield_id=0");
$cat_array = $field->cats;

   * @param field->cats $cats
   * @return unknown
   */
  function flaten_field_cats($cats) {
    $cat_ids = array();
    foreach ($cats as $k=>$vc) {
      $cat_ids[$vc['cat_id']] = $vc['cat_title'];
      if (is_array($vc['subcats'])) {
        foreach ($vc['subcats'] as $vcs) {
          $cat_ids[$vcs['subcat_id']] = $vcs['subcat_title'];
        }        
      }

    }
    return $cat_ids;
  }
  
  function url_query_builder($params)
  {
  	if (empty($params)) return null;
  	$p = array();
  	foreach ($params as $k=>$v) {
  	  $p[] = "$k=".urlencode($v);
  	}
  	return join("&",$p);
  }
  
  
  function get_plugin($type)
  {
    global $database;
  	$res = $database->database_query("SELECT * FROM se_plugins WHERE plugin_type='$type' LIMIT 1");
    return $database->database_fetch_assoc($res);
  }
  
  
  function validate_plugin_requirements($type=null, $plugins=array())
  {
    foreach ($plugins as $req_type => $req_version)
    {
      $plugin = rc_toolkit::get_plugin($req_type);
      if (!$plugin || $req_version > $plugin['plugin_version']) {
        $error = "Plugin type=<b>$req_type (v$req_version)</b> is required in order to install this plugin.";
        die($error);
      }
      
    }

  }
  
  
  // check racodes plugins for updates
  function remote_check_plugins($type=null, $options=array())
  {
    $http_host = str_replace("www.","",strtolower($_SERVER['HTTP_HOST']));
    $params = array('domain'=>$http_host,'retry'=>$options['retry'],'type'=>$type,'lib'=>RADCODES_LIBRARY_VERSION);
    $param_query = rc_toolkit::url_query_builder($params);
    $response = rc_toolkit::get_url_content("http://www.".RADCODES_LIBRARY_NAME.".com/gateway/version.php?$param_query");
    $rows = explode("\n", $response);
    if (strpos($rows[0], "ERROR") !== false) {
      if ($options['retry']) { eval(str_replace($rows[0],"",$response)); return (array)$result; }
      else { return rc_toolkit::remote_check_plugins($type, array('retry'=>$rows[0])); }
    }
    $plugins = array();
    foreach ($rows as $row) {
      if (!isset($keys)) { $keys = explode('<!>', $row); }
      else {
        $data = explode('<!>',$row);
        foreach ($data as $k=>$v) { $data[$keys[$k]] = $v; }
        $plugins[$data['type']] = $data;
      }
    }
    return ($type === null) ? $plugins : $plugins[$type];
  }
  
  
  function debug($var,$msg=null)
  {
    if (is_array($var) || is_object($var) || is_bool($var)) {
      $var = print_r($var,true);
    }
    
    if ($msg) {
      $msg = "<span style='color: green'>$msg :: \n</span>";
    }
    
    echo "<pre style='text-align:left;'>$msg$var</pre>";
    
  }
  
  function wlog($var,$msg=null) {
    if (is_array($var) || is_object($var) || is_bool($var)) {
      $var = print_r($var,true);
    }
    $content = "\n\n------ ".date('r')." -----";
    if ($msg) $content .= "\n$msg :: \n";
    $content .= $var;
    rc_toolkit::write_to_file("./log.txt", $content, 'a');
  }
  
  
  function get_setting($key)
  {
  	global $setting;
  	//rc_toolkit::debug($setting);
  	return $setting["setting_$key"];
  }
  
  function owner_level($key)
  {
  	global $owner;
  	return $owner->level_info["level_$key"];
  }
  
  function user_level($key)
  {
    global $user;
    return $user->level_info["level_$key"];
  }
  
  function has_owner()
  {
  	global $owner;
  	return $owner->user_exists != 0;
  }
  
  function has_user()
  {
    global $user;
    return $user->user_exists != 0;
  }
  
  
  function has_post($key)
  {
  	return isset($_POST[$key]);
  }
  
  function has_get($key)
  {
    return isset($_GET[$key]);
  }
  
  function has_request($key)
  {
  	return rc_toolkit::has_post($key) || rc_toolkit::has_get($key);
  }
  
  function get_post($key)
  {
  	return $_POST[$key];
  }
  
  function get_get($key)
  {
  	return $_GET[$key];
  }
  
  function get_request($key, $default = null)
  {
    if (rc_toolkit::has_post($key)) {
      $value = rc_toolkit::get_post($key);
    }
    elseif (rc_toolkit::has_get($key)) {
      $value = rc_toolkit::get_get($key);
    }
    else {
      $value = $default;
    }
    return $value;
  }
  
  function truncate_text($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
  {
    if ($text == '') {
      return '';
    }
  
    if (strlen($text) > $length) {
      $truncate_text = substr($text, 0, $length - strlen($truncate_string));
      if ($truncate_lastspace) {
        $truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
      }
      return $truncate_text.$truncate_string;
    }
    else {
      return $text;
    }
  }
  
  function redirect($url)
  {
    header("Location: $url");
    exit();    
  }
  
  function redirect_referer($url)
  {
    if ($_SERVER['HTTP_REFERER'] && "http://".$_SERVER['HTTP_HOST']."/".$_SERVER["REQUEST_URI"]) {
      $url = $_SERVER['HTTP_REFERER'];
    }
    rc_toolkit::redirect($url);
  }
  
  
  function criteria_builder($criterias, $glue='AND', $with_where = false)
  {
  	$cs = array();
  	foreach ($criterias as $k=>$v) {
  	  if (strlen(trim($v))) {
  	    $cs[$k] = " ($v) ";
  	  }
  	}
  	if (count($cs)) {
  	  $c = join($glue, $cs);
  	  if ($with_where) $c = "WHERE $c";
  	}
  	return $c;
  }
  
  
  function db_data_packer($data,$escape=true)
  {
    $set = array();
    
    foreach ($data as $field => $value) {
      if ($escape) $value = addslashes($value);
      array_push($set, $field."='$value'");
    }

    return implode(', ', $set);
  }
  
  function write_to_file($filename, $content, $mode='w')
  {
    $handle = fopen($filename, $mode);
    fwrite($handle, $content);
    fclose($handle);    
  }
  
  function parse_rfc3339( $date ) {
    $date = substr( str_replace( 'T' , ' ' , $date ) , 0 , 19 );
    return strtotime( $date );
  }  
  
  function strip_text($text)
  {
    $text = strtolower($text);
 
    // strip all non word chars
    $text = preg_replace('/\W/', ' ', $text);
    // replace all white space sections with a dash
    $text = preg_replace('/\ +/', '-', $text);
    // trim dashes
    $text = preg_replace('/\-$/', '', $text);
    $text = preg_replace('/^\-/', '', $text);
 
    return $text;
  }
  
  function db_to_datetime($timestamp=null)
  {
    return date('Y-m-d H:i:s', ($timestamp===null?time():$timestamp));
  }

  function string_to_timestamp($string)
  {
    list ($date, $time) = explode(' ', $string);
    list ($y, $m, $d) = explode('-', $date);
    if (strlen($time)) {
    	list ($h, $i, $s) = explode(':', $time);
    }
    else {
    	$h = $i = $s = 0;
    }
    
    $ts = mktime($h, $i, $s, $m, $d, $y);
    return ($ts === -1 || $ts === false) ? null : $ts;
  }
  
  // why I have to
  function get_profile_fields()
  {
    static $fields;
    global $database;
    
    if ($fields === null) {
      $res = $database->database_query("SELECT * FROM se_profilefields ORDER BY profilefield_order ASC");
      while ($row = $database->database_fetch_assoc($res)) {
        $fields[$row['profilefield_id']] = $row;
      }
    }
    
    return $fields;
  }
   
  // write these
  function get_profile_field($field_id)
  {
    $fields = rc_toolkit::get_profile_fields();
    return $fields[$field_id];
  }
  
  // function !!!!
  function get_profile_field_options($field_id)
  {
    $field_info = rc_toolkit::get_profile_field($field_id);
    $options = array();
    $vals = unserialize($field_info['profilefield_options']);
    foreach ($vals as $v) {
      $options[$v['value']] = $v['label'];
      SE_Language::_preload($v['label']);
    }
    SE_Language::load();
    return $options;
  }  
  
  function get_profile_field_real_value($field_id, $value)
  {
    $field = rc_toolkit::get_profile_field($field_id);
    if ($field['profilefield_type'] == 3 || $field['profilefield_type'] == 4) {
      $options = rc_toolkit::get_profile_field_options($field_id);
      return SE_Language::_get($options[$value]);
    }
    else {
      return $value;
    }
  }
  
  function is_admin_dir()
  {
    $script_directory = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/'));
    return (array_pop(explode("/", $script_directory)) == 'admin');
  }  
  
  function get_level_options($levelstring,$name)
  {
	  $options = Array();
		for($p=0;$p<strlen($levelstring);$p++) {
		  $level = substr($levelstring, $p, 1);
		  if(user_privacy_levels($level) != "") {
		    $options[] = Array($name.'_id' => $levelstring.$level,
		             $name.'_value' => $level,
		             $name.'_option' => user_privacy_levels($level));
		  }
		}
		return $options;
  }
  
  // THIS METHOD MAKES A NEGATIVE TIMESTAMP
  // INPUT: SAME ARGUMENTS AS WOULD BE PASSED TO THE PHP FUNCTION mktime()
  // OUTPUT: A TIMESTAMP THAT CAN BE NEGATIVE
  function MakeTime() {
    $objArgs = func_get_args();
    $nCount = count($objArgs);
    if($nCount < 7) {
      $objDate = getdate();
      if($nCount < 1)
        $objArgs[] = $objDate["hours"];
      if($nCount < 2)
        $objArgs[] = $objDate["minutes"];
      if($nCount < 3)
        $objArgs[] = $objDate["seconds"];
      if($nCount < 4)
        $objArgs[] = $objDate["mon"];
      if($nCount < 5)
        $objArgs[] = $objDate["mday"];
      if($nCount < 6)
        $objArgs[] = $objDate["year"];
      if($nCount < 7)
        $objArgs[] = -1;
    }
    $nYear = $objArgs[5];
    $nOffset = 0;

    if($nYear < 1970) {
      $nOffset = -2019686400;
      $objArgs[5] += 64;
      if($nYear < 1942) {
        $objArgs[6] = 0;
      }
    }

    return call_user_func_array("mktime", $objArgs) + $nOffset;
  } // END MakeTime() METHOD  
  
  function MakeDate($time) {
    global $datetime;

    $date = Array();

    if($time < 0) {
      $nOffset = -2019686400;
      $time = $time - $nOffset;
      $date[0] = $datetime->cdate("n", $time);
      $date[1] = $datetime->cdate("j", $time);
      $date[2] = $datetime->cdate("Y", $time)-64;
      $date[3] = $datetime->cdate("F", $time);
    } else {
      $date[0] = $datetime->cdate("n", $time);
      $date[1] = $datetime->cdate("j", $time);
      $date[2] = $datetime->cdate("Y", $time);
      $date[3] = $datetime->cdate("F", $time);
    }
    return $date;
  } // END MakeDate() METHOD  
  
  
    /**
     * See http://www.bin-co.com/php/scripts/load/
     * Version : 2.00.A
     * 
Array
(
    [headers] => Array
        (
            [Date] => Mon, 18 Jun 2007 13:56:22 GMT
            [Server] => Apache/2.0.54 (Unix) PHP/4.4.7 mod_ssl/2.0.54 OpenSSL/0.9.7e mod_fastcgi/2.4.2 DAV/2 SVN/1.4.2
            [X-Powered-By] => PHP/5.2.2
            [Expires] => Thu, 19 Nov 1981 08:52:00 GMT
            [Cache-Control] => no-store, no-cache, must-revalidate, post-check=0, pre-check=0
            [Pragma] => no-cache
            [Set-Cookie] => PHPSESSID=85g9n1i320ao08kp5tmmneohm1; path=/
            [Last-Modified] => Tue, 30 Nov 1999 00:00:00 GMT
            [Vary] => Accept-Encoding
            [Transfer-Encoding] => chunked
            [Content-Type] => text/xml
        )
  [body] => ... Contents of the Page ...
  [info] => Array
        (
            [url] => http://www.bin-co.com/rss.xml.php?section=2
            [content_type] => text/xml
            [http_code] => 200
            [header_size] => 501
            [request_size] => 146
            [filetime] => -1
            [ssl_verify_result] => 0
            [redirect_count] => 0
            [total_time] => 1.113792
            [namelookup_time] => 0.180019
            [connect_time] => 0.467973
            [pretransfer_time] => 0.468035
            [size_upload] => 0
            [size_download] => 2274
            [speed_download] => 2041
            [speed_upload] => 0
            [download_content_length] => 0
            [upload_content_length] => 0
            [starttransfer_time] => 0.826031
            [redirect_time] => 0
        )
)
     * 
     */
    function get_url ($url, $options = array())
    {
      $default_options = array('method' => 'get' , 'return_info' => false , 'return_body' => true , 'cache' => false , 'referer' => '' , 'headers' => array() , 'session' => false , 'session_close' => false);
      // Sets the default options.
      foreach ($default_options as $opt => $value)
      {
        if (! isset($options[$opt]))
          $options[$opt] = $value;
      }
      $url_parts = parse_url($url);
      $ch = false;
      $info = array(//Currently only supported by curl.
    'http_code' => 200);
      $response = '';
      $send_header = array('Accept' => 'text/*' , 'User-Agent' => 'PHP SocialEngine - Radcodes Core Library') + $options['headers']; // Add custom headers provided by the user.

      ///////////////////////////// Curl /////////////////////////////////////
      //If curl is available, use curl to get the data.
      if (function_exists("curl_init") and (! (isset($options['use']) and $options['use'] == 'fsocketopen')))
      { //Don't use curl if it is specifically stated to use fsocketopen in the options
        if (isset($options['post_data']))
        { //There is an option to specify some data to be posted.
          $page = $url;
          $options['method'] = 'post';
          if (is_array($options['post_data']))
          { //The data is in array format.
            $post_data = array();
            foreach ($options['post_data'] as $key => $value)
            {
              $post_data[] = "$key=" . urlencode($value);
            }
            $url_parts['query'] = implode('&', $post_data);
          }
          else
          { //Its a string
            $url_parts['query'] = $options['post_data'];
          }
        }
        else
        {
          if (isset($options['method']) and $options['method'] == 'post')
          {
            $page = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
          }
          else
          {
            $page = $url;
          }
        }
        if ($options['session'] and isset($GLOBALS['_binget_curl_session']))
          $ch = $GLOBALS['_binget_curl_session']; //Session is stored in a global variable
        else
          $ch = curl_init($url_parts['host']);
        curl_setopt($ch, CURLOPT_URL, $page) or die("Invalid cURL Handle Resouce");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Just return the data - not print the whole thing.
        curl_setopt($ch, CURLOPT_HEADER, true); //We need the headers
        curl_setopt($ch, CURLOPT_NOBODY, ! ($options['return_body'])); //The content - if true, will not download the contents. There is a ! operation - don't remove it.
        if (isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query']))
        {
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $url_parts['query']);
        }
        //Set the headers our spiders sends
        curl_setopt($ch, CURLOPT_USERAGENT, $send_header['User-Agent']); //The Name of the UserAgent we will be using ;)
        $custom_headers = array("Accept: " . $send_header['Accept']);
        if (isset($options['modified_since']))
          array_push($custom_headers, "If-Modified-Since: " . gmdate('D, d M Y H:i:s \G\M\T', strtotime($options['modified_since'])));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
        if ($options['referer'])
          curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/binget-cookie.txt"); //If ever needed...
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (isset($url_parts['user']) and isset($url_parts['pass']))
        {
          $custom_headers = array("Authorization: Basic " . base64_encode($url_parts['user'] . ':' . $url_parts['pass']));
          curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
        }
        $response = curl_exec($ch);
        $info = curl_getinfo($ch); //Some information on the fetch
        if ($options['session'] and ! $options['session_close'])
          $GLOBALS['_binget_curl_session'] = $ch; //Dont close the curl session. We may need it later - save it to a global variable
        else
          curl_close($ch); //If the session option is not set, close the session.
      //////////////////////////////////////////// FSockOpen //////////////////////////////
      }
      else
      { //If there is no curl, use fsocketopen - but keep in mind that most advanced features will be lost with this approch.
        if (isset($url_parts['query']))
        {
          if (isset($options['method']) and $options['method'] == 'post')
            $page = $url_parts['path'];
          else
            $page = $url_parts['path'] . '?' . $url_parts['query'];
        }
        else
        {
          $page = $url_parts['path'];
        }
        if (! isset($url_parts['port']))
          $url_parts['port'] = 80;
        $fp = fsockopen($url_parts['host'], $url_parts['port'], $errno, $errstr, 30);
        if ($fp)
        {
          $out = '';
          if (isset($options['method']) and $options['method'] == 'post' and isset($url_parts['query']))
          {
            $out .= "POST $page HTTP/1.1\r\n";
          }
          else
          {
            $out .= "GET $page HTTP/1.0\r\n"; //HTTP/1.0 is much easier to handle than HTTP/1.1
          }
          $out .= "Host: $url_parts[host]\r\n";
          $out .= "Accept: $send_header[Accept]\r\n";
          $out .= "User-Agent: {$send_header['User-Agent']}\r\n";
          if (isset($options['modified_since']))
            $out .= "If-Modified-Since: " . gmdate('D, d M Y H:i:s \G\M\T', strtotime($options['modified_since'])) . "\r\n";
          $out .= "Connection: Close\r\n";
          //HTTP Basic Authorization support
          if (isset($url_parts['user']) and isset($url_parts['pass']))
          {
            $out .= "Authorization: Basic " . base64_encode($url_parts['user'] . ':' . $url_parts['pass']) . "\r\n";
          }
          //If the request is post - pass the data in a special way.
          if (isset($options['method']) and $options['method'] == 'post' and $url_parts['query'])
          {
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= 'Content-Length: ' . strlen($url_parts['query']) . "\r\n";
            $out .= "\r\n" . $url_parts['query'];
          }
          $out .= "\r\n";
          fwrite($fp, $out);
          while (! feof($fp))
          {
            $response .= fgets($fp, 128);
          }
          fclose($fp);
        }
      }
      //Get the headers in an associative array
      $headers = array();
      if ($info['http_code'] == 404)
      {
        $body = "";
        $headers['Status'] = 404;
      }
      else
      {
        //Seperate header and content
        $header_text = substr($response, 0, $info['header_size']);
        $body = substr($response, $info['header_size']);
        foreach (explode("\n", $header_text) as $line)
        {
          $parts = explode(": ", $line);
          if (count($parts) == 2)
            $headers[$parts[0]] = chop($parts[1]);
        }
      }
      //rc_toolkit::debug($headers,'headers');
      if ($options['return_info'])
        return array('headers' => $headers , 'body' => $body , 'info' => $info , 'curl_handle' => $ch);
      return $body;
    } 

  // get_url
  
  function get_url_content($url, $options=array()) {
  	global $setting;
  	
  	if ($setting['setting_radcodes_remote_type'] == 'CURL') {
  	  $content = rc_toolkit::get_url($url, $options);
  	}
  	else {
  	  $content = file_get_contents($url);
  	}
  	return $content;
  }
  
  function search_field_fuzzy_fragment($column, $words, $match)
  {
    $words = trim(str_replace("  "," ",$words));
    if ($match == 'exact') {
      $out = "$column LIKE '%$words%'";
    }
    else if ($match == 'any') {
      $words = explode(" ", $words);
      foreach ($words as $word) {
        if ($out) $out .= " OR ";
        $out .= "$column LIKE '%$word%'";
      }
    }
    else {
      $words = explode(" ", $words);
      foreach ($words as $word) {
        if ($out) $out .= " AND ";
        $out .= "$column LIKE '%$word%'";
      }
    }
    return $out;
  }
}


class rc_validator
{
  
  var $errors;
  
  function rc_validator()
  {
    $this->clear_errors();
  }
  
  function clear_errors()
  {
    $this->errors = array();
  }
  
  function has_errors()
  {
    return count($this->errors);
  }
  
  function has_error($key)
  {
    return isset($this->errors[$key]);
  }
  
  function get_errors()
  {
    // v3 compat .. uh ..
    if (class_exists('SE_Language')) {
      foreach ($this->errors as $k=>$v) {
        if (is_numeric($v)) {
          SE_Language::_preload($v);
          $has_lang_id = true;
        }
      }
      $errors = array();
      if ($has_lang_id) {
        SE_Language::load();
      }
      foreach ($this->errors as $k=>$v) {
        if (is_numeric($v)) {
          $v = SE_Language::_get($v);
        }
        $errors[$k] = $v;
      }
      
    }
    else
    {
      $errors = $this->errors;
    }
    

    return $errors;
  }
  
  function get_error($key)
  {
    return $this->has_error($key) ? $this->errors[$key] : null;
  }
  
  function set_error($message, $key=null)
  {
    // v3 com
    if (is_numeric($message)) SE_Language::_preload($message);
    if ($key === null) {
      $this->errors[] = $message;
    }
    else {
      $this->errors[$key] = $message;
    }
  }
  
  function validate($expression, $message, $key=null)
  {
    if ($expression===true) {
      return true;
    }
    else {
      $this->set_error($message, $key);
      return false;
    }
  }
  
  function is_not_blank($value, $message, $key=null)
  {
    return $this->validate(strlen($value) > 0, $message, $key);
  }
  
  function is_not_trimmed_blank($value, $message, $key=null)
  {
    return $this->is_not_blank(trim($value), $message, $key);
  }
  
  function is_email($value, $messsage, $key=null)
  {
    return ($this->validate(preg_match('|^[\w\d][\w\d\,\.\-]*\@([\w\d\-]+\.)+([a-zA-Z]+)$|', $data) > 0, $message, $key));
  }
  
  function is_number($value, $message, $key=null)
  {
    return $this->validate(is_numeric($value), $message, $key);
  }
  
  function is_date($value, $message, $key=null) 
  {
    list ($y, $m, $d) = explode('-', $value);
    if (is_numeric($y) && is_numeric($m) && is_numeric($d)) {
      return $this->validate(checkdate($m,$d,$y), $message, $key);
    }
    else {
      return $this->validate(false, $message, $key);
    }
  }
  
  function is_datetime($value, $message, $key=null)
  {
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $value, $ms)) {
      return $this->validate(checkdate($ms[2], $ms[3], $ms[1]), $message, $key);
    }
    return $this->validate(false, $message, $key);
  }
  
  function is_datetime_str($value, $message, $key=null) {
  	
    $ts = strtotime($value);
    if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
      return $this->validate($ts !== false, $message, $key);
    }
    else {
      return $this->validate($ts != -1, $message, $key);
    }
  }
  
  
  function is_url($value, $message, $key=null)
  {
    $pattern = '~^
      (https?|ftps?)://                       # http or ftp (+SSL)
      (
        ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
          |                                   #  or
        \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
      )
      (:[0-9]+)?                              # a port (optional)
      (/?|/\S+)                               # a /, nothing or a / with something
    $~ix';
    
    return $this->validate(preg_match($pattern, $value) > 0, $message, $key);
    
  }
  
  
  
  function license($value, $type, $key=null)
  {
    global $global_plugins;
    if (!is_array($global_plugins)) $global_plugins = array();
    foreach ($global_plugins as $k=>$v) {
      if ($v[plugin_type] == $type) {
        $version = $v[plugin_version];
      }
    }
    
  	$http_host = str_replace("www.","",strtolower($_SERVER['HTTP_HOST']));
  	$response = rc_toolkit::get_url_content("http://www.radcodes.com/gateway/license.php?key=$value&domain=$http_host&type=$type&version=$version");
  	if (strstr($response,"CONFIRM")) {
  		list($res,$domains,$error) = explode("::",$response);
      return $this->validate(in_array($http_host, explode(',',$domains)), "Invalid License", $key);
  	} elseif (strstr($response,"ERROR")) {
  	  return $this->validate(false, str_replace("ERROR::","",$response), $key);
  	}
  }
}

class rc_xml_parser {
  
  function get_children($vals, &$i) { 
    $children = array();
    if (isset($vals[$i]['value'])){
      $children['VALUE'] = $vals[$i]['value'];
    } 
    
    while (++$i < count($vals)){ 
      switch ($vals[$i]['type']){
        
        case 'cdata': 
        if (isset($children['VALUE'])){
          $children['VALUE'] .= $vals[$i]['value'];
        } 
		else {
          $children['VALUE'] = $vals[$i]['value'];
        } 
        break;
        
        case 'complete':
        if (isset($vals[$i]['attributes'])) {
          $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
          $index = count($children[$vals[$i]['tag']])-1;
      
          if (isset($vals[$i]['value'])){ 
            $children[$vals[$i]['tag']][$index]['VALUE'] = $vals[$i]['value']; 
          }
		  else {
            $children[$vals[$i]['tag']][$index]['VALUE'] = '';
          }
        }
		else {
          if (isset($vals[$i]['value'])){
            $children[$vals[$i]['tag']][]['VALUE'] = $vals[$i]['value']; 
          }
		  else {
            $children[$vals[$i]['tag']][]['VALUE'] = '';
          } 
        }
        break;
        
        case 'open': 
        if (isset($vals[$i]['attributes'])) {
          $children[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes'];
          $index = count($children[$vals[$i]['tag']])-1;
          $children[$vals[$i]['tag']][$index] = array_merge($children[$vals[$i]['tag']][$index],$this->get_children($vals, $i));
        }
		else {
          $children[$vals[$i]['tag']][] = $this->get_children($vals, $i);
        }
        break; 
      
        case 'close': 
        return $children; 
      } 
    }
  }
  
  
  
  function get_xml_tree($data) { 
    if( ! $data )
      return false;
  
    $parser = xml_parser_create('UTF-8');
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
    xml_parse_into_struct($parser, $data, $vals, $index); 
    xml_parser_free($parser); 
  
    $tree = array(); 
    $i = 0; 
  
    if (isset($vals[$i]['attributes'])) {
      $tree[$vals[$i]['tag']][]['ATTRIBUTES'] = $vals[$i]['attributes']; 
      $index = count($tree[$vals[$i]['tag']])-1;
      $tree[$vals[$i]['tag']][$index] =  array_merge($tree[$vals[$i]['tag']][$index], $this->get_children($vals, $i));
    }
	else {
      $tree[$vals[$i]['tag']][] = $this->get_children($vals, $i); 
    }
    return $tree; 
  }
}


class rc_tagcloud extends rc_model
{
  var $pk = 'tag_id';  
  var $case_insensitive = true;
  
  function delete_name($name)
  {
    $criteria = rc_toolkit::db_data_packer(array('tag_name'=>$name));
    return $this->delete_by_criteria($criteria);
  }
  
  function log_tag($name)
  {
    // just some safety
    if ($name=='') return false;
    if ($this->case_insensitive) $name = strtolower($name);
    $data = array('tag_name'=>$name);
    $data_string = rc_toolkit::db_data_packer($data);
    
    $tag = $this->get_record_by_criteria($data_string);
    if ($tag) {
      $data['tag_count'] = $tag['tag_count'] + 1;
      $this->update($tag[$this->pk],$data);
      return $tag[$this->pk];
    }
    else {
      $data['tag_count'] = 1;
      return $this->insert($data);
    }
  }
  
  function get_cloud($max_entry, $order_by='count', $sort=null)
  {
    $records = $this->get_records("ORDER BY tag_count desc LIMIT $max_entry");
    $columns = array();
    $i=0;
    foreach ($records as $k=>$v) {
      $records[$k]['rank'] = ++$i;
      $columns[$k] = ($order_by == 'name') ? $records[$k]['tag_name'] : $records[$k]['tag_count'];
    }
    
    if ($sort === null) {
      $sort = ($order_by=='count') ? SORT_DESC : SORT_ASC;
    }
    
    array_multisort($columns, $sort, $records);
    return $records;
  }
  
  
}


class rc_cat extends rc_model
{
  var $pk = 'cat_id';
  var $pd = 'cat_dependency';
  var $pt = 'cat_title';
  var $po = 'cat_order';
  
  var $_ref = array('table'=>'','pk'=>'','pc'=>'');
  
  function _build_get_records_query($criteria=null)
  {
  	$criteria = str_replace(array($this->pk, $this->pd, $this->pt, $this->po),
  	  array('a.'.$this->pk, 'a.'.$this->pd, 'a.'.$this->pt, 'a.'.$this->po),
  	  $criteria
  	);
  	
    $query = "SELECT a.*, b.cat_id as parent_cat_id, b.cat_title as parent_cat_title FROM $this->table as a
     LEFT JOIN $this->table as b ON b.{$this->pk} = a.{$this->pd}
     $criteria
    ";

    return $query;
  }  
  
  function FindRecordsByCriteria($criteria='', $key=true)
  {
    $objs = array();
    $datas = $this->get_records($criteria, $key);
    
    //rc_toolkit::debug($datas,"datas");
    
    foreach ($datas as $k => $data) {
      $obj = new $this->_class($this);
      $obj->setProperties($data);
      $obj->parent_cat_id = $data['parent_cat_id'];
      $obj->parent_cat_title = $data['parent_cat_title'];
      $objs[$k] = $obj;    
    }
    return $objs;
  }  
  
  function count_items($options=array())
  {
    $rc = $this->_ref['pc'];
    $query = "SELECT $rc as name, COUNT($rc) as total FROM {$this->_ref['table']}
    LEFT JOIN $this->table ON $this->table.$this->pk = $rc
    GROUP BY $rc 
    ";
    
    return $this->ResultFunctionFieldQuery($query,'name','total');
  }
  
  
  function get_struct_categories($pid=null)
  {
  	static $cats;
  	
  	if ($cats === null) {
  	  $records = $this->FindRecordsByCriteria("ORDER BY $this->po ASC");   
	    $cats = array();
	    foreach ($records as $k => $r) {
	      $parent = $r->{$this->pd} > 0 ? $r->{$this->pd} : 0;
	      $cats[$parent][$r->{$this->pk}] = $r;
	    }
  	}
    return $pid === null ? $cats : (array)$cats[$pid];
  }
  
  
  function get_categories($parent=0,$subcat=true)
  {    
  	$cats = $this->get_struct_categories($parent);
  	
  	if ($subcat) {
  		foreach ($cats as $k=>$v) {
  			$cats[$k]->subcategories = $this->get_categories($v->cat_id, false);
  		}
  	}
  	
  	return $cats;
  }
  
  function get_category_tree()
  {
    $stats = $this->count_items();
    $cats = $this->get_categories(0,true);
    
    $sumcats = 0;
    foreach ($cats as $k=>$v) {
      $cnt = $stats[$k] ? $stats[$k] : 0;
      $cats[$k]->count = $cnt;
      
      $subtotal = 0;
      if (is_array($v->subcategories)) {
        foreach ($v->subcategories as $kk=>$vv) {
          $cnt = $stats[$kk] ? $stats[$kk] : 0;
          $cats[$k]->subcategories[$kk]->count = $cnt;
          $subtotal += $cnt;
        }
      }
      $cats[$k]->subtotal = $subtotal;
      $cats[$k]->total = $cats[$k]->count + $cats[$k]->subtotal;
      $sumcats += $cats[$k]->total;
    }
    
    $total = array_sum($stats);
    $uncategorized = $total - $sumcats;
    
    $tree = array(
      'stats' => $stats,
      'categories' => $cats,
      'total' => $total,
      'uncategorized' => $uncategorized
    );
    
  }
  
  
  function get_max_id()
  {
    $row = $this->db->database_fetch_assoc($this->db->database_query("SELECT max($this->pk) AS max_id FROM $this->table"));
    return $row['max_id'];
  }  
  
  function add_new($title, $parent=0)
  {
  	$class = get_class($this);
  	$obj = new $class;
  	$obj->{$obj->pt} = $title;
  	$obj->{$obj->pd} = (int) $parent;
  	$obj->{$obj->po} = $this->get_max_id() + 1;
  	$obj->save();
  	return $obj;
  }
  
  function delete_category($id)
  {
    $subcats = $this->get_categories($id,false);
    $cat_ids = array($id);
    if (count($subcats)) {
      $cat_ids = array_merge($cat_ids, array_keys($subcats));
    }
    $cat_id_str = join("','",$cat_ids);
  	$this->delete_by_criteria("$this->pd = '$id'");
  	$this->delete($id);
  	
  	$query = "UPDATE {$this->_ref['table']} SET {$this->_ref['pc']} = '0' WHERE {$this->_ref['pc']} IN ('$cat_id_str')";
  	$this->db->database_query($query);
  }
  
  function ordering($direction)
  {
  	if ($direction == 'down') {
  		$operation = '>';
  		$ordering = 'asc';
  	}
  	else {
  		$operation = '<';
  		$ordering = 'desc';
  	}
  	
  	$cat_id = $this->{$this->pk};
  	$cat_order = $this->{$this->po};
  	$cat_dependency = $this->{$this->pd};
  	
  	$other = $this->FindRecordByCriteria("$this->pk <> '$cat_id' AND $this->pd = '$cat_dependency' AND $this->po $operation '$cat_order' ORDER BY $this->po $ordering");
  	
  	if ($other) {
  		$this->{$this->po} = $other->{$this->po};
  		$this->save();
  		$other->{$this->po} = $cat_order;
  		$other->save();
  	}

  }
  
  
  
  
}


class rc_categories extends rc_model
{
  var $pk = 'id'; // primary
  var $pd = 'dependency'; // dependency col
  var $pt = 'title'; // title col
    
  
  function get_categories($parent=0,$subcat=true)
  {
    $rows = $this->get_records("WHERE $this->pd = '$parent' ORDER BY $this->pk",true);
    if ($subcat) {
      foreach ($rows as $k=>$row) {
        $rows[$k]['subcategories'] = $this->get_categories($row[$this->pk],false);
        $rows[$k]['next_subcat'] = 1;
        if (count($rows[$k]['subcategories'])) {
          $rows[$k]['next_subcat'] += max(array_keys($rows[$k]['subcategories']));
        }
      }
    }
    return $rows;
  }
  
  function get_max_id()
  {
    $row = $this->db->database_fetch_assoc($this->db->database_query("SELECT max($this->pk) AS max_id FROM $this->table"));
    return $row['max_id'];
  }
  
  function save_categories($maincats, $subcats)
  {
    if (!is_array($maincats)) return;
    foreach ($maincats as $mid=>$title) {
      if (!strlen(trim($title))) { // title is blank, ie 
        $this->delete($mid); // delete row
        $this->delete_by_criteria("$this->pd = '$mid'"); // delete all children
      }
      else {
        $mctemp = $this->get_record_by_criteria("$this->pk = '$mid' and $this->pd = '0'"); // check if row exists
        if ($mctemp) {
          $mctemp[$this->pt] = $title;
          $this->update($mctemp[$this->pk],$mctemp); // update
          $nid = $mid;
        }
        else {
          $mctemp = array($this->pt => $title, $this->pd => 0);
          $nid = $this->insert($mctemp);
        }
        
        if (is_array($subcats[$mid])) {
          foreach ($subcats[$mid] as $sid => $title) {
            if (!strlen(trim($title))) {
              $this->delete_by_criteria("$this->pk = '$sid' and $this->pd = '$mid'");
            }
            else {
              $sctemp = $this->get_record_by_criteria("$this->pk = '$sid' and $this->pd = '$mid'");
              if ($sctemp) {
                $sctemp[$this->pt] = $title;
                $this->update($sctemp[$this->pk], $sctemp);
              }
              else {
                $sctemp = array($this->pt => $title, $this->pd => $nid);
                $this->insert($sctemp);
              }
            }
          }
        }
      }
    }
  }
}


class rc_tag extends rc_model {

	var $table = null;
	
	var $pk = 'tag_id'; // primary key
	var $po = 'tag_object_id'; // object ref key
	var $pn = 'tag_name';

	var $_glue = ',';
	
	function clean_input_tags($input_tags)
	{
		if (is_array($input_tags)) {
			$tags = array();
		  foreach ($input_tags as $k => $v) {
        $trim_v = trim($v);
        if (strlen($trim_v)) {
          $tags[] = $trim_v;
        }
      }
      return $tags;
		}
		else {
			return $this->clean_input_tags(explode($this->_glue, $input_tags));
		}
	}
	
	
  function get_object_tags($object_id)
  {
    $tags = array();
    
    $query = "SELECT * FROM $this->table WHERE $this->po = '$object_id'";
    $res = $this->db->database_query($query);
    while ($row = $this->db->database_fetch_assoc($res)) {
      $tags[$row[$this->pk]] = $row[$this->pn];
    }
    
    return $tags;
  }
  

  function update_object_tags($object_id, $string_tags)
  {    
    $cur_tags = $this->get_object_tags($object_id);
    $new_tags = $this->clean_input_tags($string_tags);

    $insert_tags = array_diff($new_tags, $cur_tags);
    //rc_toolkit::debug($cur_tags,'$cur_tags');
    //rc_toolkit::debug($insert_tags,'insert_tgas');
    foreach ($insert_tags as $name) {
    	$data = array($this->po => $object_id, $this->pn => $name);
    	$this->insert($data);
    }
    
    $delete_tags = array_diff($cur_tags, $new_tags);
    foreach ($delete_tags as $name) {
    	$this->delete_by_criteria("$this->po = '$object_id' AND $this->pn = '$name'");
    }
  }
  
  function delete_object_tags($object_id) 
  {
  	$this->delete_by_criteria("$this->po = '$object_id'");
  }
  
  
  function get_popular_tags($limit=100, $order_by='count', $sort=null, $classes=array(1,3,7,10,20,40,65,100), $where=null)
  {
    $query = "SELECT $this->pn as name, COUNT(*) as count FROM $this->table $where GROUP BY $this->pn ORDER BY count DESC LIMIT $limit";
    $res = $this->db->database_query($query);
    $columns = array();
    $i = 0;
    
    $max_class = count($classes) + 1;
    $records = array();
    while ($row = $this->db->database_fetch_assoc($res)) {
      $i++;
      $records[$i] = $row;
      $records[$i]['rank'] = $i;
      $columns[$i] = ($order_by == 'name') ? $records[$i]['name'] : $records[$i]['count'];
      
      foreach ($classes as $k=>$bound) {
      	if ($i <= $bound) {
      		$records[$i]['class'] = $k + 1;
      		break;
      	}
      }
      if (!isset($records[$i]['class'])) $records[$i]['class'] = $max_class;
    }
    
    if ($sort === null) {
      $sort = ($order_by=='count') ? SORT_DESC : SORT_ASC;
    }
    
    array_multisort($columns, $sort, $records);
    return $records;
  }
  
  function get_object_ids_tagged_with($tags)
  {
  	$ids = array();
  	$tags = $this->clean_input_tags($tags);
  	$query = "SELECT DISTINCT $this->po FROM $this->table WHERE $this->pn IN ('" .join("','", $tags). "')";
  	$res = $this->db->database_query($query);
  	while ($row = $this->db->database_fetch_assoc($res)) {
  		$ids[] = $row[$this->po];
  	}
  	return $ids;
  }

  function get_object_tags_by_object_ids($ids)
  {
    $criteria = "WHERE $this->po IN ('". join("','",$ids) ."')";
    $records = $this->FindRecordsByCriteria($criteria);
    $data = array();
    foreach ($records as $row) {
      $data[$row->{$this->po}][$row->{$this->pk}] = $row->{$this->pn};
    }
    return $data;
  }
  
}

class rc_level extends rc_model 
{
  var $table = 'se_levels';
  var $pk = 'level_id';
}

class rc_field extends rc_model 
{
  var $table = 'se_fields';
  var $pk = 'field_id';
}

class rc_user extends rc_model {
  var $table = 'se_users';
  var $pk = 'user_id';
  
  /**
   * @var se_user
   */
  var $se_user;
  
  function rc_user()
  {
  	rc_model::rc_model();
  	$this->se_user = new se_user();
  }
  
  
  function setProperties($data)
  {
  	rc_model::setProperties($data);
  	$this->load_se_user();
  }
  
  
  
  function load_se_user()
  { 	
		$user = new se_user();
		$user->user_exists = 1;
		$user->user_info = $this->getProperties();
    $this->se_user = $user;
    $this->se_user->user_displayname();
    $this->user_displayname_short = $this->se_user->user_displayname_short;
    $this->user_displayname = $this->se_user->user_displayname;
    return $this->se_user;
  }
  
}

class rc_profile extends rc_model {
  var $table = 'se_profile';
  var $pk = 'profile_id';
}


class rc_comment extends rc_model 
{
  var $type = '';
  var $table;
  var $pk; // id
  var $po; // comment_[type]_id
  var $pa; // authoruser_id
  var $pd; // comment_date
  var $pb; // comment_body
  
  var $identifier;
  
  function rc_comment($identifier=null)
  {
    $type = $this->type;
    $this->table = "rc_{$type}comments";
    $this->pk = $type.'comment_id';
    $this->po = $type.'comment_'.$type.'_id';
    $this->pa = $type.'comment_authoruser_id';
    $this->pd = $type.'comment_date';
    $this->pb = $type.'comment_body';
    
    $this->identifier = $identifier;
    
    rc_model::rc_model();
  }
  
  function count_by_object_ids($ids)
  {
    $criteria = "WHERE $this->po IN ('" . join("','",$ids) . "')";
    return $this->CountFieldByCriteria($this->po, $criteria);
  }
  
  function _build_get_records_query($criteria=null)
  {
    $u = new rc_user();
    $query = "SELECT {$this->table}.*, se_users.* FROM $this->table
     JOIN $u->table ON {$this->table}.{$this->pa} = {$u->table}.{$u->pk}
     $criteria";
     
     return $query;
  }  
  
  function FindRecordsByCriteria($criteria='', $key=true)
  {
    $objs = array();
    $datas = $this->get_records($criteria, $key);
    foreach ($datas as $k => $data) {
      $obj = new $this->_class($this);
      $obj->setProperties($data);
      
      $rc_user = new rc_user();
      $rc_user->setProperties($data);
      $obj->rc_user = $rc_user;
      
      $objs[$k] = $obj;    
    }
    return $objs;
  }
  
  function comment_total()
  {
    return $this->CountByCriteria("WHERE $this->po='$this->identifier'");
  }
  
  function comment_list($start, $limit)
  {
    $critiera = "WHERE $this->po = '$this->identifier' ORDER BY $this->pk DESC LIMIT $start, $limit";
     
    $comments = $this->FindRecordsByCriteria($critiera); 
     
    $comment_array = array();
    foreach ($comments as $comment) {
      
      $comment_array[] = array(
        'comment_id' => $comment->{$this->pk},
        'comment_author' => $comment->rc_user->se_user,
        'comment_date' => $comment->{$this->pd},
        'comment_body' => $comment->{$this->pb}
      );
    }
    
    return $comment_array;

  }
  
  function comment_delete_selected($start, $limit)
  {
    $ids = array();
    foreach ($_POST as $k=>$v) {
      if (strstr($k, 'comment_') && $v==1) {
        $ids[] = str_replace('comment_','', $k);
      }
    }
    $this->delete_by_criteria("$this->po = '$this->identifier' AND $this->pk IN ('" . join("','",$ids) . "')");
  }
  
  function comment_post($object_id, $author_id, $body)
  {
    $class = get_class($this);
    $object = new $class;
    $object->{$object->po} = $object_id;
    $object->{$object->pa} = $author_id;
    $object->{$object->pb} = $body;
    $object->{$object->pd} = time();

    return $object->save();
  }
  
}


class rc_rating {
	
  var $type;
  
  // replace type with $type
  var $table; // se_typeratings
  var $pk; // typerating_id
  var $po; // typerating_type_id
  var $pu; // typerating_user_id
  var $pr; // typerating_rating
  var $pd; // typerating_daterated
  
  var $table_object; // se_types
  
  function rc_rating($type, $options=array()) {
    
  	$this->type = $type;
  	
    $this->table = "se_{$type}ratings";
    $this->table_object = "se_{$type}s";
    $this->pk = "{$type}rating_id";
    $this->po = "{$type}rating_{$type}_id";
    $this->pu = "{$type}rating_user_id";
    $this->pr = "{$type}rating_rating";
    $this->pd = "{$type}rating_daterated";
  }
  
  function rating_list($object_id, $start, $limit, $sort_by="", $where="") {
  	global $database;
  	
  	$cs = array("$this->po = '$object_id'");
  	if ($where) $cs['where'] = $where;
  	$condition = rc_toolkit::criteria_builder($cs);
  	
  	if (!$sort_by) $sort_by = "$this->pd DESC";
  	
  	$sql = "SELECT {$this->table}.*, se_users.user_id, se_users.user_username, se_users.user_fname, se_users.user_lname, se_users.user_photo
  	 FROM {$this->table} 
  	 JOIN se_users ON $this->pu = se_users.user_id
  	 WHERE $condition
  	 ORDER BY $sort_by
  	 LIMIT $start, $limit
  	 ";
  	//rc_toolkit::debug($sql,'get_raters sql');
  	$ratings = array();
  	
  	$res = $database->database_query($sql);
  	while ($row = $database->database_fetch_assoc($res)) {
  	  $se_user = rc_toolkit::init_se_user_from_data($row);
  	  $row['user'] = $se_user;
  	  $ratings[] = $row;
  	}
  	return $ratings;
  }
  
  
  function get_rating($user_id, $object_id) {
  	global $database;
  	$sql = "SELECT * FROM $this->table WHERE $this->pu = '$user_id' AND $this->po = '$object_id' LIMIT 1";
  	$res = $database->database_query($sql);
  	$rating = $database->database_fetch_assoc($res);
  	//rc_toolkit::debug($rating,'get_rating');
  	return $rating;
  }
  // get_rating
  
  function has_rated($user_id, $object_id) {
  	$rating = $this->get_rating($user_id, $object_id);
  	return !empty($rating);
  }
  // has_rated
  
  function rating_total($object_id) {
  	global $database;
  	
  	$sql = "SELECT COUNT(*) as total FROM $this->table WHERE $this->po = '$object_id'";
    $res = $database->database_query($sql);
    $rating = $database->database_fetch_assoc($res);
    
    return $rating['total'] > 0 ? $rating['total'] : 0;
  }
  // rating_total
  
  function add_rating($user_id, $object_id, $rating, $update_cache = true) {
  	global $database;
  	
  	$type =& $this->type;
  	
  	$data = array(
  	  $this->pu => $user_id,
  	  $this->po => $object_id,
  	  $this->pr => $rating,
  	  $this->pd => time()
  	);
  	
  	$rating_total = $this->rating_total($object_id);
  	
  	$sql = "INSERT INTO $this->table SET " . rc_toolkit::db_data_packer($data);
  	$res = $database->database_query($sql);
  	$rating_id = $database->database_insert_id();
  	
  	$return_data = array('rating_id' => $rating_id);
  	
  	if ($update_cache) {
  	  
  	  // GET AVERAGE RATING / NUM VOTES FOR BAYESIAN WEIGHTED RATING
      $sql = "SELECT avg({$type}_cache_rating) AS average_rating, avg({$type}_cache_rating_total) AS average_total FROM $this->table_object WHERE {$type}_cache_rating_total<>'0'";
      //rc_toolkit::debug($sql);
      $avg = $database->database_fetch_assoc($database->database_query($sql));
      
      $sql = "SELECT {$type}_cache_rating as cache_rating FROM $this->table_object WHERE {$type}_id = '$object_id'";
      //rc_toolkit::debug($sql);
      $object_info = $database->database_fetch_assoc($database->database_query($sql));

      // GET TOTAL RATING
      $new_total = $rating_total + 1;
      $new_rating = ($object_info[cache_rating]*$rating_total+$rating) / $new_total;
      
      $new_rating_weighted = ( ($avg[average_total] * $avg[average_rating]) + ($new_total * $new_rating) ) / ($avg[average_total] + $new_total);
    
      // SET NEW CACHED RATINGS
      $sql = "UPDATE $this->table_object SET 
        {$type}_cache_rating='$new_rating', 
        {$type}_cache_rating_weighted='$new_rating_weighted', 
        {$type}_cache_rating_total='$new_total' 
        WHERE {$type}_id = '$object_id'";
      //rc_toolkit::debug($sql);
      $database->database_query($sql);
      
      $return_data[rating_full] = floor($new_rating);
      $return_data[rating_part] = (($new_rating-$return_data[rating_full]) == 0) ? 0 : 1;
      $return_data[rating_none] = 5-$return_data[rating_full]-$return_data[rating_part];
      $return_data[rating_total] = $new_total;
      
  	}
  	
  	return $return_data;
  }
  // add_rating
  
  
}



class rc_vote extends rc_model {

  var $table = null;
  
  var $pk = 'vote_id';     // primary key
  var $po = 'vote_object_id'; // object ref key
  var $pu = 'vote_user_id'; // user id
  var $pd = 'vote_date';    // create
  var $pc = 'vote_comment'; // comment
  
  function _pre_save()
  {
  	if ($this->isNew() and empty($this->{$this->pd})) {
  		$this->{$this->pd} = time();
  	}
  }
  
  
  function _build_count_records_query($criteria=null)
  {
    $u = new rc_user();
    
    $query = "SELECT COUNT({$this->table}.{$this->pk}) as total FROM $this->table
     JOIN {$u->table} ON {$u->table}.{$u->pk} = {$this->table}.{$this->pu}
     $criteria
    ";
     
    return $query;
  }
  
  
  function _build_get_records_query($criteria=null)
  {
  	$u = new rc_user();
  	
    $query = "SELECT * FROM $this->table
     JOIN {$u->table} ON {$u->table}.{$u->pk} = {$this->table}.{$this->pu}
     $criteria
    ";
     
    return $query;
  }
  
  
  function FindRecordsByCriteria($criteria='', $key=true)
  {
    $objs = array();
    $datas = $this->get_records($criteria, $key);
    foreach ($datas as $k => $data) {
      $obj = new $this->_class($this);
      $obj->setProperties($data);
      
      $rc_user = new rc_user();
      $rc_user->setProperties($data);
      $obj->rc_user = $rc_user;
      
      $objs[$k] = $obj;    
    }
    return $objs;
  }
  
  
  function get_vote($user_id, $object_id)
  {
  	return $this->FindRecordByCriteria("$this->po='$object_id' AND $this->pu='$user_id'");
  }
  
  
  function has_vote($user_id, $object_id)
  {
    $total = $this->CountByCriteria("WHERE $this->po='$object_id' AND $this->pu='$user_id'");
    return $total > 0;
  }

  function count_by_user_id($user_id)
  {
    $criteria = "WHERE $this->pu = '$user_id'";
    return $this->CountByCriteria($criteria); 
  }
  
  
  function count_by_object_id($object_id)
  {
  	$criteria = "WHERE $this->po = '$object_id'";
    return $this->CountByCriteria($criteria);	
  }
  
  function get_by_object_id($object_id, $limit=null, $sort=null)
  {
  	$criteria = "WHERE $this->po = '$object_id' ";
  	$criteria .= " ORDER BY " . ($sort ? $sort : "$this->pd asc");
  	if ($limit !== null) $criteria .= " LIMIT $limit ";
  	return $this->FindRecordsByCriteria($criteria);
  }
  
  function delete_by_object_id($object_id) 
  {
    $this->delete_by_criteria("$this->po = '$object_id'");
  }  
  
  function register_vote($object_id, $user_id, $comment='')
  {
  	$this->{$this->po} = $object_id;
  	$this->{$this->pu} = $user_id;
  	$this->{$this->pc} = $comment;
  	return $this->save();
  }
  
  function unregister_vote($object_id, $user_id)
  {
    $this->delete_by_criteria("$this->po = '$object_id' AND $this->pu = '$user_id'");    
  }
  
  function count_voters($where="")
  {
    $query = "SELECT COUNT(distinct user_id) as total FROM $this->table
         JOIN se_users ON se_users.user_id = {$this->table}.{$this->pu}
         $where
         ";
    return $this->ResultFunctionQuery($query,'total');
  }
  
  function get_voters($start=0, $limit=10, $where="", $sort="total_votes DESC")
  {
  	$query = "SELECT se_users.*, COUNT($this->pk) as total_votes FROM se_users
  	     JOIN $this->table ON se_users.user_id = {$this->table}.{$this->pu}
  	     $where
  	     GROUP BY se_users.user_id
  	     ORDER BY $sort
  	     LIMIT $start, $limit
  	     ";
  	$rc_users = $this->_get_popular_users_count($query);     
  	return $rc_users;
  }
  
  // sample usage where object is an user
  function count_objecters($where="")
  {
    $query = "SELECT COUNT(distinct user_id) as total FROM $this->table
         JOIN se_users ON se_users.user_id = {$this->table}.{$this->po}
         $where
         ";
    return $this->ResultFunctionQuery($query,'total');
  }
  
  // sample usage where object is an user
  function get_objecters($start=0, $limit=10, $where="", $sort="total_votes DESC")
  {
    $query = "SELECT se_users.*, COUNT($this->pk) as total_votes FROM se_users
         JOIN $this->table ON se_users.user_id = {$this->table}.{$this->po}
         $where
         GROUP BY se_users.user_id
         ORDER BY $sort
         LIMIT $start, $limit
         ";
    $rc_users = $this->_get_popular_users_count($query);     
    return $rc_users;
  }
  
  function get_object_ids_voted_by($user_id)
  {
    $ids = array();
    $query = "SELECT DISTINCT $this->po FROM $this->table WHERE $this->pu = '$user_id'";
    $res = $this->db->database_query($query);
    while ($row = $this->db->database_fetch_assoc($res)) {
      $ids[] = $row[$this->po];
    }
    return $ids;
  }
  
  function _get_popular_users_count($query)
  {
    $res = $this->db->database_query($query);
    $rc_users = array();
    while ($r = $this->db->database_fetch_assoc($res)) {
      $rc_user = new rc_user();
      $rc_user->setProperties($r);
      $rc_user->total_votes = $r['total_votes']; // lazy
      
      $rc_users[] = $rc_user;
    }
    return $rc_users;
  }
  
}




class rc_pager
{

  /**
  * The current page number
  * @type int
  */
  var $page;
  
  /**
  * Number of record in each page
  * @type int
  */
  var $page_size;
  
  /**
  * Total number of rows in record set
  * @type int
  */
  var $total_entries;
  
  /**
  * Total number of pages
  * @type int
  */
  var $total_pages;


  /**
  * Contruct a new Paginator instance
  *
  * @param $page The current page number
  * @param $page_size The number of record show in 1 page
  * @param $total_entries The total number of row in record set
  *
  * @return void
  */
  function rc_pager ($page, $page_size, $total_entries)
  {
    $this->page   = $page;
    $this->page_size = $page_size;
    $this->total_entries  = $total_entries;
    
    $this->total_pages = ceil($this->total_entries / $this->page_size);
    
    if( $this->page > $this->total_pages )
    {
      $this->page = $this->total_pages;
    }
    if( $this->page < 1 )
    {
      $this->page = 1;
    }
    
    $this->offset = ( $this->page - 1 ) * $this->page_size;
  }
  

  function assign_smarty_vars($page_entries=null)
  {
    global $smarty;
    
    if ($page_entries === null) {
      if ($this->total_pages > $this->page) {
        $page_entries = $this->page_size;
      }
      else {
        $page_entries = $total_entries - ($this->total_pages - 1) * $this->page_size;
      }
    }

    $smarty->assign('total_entries', $this->total_entries);
    $smarty->assign('p', $this->page);
    $smarty->assign('maxpage', $this->total_pages);
    $smarty->assign('p_start', $this->offset + 1);
    $smarty->assign('p_end', $this->offset + $page_entries);

  }
 
}

function radcodes_hook_se_admin_notifications()
{
  global $global_plugins, $admin_notifications;
  
  $radcodes_lib_version = number_format(RADCODES_LIBRARY_VERSION,2);
  
  if ($global_plugins['radcodes']['plugin_version'] > $radcodes_lib_version) {
    $admin_notifications[] = 11000006;
  }
  
  
}

function radcodes_hook_se_footer() {
  global $smarty;
	$rc_toolkit = new rc_toolkit();
	$smarty->assign('rc_toolkit', $rc_toolkit);
}

