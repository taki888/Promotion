(function() {
    app.controller("noticeDialog",['$scope','http',function (scope,http) {
        scope.notice={
            gg_content:""
        };
        if(scope.ngDialogData){
            scope.notice = scope.ngDialogData;
        }
        scope.sub=function () {
            if(!scope.loading){
                scope.loading=true;
                http.post("api/gg_add.php",scope.notice,function (res) {
                    scope.loading=false;
                    if(res.error==0){
                        scope.closeThisDialog(res);
                    }else{
                        scope.error=true;
                        scope.errorMsg=res.msg;
                    }
                })
            }
        }

    }]);
    app.controller('notice', ['$scope', '$rootScope', 'Table','ngDialog','http',function (s, rs,Table,ngDialog,http) {
        rs.patchName = "notice";
        s.table =Table.init({link: "api/gg_list.php",query:{pagesize:999}});
        s.table.getList();
        s.change={
            count:null,
            setting:false,
            setCount:function () {
                var me=this;
                if(!me.loading){
                    me.loading=true;
                    http.get("api/ok_num.php",{num:me.count},function (res) {
                        me.loading=false;
                        if(res.error==0){
                            me.setting=false;
                            if(res.num)
                                me.count=res.num;
                        }else{
                            me.error=true;
                            me.errorMsg=res.msg;
                        }
                    });
                }
            },
            delList:function () {
                ngDialog.open({
                    template:'<div class="confirm-dialog"> \
                <h2>您确定要清空全部成功办理记录吗？</h2>\
                <div align="center">\
                    <button type="button" class="btn-red" ng-click="closeThisDialog(\'CONFIRM\')">确定</button>\
                    <button type="button" class="btn" ng-click="closeThisDialog()">取消</button>\
                </div></div>',
                    plain: true
                }).closePromise.then(function (data) {
                    if (data.value && data.value=='CONFIRM') {
                        http.get("api/ok_num.php",{del:1},function (res) {

                        })
                    }
                });
            }
        };
        s.change.setCount();
        s.addNotice=function () {
            ngDialog.open({
                template:"template/noticeDialog.html",
                controller:"noticeDialog"
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.table.getList();
                }
            });
        };
        s.editNotice=function (notice) {
            ngDialog.open({
                template:"template/noticeDialog.html",
                controller:"noticeDialog",
                data:notice
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.table.getList();
                }
            });
        };
        s.delNotice=function (notice) {
            ngDialog.open({
                template:'<div class="confirm-dialog"> \
                <h2>您确定要删除此公告吗？</h2>\
                <div align="center">\
                    <button type="button" class="btn-red" ng-click="closeThisDialog(\'CONFIRM\')">确定</button>\
                    <button type="button" class="btn" ng-click="closeThisDialog()">取消</button>\
                </div></div>',
                plain: true
            }).closePromise.then(function (data) {
                if (data.value && data.value=='CONFIRM') {
                    http.post("api/gg_del.php",{gg_id:notice.gg_id},function (res) {
                        if(res.error==0){
                            s.table.getList();
                        }
                    })
                }
            });
        };
        s.changeNotice=function (list,n) {
            http.get("api/gg_status.php",{gg_id:list.gg_id,gg_status:n},function (res) {
                if(res.error==0){
                    list.gg_status=n;
                }
            });
            s.table.list.forEach(function(notice){
                if(notice.gg_status==n){
                    http.get("api/gg_status.php",{gg_id:notice.gg_id,gg_status:Math.abs(n-1)},function (res) {
                        if(res.error==0){
                            notice.gg_status=Math.abs(n-1);
                        }
                    });
                    return false
                }
            })
        }
    }]);
})();