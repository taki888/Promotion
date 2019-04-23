angular.module('user',[]).directive("user",["$rootScope","$http","$state",function(rs,$http,$state){
    return {
        restrict: "AE",
        template :'<div class="user">\
                    <i class="icon-user"></i>\
                    <a ui-sref="account" class="username"></a>\
                    <a href="logout.php">退出登录</a>\
                </div>',
        link:function(scope, element, attrs, ctrl){
            $http({url:"api/user.php",method:'GET',headers:{"X-Requested-With":"XMLHttpRequest"}}).then(function (bk) {
                var res=bk.data;
                if(res.error==0){
                    rs.userInfo=res.data;
                    $(element).find(".username").html(res.data.u_name);
                    if(rs.userInfo.u_lastlogin==0){
                        $state.go("account");
                    }
                }else{
                    location.href="login.html";
                }
            })
        }
    }
}]);
