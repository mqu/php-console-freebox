function to02(val)
{
  if (val < 10)
    return '0' + val;
  return val;
}

function selectService(idx, id_sel)
{
    var str = "";
    var srv_a = serv_a[idx].service;
    var prived = false;
    for (var lo = 0; lo < srv_a.length; lo++) {
	var srv_h = srv_a[lo];
	if (srv_h.pvr_mode == "disabled")
	    continue;
	var priv = "";
	if (srv_h.pvr_mode == "private") {
	    priv = "*";
	    prived = true;
	}
	var sel = (id_sel && id_sel == srv_h.id);
	str += "<option value='" + srv_h.id + "'" + (sel ? "selected='selected'" : "") + ">" + (srv_h.desc != "" ? srv_h.desc : "Par défaut") + priv + "</option>";
    }
    if (str == "")
	$("#service").html("<input type='hidden' name='service' value='-1'>[non enregistrable]");
    else
	$("#service").html("<select name='service'>" + str + "</select>" + (prived ? "<br>*enregistrable en interne seulement" : ""));
}

function selectChaines(){
	var str = "";
	var ch_sel = 0;
	for (var lo = 0; lo < serv_a.length; lo++) {
	  var chaine_h = serv_a[lo];
	  var sel = ('' == chaine_h.id);
	  if (sel)
		ch_sel = lo;
	  str += "<option value='" + chaine_h.id + "' " + (sel ? "selected='selected'" : "") + ">" + chaine_h.name + "</option>";
	}
	$("#chaine").html(str);

	if (ch_sel)
	  selectService(ch_sel, +0);
	else
	  selectService(0);
 
}
