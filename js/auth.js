
function clickMenu(idEl, oldpl)
{
	if (oldpl != idEl)
	{
		$(oldpl).hide();
		$(idEl).show();
	}
}

function showLogin()
{
	clickMenu("#flogin",oldplace3);
	oldplace3 = "#flogin";
}

function showRegister()
{
	clickMenu("#fregister",oldplace3);
	oldplace3 = "#fregister";
}


function testCharLogin(charr)
{
	if ((charr >= '0') && (charr <= '9') ||
		(charr >= 'a') && (charr <= 'z') ||
		(charr >= 'A') && (charr <= 'Z') ||
		(charr >= 'а') && (charr <= 'я') ||
		(charr >= 'А') && (charr <= 'Я') ||
		(charr == '_'))
		return true;
	else
		return false;
}

function testCharPass(charr)
{
	if ((charr >= '0') && (charr <= '9') ||
		(charr >= 'a') && (charr <= 'z') ||
		(charr >= 'A') && (charr <= 'Z') ||
		(charr >= 'а') && (charr <= 'я') ||
		(charr >= 'А') && (charr <= 'Я'))
		return true;
	else
		return false;
}

function testLog()
{
	var err = 0;
	var str = $("input[name='rlogin']")[0].value;
	for (i=0; i<str.length; i++)
		if (!testCharLogin(str[i])) err = 1;
	if (err == 1) $("#warnlog").show();
	else $("#warnlog").hide();
	
	$("#warnlog2").hide();
	testStateForButton();
}

function testPas()
{
	var err = 0;
	var str = $("input[name='rpassword']")[0].value;
	for (i=0; i<str.length; i++)
		if (!testCharPass(str[i])) err = 1;
	if (err == 1) $("#warnpas").show();
	else $("#warnpas").hide();
	testStateForButton();
}


function testRepPas()
{
	if ($("input[name='rpassword']")[0].value != $("#rreppas")[0].value)
		$("#warnreppas")[0].style.display="block";
	else
		$("#warnreppas")[0].style.display="none";
	testStateForButton();
	
}

function testEmail()
{
	$("#warnemail").hide();
	$("#warnemail2").hide();
	testStateForButton();
}


function testStateForButton()
{
	
	if (($("#warnlog")[0].style.display=="block")||
		($("#warnlog2")[0].style.display=="block")||
		($("#warnpas")[0].style.display=="block")||
		($("#warnreppas")[0].style.display=="block")||
		($("#warnemail")[0].style.display=="block") ||
		($("#warnemail2")[0].style.display=="block"))
	  $("#btnsubm")[0].disabled=true;
	else
	{
		if (($("input[name='rlogin']")[0].value=="")||
			($("input[name='rpassword']").value=="")||
			($("#rreppas")[0].value=="")||
			($("input[name='remail']")[0].value==""))
		  $("#btnsubm")[0].disabled=true;
		else
		  $("#btnsubm")[0].disabled=false;
	}
}
