import Vue from 'vue'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import { App, plugin } from '@inertiajs/inertia-vue'
import { InertiaProgress } from '@inertiajs/progress/src'
import vuetify from '@/Plugins/vuetify'
import Vue2Filters from 'vue2-filters'
import VCurrencyField from 'v-currency-field'
import EventBus from '@/utils/EventBus'


import { setDefaultOptions } from 'date-fns'
import { es } from 'date-fns/locale'
setDefaultOptions({ locale: es })

Vue.config.productionTip = false
Vue.mixin({ methods: { route: window.route } })
Vue.use(plugin)
Vue.use(PortalVue)
Vue.use(VueMeta)
Vue.use(Vue2Filters,{
  currency: {
    symbol: '$',
    decimalDigits: 2,
    thousandsSeparator: ',',
    decimalSeparator: '.',
    symbolOnLeft: true,
    spaceBetweenAmountAndSymbol: true,
    showPlusSign: false
  },
})
Vue.use(VCurrencyField)
Vue.use(EventBus)

InertiaProgress.init({
  // The delay after which the progress bar will
  // appear during navigation, in milliseconds.
  delay: 250,

  // The color of the progress bar.
  color: '#29d',

  // Whether to include the default NProgress styles.
  includeCSS: true,

  // Whether the NProgress spinner will be shown.
  showSpinner: true,
})

let app = document.getElementById('app')
const page = JSON.parse(app.dataset.page)

new Vue({
  vuetify,
  EventBus,
  metaInfo: {
    titleTemplate: title => (title ? `${title} - Rentas` : 'Rentas'),
  },
  render: h =>
    h(App, {
      props: {
        initialPage: page,
        resolveComponent: name =>
          import(`@/Pages/${name}`).then(module => module.default),
      },
    }),
}).$mount(app)
