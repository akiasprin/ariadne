import * as good from './good'
import * as user from './user'
import * as category from './category'
import * as comment from './comment'
import * as order from './order'
import * as address from './address'
import * as cart from './cart'

const apiObj = {
  category,
  good,
  order,
  user,
  comment,
  address,
  cart,
}

const install = function (Vue) {
  if (install.installed) return
  install.installed = true

  //定义属性到Vue原型中
  Object.defineProperties(Vue.prototype, {
    $fetch: {
      get() {
        return apiObj
      }
    }
  })
}

export default {
  install
}
