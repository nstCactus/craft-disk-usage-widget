/* global $, Garnish */
// eslint-disable-next-line no-unused-vars
const DiskUsageWidget = Garnish.Base.extend(
    {
        $container: null,
        namespace: null,
        $areQuotasUsedLightswitch: null,

        init: function(id, namespace) {
            this.$container = $('#' + id);
            this.namespace = namespace;
            this.$areQuotasUsedLightswitch = $('#' + namespace + '-areQuotasUsed');
            this.$overrideSoftLimitLightswitch = $('#' + namespace + '-overrideSoftLimit');

            this.addListener(this.$areQuotasUsedLightswitch, 'change', function(event) {
                const useDiskQuotas = $(event.target).data('lightswitch').on;
                this.$container.toggleClass('disk-usage-widget-settings--with-disk-quotas', useDiskQuotas);
            });

            this.addListener(this.$overrideSoftLimitLightswitch, 'change', function (event) {
                const overrideSoftLimit = $(event.target).data('lightswitch').on;
                this.$container.toggleClass('disk-usage-widget-settings--override-soft-limit', overrideSoftLimit);
            })
        },
    }
);
