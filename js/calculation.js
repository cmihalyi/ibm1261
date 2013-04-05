/************************************
Functions:

calcB()
calcC()
calcG()
calcG3()
calculateFields()
getClusterGrowthRate()
getY1_server_count_wout_symphony()
getAnn_dep_cost_per_host()
getAnn_hw_sw_maint_per_host()
getBlnd_eff_gain_app_mix()
getY1_server_count_w_symphony(blnd_eff_gain)
getTotal_kwatts_per_server()
getAnn_power_cost_per_server()
getAnn_cost_for_system_admin_staff()
getCost_for_system_admin_staff()
clusterCompoundGrowth(in_which_year)
laborCompoundGrowth(in_which_year)
getServer_infrastructure(in_which_year)
getHw_sw_maintenance(in_which_year)
getPower_cooling_cost(in_which_year)
getTotal_grid_sw_cost(in_which_year)
getPro_rated_cost_of_admin(in_which_year)
getTotal_cost_wout_symphony(in_which_year)
getServer_infrastructure_w_symphony(in_which_year)
getHw_sw_maintenance_w_symphony(in_which_year)
getPower_cooling_cost_w_symphony(in_which_year)
getTotal_grid_sw_cost_w_symphony(in_which_year)
getTotal_grid_support_w_symphony(in_which_year)
getTotal_Pro_rated_cost_of_admin_w_symphony(in_which_year)
getTotal_cost_w_symphony(in_which_year)
getOverallSavings()

************************************/

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

// in_which_year = 0 first year, 1 second year, 2 = third year
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

function getTotal_grid_support_w_symphony(in_which_year){
	  return getY1_server_count_w_symphony(getBlnd_eff_gain_app_mix()) * _ASSUMPTIONS_.symphony_ann_support_per_host * clusterCompoundGrowth(in_which_year);
}

function getTotal_Pro_rated_cost_of_admin_w_symphony(in_which_year){
    return getCost_for_system_admin_staff()  * laborCompoundGrowth(in_which_year);
}

function getTotal_cost_w_symphony(in_which_year){
	  return getServer_infrastructure_w_symphony(in_which_year) + getHw_sw_maintenance_w_symphony(in_which_year) + getPower_cooling_cost_w_symphony(in_which_year) + getTotal_grid_sw_cost_w_symphony(in_which_year) + getTotal_grid_support_w_symphony(in_which_year) + getTotal_Pro_rated_cost_of_admin_w_symphony(in_which_year);
}

function getOverallSavings(){
    var sym_cost = getTotal_cost_w_symphony(0) + getTotal_cost_w_symphony(1) + getTotal_cost_w_symphony(2);
    var other_cost = getTotal_cost_wout_symphony(0) + getTotal_cost_wout_symphony(1) + getTotal_cost_wout_symphony(2);
    var savings = formatUSDCurrency( getSavings(other_cost, sym_cost), 0);

    return savings;        
}
