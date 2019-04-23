(function() {
    app.controller("changePassword",['$scope', '$http', function (scope, $http) {
        scope.change={
            pwd:'',
            new_pwd:'',
            new_pwd_confirm:''
        };
        scope.check=function () {
            if(!scope.change.pwd){
                scope.error=true;
                scope.errorMsg="请输入原密码";
                return false
            }
            if(!scope.change.new_pwd){
                scope.error=true;
                scope.errorMsg="请输入新密码";
                return false
            }
            if(!scope.change.new_pwd_confirm){
                scope.error=true;
                scope.errorMsg="请再次输入新密码";
                return false
            }
            if(scope.change.new_pwd!=scope.change.new_pwd_confirm){
                scope.error=true;
                scope.errorMsg="两次输入的新密码不一致，请确认后重新输入";
                return false
            }
            return true
        };
        scope.sub=function () {
            if(!scope.loading && scope.check()){
                scope.loading=true;
                $http({url:"api/user_pwd.php",data:{pwd:scope.change.pwd,new_pwd:scope.change.new_pwd},method:'POST',headers:{"X-Requested-With":"XMLHttpRequest"}}).then(function (bk) {
                    scope.loading=false;
                    var res=bk.data;
                    if(res.error==0){
                        scope.closeThisDialog();
                    }else{
                        scope.error=true;
                        scope.errorMsg=res.msg;
                    }
                })
            }
        }

    }]);
    app.controller('account', ['$scope', '$rootScope', 'ngDialog', function (s, rs, ngDialog) {
        rs.patchName = "";
        s.changePassword=function () {
            ngDialog.open({
                template:"template/changePasswordDialog.html",
                controller:"changePassword",
                closeByDocument:false,
                closeByEscape:false,
                showClose:false
            })
        };
        if(rs.userInfo && rs.userInfo.u_lastlogin==0){
            s.changePassword();
        }
    }])
})();