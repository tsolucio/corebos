<link rel="stylesheet" href="themes/login/lds/sfdc_210.css">
<link rel="stylesheet" href="themes/login/BluewhiteBoxed/BluewhiteBoxed.css">
</head>
<body onload="set_focus()" data-gr-c-s-loaded="true">
   <div class="cbds-main cbds-Bluebox">
   <div class="cbds-submain1">
   </div>
   <div class="cbds-submain">
   <div class="cbds-left cbds-leftbox">
      <div class="cbds-container cbds-sizecontainer">
         <div class="cbds-crmlogo">
            <a href="index.php"><img src="{$COMPANY_DETAILS.companylogo}"></a>
         </div>
         <ul class="cbds-desc cbds-descbox">
            <li>
               <img id='cbds-iconimage' src="themes/login/images/checked.png" style="width:25px;height:25px">
               <p style="padding-left:1rem">{'LBL_Bussiness'|getTranslatedString}</p>
            </li>
            <li>
               <img id='cbds-iconimage' src="themes/login/images/checked.png" style="width:25px;height:25px">
               <p style="padding-left:1rem">{'LBL_Management'|getTranslatedString}</p>
            </li>
            <li>
               <img id='cbds-iconimage' src="themes/login/images/checked.png" style="width:25px;height:25px">
               <p style="padding-left:1rem">{'LBL_Statistics'|getTranslatedString}</p>
            </li>
         </ul>
         <div class="cbds-pic-ctn">
            <img src="themes/login/images/undraw_creative_team.png">
         </div>
         <div id="cbds-footer">© Powered by {$coreBOS_uiapp_name}.</div>
      </div>
   </div>
   <div class="cbds-right cbds-rightbox">
      <div class="cbds-container">
         <span id='headlogin'; style="font-size:44px; font-weight: 600 !important; color:#04589B; font-family: 'Roboto', sans-serif;">{'LBL_LOGIN'|getTranslatedString:'Users'|upper}</span>
         <p style="font-size:20px;padding:1rem 0;color: black;">{'LBL_Please_login_to_your_account'|getTranslatedString:'Users'}</p>
         <div class="inpt">
            <div id="cbds-theloginform" style="display: block;">
               {if $LOGIN_ERROR neq ''}
               <div class="errorMessage">{$LOGIN_ERROR}</div>
               {/if}
               <form method="post" id="login_form" action="index.php" target="_top" autocomplete="off" novalidate="novalidate">
                  <input type="hidden" name="module" value="Users" />
                  <input type="hidden" name="action" value="Authenticate" />
                  <input type="hidden" name="return_module" value="Users" />
                  <input type="hidden" name="return_action" value="Login" />
                  <input type="hidden" name="twofauserauth" value="{$authuserid}" />
                  <div id="usernamegroup" class="inputgroup">
                     <label for="username" class="cbds-label">{'Email'|getTranslatedString:'Users'|upper}</label>
                     <div id="username_container">
                        <input class="input r4 wide mb16 mt8 username cbds-usrname cbds-bordercolor" type="email" value="{$uname}" name="user_name" id="username" style="display: block;">
                     </div>
                  </div>
                  <div class="inpt">
                     <label for="password" class="cbds-label">{'LBL_2FACODE'|getTranslatedString:'Users'|upper}</label>
                     <div class="cbds-iconB">
                        <input class="input r4 wide mb16 mt8 password cbds-usrname cbds-bordercolor" type="text" id="user_2facode" name="user_2facode"
                           onkeypress="checkCaps(event)" autocomplete="off">
                        <svg class="slds-button__icon  cbds-button__primary" id="btnid" aria-hidden="true" onclick="showPassword()">
                           <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#preview"></use>
                        </svg>
                     </div>
                  </div>
                  <div class="mb16"><a href="javascript:sendnew2facode({$authuserid});">{'LBL_2FAGETCODE'|getTranslatedString:'Users'}</a></div>
                  <div id="pwcaps" class="mb16" style="display: none">
                     <img id="pwcapsicon" alt="{'CapsLockActive'|getTranslatedString}" width="12" src="themes/login/lds/capslock_blue.png">
                     {'CapsLockActive'|getTranslatedString}
                  </div>
                  <input type="submit" id="cbds-Login" name="Login" value="{'LBL_LOGIN'|getTranslatedString:'Users'|upper}">
               </form>
            </div>
         </div>
      </div>
      <div id="cbds-footer2">© Powered by {$coreBOS_uiapp_name}.</div>
   </div>
</body>
<script src="themes/login/BluewhiteBoxed/Bluewhite.js"></script>