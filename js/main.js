
function clickMenu(idEl, oldpl)
{
	if (oldpl != idEl)
	{
		$(oldpl).hide();
		$(idEl).show();
	}
}




function showBuySell()
{
	clickMenu("#sellbuy",oldplace);
	oldplace = "#sellbuy";
}

function showMessages()
{
	clickMenu("#messages",oldplace);
	oldplace = "#messages";
}

function showPurchased()
{
	clickMenu("#buytime",oldplace);
	oldplace = "#buytime";
}

function showSold()
{
	clickMenu("#soldtime",oldplace);
	oldplace = "#soldtime";
}

function showMess()
{
	clickMenu("#admmess",oldplace);
	oldplace = "#admmess";
}


function showBuy()
{
	clickMenu("#placebuy",oldplace2);
	oldplace2 = "#placebuy";
}
function showSell()
{
	clickMenu("#placesell",oldplace2);
	oldplace2 = "#placesell";
}


function logout()
{
	document.cookie = "var1=0;max-age=0;";
	document.cookie = "var2=0;max-age=0;";
	document.cookie = "PHPSESSID=0;max-age=0;";
	document.location = "index.php";
}

function changePass()
{
	var newpass = $("#newpass")[0].value;

	var req = getXmlHttp(); 
	req.open('POST', 'index.php?query=setPass&newpass='+newpass, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				if (req.responseText!="error")
					alert("Пароль измененён");
				else
					alert("Произошла ошибка. Пароль не измененён.");
			}
			else
			{
				alert("Произошла ошибка. Пароль не измененён.");
			}
        }
    }
    req.send(null);

}

function cancelOrder(id) 
{
	$("#o"+id)[0].style.color="#CCC";

	var req = getXmlHttp(); 
	req.open('POST', 'index.php?query=cancelOrder&id='+id, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				$("#o"+id)[0].remove();
			}
			else
			{
				$("#o"+id)[0].style.color="#000";
			}
        }
    }
    req.send(null);
}

function deleteSeller(id) 
{
	$("#s"+id)[0].style.color="#CCC";

	var req = getXmlHttp(); 
	req.open('POST', 'index.php?query=deleteSeller&id='+id, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				$("#s"+id)[0].remove();
				
			}
			else
			{
				$("#s"+id)[0].style.color="#000";
			}
        }
    }
    req.send(null);
}


function getXmlHttp(){
  var xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}