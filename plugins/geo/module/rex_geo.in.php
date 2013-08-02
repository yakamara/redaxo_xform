<?php

// TABLE SELECT
////////////////////////////////////////////////////////////////////////////////
$gc = rex_sql::factory();
$gc->setQuery('SHOW TABLES');
$tables = $gc->getArray();
$tbl_sel = new rex_select;
$tbl_sel->setName('VALUE[1]');
$tbl_sel->setSize(1);
$tbl_sel->addOption('Keine Tabelle ausgewählt', '');
foreach ($tables as $key => $value)
{
  $tbl_sel->addOption(current($value), current($value));
}
$tbl_sel->setSelected('REX_VALUE[1]');
$tbl_sel = $tbl_sel->get();

$plz_tbl_sel = new rex_select;
$plz_tbl_sel->setName('VALUE[8]');
$plz_tbl_sel->setSize(1);
$plz_tbl_sel->addOption('Keine Tabelle ausgewählt', '');
foreach ($tables as $key => $value) {
  $plz_tbl_sel->addOption(current($value), current($value));
}
$plz_tbl_sel->setSelected('REX_VALUE[8]');
$plz_tbl_sel = $plz_tbl_sel->get();


?>
<style type="text/css" media="screen">
  /*BAISC MODUL STYLE*/
  #xform-modul                       {margin:0;padding:0;line-height:25px;}
  #xform-modul fieldset              {background:#E4E1D1;margin:-20px 0 0 0;padding: 4px 10px 10px 10px;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;}
  #xform-modul fieldset legend       {display:block !important;position:relative !important;height:auto !important;top:0 !important;left:0 !important;width:100% !important;margin:0 0 0 0 !important;padding:30px 0 0 0px !important;background:transparent !important;border-bottom:1px solid #B1B1B1 !important;color:gray;font-size:14px;font-weight:bold;}
  #xform-modul fieldset legend em    {font-size:10px;font-weight:normal;font-style:normal;}
  #xform-modul fieldset strong.label,
  #xform-modul fieldset label        {display:inline-block !important;width:220px !important;font-weight:bold;vertical-align:top;}
  #xform-modul fieldset label span   {font-weight:normal;}
  #xform-modul input,
  #xform-modul select                {width:460px;border:auto;margin:0 !important;padding:0 !important;}
  #xform-modul input[type="checkbox"]{width:auto;}
  #xform-modul hr                    {border:0;height:0;margin:4px 0 4px 0;padding:0;border-top:1px solid #B1B1B1 !important;clear:left;}
  #xform-modul a.blank               {background:url("../files/addons/be_style/plugins/agk_skin/popup.gif") no-repeat 100% 0;padding-right:17px;}
  #xform-modul #modulinfo            {font-size:10px;text-align:right;}
  /*XFORM MODUL*/
  #xform-modul textarea              {min-height:50px;font-family:monospace;font-size:12px;}
  #xform-modul pre                   {clear:left;}
  #xform-modul strong span           {font-weight:normal;}
  #xform-modul .help                 {display:none;color:#2C8EC0;line-height:12px;}
  #xform-modul .area-wrapper         {background:white;border:1px solid #737373;margin-bottom:10px;width:100%;}
  #xform-modul .fullwidth            {width:100% !important;}
  #xform-modul #thx-markup           {width:auto !important;}
  #xform-modul #thx-markup input     {width:auto !important;}
  #xform-modul #xform-placeholders-help,
  #xform-modul #xform-where-help     {display:none;}
  #xform-modul #xform-placeholders,
  #xform-modul #xform-classes-showhelp {background:white;border:1px solid #737373;margin-bottom:10px;width:100%;}
  #xform-modul #xform-placeholders {padding:4px 10px;float:none;width:auto;}
  #xform-modul #xform-placeholders span:hover {color:red;cursor:pointer;}
  #xform-modul em.hint               {color:silver;margin:0;padding:0 0 0 10px;}
  /*SHOWHELP OVERRIDES*/
  #xform-modul ul.xform.root         {border:0;outline:0;margin:4px 0;padding:0;width:100%;background:transparent;}
  #xform-modul ul.xform              {font-size:1.1em;line-height:1.4em;}
</style>

<div id="xform-modul">
  <fieldset>
    <legend>XForm GeoModul</legend>

    <label>Quell-Tabelle</label>
    <?php echo $tbl_sel;?>

    <label>Lat Feld <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <input type="text" name="VALUE[3]" value="REX_VALUE[3]" />
    <ul class="help">
    <li>Feld(name) der Quell-Tabelle welcher die Lat (latitude) Position enthält</li>
    </ul>

    <label>Lng Feld <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <input type="text" name="VALUE[2]" value="REX_VALUE[2]" />
    <ul class="help">
    <li>Feld(name) der Quell-Tabelle welcher die Lng (longitude) Position enthält</li>
    </ul>

	<hr />
	
	<label>PLZ-Tabelle [optional]</label>
    <?php echo $plz_tbl_sel;?>
	
	<label>PLZ Felder [plz,lat,lng,city,state] <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <input type="text" name="VALUE[9]" value="REX_VALUE[9]" />
    <ul class="help">
    <li>Feldnamen kommasepariert</li>
    </ul>
	  
    <hr />

    <label>Zu beziehende Felder <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <input type="text" name="VALUE[4]" value="REX_VALUE[4]" />
    <ul class="help">
    <li>Feldnamen kommasepariert</li>
    </ul>


    <label>Volltextsuchfelder <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <input type="text" name="VALUE[5]" value="REX_VALUE[5]" />
    <ul class="help">
    <li>Feldnamen kommasepariert</li>
    </ul>

    <label>WHERE condition <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <input type="text" name="VALUE[6]" value="REX_VALUE[6]" />
    <ul class="help">
    <li>optional</li>
    </ul>

    <hr />

    <label>Sidebar HTML <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <textarea rows="6" name="VALUE[7]" class="fullwidth">REX_VALUE[7]</textarea>
    <ul class="help">
    <li>Mit ###id### als Ersetzungen, ***id*** für urlencoded Ersetzungen</li>
    </ul>

    <label>Map HTML <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <textarea rows="6" name="VALUE[10]" class="fullwidth">REX_VALUE[10]</textarea>
    <ul class="help">
    <li>Mit ###id### als Ersetzungen, ***id*** für urlencoded Ersetzungen</li>
    </ul>
	  	  
    <label>Druckversion HTML <span>[<a href="#" class="help-toggler">?</a>]</span></label>
    <textarea rows="6" name="VALUE[11]" class="fullwidth">REX_VALUE[11]</textarea>
    <ul class="help">
    <li>Mit ###id### als Ersetzungen, ***id*** für urlencoded Ersetzungen</li>
    </ul>
	  
	  
	  
	  
  </fieldset>
  <p id="modulinfo">XForm GeoModul v1.1</p>
</div><!-- #xform-modul -->

<script type="text/javascript">
<!--
(function($){

  // HELP TOGGLER
  $('.help-toggler').click(function(){
    $(this).parentsUntil('label').parent().nextUntil('.help').next().toggle(50);
    return false;
  });


})(jQuery)
//-->
</script>