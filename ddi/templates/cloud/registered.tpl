{ assign var="t_registered_correctly" value="Hooray! You have registered correctly!" }
{ assign var="t_receive_active_login" value="receive activate login" }

<!--
<div align="center">
<img src="images/robots/robot1.png">
</div>
-->
<br />
<br />
<br />

<div class="cloud_registered">
<p>{$translations[$t_registered_correctly]}</p>
</div>

<br/>
<br/>
<br/>

<!--<p>Now you need to <b>activate</b> your account.</p>
<br/> -->
<div class="cloud_registered_medium">
<img src="images/attention.png">
<p>{$translations[$t_receive_active_login]}</p>
<!-- <a class="button" href="?module=main&action=login"> Login </a> -->
</div>

{literal}
<script type="text/javascript">
trak.io.track('Signed up');
</script>
{/literal}


