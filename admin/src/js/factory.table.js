(function() {
    app.factory('Table', ['$http', function ($http) {
        function Table(){
            this.list=[];
            this.query={
                page: 1,
                pagesize: 10
            }
        }
        Table.prototype.init=function (option) {
            this.error=false;
            this.ready=false;
            this.list=[];
            this.query={
                page: 1,
                pagesize: 10
            };
            this.link = option.link;
            this.callback=option.callback || function () {};
            if (option.query)
                this.query = $.extend(this.query, option.query);
            return this;
        };
        Table.prototype.getList=function (page) {
            var me=this;
            page = page || me.query.page;
            me.query.page = page;
            $http({
                url: me.link,
                params: me.query,
                method: 'GET',
                headers: {"X-Requested-With": "XMLHttpRequest"}
            }).then(function (bk) {
                var res = bk.data;
                if (res.error == 0) {
                    me.list = res.data.list;
                    me.pageCount = res.data.page_count;
                    me.num=res.data.num;
                }
                else {
                    me.error = true;
                    me.errorMsg = res.msg;
                }
                me.callback(res);
            }).catch(function (err) {
                me.error = true;
                me.errorMsg = err.status;
            }).finally(function () {
                me.ready = true;
            });
        };
        return new Table();
    }]);
    app.directive("tablePage", function () {
        return {
            restrict: "AE",
            template: '<div class="table-page"></div>',
            link: function (scope, element, attrs, ctrl) {
                var tablep;
                scope.$watch(attrs.tablePage, function (table) {
                    tablep=table;
                    if (table && table.pageCount && table.pageCount > 1) {
                        $(element).find(".table-page").html('<span class="pre">上一页</span>\
                        <span class="next">下一页</span>');
                        if (table.query.page == 1) {
                            $(element).find(".pre").addClass("disabled")
                        } else if (table.query.page == table.pageCount) {
                            $(element).find(".next").addClass("disabled")
                        }
                        var html = "";
                        if (table.pageCount > 10) {
                            if (table.query.page < 6) {
                                for (var i = 1; i <= table.query.page + 2; i++) {
                                    if (i == table.query.page)
                                        html += '<span class="page-to now">' + i + '</span>';
                                    else
                                        html += '<span class="page-to">' + i + '</span>';
                                }

                                html += '...<span class="page-to">' + table.pageCount + '</span>'
                            }
                            else if (table.query.page >= 6 && table.query.page < table.pageCount - 2) {
                                html += '<span class="page-to">1</span>...';
                                for (var i = table.query.page - 2; i <= table.query.page + 2; i++) {
                                    if (i == table.query.page)
                                        html += '<span class="page-to now">' + i + '</span>';
                                    else
                                        html += '<span class="page-to">' + i + '</span>';
                                }
                                html += '...<span class="page-to">' + table.pageCount + '</span>'
                            } else {
                                html += '<span class="page-to">1</span>...';
                                for (var i = table.query.page - 2; i <= table.pageCount; i++) {
                                    if (i == table.query.page)
                                        html += '<span class="page-to now">' + i + '</span>';
                                    else
                                        html += '<span class="page-to">' + i + '</span>';
                                }
                            }
                        } else {
                            for (var i = 1; i <= table.pageCount; i++) {
                                if (i == table.query.page)
                                    html += '<span class="page-to now">' + i + '</span>';
                                else
                                    html += '<span class="page-to">' + i + '</span>';
                            }
                        }
                        $(element).find(".pre").after(html);
                    }else{
                        $(element).find(".table-page").html("");
                    }
                }, true);

                $(element).on('click', '.page-to', function () {
                    if (!$(this).hasClass("now")) {
                        tablep.getList(parseInt($(this).text()));
                    }
                });
                $(element).on('click', '.pre', function () {
                    if (!$(this).hasClass("disabled")) {
                        tablep.getList(tablep.query.page - 1);
                    }
                });
                $(element).on('click', '.next', function () {
                    if (!$(this).hasClass("disabled")) {
                        tablep.getList(tablep.query.page + 1);
                    }
                });
            }
        }
    })
})()