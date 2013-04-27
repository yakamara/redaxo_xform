
/*
function em_openRelation(id,subpage,fieldname)
{
  var value = document.getElementById("REX_RELATION_"+id).value;
  newPoolWindow('index.php?page=editme&subpage=' + subpage + '&rex_em_opener_field=' + id + '&rex_em_opener_fieldname=' + fieldname);
}

function em_deleteRelation(id,subpage,fieldname)
{
  document.getElementById("REX_RELATION_" + id).value = "";
  document.getElementById("REX_RELATION_TITLE_" + id).value = "";
}

function em_addRelation(id,subpage,fieldname)
{
  newPoolWindow('index.php?page=editme&subpage=' + subpage + '&func=add&rex_em_opener_field=' + id + '&rex_em_opener_fieldname=' + fieldname);
}

function em_setData(id,data_id,data_name)
{
  if ( typeof(data_name) == 'undefined')
  {
    data_name = '';
  }
  opener.document.getElementById("REX_RELATION_" + id).value = data_id;
  opener.document.getElementById("REX_RELATION_TITLE_" + id).value = data_name;
  self.close();
}
*/


function xform_manager_openDatalist(id, field, link, multiple)
{
  newLinkMapWindow(link+'&rex_xform_manager_opener[id]='+id+'&rex_xform_manager_opener[field]='+field+'&rex_xform_manager_opener[multiple]='+multiple);
}

function xform_manager_deleteDatalist(id, multiple){
  if(multiple == 1) {
    deleteREX(id, 'XFORM_MANAGER_DATALIST_', 'XFORM_MANAGER_DATALIST_SELECT_');
  } else {
    var a = new getObj("XFORM_MANAGER_DATANAME_"+id);
    a.obj.value = "";
    var a = new getObj("XFORM_MANAGER_DATA_"+id);
    a.obj.value = "";
  }
}

function xform_manager_moveDatalist(id, direction){
  moveREX(id, 'XFORM_MANAGER_DATALIST_', 'XFORM_MANAGER_DATALIST_SELECT_', direction);
}

function xform_manager_writeDatalist(id){
  writeREX(id, 'XFORM_MANAGER_DATALIST_', 'XFORM_MANAGER_DATALIST_SELECT_');
}

function xform_manager_setData(id, data_id, data_name, multiple){

  if(multiple == 1) {
    var datalist = "XFORM_MANAGER_DATALIST_SELECT_"+id;
    var source = opener.document.getElementById(datalist);
    var sourcelength = source.options.length;

    option = opener.document.createElement("OPTION");
    option.text = data_name;
    option.value = data_id;

    source.options.add(option, sourcelength);
    opener.writeREX(id, 'XFORM_MANAGER_DATALIST_', 'XFORM_MANAGER_DATALIST_SELECT_');
  }else {
    var data_field_name = "XFORM_MANAGER_DATANAME_"+id;
    var data_field_id = "XFORM_MANAGER_DATA_"+id;
    opener.document.getElementById(data_field_name).value = data_name;
    opener.document.getElementById(data_field_id).value = data_id;
    self.close();
  }

}
