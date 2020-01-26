{ assign var="manage_rules" value="Manage Rules" }
{ assign var="dlp_rules" value="Rules" }
{ assign var="t_search" value="Search" }
{ assign var="t_cancel" value="Cancel" }
{ assign var="t_file_type_filter" value="File Type Filter:" }
{ assign var="t_all" value="All" }
{ assign var="t_calendar" value="Calendar" }
{ assign var="t_comunication" value="Comunication" }
{ assign var="t_contact" value="Contact" }
{ assign var="t_document" value="Document" }
{ assign var="t_email" value="Email" }
{ assign var="t_feed" value="Feed" }
{ assign var="t_folder" value="Folder" }
{ assign var="t_game" value="Game" }
{ assign var="t_instant_message" value="Instant Message" }
{ assign var="t_journal" value="Journal" }
{ assign var="t_link" value="Link" }
{ assign var="t_movie" value="Movie" }
{ assign var="t_music" value="Music" }
{ assign var="t_note" value="Note" }
{ assign var="t_picture" value="Picture" }
{ assign var="t_program" value="Program" }
{ assign var="t_recorded_tv" value="Recorded TV" }
{ assign var="t_saved_search" value="Saved Search" }
{ assign var="t_task" value="Task" }
{ assign var="t_video" value="Video" }
{ assign var="t_web_history" value="Web History" }
{ assign var="t_result" value="Results (0)" }

{literal} 
    <style TYPE="text/css">
        #hor-zebra {
            font-family: Arial,'DejaVu Sans','Liberation Sans',Freesans,sans-serif; font-size:small;
            font-size:12px;
            margin: 13px;
            width: 480px;
            text-align: left;
            border-collapse: collapse;
        }
        #hor-zebra th {
            font-size: 13px;
            font-weight: normal;
            padding: 10px 8px;
            color: #F0F0F0;
        }
        #hor-zebra td {
            padding: 8px;
            color: #7c7c7c;
            font-size: 12px;
            color: #9F503B;
        }
        #hor-zebra .odd {
            background: #F0F0F0; 
        }
    </style>
{/literal}

<script language="javascript">
    var account_type = "{$account_type}";
</script>

<h2><a href="#">{$software_version}</a></h2>

<fieldset>

    <legend>{$translations[$t_search]}</legend>

    <div id="forensics_remote_search">
        <div id="login_content">
            <form id="remote_search_form" method="post" action="?module=forensics&action=remoteQuery">
                <input type="hidden" name="id"       value=""        id="remote_search_id" />
                <input type="hidden" name="channel"  value="*"       id="remote_search_channel" />
                <input type="hidden" name="command"  value="search"  id="remote_search_command" />
                <input type="text"   name="args"     value=""        id="remote_search_args"         class="text requiered" />
                <input type="hidden" name="last_id"  value=""        id="remote_search_last_id" />
                <input id="remote_search" class="button" type="submit" value="{$translations[$t_search]}" />
                <input id="cancel_search" class="button" type="submit" value="{$translations[$t_cancel]}" style="display:none"/>
                <p>
                    <span class="entitle_short">{$translations[$t_file_type_filter]}</span>
                    <select name="type" id="file_type_filter">
                        <option value="all">{$translations[$t_all]}</option>
                        <option value="calendar">{$translations[$t_calendar]}</option>
                        <option value="comunication">{$translations[$t_comunication]}</option>
                        <option value="contact">{$translations[$t_contact]}</option>
                        <option value="document">{$translations[$t_document]}</option>
                        <option value="email">{$translations[$t_email]}</option>
                        <option value="feed">{$translations[$t_feed]}</option>
                        <option value="folder">{$translations[$t_folder]}</option>
                        <option value="game">{$translations[$t_game]}</option>
                        <option value="instant message">{$translations[$t_instant_message]}</option>
                        <option value="journal">{$translations[$t_journal]}</option>
                        <option value="link">{$translations[$t_link]}</option>
                        <option value="movie">{$translations[$t_movie]}</option>
                        <option value="music">{$translations[$t_music]}</option>
                        <option value="note">{$translations[$t_note]}</option>
                        <option value="picture">{$translations[$t_picture]}</option>
                        <option value="program">{$translations[$t_program]}</option>
                        <option value="recorder tv">{$translations[$t_recorded_tv]}</option>
                        <option value="saved search">{$translations[$t_saved_search]}</option>
                        <option value="task">{$translations[$t_task]}</option>
                        <option value="video">{$translations[$t_video]}</option>
                        <option value="web history">{$translations[$t_web_history]}</option>                        
                    </select>
                </p>
            </form>
        </div>
    </div>

</fieldset>

<fieldset id="results_search" style="display: none;">
    <legend id="nro_results">{$translations[$t_result]}</legend>
    <center>
        <img id="loading_search" src="images/loading_search.gif" style="padding-top: 20px;display:none"/>
    </center>
    <div id="info_messages" style="font-size: 16px;"></div>
    <div id="results">
    </div>
</fieldset>
