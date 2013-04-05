/************************************
Functions:

createForms()
createForm(json)
createDemoForm(json)
createEmailForm(json)
createFormBlock(json)
createFormRow(json)
createFieldset(lgnd)
createLegend(json)
createControlGroup(json, cntrl, cntrlLbl)
createLabel(json)
createInfoIcon(e, json)
createControls(json, inp)
createEditIcon()
createInput(json, cl)
createDropdown(ele)
createPrependAppend(inp, json)
createHelpText(json)
createFormButton(json)
calcInpEvents(inp, id)

************************************/

function createForms(){
    createDemoForm(_DEMOGRAPHICS_);
    createForm(_CALCULATOR_);
    createEmailForm(_EMAIL_);
}


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
