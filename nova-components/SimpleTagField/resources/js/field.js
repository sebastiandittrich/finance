Nova.booting((Vue, router, store) => {
  Vue.component('index-simple-tag-field', require('./components/IndexField'))
  Vue.component('detail-simple-tag-field', require('./components/DetailField'))
  Vue.component('form-simple-tag-field', require('./components/FormField'))
})
