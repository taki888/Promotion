!function(){app.controller("changePassword",["$scope","$http",function(e,r){e.change={pwd:"",new_pwd:"",new_pwd_confirm:""},e.check=function(){return e.change.pwd?e.change.new_pwd?e.change.new_pwd_confirm?e.change.new_pwd==e.change.new_pwd_confirm||(e.error=!0,e.errorMsg="两次输入的新密码不一致，请确认后重新输入",!1):(e.error=!0,e.errorMsg="请再次输入新密码",!1):(e.error=!0,e.errorMsg="请输入新密码",!1):(e.error=!0,e.errorMsg="请输入原密码",!1)},e.sub=function(){!e.loading&&e.check()&&(e.loading=!0,r({url:"api/user_pwd.php",data:{pwd:e.change.pwd,new_pwd:e.change.new_pwd},method:"POST",headers:{"X-Requested-With":"XMLHttpRequest"}}).then(function(r){e.loading=!1;var o=r.data;0==o.error?e.closeThisDialog():(e.error=!0,e.errorMsg=o.msg)}))}}]),app.controller("account",["$scope","$rootScope","ngDialog",function(e,r,o){r.patchName="",e.changePassword=function(){o.open({template:"template/changePasswordDialog.html",controller:"changePassword",closeByDocument:!1,closeByEscape:!1,showClose:!1})},r.userInfo&&0==r.userInfo.u_lastlogin&&e.changePassword()}])}();