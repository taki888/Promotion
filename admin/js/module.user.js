angular.module("user",[]).directive("user",["$rootScope","$http","$state",function(e,t,a){return{restrict:"AE",template:'<div class="user">                    <i class="icon-user"></i>                    <a ui-sref="account" class="username"></a>                    <a href="logout.php">退出登录</a>                </div>',link:function(r,u,n,s){t({url:"api/user.php",method:"GET",headers:{"X-Requested-With":"XMLHttpRequest"}}).then(function(t){var r=t.data;0==r.error?(e.userInfo=r.data,$(u).find(".username").html(r.data.u_name),0==e.userInfo.u_lastlogin&&a.go("account")):location.href="login.html"})}}}]);