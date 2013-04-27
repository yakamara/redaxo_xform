<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// module:xform_basic_out
// v0.2.2
//--------------------------------------------------------------------------------

$xform = new rex_xform;
if ("REX_VALUE[7]" == 1) { $xform->setDebug(TRUE); }
$form_data = 'REX_VALUE[3]';
$form_data = trim(str_replace("<br />","",rex_xform::unhtmlentities($form_data)));
$xform->setFormData($form_data);
$xform->setRedaxoVars(REX_ARTICLE_ID,REX_CLANG_ID); 

?>REX_PHP_VALUE[9]<?php

// action - showtext
if("REX_IS_VALUE[6]" == "true")
{
  $html = "0"; // plaintext
  if('REX_VALUE[11]' == 1) $html = "1"; // html
  if('REX_VALUE[11]' == 2) $html = "2"; // textile
  
  $e3 = ''; $e4 = '';
  if ($html == "0") {
    $e3 = '<div class="rex-message"><div class="rex-info"><p>';
    $e4 = '</p></div></div>';
  }
  
  $t = str_replace("<br />","",rex_xform::unhtmlentities('REX_VALUE[6]'));
  $xform->setActionField("showtext",array(
      $t,
      $e3,
      $e4,
      $html // als HTML interpretieren
    )
  );
}

$form_type = "REX_VALUE[1]";

// action - email
if ($form_type == "1" || $form_type == "2" || $form_type == "3")
{
  $mail_from = $REX['ERROR_EMAIL'];
  if("REX_VALUE[2]" != "") $mail_from = "REX_VALUE[2]";
  $mail_to = $REX['ERROR_EMAIL'];
  if("REX_VALUE[12]" != "") $mail_to = "REX_VALUE[12]";
  $mail_subject = "REX_VALUE[4]";
  $mail_body = str_replace("<br />","",rex_xform::unhtmlentities('REX_VALUE[5]'));
  $xform->setActionField("email", array(
      $mail_from,
      $mail_to,
      $mail_subject,
      $mail_body
    )
  );
}

// action - db
if ($form_type == "0" || $form_type == "2" || $form_type == "3")
{
  $xform->setObjectparams('main_table', 'REX_VALUE[8]');
  
  //getdata
  if ("REX_VALUE[10]" != "")
    $xform->setObjectparams("getdata",TRUE);
  
  $xform->setActionField("db", array(
      "REX_VALUE[8]", // table
      $xform->objparams["main_where"], // where
      )
    );
}

echo $xform->getForm();

?>
