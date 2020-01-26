{ assign var="t_general_configuration" value="General Configuration" }
{ assign var="t_save" value="Save" }
{ assign var="t_subscription" value="Subscription" }
{ assign var="t_help_box_subscription" value="Help Box Subscription" }
{ assign var="t_license" value="License" }
{ assign var="t_company" value="Company" }
{ assign var="t_plan" value="Plan" }
{ assign var="t_number_users" value="Number of users" }
{ assign var="t_cost_user_month" value="Cost of user per month" }
{ assign var="t_extra_users" value="Add extra users" }
{ assign var="t_period" value="Period" }
{ assign var="t_month" value="month" }
{ assign var="t_year" value="year" }
{ assign var="t_years" value="years" }
{ assign var="t_total_amount" value="Total amount" }
{ assign var="t_discount" value="discount" }
{ assign var="t_expiry" value="Expiry" }
{ assign var="t_reset" value="Reset" }
{ assign var="t_add" value="Add" }


<form action="?module=main&action=saveCloudConfig" method="POST" enctype="multipart/form-data" id="advanced_config">
    <fieldset>
        <legend>{$translations[$t_subscription]}</legend>
        <p>
        <div class="help_box">
            {$translations[$t_help_box_subscription]}
        </div>
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_license]}</span>
            <input type="text" name="license" value="{$client.license}" readonly="readonly" id="client_license" class="text medium">
        </p>
        {if $client.company neq ""}
            <p>
                <span class="entitle_medium">{$translations[$t_company]}</span>
                <input type="text" name="company" value="{$client.company}" readonly="readonly" id="client_company" class="text medium">
            </p>
        {/if}
        <p>
            <span class="entitle_medium">{$translations[$t_plan]}</span>
            <span>
                {* Verify last check in all browsers *}
                <input type="radio" name="type" value="premium" checked="checked" id="client_type_premium" /> <label for="">Premium</label>
                <input type="radio" name="type" value="freemium" {if $client.type eq "freemium"} checked="checked" {/if} {if $client.type eq "premium"} disabled="disabled" {/if} id="client_type_freemium" /> <label for="">Freemium</label>
            </span>
        </p>
        <div id="preemium_client_opt" {if $client.type == "freemium"} style="display:none;" {/if}>
            <div class="line">
                <p>
                    <span class="entitle_medium">{$translations[$t_number_users]}</span>
                    <input type="text" name="nbr_users" value="{$client.nbr_users}" {if $client.type eq "premium"} readonly="readonly" {/if} id="client_nbr_users" class="text medium required line" />
                </p>
                <p>
                    <span class="entitle_medium line">{$translations[$t_cost_user_month]}</span>
                    <input type="text" name="cost_user_per_month" {if $client.cost_user_per_month} value="{$client.cost_user_per_month}" {else} value="6.99" {/if} readonly="readonly" id="client_cost_user_month" class="text short required" /> 
                </p>
                <div class="pijadita">$</div>
                
                {if $client.type == "premium"}
                    <p>
                        <span class="entitle_medium">{$translations[$t_extra_users]}</span>
                        <input type="text" name="extra_users" value="" id="extra_users" class="text medium required" />
                        <input type="button" name="add_extra_users" value="{$translations[$t_add]}" id="add_extra_users" class="button line" />
                        <input type="reset" name="reset_extra" value="{$translations[$t_reset]}" id="reset_extra_users" class="button line red" />
                    </p>
                {/if}
            </div>
            
            <div id="period_block">
            <p>
                <span class="entitle_medium">{$translations[$t_period]}</span>
                <span id="periods">
                    {* Verify last check in all browsers *}
                    <input type="radio" name="period" value="1"  {if $client.period eq 1}  checked="checked" {/if} {if $client.type eq "premium"} disabled="disabled" {/if} id="client_period_1m"  /> <label for="">1 {$translations[$t_month]}</label>
                    <input type="radio" name="period" value="12" {if $client.period eq 12} checked="checked" {/if} {if $client.type eq "premium"} disabled="disabled" {/if} id="client_period_12m" /> <label for="">1 {$translations[$t_year]} </label>
                    <input type="radio" name="period" value="24" {if $client.period eq 24} checked="checked" {/if} {if $client.type eq "premium"} disabled="disabled" {/if} id="client_period_24m" /> <label for="">2 {$translations[$t_years]}</label>
                    <input type="radio" name="period" value="36" {if $client.period eq 36} checked="checked" {/if} {if $client.type eq "premium"} disabled="disabled" {/if} id="client_period_36m" /> <label for="">3 {$translations[$t_years]}</label>
                </span>
                <span style="display:none;" id="xperiod">
                    <input type="text"  name="xperiod" value="{$client.period} month(s)" readonly="readonly" id="client_period_xm" class="text medium required" />
                </span>
            </p>
            </div>
            
            <div id="total_amount_block">
                <p>
                    <span class="entitle_medium">{$translations[$t_total_amount]}</span>
                    <input type="text" name="total_amount"     value="0.00" readonly="readonly" id="total_amount"     class="text short required line" /> - 
                    <input type="text" name="percent_discount" value="0%"   readonly="readonly" id="percent_discount" class="text short required line" /> = 
                    <input type="text" name="final_amount"     value="0.00" readonly="readonly" id="final_amount"     class="text short required line" />
                </p>
                <div class="pijaditas">
                    <div>$</div>
                    <div>{$translations[$t_discount]}</div>
                    <div>$</div>
                </div>
            </div>
        </div>
                
        {if $client.type == 'premium'}
            <p>
                <span class="entitle_medium">{$translations[$t_expiry]}</span>
                <input type="text" name="expiry" value="{$client.expiry}" readonly="readonly" id="client_expiry" class="text medium required" />
            </p>
        {/if}                
    </fieldset>

    <div id="button_freemium_cloud_user" {if $client.type eq "premium"} style="display:none;" {/if}>
        <input class="button red" type="submit" value="{$translations[$t_save]}" />
    </div>
</form>    

{if $client.type neq "premiumo"}
    <div id="button_premium_cloud_user" {if $client.type eq "freemium" or $client.expiry neq ""} style="display:none;" {/if}>
        {*
        <form id="subscription_payment_form" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_blank" {if $client.type eq "premium" and $client.period neq 1} style="display:none" {/if}>
            <input type="hidden" name="cmd" value="_xclick-subscriptions" />
            <input type="hidden" name="business" value="1nfO_1354732629_biz@drainware.com" />
            <input type="hidden" name="lc" value="ES" />
            <input type="hidden" name="item_name" value="Drainware Premium Subscription" />
            <input type="hidden" name="item_number" value="0" id="subscription_payment_id" />
            <input type="hidden" name="no_note" value="1" />
            <input type="hidden" name="src" value="1" />
            <input type="hidden" name="a3" value="9999.99" id="subscription_payment_amount" />
            <input type="hidden" name="p3" value="1" />
            <input type="hidden" name="t3" value="M" />
            <input type="hidden" name="currency_code" value="USD" />
            <input type="hidden" name="notify_url" value="https://www.drainware.com/ddi/?module=cloud&action=paymentNotification" />
            <input type="hidden" name="bn" value="PP-SubscriptionsBF:btn_subscribe_LG.gif:NonHostedGuest" />
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal." />
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" />
        </form>   
        *}
        
        <form id="payment_form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" {*if $client.type eq "freemium" or $client.period eq 1} style="display:none;" {/if*}>
            <input type="hidden" name="cmd" value="_xclick" />
            <input type="hidden" name="business" value="sales@drainware.com" />
            <input type="hidden" name="lc" value="ES" />
            <input type="hidden" name="item_name" value="Drainware Premium Pack 1 year" id="payment_pack_name"/>
            <input type="hidden" name="item_number" value="0" id="payment_id" />
            <input type="hidden" name="amount" value="9999.99" id="payment_amount" />
            <input type="hidden" name="button_subtype" value="services" />
            <input type="hidden" name="no_note" value="1" />
            <input type="hidden" name="no_shipping" value="2" />
            <input type="hidden" name="currency_code" value="USD" />
            <input type="hidden" name="notify_url" value="https://www.drainware.com/ddi/?module=cloud&action=paymentNotification" />
            <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted" />
            <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" border="0" name="submit" alt="PayPal. La forma rÃ¡pida y segura de pagar en Internet." id="button_premium_cloud_user_submit" />
            {*<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" />*}
        </form>    
    </div>
{/if}
