CJMA.Dashboard = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        form = di.get('form'),
        defaultConfig = {
            route: 'dashboard',
            tableSelector: ".orders",
            viewOrderRoute: "/order/{id}/show",
            editOrderRoute: "/order/{id}/edit"
        },
        config = {},
        datatable = null;

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        if (app.routeMatches(config.route)) {
            initDataTable();
            initTableSelect();
        }
    };

    function initDataTable() {
        var $table = $(config.tableSelector);
        datatable = $table.DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            responsive: true,
            ajax: $table.data('source'),
            columns: [
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'orderTimeLeft',
                    name: 'deadline'
                },
                {
                    data: 'report_type.name',
                    name: 'report_type.name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'customerStatus',
                    name: 'status'
                },
                {
                    data: 'paid',
                    name: 'paid',
                    orderable: false,
                    searchable: false
                },
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        datatable.on('draw', bindTableActionHandlers);
    }

    function initTableSelect() {
        $('select[name="DataTables_Table_0_length"]').select2({
            minimumResultsForSearch: -1
        });
    }

    function bindTableActionHandlers() {
        $(config.tableSelector + ' [data-role=action]').click(onRowActionClick);
    }

    function onRowActionClick() {
        var action = $(this).data('action'),
            data = datatable.row($(this).parents('tr').get(0)).data();
        if (typeof handlers[action] == 'function') {
            handlers[action].call(this, data);
        }
    }

    var handlers = {
        view: function (row) {
            window.location = config.viewOrderRoute.replace("{id}", row.id);
        },
        edit: function (row) {
            window.location = config.editOrderRoute.replace("{id}", row.id) + 'order/edit/' + row.id;
        },
        comment: function (row) {
            window.location = config.viewOrderRoute.replace("{id}", row.id) + "#comments";
        },
        download: function (row) {
            alert('Download here');
            //Using approach with iframe!!!! http://stackoverflow.com/a/3749395
            function makeHttpObject() {
              try {return new XMLHttpRequest();}
              catch (error) {}
              try {return new ActiveXObject("Msxml2.XMLHTTP");}
              catch (error) {}
              try {return new ActiveXObject("Microsoft.XMLHTTP");}
              catch (error) {}

              throw new Error("Could not create HTTP request object.");
            }
            var request = makeHttpObject();
            request.open("GET", "/download/"+row.id, false);
            request.send(null);
            if (JSON.parse(request.responseText).success) {
                console.log(JSON.parse(request.responseText).message);
                window.location.href = JSON.parse(request.responseText).message;
            } else {
                alert(JSON.parse(request.responseText).message);
            };
        },
        cancel: function (row) {
            alert('Cancel here');
        },
        leaveFeedback: function (row) {
            alert('Feedback here');
        }
    };

    return exports;
};