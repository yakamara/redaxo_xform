<?php
 /**
 * @package formsign-1.0
 * @ class formsign
 * @version 1.0
 *  @PHP 5
 * @author Adam Steuer <as@nd-info.de>
 * @copyright Adam Steuer
 *  @file formsign.class.php
 *  @last modified Sunday, October 17, 2010
 * @license GNU Lesser General Public License http://www.gnu.org/licenses/lgpl.html

  This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Fundation, either version 3 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU Lesser General Public License for more details.
 You should have received a copy of the GNU Lesser General Public License along with this program.
 If not, see <http://www.gnu.org/licenses/>.
 */

 class formsign
 {
 /**
   * The current UNIX Timestamp.
   *
   * @var int
   */
  private $cur_time;
 /**
   * The refered UNIX Timestamp.
   *
   * @var int
   */
  private $ref_time;

 /**
   * The Name of hidden field
   * @var string
   */
   private $field_name;
 /**
   * The Name of check field
   * @var string
   */
   private $check_name;
/**
   * The string used by creating of field_name
   * @var string
   */
   private $salt;
 /**
   * The minimal difference between creating and sending of form in seconds
   * @var int
   */
   private $min_limit;
 /**
   * The maximal difference between creating and sending of form in seconds
   * @var int
   */
   private $max_limit;
 /**
   * The difference between creating and sending of form in seconds
   * @var int
   */
    private  $gen_send_diff;
 /**
   * The doctype (HTML or XHTML)
   * @var bool
   */
    private  $xhtml;
 /**
   * Using file or DB or no
   * @var int
   * no = 0
   * file = 1
   * mysql = 2
  * sqlite=3
  * pdo=4

   */
    private  $no_file_db;

 /**
   * The name of the file if it's used
   * @var string
   */
    private  $file_name;
 /**
   * The name of the Sqlite database if it's used
   * @var string
   */
    private  $sqlite_db;
 /**
   * The name of the database table if it's used
   * @var string
   */
    private  $db_tbl;

 /**
   * The database ressource
   * @var object
   */
    private  $db_res;

 /**
  * Constructor
  *
  * @param string $salt
  * @param int $min_limit
  * @param int $max_limit
  */

public function __construct( $salt, $min_limit=30, $max_limit=1800 ) {
 $this->cur_time=time();
 $this->salt=$salt;
 $this->min_limit=$min_limit;
 $this->max_limit=$max_limit;
 $this->field_name=md5($this->salt.'-'.$this->cur_time.'-'.$_SERVER['REMOTE_ADDR']);
}

public function create_sign( $xhtml=1 ) {
 if($this->check_name=='') $this->check_name=md5('check');
 $xhtml==1 ? $this->xhtml=TRUE : $this->xhtml=FALSE;

 if($xhtml) {
 return '<input type="hidden" name="'.$this->field_name.'" value="'.$this->field_name.'" /><input type="hidden" name="'.$this->check_name.'" value="'.$this->cur_time.'" />';
 } else {
 return '<input type="hidden" name="'.$this->field_name.'" value="'.$this->field_name.'"><input type="hidden" name="'.$this->check_name.'" value="'.$this->cur_time.'">';
 }
}

public function check_sign($dat_arr) {
/**
* @param array $dat_arr ($_GET or $_POST)
* return bool
*/

$this->ref_time=$dat_arr[$this->check_name];
if(formsign::check_file_db()) {
$test_limit=$this->gen_send_diff=time()-$this->ref_time;
if($test_limit<=$this->min_limit || $test_limit>=$this->max_limit ) {
return FALSE;
} else {
$gen_fieldname=md5($this->salt.'-'.$this->ref_time.'-'.$_SERVER['REMOTE_ADDR']);
$dat=$dat_arr[$gen_fieldname];
if(!empty($dat)) {
  return TRUE;
} else return FALSE;
 }
} else {
return FALSE;
}
 return FALSE;
}  // end function check_sign
  public function set_check_name($name="check") {
 $this->check_name=md5($name);
 } // end function set_check_name
 public function get_gs_diff() {
 return $this->gen_send_diff;
 } // end function get_gs_diff

 public function use_file($filename="dat") {
 $this->file_name=$filename;

 if( $F=fopen($this->file_name, "a") ) {
fwrite($F,$this->field_name.':'.$this->cur_time.';');
fclose($F);
$this->no_file_db=1;
return TRUE;
} else return FALSE;
}  // end function use_file

public function use_database($res, $table="formsign") {
 $this->db_tbl=$table;
$q="insert into ".$this->db_tbl."(sign, time) values('$this->field_name', '$this->cur_time');";

if(mysql_query($q,$res)or die($q)) {
$this->no_file_db=2;
$this->db_res=$res;
return TRUE;
} else return FALSE;
}  // end function use_database

public function use_mysql($res, $table="formsign") {
 $this->db_tbl=$table;
$q="insert into ".$this->db_tbl."(sign, time) values('$this->field_name', '$this->cur_time');";

if(mysql_query($q,$res)or die($q)) {
$this->no_file_db=2;
$this->db_res=$res;
return TRUE;
} else return FALSE;
}   // end function use_mysql


public function use_sqlite($db="sqldb", $tbl="formsign") {
$this->sqlite_db=$db;
$this->db_tbl=$tbl;
$this->db_res = new SQLiteDatabase($this->sqlite_db, 0666);
@$this->db_res->queryExec("CREATE TABLE  ".$this->db_tbl." (sign CHAR(32) NOT NULL, time int(10))");
$this->db_res->queryExec("INSERT INTO ".$tbl." (sign, time) values('$this->field_name', '$this->cur_time');");
$this->no_file_db=3;
return true;
} // end function use_sqlite

public function use_pdo($res_dbserver, $DB_Name, $table="formsign", $host=NULL, $db_user=NULL, $db_pass=NULL) {
 $this->db_tbl=$table;
$this->no_file_db=4;

if(is_object($res_dbserver))  {
$this->db_res=$res_dbserver;
} else {

// pgsql
if($res_dbserver=="pgsql") {
 $res = new PDO("pgsql:dbname=$DB_Name;host=$host", $db_user, $db_pass);
}
// SQLite
if($res_dbserver=="sqlite") {
 $res = new PDO("sqlite:$DB_Name");
$q="CREATE TABLE IF NOT EXISTS  ".$this->db_tbl." (sign CHAR(32) NOT NULL, time int(10))";
$res->query($q);
}
//MySQL
if($res_dbserver=="mysql") {
 $res = new PDO("mysql:host=$host;dbname=$DB_Name", $db_user, $db_pass);
    }

//Informix
if($res_dbserver=="informix") {
 $res = new PDO("informix:DSN=$host", $db_user, $db_pass);
    }

//Oracle
if($res_dbserver=="oci") {
 $res = new PDO("oci:dbname=$DB_Name;charset=UTF-8", $db_user, $db_pass);
  }

//mssql
if($res_dbserver=="mssql") {
 $res = new PDO ("mssql:host=$host;dbname=$DB_Name","$db_user","$db_pass");
}
$this->db_res=$res;
}
$q="insert into ".$this->db_tbl."(sign, time) values('$this->field_name', '$this->cur_time');";
if($this->db_res->query($q)) {
return TRUE;
} else return FALSE;
}   // end function use_pdo

private function check_file_db() {
$exp_time=$this->cur_time-45000;
$x=-1;

if($this->no_file_db==1) {
$d_str=implode('',file($this->file_name));
$d_arr=explode(';', $d_str);
$j=0;
for($i=0; $i<count($d_arr); $i++) {
$dx=explode(":",$d_arr[$i]);
if($dx[1]>$exp_time) {

if(strstr($d_arr[$i],md5($this->salt.'-'.$this->ref_time.'-'.$_SERVER['REMOTE_ADDR']))!="") {
$x=$i;
} else {
if($d_arr[$i]!='') {
$e_arr[$j]=$d_arr[$i].';';
$j++;
}
}
}
}

if($x<0) $this->gen_send_diff=$x;
$e_str=implode('',$e_arr);
$F=fopen($this->file_name, "w");
fwrite($F,$e_str,strlen($e_str));
fclose($F);

if($x>=0) return TRUE;
else return FALSE;
} else if($this->no_file_db==2) {
$q="delete from ".$this->db_tbl." where time<$exp_time;";
$r=mysql_query($q, $this->db_res)or die($q);
$fn=md5($this->salt.'-'.$this->ref_time.'-'.$_SERVER['REMOTE_ADDR']);
$q="select time from ".$this->db_tbl."
where sign='$fn';";
$r=mysql_query($q, $this->db_res)or die($q);
if(mysql_num_rows($r)>0) {
 $q="delete from ".$this->db_tbl."  where sign='$fn';";
 $r=mysql_query($q, $this->db_res)or die($q);
 return TRUE;
}
else {
$this->gen_send_diff=$x;
return false;
}
 } else if($this->no_file_db==3) {
 $q="delete from ".$this->db_tbl." where time<$exp_time;";
 $this->db_res->query($q);
 $fn=md5($this->salt.'-'.$this->ref_time.'-'.$_SERVER['REMOTE_ADDR']);
 $q="select time from ".$this->db_tbl." where sign='$fn';";
 $r=$this->db_res->query($q);
if($r->numRows()>0) {
  $q="delete from ".$this->db_tbl." where sign='$fn';";
 $this->db_res->query($q);
 return TRUE;
}else {
$this->gen_send_diff=$x;
return FALSE;
}
} else if($this->no_file_db==4) {
$q="delete from ".$this->db_tbl." where time<$exp_time;";
$this->db_res->query($q);
$fn=md5($this->salt.'-'.$this->ref_time.'-'.$_SERVER['REMOTE_ADDR']);
$q="select count(*)from ".$this->db_tbl."
where sign='$fn';";
$rx=$this->db_res->query($q);

$r=$rx->fetch();

if($r[0]>0) {
 $q="delete from ".$this->db_tbl."
 where sign='$fn';";
$this->db_res->query($q);
 return TRUE;
} else {
$this->gen_send_diff=$x;
return false;
}
 }
 else return TRUE;
 }  // end function
}

?>