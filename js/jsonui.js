(function ( $ ) {
    $.fn.jsonUI = function(opts) {
        var settings = $.extend($.fn.jsonUI.defaults, opts);

        if(settings.obj !== undefined)
        {
            renderUIFromObj(settings.obj, this);
        }
        else if(settings.json !== undefined)
        {
            renderUIFromJSON(settings.json, this);
        }
        return this;
    };

    $.fn.jsonUI.defaults = {
    };

    function renderUIFromJSON(json, rootObj) {
        var object = JSON.parse(json);
        renderUIFromObj(object, rootObj);
    }

    function renderUIFromObj(object, rootObj) {
        switch(object.type)
        {
            case "Div":
                renderDiv(object, rootObj);
                break;
            case "FormGroup":
                renderFormGroup(object, rootObj);
                break;
            case "Label":
                renderLabel(object, rootObj);
                break;
            case "Wizard":
                renderWizard(object, rootObj);
                break;
            default:
                console.log(object);
                break;
        }
    }

    function renderDiv(divObj, rootObj, extraClass)
    {
        if(extraClass === undefined)
        {
            extraClass = '';
        }
        if(divObj.size !== undefined)
        {
            extraClass+=' col-'+divObj.size;
        }
        var elem = $(rootObj);
        var div = $('<div class="'+extraClass+'">');
        if(divObj.content !== undefined)
        {
            for(var i = 0; i < divObj.content.length; i++)
            {
                 renderUIFromObj(divObj.content[i], div);
            }
        }
        elem.append(div);
    }

    function renderLabel(labelObj, rootObj)
    {
        var className = 'control-label';
        if(labelObj.size !== undefined)
        {
            className += ' col-'+labelObj.size;
        }
        var elem = $(rootObj);
        var label = $('<label class="'+className+'">'+labelObj.text+'</label>');
        elem.append(label);
    }

    function renderWizard(wizardObj, rootObj) {
        var elem = $(rootObj);
        var nav = $('<ul class="nav nav-tabs" role="tablist">');
        var content = $('<div class="tab-content">');
        for(var i = 0; i < wizardObj.pages.length; i++)
        {
            var id;
            if(wizardObj.pages[i].id === undefined)
            {
                id = 'tab'+i;
            }
            else
            {
                id = wizardObj.pages[i].id;
            }
            nav.append('<li class="nav-item"><a href="#'+id+'" class="nav-link">'+wizardObj.pages[i].title+'</a></li>');
            if(wizardObj.pages[i].content !== undefined)
            {
                var pane = $('<div role="tabpanel" class="tab-pane" id="'+id+'">');
                for(var j = 0; j < wizardObj.pages[i].content.length; j++)
                {
                     renderUIFromObj(wizardObj.pages[i].content[j], pane);
                }
                content.append(pane);
            }
        }
        elem.append(nav).append(content);
        elem.on('click', 'li a', wizardTabClicked);
    }

    function renderFormGroup(formGroupObj, rootObj)
    {
        renderDiv(formGroupObj, rootObj, 'form-group');
    }

    function wizardTabClicked(e) {
        e.preventDefault();
        $(this).tab('show');
    }
}( jQuery ));
