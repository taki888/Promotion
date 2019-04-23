(function() {
    app.controller("memberCheckDialog",['$scope', 'http', function (scope, http) {
        scope.list = scope.ngDialogData.list;
        scope.act = scope.ngDialogData.act;
        scope.act.expend=[];
        if(scope.act.hd_zd_names)
            scope.act.hd_zd_names.split(",").forEach(function (name,i) {
                scope.act.expend.push({name:name,title:scope.act.hd_zd_pys.split(",")[i]})
            });
        http.get("api/msg.php",{hd_id:scope.list.hd_id,u_id:scope.list.u_id},function (res) {
            if(res.error==0){
                scope.list=angular.extend(scope.list,res.data[0]);
            }
        });
        scope.loading = false;
        scope.passCheck = function (n) {
            if (!scope.loading) {
                scope.loading=true;
                var data = {
                    u_name:scope.list.u_name,
                    hd_id:scope.list.hd_id,
                    u_id: scope.list.u_id,
                    check: n || scope.list.is_check,
                    msg: scope.list.msg
                };
                http.post("api/hd_check.php",data,function (res) {
                    scope.loading=false;
                    if (res.error == 0) {
                        scope.closeThisDialog(res);
                    } else {
                        scope.error = true;
                        scope.errorMsg = res.msg;
                    }
                })
            }
        }
    }]);
    app.controller('searchMember', ['$scope', '$rootScope', 'Table', '$stateParams', 'ngDialog','http', function (s, rs, Table, $stateParams, ngDialog,http) {
        rs.patchName = "searchMember";
        s.table = Table.init({link: "api/huiyuan.php"});
        s.table.getList();
        http.get("api/hd_list.php?page=1&pagesize=99",{},function (res) {
            if(res.error==0){
                s.huodong=res.data.list;
            }
        });
        if($stateParams && $stateParams.user){
            s.table.query.user=$stateParams.user;
            s.table.getList();
        }
        $(".search-input input").on("keydown",function (e) {
            if(e.keyCode == 13){
                s.table.getList();
            }
        });
        s.check = function (list) {
            var act=s.huodong.filter(function (i) {
                return list.hd_id==i.hd_id;
            })[0];
            s.dialog = ngDialog.open({
                template: "template/checkDialog.html",
                data: {list:list,act:act},
                controller: 'memberCheckDialog'
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.table.getList();
                }
            });
        };

    }]);
})()