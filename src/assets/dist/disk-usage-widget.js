const DiskUsageWidget = Garnish.Base.extend(
    {
        $container: null,
        namespace: null,
        $lightswitch: null,

        init: function(id, namespace) {
            this.$container = $('#' + id);
            this.namespace = namespace;
            this.$lightswitch = $('#' + namespace + '-areQuotasUsed');

            this.addListener(this.$lightswitch, 'change', '_toggleFieldsets');
        },

        _toggleFieldsets: function(event) {
            var useDiskQuotas = $(event.target).data('lightswitch').on;

            this.$container.toggleClass('disk-usage-widget-settings--with-disk-quotas');
        },
    }
);
