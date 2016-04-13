/**
 * Created by 204072257 on 4/11/2016.
 */
var cf = angular.module('cf', []);

cf.controller('test', function ($scope, $http) {
    console.log("Hello");
    $scope.getInfo = function (){
        console.log("ok");
        $http.get('https://api.system.aws-usw02-pr.ice.predix.io/info').then(function(res){
            console.log(res.data);
        });
    };
});
