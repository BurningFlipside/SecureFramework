function get_id_from_row(row)
{
    if(row._id.$id !== undefined)
    {
        return row._id.$id;
    }
    return row._id;
}

function render_camp_logo(data, type, row, meta)
{
    if(data === undefined)
    {
        return '';
    }
    return '<a href="view_tc.php?id='+get_id_from_row(row)+'"><img src="'+data+'" style="max-width:100px; max-height:100px;"/></a>';
}

function render_camp_name(data, type, row, meta)
{
    return '<a href="view_tc.php?id='+get_id_from_row(row)+'">'+data+'</a>';
}

function render_art_logo(data, type, row, meta)
{
    if(data === undefined)
    {
        return '';
    }
    return '<a href="view_art.php?id='+get_id_from_row(row)+'"><img src="'+data+'" style="max-width:100px; max-height:100px;"/></a>';
}

function render_art_name(data, type, row, meta)
{
    return '<a href="view_art.php?id='+get_id_from_row(row)+'">'+data+'</a>';
}

function render_dmv_logo(data, type, row, meta)
{
    if(data === undefined)
    {
        return '';
    }
    return '<a href="view_dmv.php?id='+get_id_from_row(row)+'"><img src="'+data+'" style="max-width:100px; max-height:100px;"/></a>';
}

function render_dmv_name(data, type, row, meta)
{
    return '<a href="view_dmv.php?id='+get_id_from_row(row)+'">'+data+'</a>';
}

function render_event_logo(data, type, row, meta)
{
    if(data === undefined)
    {
        return '';
    }
    return '<a href="view_event.php?id='+get_id_from_row(row)+'"><img src="'+data+'" style="max-width:100px; max-height:100px;"/></a>';
}

function render_event_name(data, type, row, meta)
{
    return '<a href="view_event.php?id='+get_id_from_row(row)+'">'+data+'</a>';
}

function render_event_time(data, type, row, meta)
{
    var ret = [];
    if(row.Thursday === 'true')
    {
        ret.push('Thursday '+row.start+'-'+row.end);
    }
    if(row.Friday === 'true')
    {
        ret.push('Friday '+row.start+'-'+row.end);
    }
    if(row.Saturday === 'true')
    {
        ret.push('Saturday '+row.start+'-'+row.end);
    }
    if(row.Sunday === 'true')
    {
        ret.push('Sunday '+row.start+'-'+row.end);
    }
    if(row.Monday === 'true')
    {
        ret.push('Monday '+row.start+'-'+row.end);
    }
    return ret;
}

function init_tables()
{
    $('#themeTable').dataTable({
        'ajax': 'api/v1/themes?fmt=data-table',
        'columns': [
            {'data': 'name'},
            {'data': 'presenting'}
        ]
    });
}

$(init_tables);
