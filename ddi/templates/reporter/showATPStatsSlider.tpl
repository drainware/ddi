{ assign var="t_atp_few_data_message" value="ATP Few Data Message" }
{ assign var="t_applications" value="Applications"}
{ assign var="t_group" value="Group"}
{ assign var="t_period" value="Period"}
{ assign var="t_last_day" value="Last day"}
{ assign var="t_last_month" value="Last month"}
{ assign var="t_last_six_month" value="Last six month"}
{ assign var="t_last_year" value="Last year"}
{ assign var="t_all" value="All"}

<SCRIPT LANGUAGE="JavaScript">
    var t_atp_stats_by_app = "DLP Events By Application";
    var t_atp_app = "Application";
    var t_atp_stats_by_group = "DLP Events By Group";
    var t_atp_group = "Group";
    var t_atp_number_of_records = "Number Of Records";
</SCRIPT>

<br />

<div class="coda-slider"  id="slider-id">

    <div style="height: 640px">
        <h2 class="title" style="display:none">{$translations[$t_applications]}</h2>
        <div>
            <p>
                <label for="tb-atp-app">{$translations[$t_period]}:</label>
                <select name="tb-atp-app" id="tb-atp-app">
                    <option value="1D">{$translations[$t_last_day]}</option>
                    <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                    <option value="6M">{$translations[$t_last_six_month]}</option>
                    <option value="1Y">{$translations[$t_last_year]}</option>
                    <option value="ALL">{$translations[$t_all]}</option>
                </select>
            </p>
        </div>
        <br/><br/>
        <div id="atp_msg_app" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_atp_few_data_message]}
        </div>
        <div style="width:700px" id="atp_pie_app"></div>
        <div style="width:700px" id="atp_table_app"></div>
    </div>

    <div style="height: 640px">
        <h2 class="title" style="display:none">{$translations[$t_group]}</h2>
        <div>
            <p>
                <label for="tb-atp-group">{$translations[$t_period]}:</label>
                <select name="tb-atp-group" id="tb-atp-group">
                    <option value="1D">{$translations[$t_last_day]}</option>
                    <option value="1M" selected="selected">{$translations[$t_last_month]}</option>
                    <option value="6M">{$translations[$t_last_six_month]}</option>
                    <option value="1Y">{$translations[$t_last_year]}</option>
                    <option value="ALL">{$translations[$t_all]}</option>
                </select>
            </p>
        </div>
        <div id="atp_msg_group" style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
                {$translations[$t_atp_few_data_message]}
        </div>
        <div style="width:700px" id="atp_pie_group"></div>
        <div style="width:700px" id="atp_table_group"></div>
    </div>                

    <!-- jpalanco: ugly hack, don't remove this empty div -->
    <div>
    </div>        

</div>
{$msg}
