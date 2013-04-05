/****************************************************
#TO DO
-height sizing on opening of sliders
-refactor form functions into 1 form
-make for loops in form function recursive
****************************************************/

function createForm(json){
    var j = json.inputs;
    var b = json.buttons;
    var form = $("<form />").attr("class", "form-horizontal");
    
    for (t in j){
        var cl, inp, ctrl, fldset, lgnd, ele;
        
        if(j[t].header){
            lgnd = createLegend(j[t]);    
        }
        
        fldset = createFieldset(lgnd);
        
        // header, inputs
        form.append(fldset);
        for(e in j[t].inputs){
            ele = j[t].inputs[e];
            ctrlGrp = createFormRow(ele);

            fldset.append(ctrlGrp);
            
            if(j[t].inputs[e].inputs){
                for(q in j[t].inputs[e].inputs){
                    ele = j[t].inputs[e].inputs[q];
                    ctrlGrp = createFormRow(ele);
                    
                    if (q == 0){
                        var w = $("<div />").attr("class", "wrapper well well-small");
                    }
                    
                    w.append(ctrlGrp);
                    
                    if(q == j[t].inputs[e].inputs.length - 1){
                        fldset.append(w);
                    }
                }                    
            }//ends if sub-level
            
        }//ends for top-level
        createFormBlock(j[t]);
    }//ends for data
    
    if(b){
        createFormButton(b[0]).appendTo(form);    
    }
    
    form.appendTo("#calculator");
    calculateFields();
}


function createFormBlock(json){
    var j = json;
    var fset = createFieldset();
    
    for(e in j.inputs){
            fset.append(createFormRow(j.inputs[e]));
        if(j.inputs[e].inputs){
            createFormBlock(j.inputs[e]);
        }
    }
}


function createFormRow(json){
    var j = json;
    var cl, inp, ctrl, ctrlGrp;
    
    ////console.log("createFormRow() id: " + json.id);
    ////console.log(_ASSUMPTIONS_);
    ////console.log(dataTable);
    ////console.log("=================================");
    
    cl = createLabel(j);
    inp = createInput(j, cl);
    ctrl = createControls(j, inp);
    ctrlGrp = createControlGroup(j, ctrl, cl);
    return ctrlGrp;
}


function createFieldset(lgnd){
    var l = lgnd;
    var fldset = $("<fieldset />");
    
    if(l){
        l.appendTo(fldset);
    }
    
    return fldset;
}


function createLegend(json){
    var j = json;
    var lgnd = $("<legend />");
    
    lgnd.text(j.header);
    
    return lgnd;
}


function createControlGroup(json, cntrl, cntrlLbl){
    var j = json;
    var label = cntrlLbl;
    var control = cntrl;
    var ctrlGrp = $("<div />").attr({
        "id" : j.id,
        "class" : "control-group"
    });
    
    label.appendTo(ctrlGrp);
    control.appendTo(ctrlGrp);
    
    return ctrlGrp;
}


function createLabel(json){
    var j = json;
    var ctrlLbl = $("<label />");
    var i = $("<i />");
    
    switch (j.type){
        case "text":
            ctrlLbl = ctrlLbl.attr("class", "control-label");
            break;                        
        case "check":
            ctrlLbl = ctrlLbl.attr("class", "checkbox");
            break;
        case "radio":
            ctrlLbl = ctrlLbl.attr("class", "radio");
            break;
        case "email":
            ctrlLbl = ctrlLbl.attr("class", "control-label");
            
    }

    ctrlLbl.text(j.label);
    
    if (j.helptext){
        i = createInfoIcon(i, j).appendTo(ctrlLbl);
    }
    
    return ctrlLbl;
}


function createInfoIcon(e, json){
    var j = json;
    var ele = $("<div />").attr("class", "info-wrapper");
    e.attr({
        "class" : "info-icon",
        "data-content" : j.helptext,
        "data-placement" : "top",
        "data-trigger" : "hover",
        "data-delay" : 200
    });
    
    e.appendTo(ele);
    
    e.popover();
    return ele;
}


function createControls(json, inp){
    var j = json;
    var ctrls = $("<div />").attr("class", "controls");

    if(j.helptext){
        createHelpText(j).appendTo(ctrls);
    }
    
    inp.appendTo(ctrls);

    if(j.inputs){
        createEditIcon().appendTo(ctrls);
    }

    return ctrls;
}


function createEditIcon(){
    var ele = $("<div />").attr("class", "pencil-wrapper").wrapInner($("<i />").attr("class", "icon-pencil icon-white"));
    
    ele.click(function(){
        var foo = $(this).parent().parent().next();
        foo.slideToggle("slow");
    });
    
    return ele;
}


function createInput(json, cl){
    var j = json;
    var label = cl;
    var inp;

    switch (j.type){
        case "text":
            inp = $("<input />").val(j.defaultvalue).attr("type", "text");
            break;                        
        case "check":
            inp = $("<input />").attr("type", "checkbox");
            break;
        case "radio":
            inp = $("<input />").attr("type", "radio");
            break;
        case "email":
            inp = $("<input />").attr("type", "email");
    }
    
    if(j.pattern){
        inp.attr("pattern", j.pattern);
    }
    
    if(j.title){
        inp.attr("title", j.title);
    }
    
    if(j.inputs){
        inp.attr("disabled", "disabled");
    }  

    if(j.unit){
        inp = createPrependAppend(inp, j.unit);
    }
    
    calcInpEvents(inp, j.id);
    
    return inp;
}


/*
 * Chris - I left the defaultvalue property in the JSON and am using it to set the selected index.
 * 	That keeps the model orthagonal and allows the possibility of defaulting to something other
 * 	than 0.
 * 	On a related note, the country dropdown should (probably) auto-default the cooresponding email 
 * 	dropdown - or perhaps the email dropdown is not needed at all. If we do the former, care must be
 * 	taken to keep the two dropdowns in sync.
 */
function createDropdown(ele){
    var e = ele;
    var id = e.id;
    var inp = $("<select />");
        
	var i = 0;
    for (opt in e.options){
    	var val = i++;
    	$('<option />', {value: val, text: e.options[opt]}).appendTo(inp);
    }
              
    // set the selected index property of the select tag
	$(inp).prop("selectedIndex", e.defaultvalue);
	
    return inp;
}


function createPrependAppend(inp, json){
    var j = json;
    var i = inp;
    var span = $("<span />").attr("class", "add-on");
    var div = $("<div />")

    if(j.prepend){
        div.attr("class", "input-prepend");
        span.text(j.prepend);
        
        div.append(span);
        inp = div.append(inp);
    }
    
    if(j.append){
        div.attr("class", "input-append");
        span.text(j.append);
        
        div.append(inp);
        inp = div.append(span);
    }        
    
    return inp;
}


function createHelpText(json){
    var j = json;
    var txt = $("<span />").text(j.helptext).attr("class", "help-block");
    
    return txt;
}


function createFormButton(json){
	var j = json;
    var btn = $("<input />");

    btn.attr({
        "type" : "button",
        "id" : j.id,
        "class" : j['class'],
        "value" : j.value
    });

    if(j.data_dismiss){
        btn.attr("data-dismiss", j.data_dismiss);
    }
    
    if(j.href){
        btn.attr("href", j.href);
    }
    
    if(j.data_toggle){
        btn.attr("data-toggle", j.data_toggle);
    }
    
    if(j.id == "calculator-btn"){
        // check to make sure that the demographic questions have been answered before showing the results pane
        btn.on("click", function(e){
            if( $("#j select").val() == "0" || $("#k select").val() == "0" || $("#l select").val() == "0"){
                checkDemographicQs();

            }else{
                $("#summary").fadeTo(500, 0, function(){
                    $("#calculator").animate({left : "45px"}, function(){
                        $("#results").css('visibility', 'visible');
						$("#results").fadeTo(500, 1);
                        btn.val(_CALCULATOR_.buttons[0].value_alt);
                    });
                });
                btn.off("click");
                btn.on("click", function(){
                    resetCalculator();
                });
            }            
        });
    }
    
    if(j.id == "send-email-btn"){
        btn.click(function(){
            sendEmail();
        });
    }
    
    return btn;
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


function createDemoForm(json){
    var cl, inp, ctrl, fldset, lgnd, ele, demo, form;
    
    j = json;
    form = $("<form />").attr("class", "demographics");

    lgnd = createLegend(j);
    fldset = createFieldset(lgnd);
    form.append(fldset);
       
    for(e in j.inputs){
        ele = j.inputs[e];
        cl = createLabel(ele);
        inp = createDropdown(ele);
        ctrl = createControls(ele, inp);
        ctrlGrp = createControlGroup(ele, ctrl, cl);
        fldset.append(ctrlGrp);
        
        
    }//ends for top-level
            
    form.appendTo("#demographics-form-block");
}


// consider making form functions more generic.
// need to check for button objects on form object
// need to check for  input type
// need to pass in id for element buttons and form is appended to
// need to pass in classes on form element
function createEmailForm(json){
    var cl, inp, ctrl, fldset, lgnd, ele, email, form;
    
    j = json.form;
    form = $("<form />").attr("class", "form-horizontal email");

    $("#myModalLabel").text(j.header);
    
    for(e in j.inputs){
        ele = j.inputs[e];

        //check to see if options list is available on element. This lets us know whether or not we need to create a text or select input.    
        if(ele.options){
            inp = createDropdown(ele);
        }else{
            inp = createInput(ele, cl);
        }
        
        if(ele.type == "check"){
            //input into label
            //label into controls
            inp.prop("checked", true);
            cl = createLabel(ele);
            ctrl = createControls(ele, inp);
            ctrlGrp = createControlGroup(ele, ctrl, cl);
            
            inp.appendTo(cl);
            cl.appendTo(ctrl);
        }else{
            cl = createLabel(ele);
            ctrl = createControls(ele, inp);
            ctrlGrp = createControlGroup(ele, ctrl, cl);
        }
        
        ctrlGrp.appendTo(form);
    }//ends for top-level

    if(j.buttons){
        createFormButton(j.buttons[1]).appendTo($(".modal-footer"));
        createFormButton(j.buttons[0]).appendTo($("#results-email-block"));
    }
    
    form.appendTo(".modal-body");
}


function calcB(){
    var b1 = $("#b1 input").val();
    
    $("#b input").val(b1);
}


function calcC(){
    //var #c = (#c1+#c2)/1000*24*365*#c3
    var c1 = parseInt($("#c1 input").val());
    var c2 = parseInt($("#c2 input").val());
    var c3 = parseFloat($("#c3 input").val());
    var c = parseFloat(((c1 + c2)/1000*24*365*c3).toFixed(2));
    
    $("#c input").val(c); 
}


function calcG(){
    //var  #g1 = #a*#g3/100
    var a = parseInt($("#a input").val());
    var g3 = parseFloat($("#g3 input").val());
    var g1 = a*g3/100;
    
    $("#g1 input").val(g1);
    $("#g input").val(g1);        
}


function calcG3(){
    var a = parseInt($("#a input").val());
    var g1 = parseFloat($("#g1 input").val());
    
    var g3 = g1*100/a;
    
    $("#g3 input").val(g3);
    $("#g input").val(g1);
}


function calculateFields(){
	if(!($("#calculator input" ).hasClass("invalid"))){
		// refuse to calculate -- probably should be disabling the button...
		calcB();
		calcC();
		calcG();
	}
}

function suppressLeadingZeroes(str){
	return str.replace(/^[0]+/g,"");
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


function calcInpEvents(inp, id){
    var ele;
    var id = id;
        
    if(inp.attr("pattern")){
        ele = inp;
    }else{
        ele = inp.find("input");
    }
    
    ele.keyup(function(){
        //check to make sure input is valid - need to adjust pattern to not allow leading zeroes. 0 = true -1 = false
        if(ele.val().search(new RegExp(ele.attr("pattern"))) !== -1){
            ele.removeClass("invalid");
        	if(id == "a" && ele.val() == 0){
        		ele.addClass("invalid");
        	}
        }else{
            ele.addClass("invalid");
        }
    });

    ele.change(function(){
        //if the input is empty set to 0
        if(ele.val() == ""){
            ele.val(0);
        }    


        //if input is valid then make calculations            
        if(!(ele.hasClass("invalid"))){
            if(id == "b1"){
                //console.log("this is " + id + " calcB()");
                calcB();
            }

            if(id == "c1" || id == "c2" || id == "c3"){
                //console.log("this is " + id + " calcC()");
                calcC();
            }

            if(id == "a" || id == "g3"){
                //console.log("this is " + id + " calcG()");
                calcG();
            }

            if(id == "g1"){
                calcG3();
            }
            
            //update graph with valid input
            drawGoogle( googleTable(), "table", "results-table-block");
            drawGoogle( googleLineChart(), "line", "results-graph-block");
            
        }
    });        
}

function getClusterGrowthRate(){
    return (1 + getPercent($("#h input").val()));
}


//Without Symphony    
function getY1_server_count_wout_symphony(){
	  return parseInt($("#a input").val());
}

function getAnn_dep_cost_per_host(){
	  return  parseFloat((parseInt($("#b1 input").val()) + parseInt($("#b2 input").val())) / parseInt(_ASSUMPTIONS_.capital_depreciation_period));
}

function getAnn_hw_sw_maint_per_host(){
	  return $("#b3 input").val();
}

//With Symphony    
function getBlnd_eff_gain_app_mix(){
	  return (getPercent($("#f1 input").val()) * getPercent($("#f2 input").val())) + ((1 - getPercent($("#f1 input").val())) * getPercent($("#f3 input").val()));
}

function getY1_server_count_w_symphony(blnd_eff_gain){
	  return getY1_server_count_wout_symphony() * (1 - blnd_eff_gain);
}

function getTotal_kwatts_per_server(){ 
	  return (parseFloat($("#c1 input").val()) + parseFloat($("#c2 input").val())) / 1000;
}

function getAnn_power_cost_per_server(){
	  return parseFloat($("#c3 input").val()) * 24 * 365 * getTotal_kwatts_per_server();
}

function getAnn_cost_for_system_admin_staff(){
	  return parseInt($("#g1 input").val()) * parseInt($("#g2 input").val());
}

function getCost_for_system_admin_staff(){
	  return (getAnn_cost_for_system_admin_staff() * (1 - parseFloat(_ASSUMPTIONS_.assumed_gain_admin_eff_after_symphony)));
}

/*
* In the following functions that compute compound growth rates:
* in_which_year should be 0 for the first year, 1 for the second, 2 for the third, and so on
*/
function clusterCompoundGrowth(in_which_year){
    return Math.pow(1 + (getPercent($("#h input").val())), in_which_year);
}

function laborCompoundGrowth(in_which_year){
    return Math.pow((1 + _ASSUMPTIONS_.estimated_labor_inflation_per_year), in_which_year);
}


//Competing grid manager convenience calculations
function getServer_infrastructure(in_which_year){
	  return getY1_server_count_wout_symphony() * getAnn_dep_cost_per_host() * clusterCompoundGrowth(in_which_year);
}

function getHw_sw_maintenance(in_which_year){
	  return getAnn_hw_sw_maint_per_host() * getY1_server_count_wout_symphony() * clusterCompoundGrowth(in_which_year);
}

function getPower_cooling_cost(in_which_year){
	  return getAnn_power_cost_per_server() * getY1_server_count_wout_symphony() * clusterCompoundGrowth(in_which_year);
}

function getTotal_grid_sw_cost(in_which_year){
	  return _ASSUMPTIONS_.cost_per_node_for_comp_grid_mgmt_sw * getY1_server_count_wout_symphony() * clusterCompoundGrowth(in_which_year);
}

function getPro_rated_cost_of_admin(in_which_year){
	  return getAnn_cost_for_system_admin_staff()  * laborCompoundGrowth(in_which_year);
}

function getTotal_cost_wout_symphony(in_which_year){
	  return getServer_infrastructure(in_which_year) + getHw_sw_maintenance(in_which_year) + getPower_cooling_cost(in_which_year) + getTotal_grid_sw_cost(in_which_year) + getPro_rated_cost_of_admin(in_which_year);

}


//Post-Symphony convenience calculations 
function getServer_infrastructure_w_symphony(in_which_year){
    return getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * getAnn_dep_cost_per_host() * clusterCompoundGrowth(in_which_year);
}

function getHw_sw_maintenance_w_symphony(in_which_year){
    return getAnn_hw_sw_maint_per_host() * getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * clusterCompoundGrowth(in_which_year);
}

function getPower_cooling_cost_w_symphony(in_which_year){
    return getAnn_power_cost_per_server() * getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * clusterCompoundGrowth(in_which_year);
}

function getTotal_grid_sw_cost_w_symphony(in_which_year){
	  var grid_sw_cost = 0.0;
	  switch (in_which_year) {
		   case 0:
			    grid_sw_cost = _ASSUMPTIONS_.symphony_perpetual_cost_per_host * getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix());
			    break;
		   case 1:
			   var y1_server_count = getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix());
				  var y2_server_count = y1_server_count * clusterCompoundGrowth(1);
			    grid_sw_cost = _ASSUMPTIONS_.symphony_perpetual_cost_per_host * (y2_server_count - y1_server_count);
			    break;
		   case 2:
			    var y2_server_count = getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * clusterCompoundGrowth(1);
			    var y3_server_count = getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * clusterCompoundGrowth(2);
			    grid_sw_cost = _ASSUMPTIONS_.symphony_perpetual_cost_per_host * (y3_server_count - y2_server_count);
			    break;
	  }
	  return grid_sw_cost;
}


//function getTotal_grid_sw_cost_w_symphony_y1(data){
//    return data.symphony_perpetual_cost_per_host * getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix());
//}
//
//function getTotal_grid_sw_cost_w_symphony_y2(data){
//	  var y1_server_count = getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix());
//	  var y2_server_count = y1_server_count * clusterCompoundGrowth(1);
//    return data.symphony_perpetual_cost_per_host * (y2_server_count - y1_server_count);
//}
//
//function getTotal_grid_sw_cost_w_symphony_y3(data){
//    var y2_server_count = getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * clusterCompoundGrowth(1);
//    var y3_server_count = getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * clusterCompoundGrowth(2);
//    return data.symphony_perpetual_cost_per_host * (y3_server_count - y2_server_count);
//}


function getTotal_grid_support_w_symphony(in_which_year){
	  return getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * _ASSUMPTIONS_.symphony_ann_support_per_host * clusterCompoundGrowth(in_which_year);
}

function getTotal_Pro_rated_cost_of_admin_w_symphony(in_which_year){
    return getCost_for_system_admin_staff()  * laborCompoundGrowth(in_which_year);
}

function getTotal_cost_w_symphony(in_which_year){
//	  var grid_sw_cost = 0.0;
//	  switch (in_which_year) {
//		   case 0:
//			    grid_sw_cost = getTotal_grid_sw_cost_w_symphony_y1(data);
//			    break;
//		   case 1:
//			    grid_sw_cost = getTotal_grid_sw_cost_w_symphony_y2(data);
//			    break;
//		   case 2:
//			    grid_sw_cost = getTotal_grid_sw_cost_w_symphony_y3(data);
//	  }
		  
	  return getServer_infrastructure_w_symphony(in_which_year) + getHw_sw_maintenance_w_symphony(in_which_year) + getPower_cooling_cost_w_symphony(in_which_year) + getTotal_grid_sw_cost_w_symphony(in_which_year) + getTotal_grid_support_w_symphony(in_which_year) + getTotal_Pro_rated_cost_of_admin_w_symphony(in_which_year);
}


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

//Not currently being used.  Attempt at making a dynamic google data object for charts/graphs
function createDataTable(){
    var dtData = _CHARTS_.table;
    var yrs = _ASSUMPTIONS_.capital_depreciation_period;
    var dt = {};
    dt.cols = [];
    dt.rows = [];

    
    for(var i = 0; i < 3; i ++){
        dt.cols[i] = {};
        dt.rows[i] = {};
        dt.rows[i].c = [];
        
        for(var y = 0; y < yrs; y++){
            dt.rows[i].c[y] = {};

            //dt.rows[i].c[y].v = i + " " + y;
            //dt.rows[i].c[y].f = i + " " + y;

            switch (i) {
                case 0:
                    dt.rows[i].c[y].v = dtData.row_headers[i];
                    break;                        
                case 1:
                    dt.rows[i].c[y].v = getTotal_cost_wout_symphony(y);
                    dt.rows[i].c[y].f = "format";
                    dt.rows[i].c[y].y = y;                    
                    break;
                case 2:
                    dt.rows[i].c[y].v = getTotal_grid_sw_cost_w_symphony(y);
                    dt.rows[i].c[y].f = "format";
                    break;
                case 3:
                    dt.rows[i].c[y].v = "savings";
                    dt.rows[i].c[y].f = "format";
            }
        }
    }
    //console.log(dt);
}


function googleLineChart(pdf){
    var chart = _CHARTS_.figure1;
    var gObj = {};
    
    //Cummulative Costs - you should be able to move these 9 vars anywhere...
    var other_grid_manager_y1 = getTotal_cost_wout_symphony(0);
    var symphony_y1 = getTotal_cost_w_symphony(0);
    var savings_y1 = (1 - (Number(symphony_y1) / Number(other_grid_manager_y1))) * 100;
    var other_grid_manager_y2 = other_grid_manager_y1 + getTotal_cost_wout_symphony(1);
    var symphony_y2 = symphony_y1 + getTotal_cost_w_symphony(1);
    var savings_y2 = (1 - (Number(symphony_y2) / Number(other_grid_manager_y2))) * 100;
    var other_grid_manager_y3 = other_grid_manager_y2 + getTotal_cost_wout_symphony(2);
    var symphony_y3 = symphony_y2 + getTotal_cost_w_symphony(2);
    var savings_y3 = (1 - (Number(symphony_y3) / Number(other_grid_manager_y3))) * 100;

    var data = [
      ['Year', chart.legend],
      [chart.col_headers[0], Math.roundTo(savings_y1, 2)],
      [chart.col_headers[1], Math.roundTo(savings_y2, 2)],
      [chart.col_headers[2], Math.roundTo(savings_y3, 2)]
    ];

    var options = {
      "title" : chart.title
    };
    
    if(pdf){
        gObj.data = data;    
    }else{
        gObj.data = google.visualization.arrayToDataTable(data);
    }
    
    gObj.options = options;

    return gObj;
}


function googleTable(pdf){
    var table = _CHARTS_.table3;
    var gObj = {};
    var data = {
         cols: [
            {id: 'header', label: '', type: 'string'},
            {id: 'year1', label: table.col_headers[0], type: 'number'},
            {id: 'year2', label: table.col_headers[1], type: 'number'},
            {id: 'year3', label: table.col_headers[2], type: 'number'}
         ],
         rows: [
            {c:[
                {v: table.row_headers[0]},
                {v: formatUSDCurrency( getTotal_cost_wout_symphony(0), 0)}, // ~5,000,000
                {v: formatUSDCurrency( getTotal_cost_wout_symphony(1), 0)}, // ~6,000,000
                {v: formatUSDCurrency( getTotal_cost_wout_symphony(2), 0)} // ~7,000,000
            ]},
            {c:[
                {v: table.row_headers[1]},
                {v: formatUSDCurrency( getTotal_cost_w_symphony(0), 0)},
                {v: formatUSDCurrency( getTotal_cost_w_symphony(1), 0)},
                {v: formatUSDCurrency( getTotal_cost_w_symphony(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[2]},
                {v: formatUSDCurrency( getSavings(getTotal_cost_wout_symphony(0), getTotal_cost_w_symphony(0)), 0)},
                {v: formatUSDCurrency( getSavings(getTotal_cost_wout_symphony(1), getTotal_cost_w_symphony(1)), 0)},
                {v: formatUSDCurrency( getSavings(getTotal_cost_wout_symphony(2), getTotal_cost_w_symphony(2)), 0)}
            ]}
         ]
    }

    var options = {
        width: 405,
        title: table.title
    };

    //checks to see if object is being sent over to a pdf or to the tool
    //sends to _PDF_ as a raw object instead of wrapping in the google code
    if(pdf){
        gObj.data = data;
        options.title = table.pdf_title;
    }else{
        gObj.data = new google.visualization.DataTable(data);
    }
    
    updateSavingsValue( $("#results-table-summary-block strong"), getOverallSavings());
        
    gObj.options = options;
    
    return gObj;    
}

function googleCostTable(pdf){
    var table = _CHARTS_.table2;
    var gObj = {};

    var data = {
         cols: [
            {id: 'header', label: table.col_headers[0], type: 'string'},
            {id: 'year1', label: table.col_headers[1], type: 'number'},
            {id: 'year2', label: table.col_headers[2], type: 'number'},
            {id: 'year3', label: table.col_headers[3], type: 'number'},
            {id: 'total', label: table.col_headers[4], type: 'number'}
         ],
         rows: [
            {c:[
                {v: table.row_headers[0]},
                {v: formatUSDCurrency( getServer_infrastructure(0), 0)},
                {v: formatUSDCurrency( getServer_infrastructure(1), 0)},
                {v: formatUSDCurrency( getServer_infrastructure(2), 0)},
                {v: formatUSDCurrency( getServer_infrastructure(0) + getServer_infrastructure(1) + getServer_infrastructure(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[1]},
                {v: formatUSDCurrency( getHw_sw_maintenance(0), 0)},
                {v: formatUSDCurrency( getHw_sw_maintenance(1), 0)},
                {v: formatUSDCurrency( getHw_sw_maintenance(2), 0)},
                {v: formatUSDCurrency( getHw_sw_maintenance(0) + getHw_sw_maintenance(1) + getHw_sw_maintenance(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[2]},
                {v: formatUSDCurrency( getPower_cooling_cost(0), 0)},
                {v: formatUSDCurrency( getPower_cooling_cost(1), 0)},
                {v: formatUSDCurrency( getPower_cooling_cost(2), 0)},
                {v: formatUSDCurrency( getPower_cooling_cost(0) + getPower_cooling_cost(1) + getPower_cooling_cost(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[3]},
                {v: formatUSDCurrency( getTotal_grid_sw_cost(0), 0)},
                {v: formatUSDCurrency( getTotal_grid_sw_cost(1), 0)},
                {v: formatUSDCurrency( getTotal_grid_sw_cost(2), 0)},
                {v: formatUSDCurrency( getTotal_grid_sw_cost(0) + getTotal_grid_sw_cost(1) + getTotal_grid_sw_cost(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[4]},
                {v: formatUSDCurrency( getPro_rated_cost_of_admin(0), 0)},
                {v: formatUSDCurrency( getPro_rated_cost_of_admin(1), 0)},
                {v: formatUSDCurrency( getPro_rated_cost_of_admin(2), 0)},
                {v: formatUSDCurrency( getPro_rated_cost_of_admin(0) + getPro_rated_cost_of_admin(1) + getPro_rated_cost_of_admin(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[5]},
                {v: formatUSDCurrency( getTotal_cost_wout_symphony(0), 0)},
                {v: formatUSDCurrency( getTotal_cost_wout_symphony(1), 0)},
                {v: formatUSDCurrency( getTotal_cost_wout_symphony(2), 0)},
                {v: formatUSDCurrency( getTotal_cost_wout_symphony(0) + getTotal_cost_wout_symphony(1) + getTotal_cost_wout_symphony(2), 0)}
            ]}
         ]
    };

    var options = {
      height: 156,
      width: 405,
      title: table.title
    };

    //checks to see if object is being sent over to a pdf and sends as raw data and not a google object
    if(pdf){
        gObj.data = data;
    }else{
        gObj.data = new google.visualization.DataTable(data);
    }
    
    gObj.options = options;

    return gObj;    
}


function googleSymCostTable(pdf){
    var table = _CHARTS_.table1;
    var gObj = {};
    
    var data = {
         cols: [
            {id: 'header', label: table.col_headers[0], type: 'string'},
            {id: 'year1', label: table.col_headers[1], type: 'number'},
            {id: 'year2', label: table.col_headers[2], type: 'number'},
            {id: 'year3', label: table.col_headers[3], type: 'number'},
            {id: 'total', label: table.col_headers[4], type: 'number'}
         ],
         rows: [
            {c:[
                {v: table.row_headers[0]},
                {v: formatUSDCurrency( getServer_infrastructure_w_symphony(0), 0)},
                {v: formatUSDCurrency( getServer_infrastructure_w_symphony(1), 0)},
                {v: formatUSDCurrency( getServer_infrastructure_w_symphony(2), 0)},
                {v: formatUSDCurrency( getServer_infrastructure_w_symphony(0) + getServer_infrastructure_w_symphony(1) + getServer_infrastructure_w_symphony(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[1]},
                {v: formatUSDCurrency( getHw_sw_maintenance_w_symphony(0), 0)},
                {v: formatUSDCurrency( getHw_sw_maintenance_w_symphony(1), 0)},
                {v: formatUSDCurrency( getHw_sw_maintenance_w_symphony(2), 0)},
                {v: formatUSDCurrency( getHw_sw_maintenance_w_symphony(0) + getHw_sw_maintenance_w_symphony(1) + getHw_sw_maintenance_w_symphony(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[2]},
                {v: formatUSDCurrency( getPower_cooling_cost_w_symphony(0), 0)},
                {v: formatUSDCurrency( getPower_cooling_cost_w_symphony(1), 0)},
                {v: formatUSDCurrency( getPower_cooling_cost_w_symphony(2), 0)},
                {v: formatUSDCurrency( getPower_cooling_cost_w_symphony(0) + getPower_cooling_cost_w_symphony(1) + getPower_cooling_cost_w_symphony(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[3]},
                {v: formatUSDCurrency( getTotal_grid_sw_cost_w_symphony(0), 0)},
                {v: formatUSDCurrency( getTotal_grid_sw_cost_w_symphony(1), 0)},
                {v: formatUSDCurrency( getTotal_grid_sw_cost_w_symphony(2), 0)},
                {v: formatUSDCurrency( getTotal_grid_sw_cost_w_symphony(0) + getTotal_grid_sw_cost_w_symphony(1) + getTotal_grid_sw_cost_w_symphony(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[4]},
                {v: formatUSDCurrency( getTotal_Pro_rated_cost_of_admin_w_symphony(0), 0)},
                {v: formatUSDCurrency( getTotal_Pro_rated_cost_of_admin_w_symphony(1), 0)},
                {v: formatUSDCurrency( getTotal_Pro_rated_cost_of_admin_w_symphony(2), 0)},
                {v: formatUSDCurrency( getTotal_Pro_rated_cost_of_admin_w_symphony(0) + getTotal_Pro_rated_cost_of_admin_w_symphony(1) + getTotal_Pro_rated_cost_of_admin_w_symphony(2), 0)}
            ]},
            {c:[
                {v: table.row_headers[5]},
                {v: formatUSDCurrency( getTotal_cost_w_symphony(0), 0)},
                {v: formatUSDCurrency( getTotal_cost_w_symphony(1), 0)},
                {v: formatUSDCurrency( getTotal_cost_w_symphony(2), 0)},
                {v: formatUSDCurrency( getTotal_cost_w_symphony(0) + getTotal_cost_w_symphony(1) + getTotal_cost_w_symphony(2), 0)}
            ]}
         ]
    };

    var options = {
      height: 156,
      width: 405,
      title: table.title
    };

    //checks to see if object is being sent over to a pdf and sends as raw data and not a google object
    if(pdf){
        gObj.data = data;
    }else{
        gObj.data = new google.visualization.DataTable(data);
    }
    
    gObj.options = options;

    return gObj;    
}


function drawGoogle(gObj, type, id){
    var chart;

    switch (type){
        case "table":
            chart = new google.visualization.Table(document.getElementById(id));
            break;                        
        case "line":
            chart = new google.visualization.LineChart(document.getElementById(id));
    }

    chart.draw(gObj.data, gObj.options);
}











function sendEmail(){
    if($(".email input").hasClass("invalid") || $("#m input").val() == "" || $("#p input").val() == "") {
        //display alert msg
        createAlert("alert-error", _EMAIL_.form.form_error)
    }else{
        addContentTo_PDF_();
		getUserEmailData();
		console.log(_EMAIL_);
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


function createForms(){
    createDemoForm(_DEMOGRAPHICS_);
    createForm(_CALCULATOR_);
    createEmailForm(_EMAIL_);
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

function getOverallSavings(){
    var sym_cost = getTotal_cost_w_symphony(0) + getTotal_cost_w_symphony(1) + getTotal_cost_w_symphony(2);
    var other_cost = getTotal_cost_wout_symphony(0) + getTotal_cost_wout_symphony(1) + getTotal_cost_wout_symphony(2);
    var savings = formatUSDCurrency( getSavings(other_cost, sym_cost), 0);

    return savings;        
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
