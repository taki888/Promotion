var nav = angular.module("nav", ["ui.router", "oc.lazyLoad"]),
version = +new Date;
nav.constant("myModulesConfig", [{
    name: "ngDialog",
    files: ["//cdn.bootcss.com/ng-dialog/0.6.4/js/ngDialog.min.js", "//cdn.bootcss.com/ng-dialog/0.6.4/css/ngDialog.min.css", "//cdn.bootcss.com/ng-dialog/0.6.4/css/ngDialog-theme-default.min.css", "css/dialog.css"]
},
{
    name: "datePicker",
    files: ["js/My97DatePicker/WdatePicker.js"]
},
{
    name: "KindEditor",
    files:[
            "js/kindeditor/themes/default/default.css",
            "js/kindeditor/plugins/code/prettify.css",
            "js/kindeditor/lang/zh-CN.js",
            "js/kindeditor/plugins/code/prettify.js"
        ]
},{
    name: "localResizeIMG",
    files: ["js/localResizeIMG/lrz.all.bundle.js"]
},
{
    name: "table",
    files: ["js/factory.table.js?v=" + version, "css/table.css?v=" + version]
},
{
    name: "statistics",
    files: ["js/controller.statistics.js?v=" + version, "css/statistics.css?v=" + version]
},
{
    name: "actList",
    files: ["js/controller.actList.js?v=" + version, "css/actList.css?v=" + version]
},
{
    name: "index",
    files: ["js/controller.index.js?v=" + version, "css/index.css?v=" + version]
},
{
    name: "act",
    files: ["js/controller.act.js?v=" + version, "css/act.css?v=" + version]
},
{
  name:'actEdit',
  files:[
      "src/js/controller.actEdit.js?v="+version,
      "css/actEdit.css?v="+version
  ]
},
{
    name: "uploadImg",
    files: [
        "js/controller.uploadImg.js?v="+version,
        //"src/js/controller.uploadImg.js",
        "css/uploadImg.css"
    ]
},
{
    name: "userList",
    files: ["js/controller.userList.js?v=" + version, "css/userList.css"]
},
{
    name: "account",
    files: ["js/controller.account.js?v=" + version, "css/account.css"]
},
{
    name: "domain",
    files: ["js/controller.domain.js?v=" + version, "css/domain.css"]
},
{
    name: "notice",
    files: ["js/controller.notice.js?v=" + version, "css/notice.css"]
},
{
    name: "searchMember",
    files: ["js/controller.searchMember.js?v=" + version, "css/searchMember.css"]
}]),
nav.config(["$stateProvider", "$urlRouterProvider", "$ocLazyLoadProvider", "myModulesConfig",
function(e, t, s, a) {
    function r() {
        var e = arguments;
        return {
            deps: ["$ocLazyLoad", "$q",
            function(t, s) {
                function a(e) {
                    return r.then(function() {
                        return t.load(e)
                    })
                }
                if (1 == e.length) return t.load(e[0]);
                for (var r = s.when(1), i = 0, l = e.length; i < l; i++) r = a(e[i]);
                return r
            }]
        }
    }
    s.config({
        debug: !1,
        events: !1,
        modules: a
    }),
    t.when("", "/act"),
    e.state("index", {
        url: "/index",
        templateUrl: "template/index.html",
        resolve: r("index"),
        controller: "index"
    }).state("statistics", {
        url: "/statistics",
        templateUrl: "template/statistics.html",
        resolve: r("statistics"),
        controller: "statistics"
    }).state("actList", {
        url: "/actList/:id",
        templateUrl: "template/actList.html",
        resolve: r("datePicker", "table", "ngDialog", "actList"),
        controller: "actList"
    }).state("act", {
        url: "/act",
        templateUrl: "template/act.html",
        resolve: r("datePicker", "table", "ngDialog", "act", "uploadImg"),
        controller: "act"
    }).state("actEdit", {
        url: "/actEdit/:id",
        templateUrl: "template/actEdit.html",
        resolve: r("ngDialog", "datePicker", "KindEditor", "localResizeIMG", "actEdit"),
        controller: "actEdit"
    }).state("userList", {
        url: "/userList",
        templateUrl: "template/userList.html",
        resolve: r("ngDialog", "table", "userList"),
        controller: "userList"
    }).state("account", {
        url: "/account",
        templateUrl: "template/account.html",
        resolve: r("ngDialog", "table", "account"),
        controller: "account"
    }).state("notice", {
        url: "/notice",
        templateUrl: "template/notice.html",
        resolve: r("ngDialog", "table", "notice"),
        controller: "notice"
    }).state("searchMember", {
        url: "/searchMember",
        templateUrl: "template/searchMember.html",
        resolve: r("ngDialog", "table", "searchMember"),
        controller: "searchMember"
    }).state("searchMemberDetail", {
        url: "/searchMember/:user",
        templateUrl: "template/searchMember.html",
        resolve: r("ngDialog", "table", "searchMember"),
        controller: "searchMember"
    })
}]).directive("nav",
function() {
    return {
        restrict: "AE",
        template: '<div class="nav"> <ul><li><a ng-if="userInfo.u_type==1 || userInfo.u_type==2" ng-class="{true: \'nav-active\'}[patchName==\'act\']" ui-sref="act">公司详情介绍</a></li></ul></div>',
        link: function(e, t, s, a) {
            $(t).find("a").click(function() {
                $(this).attr("href") && ($(".nav-active").removeClass("nav-active"), $(this).addClass("nav-active"))
            })
        }
    }
});
