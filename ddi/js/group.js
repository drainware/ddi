var groups = null;
var add_path = false;

function reloadColorbox(){
    $('.inline').colorbox({
        inline:true
    });
}

function autocompleteLDAPGroups(){
    if($("#group_input").length != 0){
        $.getJSON('?module=group&action=getGroups', function(data) {
            groups = data;
            
            $(".gpath_input").autocomplete({
                source: groups,
                select: function(event, ui) {
                    $(this).attr("disabled","disabled");
                    $(this).parent().find('img').show();
                }

            });
            
            $("#group_input").autocomplete({
                source: groups,
                select: function(event, ui) {
                    $(this).attr("disabled","disabled");
                    $("#cancel_group_selection").show();
                }

            });

            $(".cancel_path_selection").click(function() {
                $(this).hide();
                $(this).parent().find('input[name=path]').val('').removeAttr("disabled");
            });

            $("#cancel_group_selection").click(function() {
                $(this).hide();
                $("#group_input").val('').removeAttr("disabled");
            });
        });
    }
}

function createGroupPoliciesConf(gid, gname, gpath){
   
    var new_group = '<div id="gid-' + gid + '" class="element"></div>';

    var info_group = '' +
        '<div class="info">' + 
            '<div class="element-name">' +
                '<span>' + gname + '</span>';

    if(gpath != null){
        info_group = info_group +
                '<small id="gpath-' + gid + '">' +
                    '<a class="set-path" href="#path-' + gname + '">' + gpath + '</a>' +
                '</small>' +
                '<div style="display:none">' +
                    '<div id="path-' + gname + '" class="form-add-path" style="padding:10px; background:#fff;">' +
                        '<fieldset>' +
                            '<legend>Set path of ' + gname + '</legend>' +
                            '<br/>' +
                            '<div>' +
                                '<form method="post" action="#" id="form-add-path">' +
                                    '<p>' +
                                        '<img class="cancel_path_selection" src="images/cross.png" alt="cancel">' +
                                        '<input type="hidden" name="id" value="' + gid + '" />' +
                                        '<input style="width:85%" type="text" name="path" value="' + gpath + '" placeholder="Import LDAP Group" class="gpath_input" />' +
                                        '<input class="button red add-path" type="button" value=' + jt_save + ' />' +
                                    '</p>' +
                                '</form>' +
                            '</div>' +
                        '</fieldset>' +
                    '</div>' +
                '</div>';
    }
       
    info_group = info_group +
            '</div>' +
        '</div>';
    
    
    
    var controls_group = '<div class="controls" id="' + gid + '_controls"></div>';

    var policy_button = '<a id="policy_' + gname + '" class="button mr_10 inline cboxElement" href="#'+ gname + '">' + jt_policies + '</a>';
    var remove_button = '<a class="button red mr_10 remove-group" href="#" id="'+ gid + '">' + jt_remove + '</a>';

    var policies_group = '<div style="display:none">' + 
            '<div id="' + gname + '" class="form-dlp" style="padding:10px; background:#fff;">' +
                '<form method="post" action="?module=group&action=updatePolicies">' +
                    '<input type="hidden" name="group" value="' + gname + '">' +
                    '<fieldset >' +
                        '<legend> <span>' + gname + ' </span> - ' + jt_policies + '</legend>' +
                        $("#generic_policies").html() +
                    '</fieldset>' +
                '</form>' +
            '</div>' +
        '</div>' +
    '</div>' ;

    $("#profiles").append(new_group);

    $('#gid-' + gid).append(info_group);
    $('#gid-' + gid).append(controls_group);
    $('#gid-' + gid).append(policies_group);                            

    if($('#nro_policies').html() != '0'){
        $('#' + gid + '_controls').append(policy_button);
    }
    $('#' + gid + '_controls').append(remove_button);

    reloadColorbox();                        
}

$(document).ready(function() {

    autocompleteLDAPGroups();

    $(document).on("click", ".new-path", function(){
        var id_gpath = $(this).attr('href');
        $.colorbox({
            inline:true,
            href: id_gpath,
            onClosed: function(){
                autocompleteLDAPGroups();
            }
        });
    });

    $(document).on("click", ".set-path", function(){ 
        var id_gpath = $(this).attr('href');
        var old_gpath = $(id_gpath).find('input[name=path]').val();
        $.colorbox({
            inline : true, 
            href: id_gpath,
            onComplete: function(){
                $(id_gpath).find('img').show();
                $(id_gpath).find('input[name=path]').attr("disabled","disabled");
            },
            onClosed: function(){
                if(!add_path){
                    $(id_gpath).find('input[name=path]').val(old_gpath);
                }
                add_path = false;
                autocompleteLDAPGroups();
            }
        });
    });


    $(document).on("click", ".remove-group", function(){ 
        var gid = this.id;
        $.post("?module=group&action=remove", {
            "id" : gid
        }, function() {
            }).success(function(){
            $('#gid-' + gid).remove();
            autocompleteLDAPGroups();
        });      
    });

    $(".add-path").click(function() {
        if(groups != null){
            var id = $(this).parent().find('input[name=id]').val();
            var path = $(this).parent().find('input[name=path]').val();
            add_path = true;

            if (path != "" && groups.indexOf(path)<0) {
                dwShowNotification("default", { title:'Import Group', text:'Please, especify a valid LDAP Group'});
                $(this).parent().find('input[name=path]').val("");
                add_path = false;
            }
            
            if(add_path){
                $.ajax({
                    type: "POST",
                    url: "?module=group&action=updatePath",
                    data: {
                        id: id, 
                        path: path
                    }
                }).done(function() {
                    if(path != ""){
                        $("#gpath-" + id).find('a').attr('class', 'set-path').html(path);
                    } else{
                        path = "Set the path to validate the users correctly"
                        $("#gpath-" + id).find('a').attr('class', 'new-path').html(path);
                    }
                    $("#cboxClose").click();
                }).always(function() {
                });
            }
        }
    });

    $("#add-group").click(function() {
        
        var add = true;
        var name = $("#groupname_input").val();
        var path = $("#group_input").val();

        if(groups != null){
            if (path == ""){
                dwShowNotification("default", { title:'Import LDAP Group', text:'Please, write a LDAP Group'});
                add = false;
            } else if (groups.indexOf(path)<0) {
                dwShowNotification("default", { title:'Import LDAP Group', text:'Please, especify a valid LDAP Group'});
                $("#group_input").val("");
                add = false;
            }
        }
        
        if (name == ""){
            dwShowNotification("default", { title:'Add Group', text:'Please, write a valid Group ID'});
            add = false;
        }else if (name == "*"){
            dwShowNotification("default", { title:'Add Group', text:'Please, especify a different Group ID'});
            $("#groupname_input").val("");
            add = false;
        } else if (/\s/g.test(name)){
            dwShowNotification("default", { title:'Add Group', text:'Group name can not contain spaces'});
            add = false;
        }
        
        if (add == true){
            
            $.post("?module=group&action=create", {
                "name" : name, 
                "path" : path
            }, function() {
                }).success(function(id){
                if (id != '-1') { 
                    createGroupPoliciesConf(id, name, path);

                    $("#cancel_group_selection").click(); 
                    $("#groupname_input").val("");

                    if($('#nro_policies').html() != '0'){
                        $("#policy_" + name).click();
                    } 
                    
                    dwShowNotification("default", { title:'Add Group', text:'<b>'+name+'</b> group was successfully imported'});
                    
                    autocompleteLDAPGroups();
                } else {
                    dwShowNotification("default", { title:'Add Group', text:'The group <i>'+name+'</i> already exists'});
                }
            });

        }
        return false;
    });
    reloadColorbox();
});
