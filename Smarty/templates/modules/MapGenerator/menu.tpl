<link type="text/css" href="Smarty/templates/modules/MapGenerator/jquery-ui.css" rel="stylesheet"/>
<script type="text/javascript" src="include/jquery/jquery.js"></script>
<script type="text/javascript" src="include/jquery/jquery-ui.js"></script>
<link type="text/css" href="modules/MapGenerator/css/style.css" rel="stylesheet"/>
<link type="text/css" href="include/LD/assets/styles/salesforce-lightning-design-system.css" rel="stylesheet"/>
<link type="text/css" href="include/LD/assets/styles/salesforce-lightning-design-system.min.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/css/popupNotification.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="modules/MapGenerator/css/Loading.css" rel="stylesheet">
<script type="text/javascript" src="modules/MapGenerator/language/{$currlang}.lang.js"></script>
<script type="text/javascript" src="modules/MapGenerator/js/functions.js"></script>
<script type="text/javascript" src="modules/MapGenerator/js/script.js"></script>
<script type="text/javascript" src="modules/MapGenerator/js/MapGenerator.js"></script>
<script src="https://use.fontawesome.com/c74e66ed00.js"></script>

{literal}
<style>
#mvtitle {
    height: 50px;
    font-weight: bold;
    text-align: center;
    color: #717171;
    font-family: verdana;
    font-size: 34px;
    margin-top: 2%;
}




#exitlink img:hover{
    content: url('modules/MapGenerator/image/closeMouseover.png');
}


</style>
<script>
        // jQuery(function() {
        //   jQuery( "#tabs" ).tabs();
        // });


        function selectTab(elmnt) {
            // slds-active
            $(elmnt).parent("li").siblings(".slds-active").removeClass("slds-active");
            $(elmnt).parent("li").addClass("slds-active");

            // tabindex
            $(elmnt).parent("li").siblings().children("a").attr("tabindex", -1);
            $(elmnt).attr("tabindex", 0);

            // aria-selected
            $(elmnt).parent("li").siblings().children("a").attr("aria-selected", false);
            $(elmnt).attr("aria-selected", true);

            // hiding previouly selected tab (slds-show/slds-hide)
            $(elmnt).closest(".slds-tabs--default").children("div[role='tabpanel'].slds-show").addClass("slds-hide");
            $(elmnt).closest(".slds-tabs--default").children("div[role='tabpanel'].slds-show").removeClass("slds-show");
            // displaying newly selected tab (slds-show/slds-hide)
            $(elmnt).closest(".slds-tabs--default").children("div[aria-labelledby='" + elmnt.id + "']").addClass("slds-show");
            $(elmnt).closest(".slds-tabs--default").children("div[aria-labelledby='" + elmnt.id + "']").removeClass("slds-hide");
        }


    </script>
    {/literal}
    <div id="mvtitle">{$MOD.MVCreator}</div>

    <div /*id="tabs"*/ class="slds-tabs--default" style="width: 70%; margin-left: 25%; margin-top: 2%;margin-bottom: 18%; height:auto">
        <ul class="slds-tabs--default__nav" role="tablist">
            <li class="slds-tabs--default__item slds-text-heading--label slds-active" title="{$MOD.CreateView}"
            role="presentation">
            <a class="slds-tabs--default__link" href="" role="tab" tabindex="0"  data-autoload-maps="true" data-autoload-Filename="MapGenerator,createMaps"  aria-selected="true" data-autoload-id-put="CreateMaps" data-autoload-id-relation="LoadMAps"
            aria-controls="tab-default-1" id="tab-default-1__item" onclick="selectTab(this);">{$MOD.CreateView}</a>
        </li>

        <li class="slds-tabs--default__item slds-text-heading--label" title="Load  Map"
        role="presentation">
        <a class="slds-tabs--default__link" href="" role="tab" tabindex="-1" aria-selected="false" data-autoload-maps="true" data-autoload-Filename="MapGenerator,LoadAllMaps" data-autoload-id-put="LoadMAps" data-autoload-id-relation="CreateMaps" aria-controls="tab-default-2" id="tab-default-2__item"
        onclick="selectTab(this);">{$MOD.LoadMap}</a>
    </li>
    <li class="slds-tabs--default__item slds-text-heading--label" style="margin-left: 280px;" title="{$MOD.exit}"
    role="presentation">
    <a id="exitlink" class="slds-tabs--default__link" href="" role="tab" tabindex="-1" aria-selected="false"
    aria-controls="tab-default-2" id="tab-default-2__item"
    onclick="closeView();"><img src="modules/MapGenerator/image/closebtn.png" alt="Close view" style="width: 35px;height: 35px;"></a>
</li>
{*<li><a href="#tabs-2">{$MOD.GestioneViste}</a></li>*}
{*<li><a href="index.php?module=MapGenerator&action=MapGeneratorAjax&file=createView&todo=createReportTable">{$MOD.CreateScriptReport}</a></li>*}
{*<li><a href="index.php?module=MapGenerator&action=MapGeneratorAjax&file=createView&todo=createReportTable2">{$MOD.CreateScriptNameReport}</a></li>*}
{*<li><a href="index.php?module=MapGenerator&action=MapGeneratorAjax&file=createView&todo=FSscript">{$MOD.CreazioneScriptFS}</a></li>*}
</ul>
<div id="snackbar" ></div>
<div id="tab-default-1" class="slds-tabs--default__content slds-show" role="tabpanel"
aria-labelledby="tab-default-1__item">
<div id="CreateMaps">
    <span id="ShowErorrNameMap" class="error" style="margin-left: 227px;padding: 5px;background-color: red;width: 50%;font;font-size: 12px;border-radius: 9px;color: white;float: none;display: none;"> </span>
    <div id="DivObjectID">
         <div class="slds-text-title" id='labelNameView' style="float: left; overflow:hidden;"><h3 class="slds-section-title--divider">{$MOD.NameView}:</h3></div>
         <div class="slds-form-element__control allinea" id='nameViewDiv'>
              <div class="slds-form-element"  style="width:100%;height:100%; ">
                <div  class="slds-form-element__control">
                    <div class="slds-select_container">
                        <select  data-load-Map="true" data-type-select="TypeObject" class="slds-select">
                            <option value="">{$MOD.ChooseTypeOfMap}</option>
                            <option value="MaterializedView">{$MOD.MaterializedView}</option>
                            <option value="Script">{$MOD.Script}</option>
                            <option value="Map">{$MOD.Map}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
     </div>
<div id="MapDivID" style="display: none;">

    <div class="map-creator-block">

        <div class="insert-name-block">

         <div class="slds-text-title" id='labelNameView' style="float: left; overflow:hidden;"><h3 class="slds-section-title--divider">{$MOD.InsertNameQuery}:</h3></div>
         <div class="slds-form-element__control allinea" id='nameViewDiv'>
          <div class="slds-form-element"  style="margin:0; width:100%;height:100%; ">
            <div  class="slds-form-element__control">                   
               <input type="text" minlength="5" id="nameView" name="nameView" data-controll="true" data-controll-idlabel="ShowErorrNameMap" data-controll-file="MapGenerator,CheckNameOfMap" data-controll-id-relation="TypeMaps" class="slds-input" name='nameView' placeholder="{$MOD.addviewname}" />              
           </div>              
       </div>         
   </div>
</div>

<div class="choose-type-block">

 <div class="slds-text-title" id='labelNameView' style="float: left; overflow:hidden;"><h3 class="slds-section-title--divider">{$MOD.TypeMapNone}:</h3></div>
 <div class="slds-form-element__control allinea" id='nameViewDiv'>
  <div class="slds-form-element"  style="width:100%;height:100%; ">
    <div  class="slds-form-element__control">
        <div class="slds-select_container">
            <select data-load-Map="true" data-type-select="TypeMap"  data-type-select-module="MapGenerator,ChoseeObject"  id="TypeMaps" class="slds-select" disabled>
                {$Allmaps}
            </select>
        </div>
    </div>

</div>
</div>   

</div>

</div>

</div>    
</div> {* end div CreateMaps *}
</div> {* edn divtab *}

{* </div> *}

<div id="tab-default-2" class="slds-tabs--default__content slds-hide" role="tabpanel"
aria-labelledby="tab-default-2__item">
<div id="LoadMAps">

</div>

</div>
</div>

</div>

<script>

    function  closeView() {
        if (confirm('Are you sure you want to close this page')) {
            location.reload();
        } else {
            // Do nothing!
        }

    }


    App.baseUrl = '{$URLAPP}'+'/';
    App.disambleInspectelement=true;
    

</script>
