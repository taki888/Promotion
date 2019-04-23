var app=angular.module('app',['nav','user']).config(["$provide", "$compileProvider", "$controllerProvider", "$filterProvider",'$httpProvider',
    function ($provide, $compileProvider, $controllerProvider, $filterProvider,$httpProvider) {
        app.controller = $controllerProvider.register;
        app.directive = $compileProvider.directive;
        app.filter = $filterProvider.register;
        app.factory = $provide.factory;
        app.service = $provide.service;
        app.constant = $provide.constant;
        $httpProvider.defaults.transformRequest.push(function(obj){
            if(obj && typeof obj=='string'){
                obj=JSON.parse(obj);
                var str=[];
                for(var p in obj){
                    str.push(encodeURIComponent(p)+"="+encodeURIComponent(obj[p]));
                }
                return str.join("&");
            }
        });
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
    }
]);


app.directive("checkAll",function(){
    return {
        restrict: "AE",
        link: function (scope, element, attrs, ctrl) {
            $(document).on("change","."+attrs.checkAll,function () {
                scope.$apply(function(){
                    scope[attrs.ngModel]=false;
                });
                if($(this).prop("checked")){
                    scope[attrs.checkValue].push($(this).val())
                }else{
                    var me=$(this);
                    scope[attrs.checkValue]=scope[attrs.checkValue].filter(function (id) {
                        return id!=me.val();
                    })
                }
            });
            element.on("change",function () {
                var checkBoxes=$("."+attrs.checkAll+":enabled");
                if($(this).prop("checked")){
                    checkBoxes.prop("checked",true);
                    scope[attrs.checkValue]=[];
                    checkBoxes.each(function(i,checkbox){
                        scope[attrs.checkValue].push($(checkbox).val());
                    })
                }else{
                    checkBoxes.prop("checked",false);
                    scope[attrs.checkValue]=[];
                }
            })
        }
    }
});

app.factory('http', ['$http', function ($http) {
    function Http() {}
    Http.prototype.get=function (url,data, callback) {
        $http({
            url: url,
            params: data,
            method: 'GET',
            headers: {"X-Requested-With": "XMLHttpRequest"}
        }).then(function (bk) {
            var res = bk.data;
            callback(res);
        }).catch(function (err) {
            callback({
                error:err.status,
                msg:"接口错误"
            })
        })
    };
    Http.prototype.post=function (url,data, callback) {
        $http({
            url: url,
            data: JSON.stringify(data),
            method: 'POST',
            headers: {"X-Requested-With": "XMLHttpRequest"}
        }).then(function (bk) {
            var res = bk.data;
            callback(res);
        }).catch(function (err) {
            callback({
                error:err.status,
                msg:"接口错误"
            })
        })
    };
    return new Http();
}]);