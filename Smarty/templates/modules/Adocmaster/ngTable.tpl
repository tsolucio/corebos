{php}
    require_once('include/utils/UserInfoUtil.php');
    global $current_user,$mod_strings;
    $this->assign("ROLENAME", getRoleName($current_user->roleid));
    $this->assign("MOD",$mod_strings);
{/php}


<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width">
<!--<script data-require="angular.js@*" data-semver="1.2.0-rc3-nonmin" src="http://code.angularjs.org/1.2.0-rc.3/angular.js"></script>
   <script data-require="ng-table@*" data-semver="0.3.0" src="http://bazalt-cms.com/assets/ng-table/0.3.0/ng-table.js"></script>
    <script data-require="ng-table-export@0.1.0" data-semver="0.1.0" src="http://bazalt-cms.com/assets/ng-table-export/0.1.0/ng-table-export.js"></script>
    -->
    <link data-require="ng-table@*" data-semver="0.3.0" rel="stylesheet" href="http://bazalt-cms.com/assets/ng-table/0.3.0/ng-table.css" />
    <link data-require="bootstrap-css@*" data-semver="3.0.0" rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" />
        <link rel="stylesheet" href="modules/test2/css/bootstrap.min.css">
        <link rel="stylesheet" href="modules/test2/css/bootstrap-theme.min.css">
        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
      <!--<script src="modules/test/js/ng-table-export.src.js"></script>
        <script src="modules/test/js/ng-table-export.js"></script> -->
        <!--[if lt IE 9]>
        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <![endif]-->
        <script src="modules/test2/js/angular.min.js"></script>
        <script src="modules/test2/ng-table.js"></script>
        
        <link rel="stylesheet" href="modules/test2/ng-table.css">
    </head>
<body ng-app="main">
<!--<h1>Table with pagination</h1>-->
<!--koment-->
<div ng-controller="DemoCtrl">
     Grouping by:
    <select ng-model="groupby">
        <option value="---">---</option>
        <option value="product">Product</option>
        <option value="quantity">Quantity</option>
        <option value="price">Price</option>
        
    </select>
     
     
    <br>
    Grouped by: <b>{literal}{{groupby}}{/literal}</b>

    <!--<p><strong>Page:</strong> {literal}{{tableParams.page()}}{/literal}
    <p><strong>Count per page:</strong> {literal}{{tableParams.count()}}{/literal}-->
<!--<button ng-click="{literal}tableParams.sorting({}){/literal}" class="btn btn-default pull-right">Clear sorting</button>-->
    <!--<p><strong>Sorting:</strong> {literal}{{tableParams.sorting()|json}}{/literal}-->


    
        <table ng-table="tableParams"  class="table" >
          <!--  <tr ng-repeat="user in $data">
                <td data-title="'AdocdetailNo'" sortable="'name'">
                  <a href="index.php?module=Adocdetail&action=DetailView&record={literal}{{user.adocdetailid}}{/literal}">  {literal}{{user.name}}{/literal}</a>
                </td>
                <td data-title="'Nr Line'" sortable="'age'">
                   {literal} {{user.age}} {/literal}
                </td>
                <td data-title="'Product'" sortable="'accountname'">
                  <a href ="index.php?module=Products&action=DetailView&record={literal}{{user.productid}}{/literal}"> {literal} {{user.accountname}} {/literal}</a>
                </td>
                <td data-title="'Quantity'" sortable="'quantity'">
                   {literal} {{user.quantity}} {/literal}
                </td>
                 <td data-title="'Price'" sortable="'price'">
                   {literal} {{user.price}} {/literal}
                </td>
                <td data-title="'Riferimento'" sortable="'riferimento'">
                   {literal} {{user.riferimento}} {/literal}
                </td>
                <td data-title="'Stock'" sortable="'stock'">
                 <a href="index.php?module=Stock&action=DetailView&record={literal}{{user.stockid}}{/literal}">  {literal} {{user.stock}} {/literal}</a>
                </td>
                
            </tr>
        </table>-->
                   <tbody ng-repeat="group in $groups">
        <tr class="ng-table-group">
            <td colspan="{literal}{{$columns.length}}{/literal}">
                <a href="" ng-click="group.$hideRows = !group.$hideRows">
                    <span class="glyphicon" ng-class="{literal}{ 'glyphicon-chevron-right': group.$hideRows, 'glyphicon-chevron-down': !group.$hideRows }{/literal}"></span>
                    <strong>{literal}{{ group.value }}{/literal}</strong>
                </a>
            </td>
        </tr>
        <tr ng-hide="group.$hideRows" ng-repeat="user in group.data" >
            
           <td data-title="'AdocdetailNo'" sortable="'name'">
                  <a href="index.php?module=Adocdetail&action=DetailView&record={literal}{{user.adocdetailid}}{/literal}">  {literal}{{user.name}}{/literal}</a>
                </td>
                <td data-title="'Nr Line'" sortable="'age'">
                 <span ng-if="!user.$edit">  {literal} {{user.age}} {/literal}</span>
                <div ng-if="user.$edit"><input class="form-control" type="text" ng-model="user.age" /></div>
                </td>
                <td data-title="'Adocdetailname'" sortable="'adocdetailname'">
                   {literal} {{user.adocdetailname}} {/literal}
                </td>
                <td data-title="'Product'" sortable="'product'">
                  <a href ="index.php?module=Products&action=DetailView&record={literal}{{user.productid}}{/literal}"> {literal} {{user.product}} {/literal}</a>
                </td>
                <td data-title="'Quantity'" sortable="'quantity'">
                  <span ng-if="!user.$edit">  {literal} {{user.quantity}} {/literal}</span>
                   <div ng-if="user.$edit"><input class="form-control" type="text" ng-model="user.quantity" /></div>
                </td>
                 <td data-title="'Price'" sortable="'price'">
                   {literal} {{user.price}} {/literal}
                </td>
                <td data-title="'Disocunt'" sortable="'discount'" >
                   {literal} {{user.discount}} {/literal}</span>
                  
                </td>
              
                 <td data-title="'Adocdtotal'" sortable="'adocdtotal'">
                   {literal} {{(user.quantity*user.precio)+(user.quantity*user.precio*user.vat)| number:2}} {/literal}
                </td>
               <td data-title="'Adocdtax'" sortable="'adocdtax'">
                   {literal} {{user.quantity*user.precio*user.vat | number:2}} {/literal}
                </td>
                <td data-title="'Adocdtotalamount'" sortable="'adocdtotalamount'">
                   {literal} {{user.adocdtotalamount | number:2}} {/literal}
                </td>
             {*   <td data-title="'New Price'" sortable="'precio'">
                   {literal} {{user.precio}} {/literal}
                </td>*}
                 
               <td data-title="'Total'" sortable="'total'" width="90%">
                   {literal} {{user.quantity*user.precio | number:2}} {/literal}</span>
                  
                </td>
               
                 <td data-title="'Actions'" width="200">
                <a ng-if="!user.$edit" href="" class="btn btn-default btn-xs" ng-click="user.$edit = true">Edit</a>
                <a ng-if="user.$edit" href="" class="btn btn-primary btn-xs" ng-click="user.$edit = false;setEditId(user.age,user.quantity,user.adocdetailid,user.adocmasterid,user.newtax,user.newadoctotal,user.newadoctotalamount,user.productid);setEditId2(user.age,user.quantity,user.adocdetailid,user.adocmasterid,user.newtax,user.newadoctotal,user.newadoctotalamount,user.productid);">Save</a>
                <a ng-if="user.$edit" href="" class="btn btn-primary btn-xs" ng-click="user.$edit = false;">Cancel</a>
            </td>
        </tr>
        </tbody>
    </table>

        <script>
            {literal}
                var prova7={/literal}{$vleratest}{literal};
                var kURL = "module=Adocmaster&action=AdocmasterAjax&file=prova3&shembulli=prova7";
               // alert(prova7);;
         var record=document.getElementsByName('record').item(0).value;
         var prova7={/literal}{$vleratest}{literal};
         //alert(record);
        var app = angular.module('main', ['ngTable']).
        controller('DemoCtrl', function($scope, $filter,$http,ngTableParams,$sce) {
       { 
          
             
                       
               var data = {/literal}{$vleratest}{literal};
            var data2={/literal}{$vleratest2}{literal};
              
       $scope.groupby='---';
                    
        }
                 

            $scope.tableParams = new ngTableParams({
                page: 1,            // show first page
                count: 10 ,// count per page
                sorting: {
                    name: 'asc'     // initial sorting
                }
            }, {
               groupBy: $scope.groupby,
                        total: data.length, // length of data
                        getData: function($defer, params) {
                            
                            var orderedData = params.sorting() ?
                                     $filter('orderBy')(data, params.orderBy()) :
                                data;

                            $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
                   $http.get('index.php?'+kURL+'&kaction=retrieve&record='+record).
                    success(function(data, status) {
                      var orderedData = data;
                      params.total(data.length);
                      $defer.resolve(orderedData.slice((params.page() - 1) * params.count(),params.page() * params.count()));
                      //alert(record);
                    
    })
                }
            });
  
            
            
             $scope.$watch('groupby', function(value){
                        $scope.tableParams.settings().groupBy = value;
                        console.log('Scope Value', $scope.groupby);
                        console.log('Watch value', this.last);
                        console.log('new table',$scope.tableParams);
                        $scope.tableParams.reload();
                    });
                         $http.post('index.php?'+kURL+'&kaction=update&record='+record+'&shembulli='+record
                )
                .success(function(data, status) {
                      $scope.tableParams.reload();
                     
                 });
                   $scope.setEditId =  function(age,quantity,adocdetailid,adocmasterid,newtax,newadoctotal,newadoctotalamount,productid) {
             $http.post('index.php?'+kURL+'&kaction=update&stato='+age+'&sasia='+quantity+'&adocdetailid2='+adocdetailid+'&adocmasterid2='+adocmasterid+'&newtax2='+newtax+'&newadoctotal2='+newadoctotal+'&newadoctotalamount2='+newadoctotalamount+'&productid2='+productid
                )
                .success(function(data, status) {
            
                      $scope.tableParams.reload();
                     
                 });
                 
        }
             $scope.setEditId2 =  function(age,quantity,adocdetailid,adocmasterid,newtax,newadoctotal,newadoctotalamount,productid) {
             $http.post('index.php?'+kURL+'&kaction=update&stato='+age+'&sasia='+quantity+'&adocdetailid2='+adocdetailid+'&adocmasterid2='+adocmasterid+'&newtax2='+newtax+'&newadoctotal2='+newadoctotal+'&newadoctotalamount2='+newadoctotalamount+'&productid2='+productid
                )
                .success(function(data, status) {
            
                      $scope.tableParams.reload();
                     
                 });
                 
        }
        })
          
        
        
     
    {/literal}
        </script>
     
      
</div>


    </body>
</html>