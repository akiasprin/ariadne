import Vue from 'vue'
import VueRouter from 'vue-router'
import store from 'store'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

import viewPageComponent from 'pages/App'

import homeComponent from 'pages/home'
import noPageComponent from 'pages/error/404'
import registerComponent from 'pages/user/register'
import loginComponent from 'pages/user/login'
import salesGoodsComponent from 'pages/good/index'
import salesOrdersComponent from 'pages/order/base'
import salesOrdersSaveComponent from 'pages/order/save'
import salesGoodsSaveComponent from 'pages/good/save'
import categoriesComponent from 'pages/category/base'
import shopOrdersComponent from 'pages/order2/base'

import shopAddressesComponent from 'pages/address/index'
import commentsComponent from 'pages/comment/index'

import cartComponent from 'pages/cart/index'
import userOptionsComponent from 'pages/user/options'

Vue.use(VueRouter)

//使用AMD方式加载
// component: resolve => require(['pages/home'], resolve),
const routes = [{
  path: '/404',
  name: 'notPage',
  component: noPageComponent
}, {
  path: '*',
  redirect: '/404'
}, {
    path: '/user/register',
    name: 'register',
    component: registerComponent
}, {
  path: '/user/login',
  name: 'login',
  component: loginComponent
}, {
  path: '/',
  redirect: '/home',
  component: viewPageComponent,
  children: [{
    path: '/home',
    name: 'home',
    component: homeComponent,
    meta: {
      title: "主页",
      auth: true
    }
  }, {
    path: '/sales/goods',
    name: 'salesGoods',
    component: salesGoodsComponent,
    meta: {
      title: "商品管理",
      auth: true
    }
  }, {
    path: '/sales/orders',
    name: 'salesOrders',
    component: salesOrdersComponent,
    meta: {
      title: "销售订单",
      auth: true
    }
  }, {
      path: '/sales/orders/update/:id',
      name: 'salesOrdersSave',
      component: salesOrdersSaveComponent,
      meta: {
          title: "数据修改",
          auth: true
      }
  }, {
    path: '/sales/good/update/:id',
    name: 'salesGoodsSave',
    component: salesGoodsSaveComponent,
    meta: {
      title: "更新商品",
      auth: true
    }
  }, {
    path: '/sales/good/add',
    name: 'salesGoodsAdd',
    component: salesGoodsSaveComponent,
    meta: {
      title: "添加商品",
      auth: true
    }
  }, {
    path: '/shop/orders',
    name: 'shopOrders',
    component: shopOrdersComponent,
    meta: {
      title: "购买订单",
      auth: true
    }
  }, {
    path: '/shop/addresses',
    name: 'shopAddresses',
    component: shopAddressesComponent,
    meta: {
      title: "地址管理",
      auth: true
    }
  }, {
    path: '/comments',
    name: 'comments',
    component: commentsComponent,
    meta: {
      title: "评论管理",
      auth: true
    }
  }, {
    path: '/cart',
    name: 'cart',
    component: cartComponent,
    meta: {
      title: "购物车信息",
      auth: true
    }
  }, {
    path: '/admin/categories/',
    name: 'categories',
    component: categoriesComponent,
    meta: {
      title: "分类目录",
      auth: true
    }
  }, {
    path: '/user/options/',
    name: 'userOptions',
    component: userOptionsComponent,
    meta: {
      title: "用户设置",
      auth: true
    }
  }]
}]

const router = new VueRouter({
  routes,
  mode: 'hash', //default: hash ,history
  scrollBehavior (to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return {x: 0, y: 0}
    }
  }
})

//全局路由配置
//路由开始之前的操作
router.beforeEach((to, from, next) => {
  NProgress.done().start()
  let toName = to.name
  // let fromName = from.name
  let is_login = store.state.user_info.login
  console.log(toName);
  if (!is_login && (toName !== 'login' && toName !== 'register')) {
    next({
      name: 'login'
    })
  } else {
    if (is_login && toName === 'login') {
      next({
        path: '/'
      })
    } else {
      next()
    }
  }
})

//路由完成之后的操作
router.afterEach(route => {
  NProgress.done()
})

export default router
