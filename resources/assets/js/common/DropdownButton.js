CJMA.DropdownButton = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        defaultConfig = {
            icon: 'arrow_drop_down',
            name: 'Actions',
            defaultDataAttributes: {
                "ajax": true,
                "handler": "link",
                "refresh": true,
                "role": "action"
            },
            dropdownCls: 'ap-actions'
        },
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
    };

    exports.render = function (opts) {
        var id = 'dd_' + Math.floor(Math.random() * 10000),
            actionsConfig = opts.actions,
            icon = opts.icon || config.icon,
            name = opts.name || config.name,
            curCfg,
            actions = [],
            actionText,
            dataAttrs;
        for (var btn in actionsConfig) {
            if (!actionsConfig.hasOwnProperty(btn)) {
                continue;
            }
            curCfg = actionsConfig[btn];
            dataAttrs = $.extend({}, config.defaultDataAttributes, {action: btn}, curCfg);
            actionText = curCfg.text || btn[0].toUpperCase() + btn.substr(1);
            actionText = '<span>' + actionText + '</span>';
            if (curCfg.icon) {
                actionText = "<i class='material-icons " + (curCfg.align || 'left') + "'>" + curCfg.icon + "</i>"
            }
            if (curCfg.link) {
                actions.push("<a href='" + curCfg.link + "'" + renderDataAttrs(dataAttrs) + ">" + actionText + "</a>");
            }
            else {
                actions.push("<a" + renderDataAttrs(dataAttrs) + ">" + actionText + "</a>");
            }
        }
        if (actions.length == 1) { //there is only one action, no need for dropdown at all, we generate just a button
            var el = $(actions[0]).addClass('waves-effect waves-light btn');
            if (opts.name) {
                $('span', el).html(opts.name);
            }
            if (opts.icon) {
                $('i', el).text(opts.icon);
            }
            return el[0].outerHTML;
        }
        else { //we generate full-featured dropdown button
            var output = "<a class='" + config.dropdownCls + " dropdown-button btn fixed95' data-activates='content_" + id + "' >" +
                "   <i class='material-icons right'>" + icon + "</i><span>" + name + "</span>" +
                "</a>" +
                "<ul id='content_" + id + "' class='dropdown-content'>";
            for (var i = 0; i < actions.length; i++) {
                output += "<li>" + actions[i] + "</li>"
            }
            output += "</ul>";
            return output;
        }
    };

    exports.activateDropdowns = function (parent) {
        parent = parent || document.body;
        $('.' + config.dropdownCls, parent).dropdown({
                constrain_width: true, // Does not change width of dropdown to that of the activator
                hover: false, // Activate on hover
                belowOrigin: true, // Displays dropdown below the button
                alignment: 'left' // Displays dropdown with edge aligned to the left of button
            }
        );
    };

    function renderDataAttrs(attrs, skipAttrs) {
        skipAttrs = ['link', 'text', 'icon'];
        var res = ' ';
        for (var attr in attrs) {
            if (!attrs.hasOwnProperty(attr)) {
                continue;
            }
            if (skipAttrs.indexOf(attr) !== -1) {
                continue;
            }
            res += 'data-' + attr + '="' + attrs[attr] + '"';
        }
        return res;
    }

    return exports;
};