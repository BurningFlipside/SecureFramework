function login_submit_done(data)
{
    if(data.error)
    {
         var failed = getParameterByName('failed')*1;
         var return_val = window.location;
         failed++;
         if(data.return)
         {
             return_val = data.return;
         }
         window.location = 'https://profiles.burningflipside.com/login.php?failed='+failed+'&return='+return_val;
    }
    else
    {
        if(data.return)
        {
            window.location = data.return;
        }
    }
}

function login_submitted(form)
{
    $.ajax({
        url: 'https://profiles.burningflipside.com/ajax/login.php',
        data: $(form).serialize(),
        type: 'post',
        dataType: 'json',
        xhrFields: {withCredentials: true},
        success: login_submit_done});
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
    }
}

$(do_login_init);
