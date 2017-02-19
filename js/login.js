function login_submit_done(jqXHR)
{
    if(jqXHR.status != 200)
    {
        var failed = getParameterByName('failed')*1;
        var return_val = window.location;
        failed++;
        window.location = window.loginUrl+'?failed='+failed+'&return='+return_val;
    }
    else
    {
        if(jqXHR.responseJSON !== undefined)
        {
            var data = jqXHR.responseJSON;
            var url  = '';
            if(data.return)
            {
                url = data.return;
            }
            else
            {
                url = window.location;
            }
            if(data.extended)
            {
                url += '?extended='+data.extended;
            }
            window.location = url;
        }
    }
}

function login_submitted(form)
{
    var dir  = $('script[src*=login]').attr('src');
    var name = dir.split('/').pop();
    dir = dir.replace('/'+name,"");
    name = dir.split('/').pop();
    dir = dir.replace('/'+name,"");
    $.ajax({
        url: dir+'/api/v1/login',
        data: $(form).serialize(),
        type: 'post',
        dataType: 'json',
        complete: login_submit_done});
}

function login_dialog_shown()
{
    $('[name=username]').focus();
}

function do_login_init()
{
    var login_link = $(".links a[href*='login']");
    if(browser_supports_cors())
    {
        login_link.attr('data-toggle','modal');
        login_link.attr('data-target','#login-dialog');
        login_link.removeAttr('href');
        login_link.css('cursor', 'pointer');
        login_link = $("#content a[href*='login']");
        login_link.attr('data-toggle','modal');
        login_link.attr('data-target','#login-dialog');
        login_link.removeAttr('href');
        login_link.css('cursor', 'pointer');
    }
    else
    {
        login_link.attr('href', login_link.attr('href')+'?return='+document.URL);
    }
    if($('#login_main_form').length > 0)
    {
        $("#login_main_form").validate({
            submitHandler: login_submitted
        });
    }
    if($('#login_dialog_form').length > 0)
    {
        $("#login_dialog_form").validate({
            submitHandler: login_submitted
        });
    }
    if($('#login-dialog').length > 0)
    {
        $('#login-dialog').modal({show: false, backdrop: 'static'});
        $('#login-dialog').on('shown.bs.modal', login_dialog_shown);
    }
}

$(do_login_init);
