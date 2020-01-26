{ assign var="t_invite_others_managers" value="Invite other security managers to Drainware!" }
{ assign var="t_invite_friend" value="events free invite friend" }
{ assign var="t_upgrade_account" value="upgrade your account" }
{ assign var="t_invite_email" value="Invite by email" }
{ assign var="t_invite_gmail" value="Invite your gmail contact" }
{ assign var="t_invite_social_network" value="Invite security managers from Facebook or Twitter" }
{ assign var="t_share_link" value="Share this link with security managers" }
{ assign var="t_copy_link" value="Copy link" }

<script language="javascript">
    var referral_url = "{$referral_url}";
    var invite_message = "Protect your sensitive information with @Drainware. Sign up for free!";
</script>

<div class="help_box" style="padding: 10px;">
    <h1>{$translations[$t_invite_others_managers]}</h1>
    <p style="padding-top: 8px;">
        {$translations[$t_invite_friend]}
        <a href="?module=main&amp;action=showCloudConfig">{$translations[$t_upgrade_account]}</a>.
    </p>
</div>


<div>
    <form action="" method="post">
        <fieldset>
            <legend>{$translations[$t_invite_email]}</legend>
            {*
            <center>
                <p>
                    <a href="{$gmail_link}" class="big button"> {$translations[$t_invite_gmail]} </a>
                </p>
            </center>
            <br/>
            *}
            <p>
                <input type="text" name="email_list" value="" class="text medium required" id="email_list"/>
            </p>
                
               	<div align="right">
                    <img id="sending_email" src="images/loading_search.gif" style="vertical-align: middle;display:none;"/>
                    <input id="send-invites" class="big button" type="button" value="Send Invites" />
                </div>
                
           
        </fieldset>
    </form>

    <fieldset>
        <legend>{$translations[$t_invite_social_network]}</legend>
        {*
        <p>
            <textarea type="text" name="message" value="" style="width: 100%;" class="text medium required">{$invite_message}</textarea>
        </p>
        <br/>
        *}
	   <div style="clear:both"> 
                <a href="#" id="invite-twitter" class="twitter_btn"></a>
                <a href="#" id="invite-facebook" class="facebook_btn"></a>
           </div> 
    </fieldset>

    <fieldset>
        <legend>{$translations[$t_share_link]}</legend>
        <p>
            <input type="text" name="user" value="{$referral_url}" readonly="readonly" style="width: 80%;" class="text required">
            <a id="copy-link" href="#" class="button ml_10" style=""> {$translations[$t_copy_link]} </a>
            <div id="copy-text" style="display:none;">{$referral_url}</div>
        </p>
    </fieldset>



</div>
