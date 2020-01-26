{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }
{ assign var="general_configuration" value="General Configuration" }
{ assign var="modules" value="Modules" }
{ assign var="web_filter" value="Web Filter" }
{ assign var="t_antivirus" value="Antivirus" }
{ assign var="t_adv" value="Advertising Control" }
{ assign var="on" value="On" }
{ assign var="off" value="Off" }
{ assign var="change" value="Change" }

<div id="content_info">
  <h2><a href="#">{$software_version}</a></h2>
  <h3>{$translations[$general_configuration]}</h3>
</div>

<div id="modules" class="content_section">
  <fieldset>
  <legend>
  <h2>{$translations[$modules]}</h2>
  </legend>
  <div class="module_content">
    <form action="?module=main&action=modify" method="POST" id="modules">
      <input type="hidden" name="ddimodules" value="ddimodules" />
      <fieldset class="inner">
        <legend>{$translations[$web_filter]}</legend>
        <p>
          <input  name="ddiwebfilter" {if $wfstatus eq "on"}checked{/if} value="on" type="radio" />
          <label for="">{$translations[$on]}</label>
        </p>
        <p>
          <input  name="ddiwebfilter" {if $wfstatus eq "off"}checked{/if} value="off" type="radio" />
          <!--
          <input  name="ddiwebfilter" {if $wfstatus eq "off"}checked{/if} value="off" type="radio" onclick="if(!confirm('Esto deshabilitara tambien el antivirus')){ldelim} return false; {rdelim};changeOption(this.form);" />
          -->
          <label for="">{$translations[$off]}</label>
        </p>
      </fieldset>

      <fieldset class="inner odd">
        <legend>DLP</legend>
        <p>
          <input  name="ddidlp" {if $dlpstatus eq "on"}checked{/if} value="on" type="radio"/>
          <label for="">{$translations[$on]}</label>
        </p>
        <p>
          <input  name="ddidlp" {if $dlpstatus eq "off"}checked{/if} value="off" type="radio"/>
          <label for="">{$translations[$off]}</label>
        </p>
      </fieldset>

      <fieldset class="inner">
        <legend>{$translations[$t_antivirus]}</legend>
        <p>
          <input  name="ddiav" {if $avstatus eq "on"}checked{/if} value="on" type="radio"/>
          <label for="">{$translations[$on]}</label>
        </p>
        <p>
          <input  name="ddiav" {if $avstatus eq "off"}checked{/if} value="off" type="radio"/>
          <label for="">{$translations[$off]}</label>
        </p>
      </fieldset>

      <fieldset class="inner odd">
        <legend>{$translations[$t_adv]}</legend>
        <p>
          <input  name="ddiadv" {if $advstatus eq "on"}checked{/if} value="on" type="radio" />
          <label for="">{$translations[$on]}</label>
        </p>
        <p>
          <input  name="ddiadv" {if $advstatus eq "off"}checked{/if} value="off" type="radio"/>
          <label for="">{$translations[$off]}</label>
        </p>
      </fieldset>

      <fieldset class="inner">
        <legend>Anti-phising</legend>
        <p>
          <input  name="ddipsh" {if $pshstatus eq "on"}checked{/if} value="on" type="radio" />
          <label for="">{$translations[$on]}</label>
        </p>
        <p>
          <input  name="ddipsh" {if $pshstatus eq "off"}checked{/if} value="off" type="radio"/>
          <label for="">{$translations[$off]}</label>
        </p>
      </fieldset>

      <fieldset class="inner odd">
        <legend>AlienVault Feed</legend>
        <p>
          <input  name="ddimlw" {if $mlwstatus eq "on"}checked{/if} value="on" type="radio" />
          <label for="">{$translations[$on]}</label>
        </p>
        <p>
          <input  name="ddimlw" {if $mlwstatus eq "off"}checked{/if} value="off" type="radio"/>
          <label for="">{$translations[$off]}</label>
        </p>
      </fieldset>


      <!--
      <table width="90%" cellpadding="0" cellspacing="20">
        <tr>
          <td><b>Filtro web:</b></td><td><input  name="ddiwebfilter" {if $wfstatus eq "on"}checked{/if} value="on" type="radio"> Encendido</td><td><input  name="ddiwebfilter" {if $wfstatus eq "off"}checked{/if} value="off" type="radio" onclick="if(!confirm('Esto deshabilitara tambien el antivirus')){ldelim} return false; {rdelim};changeOption(this.form);"> Apagado</td>
        </tr>
        <tr>
          <td><b>Antivirus:</b></td><td><input  name="ddiav" {if $avstatus eq "on"}checked{/if} value="on" type="radio"> Encendido</td><td><input  name="ddiav" {if $avstatus eq "off"}checked{/if} value="off" type="radio"> Apagado</td>
        </tr>
      </table>
     -->

      <input type="submit" class="button" value="{$translations[$change]}">

    </form>
  </div>
 </fieldset>
</div>