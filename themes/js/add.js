function post_done(jqXHR)
{
    if(jqXHR.status === 200)
    {
        alert('Success!');
        location = 'index.php';
    }
    else
    {
        alert('Error! '+jqXHR.responseText);
    }
}

function submit_page(e)
{
    var obj = {};
    obj.name = $('#name').val();
    if(obj.name.length === 0)
    {
        $('#name').parent().addClass('has-error')
        return;
    }
    $('#name').parent().removeClass('has-error')
    if($('#presenting:checked').length > 0)
    {
        obj.presenting = true;
    }
    else
    {
        obj.presenting = false;
    }
    $.ajax({
        url: 'api/v1/themes',
        type: 'post',
        dataType: 'json',
        data: JSON.stringify(obj),
        processData: false,
        complete: post_done
    });
}

function init_page()
{
    $('[name=submit]').on('click', submit_page);
}

$(init_page);
