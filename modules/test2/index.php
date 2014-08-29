<?php 

global $adb;
$query=$adb->query("SELECT  vtiger_project.lavorato,vtiger_project.dayson,vtiger_project.lavorato,vtiger_project.projectid,vtiger_project.progetto,vtiger_project.linktoaccountscontacts,vtiger_account.accountid,vtiger_project.project_id,e2.projectname AS proj,e3.accountname as proj2,vtiger_project.linktoaccountscontacts,vtiger_project.linktobuyer,vtiger_account.accountname,vtiger_crmentity.assigned_user_id, vtiger_project.projectname,vtiger_project.progetto,vtiger_project.project_no,vtiger_project.substatusproj,vtiger_crmentity.crmid, vtiger_crmentity.createdtime,vtiger_project.serial_number,vtiger_project.rma,vtiger_crmentity.smownerid,vtiger_users.id,vtiger_users.user_name
FROM vtiger_project 
LEFT JOIN vtiger_project AS e2 ON e2.projectid=vtiger_project.progetto
INNER JOIN vtiger_crmentity
ON vtiger_project.projectid=vtiger_crmentity.crmid 
LEFT JOIN vtiger_account
ON vtiger_project.linktoaccountscontacts=vtiger_account.accountid
LEFT JOIN vtiger_account AS e3 on e3.accountid=vtiger_project.linktobuyer
LEFT JOIN vtiger_users
ON vtiger_crmentity.smownerid=vtiger_users.id 
WHERE  vtiger_crmentity.deleted=0   
");



//echo $adb->query_result($query,0,'projectid');

$i=0;
            $rows=$adb->num_rows($query);
            $return_arr = array();
$data=array();
$arr = array();
   $arr2 = array();
            while($rows=$adb->fetch_array($query)){
                $rowArr = array(
    'name' => $rows['crmid'],
                    'age' =>$rows['projectname'],
                    'accountname'=>$rows['proj2']
    
            
                  );
$return_arr[] = $rowArr;
      //array_push($arr2, $arr);
            //echo $adb->query_result($query,$i,'projectid');
            $i++;
            //echo $return_arr['name'];
            }
          for($i=0;$i<10;$i++) { //echo $rowArr['name'];
              
          }
          
            $i = 0;

$query7=$adb->query("SELECT  vtiger_project.lavorato,vtiger_project.dayson,vtiger_project.lavorato,vtiger_project.projectid,vtiger_project.progetto,vtiger_project.linktoaccountscontacts,vtiger_account.accountid,vtiger_project.project_id,e2.projectname AS proj,e3.accountname as proj2,vtiger_project.linktoaccountscontacts,vtiger_project.linktobuyer,vtiger_account.accountname,vtiger_crmentity.assigned_user_id, vtiger_project.projectname,vtiger_project.progetto,vtiger_project.project_no,vtiger_project.substatusproj,vtiger_crmentity.crmid, vtiger_crmentity.createdtime,vtiger_project.serial_number,vtiger_project.rma,vtiger_crmentity.smownerid,vtiger_users.id,vtiger_users.user_name
FROM vtiger_project 
LEFT JOIN vtiger_project AS e2 ON e2.projectid=vtiger_project.progetto
INNER JOIN vtiger_crmentity
ON vtiger_project.projectid=vtiger_crmentity.crmid 
LEFT JOIN vtiger_account
ON vtiger_project.linktoaccountscontacts=vtiger_account.accountid
LEFT JOIN vtiger_account AS e3 on e3.accountid=vtiger_project.linktobuyer
LEFT JOIN vtiger_users
ON vtiger_crmentity.smownerid=vtiger_users.id 
WHERE  vtiger_crmentity.deleted=0   
");
$rows=$adb->num_rows($query7);
$data7=array();
while($rows7=$adb->fetch_array($query7)){

    $data7[$i]['id']=$rows7['projectid'];
//echo $data7[$i]['id'];
//echo '<br>'.$rows7['projectid'];
    $i++;
}


//echo json_encode($return_arr);
$vlera=json_encode($return_arr);

$adocquery=$adb->query("select * from  vtiger_adocdetail  join vtiger_crmentity on crmid=adocdetailid join vtiger_adocmaster on adocmasterid=adoctomaster left join vtiger_products on productid=adoc_product
    where deleted=0");
$z=0;
            $rowsz=$adb->num_rows($adocquery);
            $return_arrz = array();
$dataz=array();
$arrz = array();
   $arr2z = array();
            while($rowsz=$adb->fetch_array($adocquery)){
                $rowArrz = array(
    'name' => $rowsz['adocdetailno'],
                    'age' =>$rowsz['adocdetailname'],
                    'accountname'=>$rowsz['nrline']
    
            
                  );
$return_arrz[] = $rowArrz;
      //array_push($arr2, $arr);
            //echo $adb->query_result($query,$i,'projectid');
            $z++;
            //echo $return_arr['name'];
            }
            $vleraz=json_encode($return_arrz)
//$vlera2=JSON.stringify($vlera);
//echo json_encode($return_arr->name);
//echo $arr2['name'];
//$prova=json_encode($return_arr);
//$tjeter=json_encode($return_arr);
//foreach ( $tjeter as $var1 )
//{
  //  echo $var1->name;
//}
//echo $prova;
//echo $tjeter->{'name'};
//echo $prova->name;


//$obj = json_decode($return_arr);
//echo $obj->{'name'};

    //   $prova2=array();
      // for ($i=0;$i<10;$i++){
       //    $prova2[$i]=$adb->query_result($query,$i,'projectid');
       //    echo $prova2[$i];
           
      // }



?>

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
        <link rel="stylesheet" href="modules/test/css/bootstrap.min.css">
        <link rel="stylesheet" href="modules/test/css/bootstrap-theme.min.css">
        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
      <!--<script src="modules/test/js/ng-table-export.src.js"></script>
        <script src="modules/test/js/ng-table-export.js"></script> -->
        <!--[if lt IE 9]>
        <script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <![endif]-->
        <script src="modules/test/js/angular.min.js"></script>
        <script src="modules/test/ng-table.js"></script>
        
        <link rel="stylesheet" href="modules/test/ng-table.css">
    </head>
<body ng-app="main">
<h1>Table with pagination</h1>

<div ng-controller="DemoCtrl">
<!--<a class="btn btn-primary" ng-mousedown="csv.generate()" ng-href="{{ csv.link() }}" download="test.csv">Export to CSV</a>-->
    <p><strong>Page:</strong> {{tableParams.page()}}
    <p><strong>Count per page:</strong> {{tableParams.count()}}
<button ng-click="tableParams.sorting({})" class="btn btn-default pull-right">Clear sorting</button>
    <p><strong>Sorting:</strong> {{tableParams.sorting()|json}}

        <table ng-table="tableParams"  class="table" >
            <tr ng-repeat="user in $data">
                <td data-title="'Name'" sortable="'name'">
                    {{user.name}}
                </td>
                <td data-title="'ProjectName'" sortable="'age'">
                    {{user.age}}
                </td>
                <td data-title="'AccountName'" sortable="'accountname'">
                    {{user.accountname}}
                </td>
            </tr>
        </table>

        <script>
        var app = angular.module('main', ['ngTable']).
        controller('DemoCtrl', function($scope, $filter,ngTableParams,$sce) {
       { 
           <?php 
           $data2=array();
           for($i=0;$i<10;$i++){
           $data2[$i]=$adb->query_result($query,$i,'projectid');
           //echo $data2[$i];
           }?>
                       
          var data = 
            
              <?=$vleraz?>;
                    
        }
                 

            $scope.tableParams = new ngTableParams({
                page: 1,            // show first page
                count: 10 ,// count per page
                sorting: {
                    name: 'asc'     // initial sorting
                }
            }, {
                total: data.length, // length of data
                getData: function($defer, params) {
                     var orderedData = params.sorting() ?
                                        $filter('orderBy')(data, params.orderBy()) :
                                        data;
                    $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
                }
            });
        })
        
        
     
    
        </script>
     
      
</div>


    </body>
</html>


