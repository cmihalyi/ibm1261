/************************************
Functions:

googleLineChart(pdf)
googleTable(pdf)
googleCostTable(pdf)
googleSymCostTable(pdf)
drawGoogle(gObj, type, id)

************************************/

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
        "width" : 403,
        "chartArea" : {left : 35, width : 350},
        "title" : chart.title,
        "titleTextStyle" : {fontSize : 14, fontName : "sans-serif"},
        "colors" : ["#819DC1"],
        "pointSize" : 6,
        "legend" : {position : "top", alignment : "end"},
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
        title: table.title,
        sort : 'disable',
        alternatingRowStyle : false
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
