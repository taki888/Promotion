var nav=angular.module('nav',['ui.router','oc.lazyLoad']);
var version=+new Date();
nav.constant('myModulesConfig', [
    {
        name: 'ngDialog',
        files: [
            "//cdn.bootcss.com/ng-dialog/0.6.4/js/ngDialog.min.js",
            "//cdn.bootcss.com/ng-dialog/0.6.4/css/ngDialog.min.css",
            "//cdn.bootcss.com/ng-dialog/0.6.4/css/ngDialog-theme-default.min.css",
            "css/dialog.css"
        ]
    },{
        name:'datePicker',
        files:[
            "js/My97DatePicker/WdatePicker.js"
        ]
    },{
        name:'simditor',
        files:[
            "js/simditor/styles/font-awesome.css",
            "js/simditor/styles/simditor.css",
            "js/simditor/scripts/simditor-all.min.js"
        ]
    },{
        name:'localResizeIMG',
        files:[
            "js/localResizeIMG/lrz.all.bundle.js"
        ]
    },
    {
        name: "uploadImg",
        files: [
            "js/controller.uploadImg.js?v="+version,
            //"src/js/controller.uploadImg.js",
            "css/uploadImg.css"
        ]
    },{
        name:'table',
        files:[
            "js/factory.table.js?v="+version,
            "css/table.css?v="+version
        ]
    },{
        name:'statistics',
        files:[
            "js/controller.statistics.js?v="+version,
            "css/statistics.css?v="+version
        ]
    },{
        name:'actList',
        files:[
            "js/controller.actList.js?v="+version,
            "css/actList.css?v="+version
        ]
    },{
        name:'index',
        files:[
            "js/controller.index.js?v="+version,
            "css/index.css?v="+version
        ]
    },{
        name:'act',
        files:[
            "js/controller.act.js?v="+version,
            "css/act.css?v="+version
        ]
    },{
        name:'actEdit',
        files:[
            "js/controller.actEdit.js?v="+version,
            "css/actEdit.css?v="+version
        ]
    },{
        name:"userList",
        files:[
            "js/controller.userList.js?v="+version,
            "css/userList.css"
        ]
    },{
        name:"account",
        files:[
            "js/controller.account.js?v="+version,
            "css/account.css"
        ]
    },{
        name:"domain",
        files:[
            "js/controller.domain.js?v="+version,
            "css/domain.css"
        ]
    },{
        name:"notice",
        files:[
            "js/controller.notice.js?v="+version,
            "css/notice.css"
        ]
    },{
        name:"searchMember",
        files:[
            "js/controller.searchMember.js?v="+version,
            "css/searchMember.css"
        ]
    }
]);
nav.config(['$stateProvider','$urlRouterProvider','$ocLazyLoadProvider','myModulesConfig', function($stateProvider, $urlRouterProvider,$ocLazyLoadProvider,myModulesConfig) {
    $ocLazyLoadProvider.config({
        debug:false,
        events:false,
        modules:myModulesConfig
    });
    $urlRouterProvider.when("", "/act");
    $stateProvider
        .state("index", {
            url: "/index",
            templateUrl: "template/index.html",
            resolve:loadSequence("index"),
            controller:"index"
        })
        .state("statistics", {
            url: "/statistics",
            templateUrl: "template/statistics.html",
            resolve:loadSequence("statistics"),
            controller:"statistics"
        })
        .state("actList", {
            url:"/actList/:id",
            templateUrl: "template/actList.html",
            resolve:loadSequence("datePicker","table","ngDialog","actList"),
            controller:"actList"
        })
        .state("act", {
            url:"/act",
            templateUrl: "template/act.html",
            resolve:loadSequence("datePicker","table","ngDialog","act", "uploadImg"),
            controller:"act"
        })
        .state("actEdit", {
            url:"/actEdit/:id",
            templateUrl: "template/actEdit.html",
            resolve:loadSequence("ngDialog","datePicker","simditor","localResizeIMG","actEdit"),
            controller:"actEdit"
        })
        .state("userList", {
            url:"/userList",
            templateUrl: "template/userList.html",
            resolve:loadSequence("ngDialog","table","userList"),
            controller:"userList"
        })
        .state("account",{
            url:"/account",
            templateUrl: "template/account.html",
            resolve:loadSequence("ngDialog","table","account"),
            controller:"account"
        })
        // .state("domain",{
        //     url:"/domain",
        //     templateUrl: "template/domain.html",
        //     resolve:loadSequence("ngDialog","table","domain"),
        //     controller:"domain"
        // })
        .state("notice",{
            url:"/notice",
            templateUrl: "template/notice.html",
            resolve:loadSequence("ngDialog","table","notice"),
            controller:"notice"
        })
        .state("searchMember",{
            url:"/searchMember",
            templateUrl: "template/searchMember.html",
            resolve:loadSequence("ngDialog","table","searchMember"),
            controller:"searchMember"
        })
        .state("searchMemberDetail",{
            url:"/searchMember/:user",
            templateUrl: "template/searchMember.html",
            resolve:loadSequence("ngDialog","table","searchMember"),
            controller:"searchMember"
        });
    function loadSequence() {
        var _args = arguments;
        return {
            deps: ['$ocLazyLoad', '$q',
                function ($ocLL, $q) {
                    if(_args.length==1)
                        return $ocLL.load(_args[0]);
                    var promise = $q.when(1);
                    for (var i = 0, len = _args.length; i < len; i++) {
                        promise = promiseThen(_args[i])
                    }
                    return promise;
                    function promiseThen(name) {
                        return promise.then(function () {
                            return $ocLL.load(name);
                        });
                    }
                }]
        };
    }
}])
    .directive("nav",function() {
        return {
            restrict: "AE",
            template :'<div class="nav">\
                    <ul>'+
            //<li><a ng-class="{true: \'nav-active\'}[patchName==\'index\']" ui-sref="index">欢迎登陆</a></li>\
            //<li><a ng-class="{true: \'nav-active\'}[patchName==\'statistics\']" ui-sref="statistics">数据统计</a></li>\
            '<li><a ng-if="userInfo.u_type==1 || userInfo.u_type==2" ng-class="{true: \'nav-active\'}[patchName==\'act\']" ui-sref="act">优惠活动列表</a>\
            <li><a ng-if="userInfo.u_type==1 || userInfo.u_type==2" ng-class="{true: \'nav-active\'}[patchName==\'actList\']" ui-sref="actList">优惠活动审核</a>\
            <li><a ng-if="userInfo.u_type==1 || userInfo.u_type==2" ng-class="{true: \'nav-active\'}[patchName==\'searchMember\']" ui-sref="searchMember">会员申请查询</a>\
            <li><a ng-if="userInfo.u_type==1 || userInfo.u_type==2" ng-class="{true: \'nav-active\'}[patchName==\'notice\']" ui-sref="notice">优惠公告管理</a>\
            <li><a ng-if="userInfo.u_type==1" ng-class="{true: \'nav-active\'}[patchName==\'userList\']" ui-sref="userList">用户管理</a>\
            </li>\
        </ul>\
        </div>',
            link: function (scope, element, attrs, ctrl) {
                $(element).find("a").click(function(){
                    if($(this).attr("href")){
                        $(".nav-active").removeClass("nav-active");
                        $(this).addClass("nav-active");
                    }
                })
            }
        }
    });