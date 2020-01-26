{ assign var="t_general_configuration" value="General Configuration" }
{ assign var="t_save" value="Save" }
{ assign var="t_license" value="License" }
{ assign var="t_plan" value="Plan" }
{ assign var="t_number_users" value="Number of users" }
{ assign var="t_cost_user_month" value="Cost of user per month" }
{ assign var="t_total_amount" value="Total amount" }
{ assign var="t_expiry" value="Expiry" }
{ assign var="t_password" value="Password" }
{ assign var="t_extra_users" value="Extra users" }
{ assign var="t_add_extra_users" value="Add extra users" }

<fieldset>
    <legend>Wire Transfer</legend>
    <div>
        <form action="?module=main&action=saveWireTransfer" method="POST" enctype="multipart/form-data">
            <p>
                <span class="entitle_medium">{$translations[$t_license]}</span>
                <input type="text" name="license" value="" class="text medium required">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_plan]}</span>
                <span>
                    {* Verify last check in all browsers *}
                    <input type="radio" name="type" value="premium" checked="checked"/> <label for="">Premium</label>
                    <input type="radio" name="type" value="freemium" /> <label for="">Freemium</label>
                </span>
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_number_users]}</span>
                <input type="number" name="nbr_users" value="" min="1" max="99999999" class="text medium required line" />
            </p>
            <p class="line">
                <span class="entitle_medium">{$translations[$t_cost_user_month]}</span>
                <input type="text" name="cost_user_month" value="" class="text short required" id="cost_user_month"/> 
            </p>
            <div class="pijadita">$</div>
            <p class="line">
                <span class="entitle_medium">{$translations[$t_total_amount]}</span>
                <input type="text" name="total_amount" value="" class="text short required" id="total_amount"/> 
            </p>
            <div class="pijadita">$</div>
            <p>
                <span class="entitle_medium">{$translations[$t_expiry]}</span>
                <input type="text" name="expiry" value="" class="text medium required" id="datepickerexpiry">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_password]}</span>
                <input type="password" name="password" value="" class="text medium required">
            </p>
            <p>
                <input class="button red" type="submit" value="{$translations[$t_save]}" />
            </p>
        </form>
    </div>
</fieldset>

<fieldset>
    <legend>{$translations[$t_add_extra_users]}</legend>
    <div>
        <form action="?module=main&action=saveWireTransfer" method="post" enctype="multipart/form-data" id="wt-xu-form">
	    <p>
		<span class="entitle_medium" id="company_name" ></span>
	    </p>
            <p>
                <span class="entitle_medium">{$translations[$t_license]}</span>
                <input type="text" name="license" value="" class="text medium required" id="wt-xu-lincese" />            
            </p>
            <div id="wt-xu-options" style="display:none;">
                <p>
                    <span class="entitle_medium">{$translations[$t_number_users]}</span>
                    <input type="number" name="nbr_users" value="0" min="0" max="99999999" readonly="readonly" class="text medium line" id="wt-xu-users" />
                </p>
                <p>
                    <span class="entitle_medium">{$translations[$t_extra_users]}</span>
                    <input type="number" name="extra_users" value="" min="1" max="99999999" class="text medium required line" id="wt-xu-xusers" />
                </p>
            </div>
            <p>
                <span class="entitle_medium">{$translations[$t_password]}</span>
                <input type="password" name="password" value="" class="text medium required" id="wt-xu-password" />
            </p>
            <p>
                <input class="button red" type="submit" value="{$translations[$t_save]}" style="margin-left: 10px; margin-right: 55px; float: right;" id="wt-xu-submit" />
                <input class="button" type="button" value="Find customer" style="margin-left: 0px; float:right;" id="wt-xu-find-client" />
            </p>
        </form>
    </div>
</fieldset>
