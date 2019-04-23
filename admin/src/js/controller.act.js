(function() {
    app.controller('act', ['$scope', '$rootScope', 'Table', '$state', 'ngDialog','http', function (s, rs, Table, $state, ngDialog,http) {
        rs.patchName = "act";
        s.updateTime={
            value:"",
            change:false
        };
        s.subUpdateTime=function () {
            var time=s.updateTime.value.replace(/:/g,"");
            http.get("api/time.php",{update_time:time},function (res) {
                if(res.error==0){
                    s.updateTime.change=false;
                }
            })
        };
        s.table = Table.init({link: "api/hd_list.php",callback:function (res) {
            var t=res.data.time.split("");
            s.updateTime.value=t[0]+t[1]+':'+t[2]+t[3]+':'+t[4]+t[5];
        }});
        s.table.getList();
        $(".search-input input").on("keydown",function (e) {
            if(e.keyCode == 13){
                s.table.getList();
            }
        });
        s.delAct=function (act) {
            ngDialog.open({
                template:'<div class="confirm-dialog"> \
                <h2>您确定要删除公司“'+act.hd_name+'”吗？</h2>\
                <p>活动删除后，前台页面和这里都不会再显示此公司！</p>\
                <div align="center">\
                    <button type="button" class="btn-red" ng-click="closeThisDialog(\'CONFIRM\')">确定</button>\
                    <button type="button" class="btn" ng-click="closeThisDialog()">取消</button>\
                </div></div>',
                plain: true
            }).closePromise.then(function (data) {
                if (data.value && data.value=='CONFIRM') {
                    http.get("api/hd_del.php",{id:act.hd_id},function (res) {
                        if(res.error==0){
                            s.table.getList();
                        }
                    })
                }
            });
        };

        s.editAct=function (act) {
            ngDialog.open({
                template:"template/flyerDialog.html",
                controller:"addUser",
                data:act
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.getList();
                }
            });
        };

        s.changeAct=function (act,n) {
            http.get("api/hd_status.php",{id:act.hd_id,status:n},function (res) {
                if(res.error==0){
                    act.hd_status=n;
                }
            })
        };
        s.changeLimit=function (list) {
            list.hd_time_update=String(list.hd_time_update);
            list.changeLimit=true;
        };
        s.subChangeLimit=function (list) {
            http.get("api/time_update.php",{hd_id:list.hd_id,hd_time_update:list.hd_time_update},function (res) {
                if(res.error==0){
                    list.changeLimit=false;
                }
            })
        }

    }]);
})()