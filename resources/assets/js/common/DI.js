CJMA.DI = {
    autoInit: false,
    deps: {},
    get: function (dependency) {
        return this.deps[dependency] || null;
    },
    add: function (dependency, value) {
        this.deps[dependency] = value;
        if (this.autoInit && typeof value.init == 'function') {
            value.init();
        }
    }
};