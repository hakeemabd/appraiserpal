CJMA.Datagrid = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        dropdownButton = di.get('dropdownButton'),
        loader = di.get('loader'),
        confirmation = di.get('confirmation'),
        defaultConfig = {
            datagridSelector: ".datagrid",
            rowActionSelector: "[data-role=action]",
            tooltippedSelector: ".row-tooltip",
            fullNameRenderer: fullNameRenderer,
            timeFromMinutesRenderer: timeFromMinutesRenderer,
            textRenderer: $.fn.dataTable.render.text(),
            moneyRenderer: moneyRenderer,
            usDateTimeRenderer: usDateTimeRenderer,
            responsive: true,
            appendActions: true,
            actionSelector: '[data-role=action]',
            errorText: 'Sorry, an error occurred'
        },
        config = {},
        datagrids = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
    };

    exports.addGrid = function (opts) {
        opts = opts || {};
        var $grid = $(opts.selector || config.datagridSelector),
            newGrids = [];
        $grid.each(function () {
            var $g = $(this),
                columns = opts.columns || $.isFunction(opts.getColumnConfig) ? opts.getColumnConfig($g, opts) : getColumnConfig($g, opts),
                datagrid = $g.DataTable({
                    processing: true,
                    serverSide: true,
                    bFilter: true,
                    bLengthChange: false,
                    ajax: $g.data('source'),
                    columns: columns,
                    responsive: opts.responsive == undefined ? config.responsive : opts.responsive
                }),
                gridId = 'g' + Math.floor(Math.random() * 1000);
            if (opts.search) {
                var fn = function() {
                    datagrid.search($(this).val()).draw();
                }, tm;
                $(opts.search).on('input', function(e) {
                    if (tm) {
                        clearTimeout(tm);
                    }
                    setTimeout(fn.bind(this), 200);
                });
            }
            $g.data('options', opts);
            $g.data('grid-id', gridId);

            datagrid.on('draw', opts.onGridReady || onGridReady);
            datagrids[gridId] = datagrid;
            newGrids.push(datagrid);
        });
        return newGrids;
    };

    function getColumnConfig($grid, opts) {
        var cols = [];
        $('thead th', $grid).each(function () {
            var $th = $(this),
                index = $th.data('index') || $th.text().toLowerCase(),
                name = $th.data('name') || index,
                renderFn,
                col = {
                    data: index,
                    name: name
                };
            if (renderFn = $th.data('render')) {
                renderFn += 'Renderer';
                col.render = opts[renderFn] || config[renderFn] || config.textRenderer;
            }
            if(!(col.name == 'actions' && opts.actions)) {
                cols.push(col);
            }
        });
        if (config.appendActions && $grid.data('action') == undefined && !($grid.data('action') == 0)) {
            if(!opts.actions) {
                $('thead tr', $grid).append("<th>Actions</th>");
            }
            cols.push({
                data: "DT_RowData",
                sortable: false,
                searchable: false,
                render: renderActions
            });
        }
        return cols;
    }

    function fullNameRenderer(data, type, row) {
        if (type != 'display') {
            return data;
        }
        if (row.first_name || row.last_name) {
            return [row.first_name, row.last_name].join(' ');
        }
        return row.email;
    }

    function timeFromMinutesRenderer(data, type, row) {
        if (type != 'display') {
            return data;
        }
        var hours = Math.floor(data / 60),
            minutes = data % 60;
        //an extravagant way to turn [5, 1] -> "05:01"
        return [hours, minutes].map(function (part) {
            return part < 10 ? '0' + part : part;
        }).join(':');
    }

    function moneyRenderer(data, type, row) {
        if (type != 'display') {
            return data;
        }
        return '$' + (data - 0).toFixed(2);
    }

    function usDateTimeRenderer(data, type, row) {
        if (type != 'display') {
            return data;
        }
        var m = moment(data);
        if (m) {
            return m.format('MM/DD/YY HH:mm:ss');
        }
        return '';
    }

    function renderActions(data, type, row) {
        if (type != 'display') {
            return '';
        }
        return dropdownButton.render(data);
    }

    function onGridReady() {
        dropdownButton.activateDropdowns(this);
        $(config.actionSelector, this).click(onRowActionClick);
        $(config.tooltippedSelector).tooltip();
    }

    function onRowActionClick(e) {
        var $btn = $(this);
        if ($btn.data('confirm')) {
            if ($btn.data('confirm-type') == 'remove') {
                confirmation.wantRemove({
                    onYes: doAction.bind(this, e, $btn)
                });
            } else {
                confirmation.generic({
                    header: $btn.data('confirm-header'),
                    message: $btn.data('confirm-message'),
                    onYes: doAction.bind(this, e, $btn)
                });
            }
            return false;
        }
        return doAction.call(this, e, $btn);
    }

    function doAction(e, $btn) {
        switch ($btn.data('handler')) {
            case 'link':
                if (!$btn.data('ajax')) {
                    return true; //we just allow normal link
                }
                e.preventDefault();
                loader.show($btn.parent());
                $.ajax({
                    url: $btn.attr('href'),
                    type: $btn.data('method') || 'GET',
                    dataType: 'json',
                    context: $btn
                }).done(function () {
                    showMessage($btn, 'success');
                }).fail(function () {
                    showMessage($btn, 'error', config.errorText);
                }).always(onDataUpdated);
                return false;
            case 'popup':
                var options = $btn.parents(config.datagridSelector).data('options'),
                    handlerName = $btn.data('action')+'Handler';
                if ($.isFunction(options[handlerName])) {
                    var data = datagrids[$btn.parents(config.datagridSelector).data('grid-id')].row($btn.parents('tr')).data();
                    return options[handlerName].call(this, $btn, data);
                }
        }
    }

    function onDataUpdated() {
        var $btn = $(this);
        loader.hide($btn.parent());
        if ($btn.data('refresh')) {
            datagrids[$btn.parents(config.datagridSelector).data('grid-id')].draw();
        }
    }

    function showMessage($btn, type, defaultMsg) {
        type = type || 'error';
        var options = $btn.parents(config.datagridSelector).data('options'),
            msgText = options[type] ? options[type][$btn.data('action')] || defaultMsg : defaultMsg;
        if (msgText) {
            app.msg(msgText);
        }
    }

    return exports;
};