(function() {
    app.controller('addDomain',['$scope','http',function (s, http) {
        s.domain={
            ym_name:'',
            ym_true_ip:'',
            ym_ip:''
        };
        s.getIp=function () {
            if(/^(?=^.{3,255}$)[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+$/.test(s.domain.ym_name)){
                http.get("api/getip.php",{ym:s.domain.ym_name},function (res) {
                   s.domain.ym_true_ip=res.msg;
                });
            }else{
                s.error=true;
                s.errorMsg="请输入正确的域名"
            }
        };
        s.check=function () {
            if(!/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/.test(s.domain.ym_ip)){
                s.error=true;
                s.errorMsg="请输入正确的ip地址";
                return false
            }
            if(!/^(?=^.{3,255}$)[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+$/.test(s.domain.ym_name)){
                s.error=true;
                s.errorMsg="请输入正确的域名";
                return false
            }
            return true
        };
        s.sub=function () {
            if(!s.loading && s.check()){
                s.loading=true;
                http.post("api/ym_add.php",{ym_name:s.domain.ym_name,ym_ip:s.domain.ym_ip},function(res){
                    if(res.error==0){
                        s.closeThisDialog(res);
                    }else{
                        s.error=true;
                        s.errorMsg=res.msg;
                    }
                })
            }
            
        }

    }]);
    app.controller('domain', ['$scope', '$rootScope', 'Table', 'http', 'ngDialog', function (s, rs, Table, http, ngDialog) {
        rs.patchName="domain";
        s.getIp=function () {
            s.table.list.forEach(function (list) {
                http.get("api/getip.php",{ym:list.ym_name},function (res) {
                    list.ym_true_ip=res.msg;
                });

            });
        };
        s.table=Table.init({link: "api/ym_list.php",callback:s.getIp});
        s.table.getList();
        s.checkValue=[];
        s.addDomain=function () {
            ngDialog.open({
                template:"template/addDomainDialog.html",
                controller:"addDomain"
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.table.getList();
                }
            });
        };
        s.changeDomain=function (domain,n) {
            http.get("api/ym_status.php",{ym_id:domain.ym_id,ym_status:n},function (res) {
                if(res.error==0){
                    domain.ym_status=n;
                }
            })
        };
        s.delDomain=function (domain) {
            if(domain || s.checkValue) {
                var msg = "", postData = {};
                if (domain) {
                    msg = '您确定要删除域名“' + domain.ym_name + '”吗？';
                    postData.ym_id = domain.ym_id;
                } else {
                    msg = '您确定要删除这些域名吗？';
                    postData.ym_id = s.checkValue.join(",");
                }
                ngDialog.open({
                    template: '<div class="confirm-dialog"> \
                <h2>'+msg+'</h2>\
                <div align="center">\
                    <button type="button" class="btn-red" ng-click="closeThisDialog(\'CONFIRM\')">确定</button>\
                    <button type="button" class="btn" ng-click="closeThisDialog()">取消</button>\
                </div></div>',
                    plain: true
                }).closePromise.then(function (data) {
                    if (data.value && data.value == 'CONFIRM') {
                        http.post("api/ym_del.php", postData, function (res) {
                            if (res.error == 0) {
                                s.table.getList();
                            }
                        })
                    }
                });
            }
        }
    }]);
})();