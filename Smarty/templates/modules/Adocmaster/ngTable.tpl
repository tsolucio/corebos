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

<div ng-controller="DemoCtrl">
     Group by:
    <select ng-model="groupby">
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
        <tr ng-hide="group.$hideRows" ng-repeat="user in group.data">
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
                
        </tr>
        </tbody>
    </table>

        <script>
            {literal}
        var app = angular.module('main', ['ngTable']).
        controller('DemoCtrl', function($scope, $filter,ngTableParams,$sce) {
       { 
          
             
                       
               var data = {/literal}{$vleratest}{literal};
            
              
       $scope.groupby='quantity';
                    
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
                }
            });
            
             $scope.$watch('groupby', function(value){
                        $scope.tableParams.settings().groupBy = value;
                        console.log('Scope Value', $scope.groupby);
                        console.log('Watch value', this.last);
                        console.log('new table',$scope.tableParams);
                        $scope.tableParams.reload();
                    });
        })
        
        
     
    {/literal}
        </script>
     
      
</div>


    </body>
</html>