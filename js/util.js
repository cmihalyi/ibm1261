/************************************
Functions:

getPercent(val)
getCurrency(val)
getSavings(v1, v2)
Math.roundTo = function(val, dec)
addCommas(nStr)
formatUSDCurrency(num, decPlaces)
suppressLeadingZeroes(str)
sendEmail()
addContentTo_PDF_()
getCalcData()
getUserEmailData()
setGlobals(json)
resetCalculator()
checkDemographicQs()
createAlert(type, content)
updateSavingsValue(ele, value)
addDynamicValuesTo_PDF_()
modalReset()
injectContent()
variableValueReplace(str, value, bold)

************************************/

function getPercent(val){
    var v = parseFloat(val).toFixed(2);
    v = v / 100;
    
    return v;
}

function getCurrency(val){
    var v = parseFloat(val).toFixed(2);
    
    return v;
}


function getSavings(v1, v2){    
    var s = Number(v1) - Number(v2);
    
    return s;
}


Math.roundTo = function(val, dec){
	if (isNaN(val)) {
		return val;
	} else {
		var mult = Math.pow(10, dec);
		return ( Math.round(val*mult) / mult);
	}
}


function addCommas(nStr){
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}


function formatUSDCurrency(num, decPlaces){
	var cents = 0;
	var multiplier = 0;
	var rounder = 0;
	var i = 0;
	
	num = num.toString().replace(/\$|\,/g, '');
	if (isNaN(num)) {
		num = "0";
	}
	
	if (isNaN(decPlaces)) {
		//console.log("decPlaces NaN");
		decPlaces = "2";
	}
	
	sign 	= (num == (num = Math.abs(num)));
	num 	= Math.roundTo(num, decPlaces).toString();	
	num	 	= addCommas(num);
	
	return (((sign) ? '' : '-') + '$' + num);
}

function suppressLeadingZeroes(str){
	return str.replace(/^[0]+/g,"");
}


function sendEmail(){
    if($(".email input").hasClass("invalid") || $("#m input").val() == "" || $("#p input").val() == "") {
        //display alert msg
        createAlert("alert-error", _EMAIL_.form.form_error)
    }else{
        addContentTo_PDF_();
		getUserEmailData();
        //send info to server
        //checks to see if status is success
        //display success alert
        if ($.browser.msie && window.XDomainRequest) {
            // Use Microsoft XDR
            var xdr = new XDomainRequest();
            xdr.open("POST", "http://www.ibmcmostudy.com/1261/create_pdf/PDFEmail_IE.php");
            //REQUIRED WORKAROUND - always include these callbacks:
            xdr.onprogress = function(){};
            xdr.ontimeout = function(){
                createAlert("alert-info", _EMAIL_.form.post_error);
            };
            xdr.onerror = function (){
                createAlert("alert-info", _EMAIL_.form.post_error);
            };
            xdr.onload = function(){
                var content = _EMAIL_.form.success.text1 + "<strong>" + _EMAIL_.form.success.bold + "</strong>" + _EMAIL_.form.success.text2;
                createAlert("alert-info", content);                    
            };
            xdr.send("data="+escape(JSON.stringify(_PDF_))+"&email_data="+escape(JSON.stringify(_EMAIL_)));
        }else{
            $.when(
                $.ajax({type : "POST", url : "http://www.ibmcmostudy.com/1261/create_pdf/PDFEmail_good.php",  data:{data:JSON.stringify(_PDF_), email_data:JSON.stringify(_EMAIL_)} })			
            ).then(function(data){
                var content = _EMAIL_.form.success.text1 + "<strong>" + _EMAIL_.form.success.bold + "</strong>" + _EMAIL_.form.success.text2;
                createAlert("alert-info", content);
            })
            .fail(function(data){
                createAlert("alert-info", _EMAIL_.form.post_error);
            });
        }

    }

}


function addContentTo_PDF_(){
    _PDF_.section[1].tables.calculator = getCalcData();
    _PDF_.section[2].tables.table1 = googleCostTable(true);
    _PDF_.section[2].tables.table2 = googleSymCostTable(true);
    _PDF_.section[2].tables.table3 = googleTable(true);
    _PDF_.section[4].figure1 = googleLineChart(true);
    addDynamicValuesTo_PDF_();            
}


function getCalcData(){
    var inputs = _CALCULATOR_.inputs;

    for(x in inputs){
        for(y in inputs[x].inputs){
            var id = "#" + inputs[x].inputs[y].id + " input";
            					
            inputs[x].inputs[y].value = $(id).val();
            delete inputs[x].inputs[y].pattern;
            delete inputs[x].inputs[y].defaultvalue;                    
            delete inputs[x].inputs[y].helptext;
            delete inputs[x].inputs[y].title;
            delete inputs[x].inputs[y].type;
            
            if(inputs[x].inputs[y].inputs){
                for(z in inputs[x].inputs[y].inputs){                        
                    id = "#" + inputs[x].inputs[y].inputs[z].id + " input";
                    
                    inputs[x].inputs[y].inputs[z].value = $(id).val();
                    delete inputs[x].inputs[y].inputs[z].pattern;
                    delete inputs[x].inputs[y].inputs[z].defaultvalue;                    
                    delete inputs[x].inputs[y].inputs[z].helptext;
                    delete inputs[x].inputs[y].inputs[z].title;
                    delete inputs[x].inputs[y].inputs[z].type;
                    
                }
            }
        }
    }
    return inputs;
}


function getUserEmailData(){
	var inputs = _EMAIL_.form.inputs;

	for(x in inputs){
		var id = "#" + inputs[x].id + " .controls input";
        if(x == 4){
           inputs[x].value = $(id).prop("checked");
        }else{
			inputs[x].value = $(id).val();
        }
	}
}


function setGlobals(json){
    _CALCULATOR_ = json.calculator;
    _ASSUMPTIONS_ = json.assumptions;
    _CONTENT_ = json.text_content;
    _CHARTS_ = json.charts;
    _DEMOGRAPHICS_ = json.demographics;
    _EMAIL_ = json.email;
}


function resetCalculator(){
    var inputs = _CALCULATOR_.inputs;

    for(x in inputs){
        for(y in inputs[x].inputs){
            var id = "#" + inputs[x].inputs[y].id + " input";
            					
            $(id).val(inputs[x].inputs[y].defaultvalue);
            
            if(inputs[x].inputs[y].inputs){
                for(z in inputs[x].inputs[y].inputs){                        
                    id = "#" + inputs[x].inputs[y].inputs[z].id + " input";
                    
                    $(id).val(inputs[x].inputs[y].inputs[z].defaultvalue);
                }
            }
        }
    }
    calculateFields();
    //update graph with valid input
    drawGoogle( googleTable(), "table", "results-table-block");
    drawGoogle( googleLineChart(), "line", "results-graph-block");
}


function checkDemographicQs(){
    $(".demographics select").each(function(index){
        if($(this).val() == 0){
            $(this).addClass("invalid");
            $(this).change(function(){
                checkDemographicQs();
            });
        }else{
            $(this).removeClass("invalid");
        }

    });

}


function createAlert(type, content){
    var ele = $("<p />");
    var alert = $("<div />").attr("class", "alert");
        
    if($(".modal-body .alert")){
        $(".modal-body .alert").remove();    
    }
    
    alert.addClass(type);
    ele.html(content).appendTo(alert);
    alert.prependTo(".modal-body");
}


function updateSavingsValue(ele, value){
    ele.text(value);
}


function addDynamicValuesTo_PDF_(){
    _PDF_.section[2].paragraphs[2] = variableValueReplace(_PDF_.section[2].paragraphs[2], getOverallSavings());
    _PDF_.section[3].section[0].paragraphs[1] = variableValueReplace(_PDF_.section[3].section[0].paragraphs[1], $("#h input").val() + $("#h .add-on").text());
    _PDF_.section[4].paragraphs[0] = variableValueReplace(_PDF_.section[4].paragraphs[0], getOverallSavings());
    _PDF_.section[3].section[4].paragraphs[0] = variableValueReplace(_PDF_.section[3].section[4].paragraphs[0], $("#g3 input").val());
    _PDF_.section[3].section[4].paragraphs[1] = variableValueReplace(_PDF_.section[3].section[4].paragraphs[1], (_ASSUMPTIONS_.assumed_gain_admin_eff_after_symphony * 100) + "%");
}


function modalReset(){
    $("#myModal").on('hidden', function () {
        $("#myModal .alert").alert("close");
        $("#m input").val("");
        $("#n input").val("");
        $("#o input").val("");
        $("#p input").val("");
    });
}

function injectContent(){
    var ul = $("<ul />");
    var h4 = $("<h4 />");
    var content = "<strong>" + _CONTENT_.chart.block1.bold + "</strong>" + _CONTENT_.chart.block1.regular;

    $("<p />").text(_CONTENT_.summary).appendTo("#summary-content-block");
    $("<p />").html(content).appendTo("#results-content-block");
    
    for(f in _CONTENT_.chart.list_items){
        $("<li />").text(_CONTENT_.chart.list_items[f]).appendTo(ul);
    }
    
    ul.appendTo("#results-content-block");
    
    $("<p />").text(_CONTENT_.chart.block2).appendTo("#results-content-block");
    h4.html(variableValueReplace(_CONTENT_.chart.savings, getOverallSavings(), true)).appendTo("#results-table-summary-block");
    $("<h1 />").text(_CONTENT_.head).appendTo(".page-head");
    $("<h2 />").text(_CONTENT_.subhead).appendTo(".page-head");
}


function variableValueReplace(str, value, bold){
    var newString;
    if(bold){
        newString = str.replace("[value]", "<strong>" + value[x] + "</strong>");
    }
    else{
        newString = str.replace("[value]", value);
    }            

    return newString;
}
