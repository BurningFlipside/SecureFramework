function login_submit_done(data)
{
    if(data.error)
    {
         alert('Login failed: '+data.error);
         console.log(data.error);
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
    login_link.attr('data-toggle','modal');
    login_link.attr('data-target','#login-dialog');
    login_link.removeAttr('href');
    login_link.css('cursor', 'pointer');
    if($('#login_main_form').length > 0)
    {
        $("#login_main_form").validate({
            debug: true,
            submitHandler: login_submitted
        });
    }
    if($('#login_dialog_form').length > 0)
    {
        $("#login_dialog_form").validate({
            debug: true,
            submitHandler: login_submitted
        });
    }
}

$(do_login_init);
