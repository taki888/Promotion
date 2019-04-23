(function() {
    app.controller('addUser',['$scope', 'http', function (s, http) {
        s.user={
            u_name:"",
            u_true_name:"",
            u_type:"2"
        };
        if(s.ngDialogData){
            s.user.u_id=s.ngDialogData.u_id;
            s.user.u_true_name=s.ngDialogData.u_true_name;
            s.user.u_name=s.ngDialogData.u_name;
            s.user.u_type=s.ngDialogData.u_type;
        }
        s.check=function(){
            if(!s.user.u_name){
                s.error=true;
                s.errorMsg="请填写用户名！";
                return false;
            }
            return true
        };
        s.sub=function () {
            if(!s.loading && s.check()){
                s.loading=true;
                http.post("api/user_add.php",s.user,function (res) {
                    s.loading=false;
                    if(res.error==0){
                        s.closeThisDialog(res)
                    }else{
                        s.error=true;
                        s.errorMsg=res.msg;
                    }
                })
            }

        }
    }]);

    app.controller('userList', ['$scope', '$rootScope','http','ngDialog', function (s, rs, http,ngDialog) {
        rs.patchName = "userList";
        s.checkValue=[];
        s.getList=function () {
            http.get("api/users.php",{},function (res) {
                if(res.error==0){
                    s.userList=res.data;
                }
            });
        };
        s.delUser=function(user){
            if(user || s.checkValue){
                var msg="",postData={};
                if(user){
                    msg='您确定要删除用户“'+user.u_name+'”吗？';
                    postData.u_id=user.u_id;
                }else{
                    msg='您确定要删除这些用户吗？';
                    postData.u_id=s.checkValue.join(",");
                }
                ngDialog.open({
                    template:'<div class="confirm-dialog"> \
                <h2>'+msg+'</h2>\
                <div align="center">\
                    <button type="button" class="btn-red" ng-click="closeThisDialog(\'CONFIRM\')">确定</button>\
                    <button type="button" class="btn" ng-click="closeThisDialog()">取消</button>\
                </div></div>',
                    plain: true
                }).closePromise.then(function (data) {
                    if (data.value && data.value=='CONFIRM') {
                        http.post("api/user_status.php",postData,function (res) {
                            if(res.error==0){
                                s.getList();
                            }
                        })
                    }
                });
            }

        };
        s.editUser=function (user) {
            ngDialog.open({
                template:"template/userDialog.html",
                controller:"addUser",
                data:user
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.getList();
                }
            });
        };
        s.addUser=function () {
            ngDialog.open({
                template:"template/userDialog.html",
                controller:"addUser"
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.getList();
                }
            });
        };
        s.getList();
    }]);
})();
