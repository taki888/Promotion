(function() {
    app.controller("actExpendDialog",['$scope',function (s) {
        if(s.ngDialogData)
            s.expend=s.ngDialogData;
        else
            s.expend={
                name:"",
                type:"text",
                val:""
            };
        s.sub=function(){
            if(!s.expend.name){
                s.error=true;
                s.errorMsg="请填写名称";
            }else if(s.expend.type=="select" && !s.expend.val){
                s.error=true;
                s.errorMsg="请添加选项";
            }else{
                s.closeThisDialog(s.expend);

            }

        };
        s.addVal={
            valList:[],
            val:""
        };
        if(s.expend.val)
            s.addVal.valList=s.expend.val.split(",");
        s.addVal.add=function () {
            s.error=false;
            if(this.val)
                this.valList.push(this.val);
            s.expend.val=this.valList.join(",");
            this.val="";
        };
        s.addVal.del=function (n) {
            s.addVal.valList.splice(s.addVal.valList.indexOf(n), 1);
        }
    }]);

    app.controller("actEdit", ['$scope','$rootScope', 'http','ngDialog','$state','$stateParams','$filter', function (s,rs,http,ngDialog,$state,$stateParams,$filter) {
        rs.patchName = "act";
        s.act={
            hd_name:"",
            hd_time1:"",
            hd_time2:"",
            hd_index:1000,
            hd_time_limit:720,
            hd_logo:"",
            hd_status:1,
            hd_expend:[]
        };
        var actIntro,actRules;
        $(function () {
            var option={
                cssPath : 'js/kindeditor/plugins/code/prettify.css',
                uploadJson : 'js/kindeditor/php/upload_json.php',
                fileManagerJson : 'js/kindeditor/php/file_manager_json.php',
                allowFileManager : true
            };

            var p1 = new Promise(function(resolve, reject){
                var timer = setInterval(function () {
                    if($('textarea[name="actIntro"]').length){
                        actIntro = KindEditor.create('textarea[name="actIntro"]', option);
                        clearInterval(timer)
                        resolve(null);
                    }
                }, 100);
            });
            var p2 = new Promise(function(resolve, reject){
              var timer2 = setInterval(function () {
                  if($('textarea[name="actRules"]').length){
                      actRules = KindEditor.create('textarea[name="actRules"]', option);
                      clearInterval(timer2)
                      resolve(null);
                  }
              }, 100);
            });
            Promise.all([p1, p2]).then(function(res){
              getData()
            })

        });

        function getData() {
          if($stateParams.id){
              s.act.hd_id=$stateParams.id;
              http.get("api/hd_detail.php",{id:s.act.hd_id},function (res) {
                  if(res.error==0){
                      s.act.hd_name=res.data.hd_name;
                      s.act.hd_time1=(res.data.hd_time1==0)?"":$filter('date')(res.data.hd_time1*1000, 'yyyy-MM-dd HH:mm:ss','UTC-4');
                      s.act.hd_time2=(res.data.hd_time2==1800000000)?"":$filter('date')(res.data.hd_time2*1000, 'yyyy-MM-dd HH:mm:ss','UTC-4');
                      s.act.hd_logo=res.data.hd_logo;
                      s.act.hd_status=res.data.hd_status;
                      s.act.hd_index=res.data.hd_index;
                      s.act.hd_time_limit=res.data.hd_time_limit;
                      if(res.data.hd_zd_names){
                          var hd_zd_names=res.data.hd_zd_names.split(",");
                          var hd_zd_types=res.data.hd_zd_types.split(",");
                          var hd_zd_vals=res.data.hd_zd_vals;
                          for(var i=0;i<hd_zd_names.length;i++){
                              s.act.hd_expend.push({name:hd_zd_names[i],type:hd_zd_types[i],val:hd_zd_vals[i]})
                          }
                      }
                      actIntro.html(res.data.hd_intro);
                      actRules.html(res.data.hd_rules);

                      if(s.act.hd_logo)
                          s.uploadImgPreview['background-image']="url("+s.act.hd_logo+")";
                  }
              })

          }
        }

        s.actExpend=function (ex) {
            s.dialog = ngDialog.open({
                template: "template/actExpendDialog.html",
                data: ex,
                controller: 'actExpendDialog',
                closeByDocument:false,
                closeByEscape:false,
                showClose:false
            }).closePromise.then(function (data) {
                if (data.value) {
                    if(!ex)
                        s.act.hd_expend.push(data.value);
                }
            });
        };
        s.actDelExp=function (n) {
            s.act.hd_expend.splice(s.act.hd_expend.indexOf(n), 1);
        };

        s.uploadImgPreview={
            'background-image':'url(images/upload-img.jpg)'
        };
        $("#file").on("change",function () {
            lrz(this.files[0],{quality:1})
                .then(function (rst) {
                    s.act.img64=rst.base64;
                    s.uploadImgPreview['background-image']="url("+rst.base64+")";
                    s.$apply();
                })
                .catch(function (err) {
                    alert("图片处理错误，请上传正确的图片")
                })
        });

        s.subAct=function(){
            if(!s.act.hd_name){
                s.error=true;
                s.errorMsg="请填写活动名称";
            }else if(!s.loading){
                s.loading=true;
                var data=angular.copy(s.act);
                data.hd_zds=angular.toJson(s.act.hd_expend);
                data.hd_intro=actIntro.html();
                data.hd_rules=actRules.html();

                data.hd_time1=$filter('date')(new Date(+new Date(data.hd_time1)+3600*12*1000), "yyyy-MM-dd HH:mm:ss");
                data.hd_time2=$filter('date')(new Date(+new Date(data.hd_time2)+3600*12*1000), "yyyy-MM-dd HH:mm:ss");
                http.post("api/hd_add.php",data,function (res) {
                    s.loading=false;
                    if(res.error==0){
                        $state.go("act");
                    }else{
                        s.error=true;
                        s.errorMsg=res.msg;
                    }
                })
            }
        }
    }])
})()
