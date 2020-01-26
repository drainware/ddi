
$("#flex1").flexigrid({
    url: '?module=reporter&action=getConsole',
    dataType: 'json',
    colModel : [
    {
        display: 'Date', 
        name : 'timetime', 
        width : 90, 
        sortable : false, 
        align: 'left'
    },

    {
        display: 'Client IP', 
        name : 'client_ip', 
        width : 70, 
        sortable : false, 
        align: 'left', 
        hide:true
    },

    {
        display: 'Username', 
        name : 'user_name', 
        width : 55, 
        sortable : false, 
        align: 'left',
        hide:false
    },

    {
        display: 'Groups', 
        name : 'groups', 
        width : 90, 
        sortable : false, 
        align: 'center', 
        hide:true
    },

    {
        display: 'Action', 
        name : 'action', 
        width : 50, 
        sortable : false, 
        align: 'center'
    },

    {
        display: 'URL', 
        name : 'url', 
        width :300, 
        sortable : false, 
        align: 'left'
    }
    ],
    searchitems : [
    {
        display: 'Client Ip', 
        name : 'client_ip'
    },

    {
        display: 'Action', 
        name : 'action'
    },

    {
        display: 'Username', 
        name : 'user_name', 
        isdefault: true
    },
    
    {
        display: 'Groups', 
        name : 'groups', 
        isdefault: true
    },

    {
        display: 'Date range', 
        name : 'timetime'
    }


    ],
    sortname: "timetime",
    sortorder: "asc",
    usepager: true,
    title: 'Console',
    useRp: true,
    rp: 20,
    showTableToggleBtn: false,
    width: 550,
    height: 480 
});