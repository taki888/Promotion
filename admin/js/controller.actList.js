(function() {
    app.controller("actListCheckDialog",['$scope', 'http', function (scope, http) {
        scope.list = angular.copy(scope.ngDialogData.list);
        scope.checkList = angular.copy(scope.ngDialogData.checkList);
        scope.act=scope.ngDialogData.act;
        scope.loading = false;
        scope.passCheck = function (n) {
            if (!scope.loading) {
                scope.loading=true;
                if(scope.list){
                	var data = {
	                    u_name:scope.list.u_name,
	                    hd_id:scope.act.hd_id,
	                    u_id: scope.list.u_id,
	                    check: n || scope.list.is_check,
	                    msg: scope.list.msg
	                };
                }else if(scope.checkList){
                	var idList = [];
                	scope.checkList.forEach(function(item){
                		idList.push(item.u_id)
                	})
                	var data = {
	                    hd_id:scope.act.hd_id,
	                    u_id: idList,
	                    check: n || scope.checkList[0].is_check,
	                    msg: scope.checkList[0].msg
	                };
                }
                
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
    app.controller('actList', ['$scope', '$rootScope', 'Table', 'http', 'ngDialog','$stateParams','$filter', function (s, rs, Table, http, ngDialog,$stateParams,filter) {
        var audio=document.createElement("AUDIO");
        var source1=document.createElement("SOURCE");
        source1.src="mp3/tip.ogg";
        var source2=document.createElement("SOURCE");
        source2.src="mp3/tip.mp3";
        audio.appendChild(source1);
        audio.appendChild(source2);
        s.actList=[];
        http.get("api/hds_info.php",{},function(res){
            if(res.error==0){
                s.actList=res.data.list;
                var index=0;
                s.actList.forEach(function(actList,k){
                    if($stateParams.id==actList.hd_id){
                        index=k
                    }
                    actList.expend=[];
                    if(actList.hd_zd_names)
                        actList.hd_zd_names.split(",").forEach(function (name,i) {
                            actList.expend.push({name:name,title:actList.hd_zd_pys.split(",")[i]})
                        })
                });
                s.searchAct(s.actList[index])
            }
        });
        s.searchAct=function (act) {
            s.activeAct=act;
            act.hasNew=false;
            s.actList.forEach(function (list) {
                list.active=false;
            });
            act.active=true;
            s.table = Table.init({link: "api/huodong.php",query:{id:act.hd_id}});
            s.table.getList();
        };
        rs.patchName = "actList";

        s.begin_time="";
        s.over_time="";
        s.searchList=function () {
            if(s.begin_time)
                s.table.query.begin_time=filter('date')(new Date(+new Date(s.begin_time)+3600*12*1000), "yyyy-MM-dd HH:mm:ss");
            else
                s.table.query.begin_time=null;
            if(s.over_time)
                s.table.query.over_time=filter('date')(new Date(+new Date(s.over_time)+3600*12*1000), "yyyy-MM-dd HH:mm:ss");
            else
                s.table.query.over_time=null;
            s.table.getList(1);
        };

        s.export=function () {
            if(s.begin_time)
                s.table.query.begin_time=filter('date')(new Date(+new Date(s.begin_time)+3600*12*1000), "yyyy-MM-dd HH:mm:ss");
            else
                s.table.query.begin_time=null;
            if(s.over_time)
                s.table.query.over_time=filter('date')(new Date(+new Date(s.over_time)+3600*12*1000), "yyyy-MM-dd HH:mm:ss");
            else
                s.table.query.over_time=null;
            if(!s.table.query.uname){
                s.table.query.uname=null;
            }
            if(!s.table.query.check){
                s.table.query.check=null;
            }
            window.open("api/excel.php?begin_time="+s.table.query.begin_time+"&check="+s.table.query.check+"&id="+s.table.query.id+
                "&over_time="+s.table.query.over_time+"&uname="+s.table.query.uname);
        };

        $(".search-input input").on("keydown",function (e) {
            if(e.keyCode == 13){
                s.searchList();
            }
        });
        s.check = function (list) {
            s.dialog = ngDialog.open({
                template: "template/checkDialog.html",
                data: {list:list,act:s.activeAct},
                controller: 'actListCheckDialog'
            }).closePromise.then(function (data) {
                if (data.value && data.value.error == 0) {
                    s.table.getList();
                }
            });
        };
        s.checkIdList = []
        s.checkList = function checkList(item, e){
        	if(e.currentTarget.checked){
        		s.checkIdList.push(item)
        	}else{
        		s.checkIdList.forEach(function(n, idx){
        			if(n.u_id === item.u_id){
        				s.checkIdList.splice(idx, 1)
        			}
        		})
        	}
        }
        s.status = false;
        s.checkAll = function checkAll(e){
            s.status = !s.status;
            s.checkIdList = [];
            // console.log($("input[type='checkbox']").length);
            for(var i = 0; i < s.table.list.length+1; i++){
                $("input[type='checkbox']").eq(i).prop("checked", s.status);
            }
            if(s.status){
                s.checkIdList = Object.assign([], s.table.list);
            }else{
                s.checkIdList = [];
            }
        }
        s.handleCheckList = function(){
        	if(!s.checkIdList.length){
        		alert("请先选择批量操作的用户")
        	}else{
        		s.dialog = ngDialog.open({
	                template: "template/checkDialog.html",
	                data: {checkList:s.checkIdList,act:s.activeAct},
	                controller: 'actListCheckDialog'
	            }).closePromise.then(function (data) {
	                if (data.value && data.value.error == 0) {
	                    s.table.getList();
	                    s.checkIdList = []
	                }
	            });
        	}
        }
        s.refreshCheck = function(){
            s.checkIdList = [];
            for(var i = 0; i < $("input[type='checkbox']").length; i++){
                $("input[type='checkbox']").eq(i).prop("checked", false)
            }
        }

        s.getNew=function () {
            http.get("api/apply_num.php",{},function (res) {
                if(res){
                    s.actList.forEach(function(list){
                        if(res[list.hd_id]){
                            if(parseInt(res[list.hd_id])>parseInt(list.new) || (!list.new && res[list.hd_id]!=0))
                                s.tipNew(list);
                            list.new=parseInt(res[list.hd_id]);
                        }
                    })
                }
            })
        };
        s.tipNew=function (list) {
            // audio.play();
            s.actList.splice(s.actList.indexOf(list),1);
            s.actList.unshift(list);
            list.hasNew=true;
            if(list.hd_id==s.activeAct.hd_id){
                s.table.getList();
                setTimeout(function () {
                    list.hasNew=false;
                    s.$apply()
                },2200)

            }

        };
        s.getNew();
        s.timer=setInterval(function () {
            s.getNew();
            s.refreshCheck()
        },60 * 1000);
        rs.$on('$stateChangeStart',function () {
            clearInterval(s.timer);
        })

    }]);
})()