{ assign var="t_dlp_few_data_message" value="DLP Few Data Message" }
{ assign var="t_reporter_activity" value="Activity"}
{ assign var="t_group_policy" value="Group by Policy"}
{ assign var="t_user_policy" value="User by Policy"}
{ assign var="t_policy" value="Policy"}
{ assign var="t_group" value="Group"}
{ assign var="t_period" value="Period"}
{ assign var="t_last_day" value="Last day"}
{ assign var="t_last_month" value="Last month"}
{ assign var="t_last_six_month" value="Last six month"}
{ assign var="t_last_year" value="Last year"}
{ assign var="t_all" value="All"}
{ assign var="t_action" value="Action"}
{ assign var="t_dlp_group_events_policy" value="DLP Group Events By Policy"}
{ assign var="t_dlp_user_events_policy" value="DLP User Events By Policy"}
{ assign var="t_dlp_events_policy" value="DLP Events By Policy"}
{ assign var="t_dlp_events_group" value="DLP Events By Group"}
{ assign var="t_dlp_events_action" value="DLP Events By Action"}
{ assign var="t_dlp_events_severity" value="DLP Events By Severity"}
{ assign var="t_number_records" value="Number Of Records"}
{ assign var="t_user" value="User"}
{ assign var="t_severity" value="Severity"}


<script language="javaScript">
    var t_dlp_stats_activity = "{$translations[$t_reporter_activity]}";
    var t_dlp_stats_group_policy = "{$translations[$t_dlp_group_events_policy]}";
    var t_dlp_group_policy = "{$translations[$t_group]}"
    var t_dlp_stats_user_policy = "{$translations[$t_dlp_user_events_policy]}";
    var t_dlp_user_policy = "{$translations[$t_user]}"
    var t_dlp_stats_by_policy = "{$translations[$t_dlp_events_policy]}";
    var t_dlp_policy = "{$translations[$t_policy]}";
    var t_dlp_stats_by_group = "{$translations[$t_dlp_events_group]}";
    var t_dlp_group = "{$translations[$t_group]}";
    var t_dlp_stats_by_action = "{$translations[$t_dlp_events_action]}";
    var t_dlp_action = "{$translations[$t_action]}";
    var t_dlp_stats_by_severity = "{$translations[$t_dlp_events_severity]}";
    var t_dlp_severity = "{$translations[$t_severity]}";
    var t_dlp_number_of_records = "{$translations[$t_number_records]}";
</script>

<div class="coda-slider"  id="slider-id">
    <div style="height: 640px;">
        <h2 class="title" style="display:none">{$translations[$t_reporter_activity]}</h2>
        <div>
            <p>
                <label for="tb-activity">{$translations[$t_period]}:</label>
                <select name="tb-activity" id="tb-activity">
                    <option value="1D">{$translations[$t_last_day]}</option>
                    <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                    <option value="6M">{$translations[$t_last_six_month]}</option>
                    <option value="1Y">{$translations[$t_last_year]}</option>
                    <option value="ALL">{$translations[$t_all]}</option>
                </select>
            </p>
        </div>
        <br/><br/>
        <div id="dlp_msg_activity" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
        <!-- <div style="width:280px" id="dlp_bubble_activity"> -->
        <div id="dlp_bubble_activity">
        </div>
        <br/>
        <div style="width:700px" id="dlp_table_activity">
        </div>
    </div>

    <div style="height: 640px;">
        <h2 class="title" style="display:none">{$translations[$t_group_policy]}</h2>
        <div class="period-filter group">
            <p>
                <label for="tb-group_policy">{$translations[$t_period]}:</label>
                <select name="tb-group_policy" id="tb-group_policy">
                    <option value="1D">{$translations[$t_last_day]}</option>
                    <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                    <option value="6M">{$translations[$t_last_six_month]}</option>
                    <option value="1Y">{$translations[$t_last_year]}</option>
                    <option value="ALL">{$translations[$t_all]}</option>
                </select>
            </p>
        </div>
        <div class="extra-filter">
            <p>
                <label for="ps-group_policy">{$translations[$t_policy]}:</label>
                <select name="ps-group_policy" id="ps-group_policy">
                    {foreach from=$gpolicies item=policy}
                        <option value="{$policy}">{$policy}</option>
                    {/foreach}
                </select>
            </p>
        </div>
        <br/><br/>
        <div id="dlp_msg_group_policy" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
        <div style="width:700px" id="dlp_bar_group_policy">
        </div>
        <br/>
        <div style="width:700px" id="dlp_table_group_policy">
        </div>
    </div>
    
    <div style="height: 640px;">
        <h2 class="title" style="display:none">{$translations[$t_user_policy]}</h2>
        <div class="period-filter user">
            <p>
                <label for="tb-user_policy">{$translations[$t_period]}:</label>
                <select name="tb-user_policy" id="tb-user_policy">
                    <option value="1D">{$translations[$t_last_day]}</option>
                    <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                    <option value="6M">{$translations[$t_last_six_month]}</option>
                    <option value="1Y">{$translations[$t_last_year]}</option>
                    <option value="ALL">{$translations[$t_all]}</option>
                </select>
            </p>
        </div>
        <div class="extra-filter">
            <p>
                <label for="ps-user_policy">{$translations[$t_policy]}:</label>
                <select name="ps-user_policy" id="ps-user_policy">
                    {foreach from=$upolicies item=policy}
                        <option value="{$policy}">{$policy}</option>
                    {/foreach}
                </select>
            </p>
        </div>
        <br/><br/>
        <div id="dlp_msg_user_policy" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
        <div style="width:700px" id="dlp_bar_user_policy">
        </div>
        <br/>
        <div style="width:700px" id="dlp_table_user_policy">
        </div>
    </div>
        
    <div style="height: 640px;">
        <h2 class="title" style="display:none">{$translations[$t_policy]}</h2>
        <div>
            <p>
            <label for="tb-policy">{$translations[$t_period]}:</label>
            <select name="tb-policy" id="tb-policy">
                <option value="1D">{$translations[$t_last_day]}</option>
                <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                <option value="6M">{$translations[$t_last_six_month]}</option>
                <option value="1Y">{$translations[$t_last_year]}</option>
                <option value="ALL">{$translations[$t_all]}</option>
            </select>
            </p>
        </div>
        <br/><br/>
        <div id="dlp_msg_policy" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
        <div style="width:700px" id="dlp_pie_policy"></div>
        <br/>
        <div style="width:700px" id="dlp_table_policy"></div>
    </div>
    <div style="height: 640px">
        <h2 class="title" style="display:none">{$translations[$t_group]}</h2>
        <div>
            <p>
            <label for="tb-group">{$translations[$t_period]}:</label>
            <select name="tb-group" id="tb-group">
                <option value="1D">{$translations[$t_last_day]}</option>
                <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                <option value="6M">{$translations[$t_last_six_month]}</option>
                <option value="1Y">{$translations[$t_last_year]}</option>
                <option value="ALL">{$translations[$t_all]}</option>
            </select>
            </p>
        </div>
        <div id="dlp_msg_group" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
        <div style="width:700px" id="dlp_pie_group"></div>
        <br/>
        <div style="width:700px" id="dlp_table_group"></div>
    </div>
    {*
    <div style="height: 640px">
        <h2 class="title" style="display:none">{$translations[$t_action]}</h2>
        <div>
            <p>
            <label for="tb-action">{$translations[$t_period]}:</label>
            <select name="tb-action" id="tb-action">
                <option value="1D">{$translations[$t_last_day]}</option>
                <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                <option value="6M">{$translations[$t_last_six_month]}</option>
                <option value="1Y">{$translations[$t_last_year]}</option>
                <option value="ALL">{$translations[$t_all]}</option>
            </select>
            </p>
        </div>
        <div id="dlp_msg_action" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
        <div style="width:700px" id="dlp_pie_action"></div>
        <br/>
        <div style="width:700px" id="dlp_table_action"></div>
    </div>

    <div style="height: 640px">
        <h2 class="title" style="display:none">{$translations[$t_severity]}</h2>
        <div>
            <p>
            <label for="tb-severity">{$translations[$t_period]}:</label>
            <select name="tb-severity" id="tb-severity">
                <option value="1D">{$translations[$t_last_day]}</option>
                <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                <option value="6M">{$translations[$t_last_six_month]}</option>
                <option value="1Y">{$translations[$t_last_year]}</option>
                <option value="ALL">{$translations[$t_all]}</option>
            </select>
            </p>
        </div>
        <div id="dlp_msg_severity" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
        <div style="width:700px" id="dlp_pie_severity"></div>
        <br/>
        <div style="width:700px" id="dlp_table_severity"></div>
    </div>                
    *}
    <!-- jpalanco: ugly hack, don't remove this empty div -->
    <div></div>
</div>
{$msg}
