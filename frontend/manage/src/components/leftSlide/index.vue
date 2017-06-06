<template>
  <div class="left-side">
    <div class="left-side-inner">
      <router-link to="/" class="logo block">
        <img src="./images/logo.png" alt="AdminX">
      </router-link>
      <el-menu
        class="menu-box"
        theme="dark"
        router
        :default-active="$route.path">
        <div
          v-for="(item, index) in nav_menu_data"
          :key="index">
          <el-menu-item
            class="menu-list"
            v-if="typeof item.child === 'undefined'"
            :index="item.path">
            <i class="icon fa" :class="item.icon"></i>
            <span v-text="item.title" class="text"></span>
          </el-menu-item>
          <el-submenu
            :index="item.path"
            v-else>
            <template slot="title">
              <i class="icon fa" :class="item.icon"></i>
              <span v-text="item.title" class="text"></span>
            </template>
            <el-menu-item
              class="menu-list"
              v-for="(sub_item, sub_index) in item.child"
              :index="sub_item.path"
              :key="sub_index">
              <!--<i class="icon fa" :class="sub_item.icon"></i>-->
              <span v-text="sub_item.title" class="text"></span>
            </el-menu-item>
          </el-submenu>
        </div>
      </el-menu>
    </div>
  </div>
</template>
<script type="text/javascript">
  import {mapGetters, mapActions} from 'vuex'
  import {GET_USER_INFO} from 'store/getters/type'
  import {SET_USER_INFO} from 'store/actions/type'
  export default{
    name: 'slide',
    computed: {
      ...mapGetters({
        get_user_info: GET_USER_INFO,
      })
    },
    data(){
      return {
        nav_menu_data: [{
          title: "主页",
          path: "/home",
          icon: "fa-home"
        }, {
          title: "出售管理",
          path: "/sales",
          icon: "fa-table",
          child: [{
            title: "商品管理",
            path: "/sales/goods"
          }, {
            title: "销售订单",
            path: "/sales/orders"
          }]
        }, {
          title: "购物管理",
          path: "/shop",
          icon: "fa-shopping-bag",
          child: [{
            title: "收货地址",
            path: "/shop/addresses"
          }, {
              title: "已购订单",
              path: "/shop/orders"
          }]
        }, {
          title: "评论管理",
          path: "/comments",
          icon: "fa-commenting-o",
        }, {
          title: "所有商品浏览",
          path: "/goods",
          icon: "fa-shopping-cart",
        }, {
          title: "查看购物车",
          path: "/cart",
          icon: "fa-shopping-cart",
        }, {
            title: "用户管理",
            path: "/user",
            icon: "fa-user-o",
            child: [{
                title: "信息修改",
                path: "/user/options"
            }]
        }]
      }
    },
    created() {
      if (this.get_user_info.user.role === 'admin') {
        this.nav_menu_data.push({
          title: "系统管理",
          path: "/admin",
          icon: "fa-cog",
          child: [{
            title: "分类管理",
            path: "/admin/categories"
          }]
        })
      }
    }
  }
</script>
