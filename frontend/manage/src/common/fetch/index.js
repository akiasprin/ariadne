//导入模块
import axios from 'axios'
import {Message} from 'element-ui'
import store from 'store'
import {SET_USER_INFO} from 'store/actions/type'
import {server_base_url} from 'common/config'

export default function fetch(options) {
    return new Promise((resolve, reject) => {
        // https://github.com/mzabriskie/axios

        //创建一个axios实例
        const instance = axios.create({
            //设置默认根地址
            baseURL: server_base_url,
            //设置请求超时设置
            timeout: 2000,
            //设置请求时的header
           headers: {'Authorization': `Bearer ${store.getters.get_token}`}
        })

        console.log(options)

        //请求处理
        instance(options)
            .then(({data: {code, msg, data}}) => {
                console.log({code, msg, data})
                //请求成功时,根据业务判断状态
                if (code === 201)
                    Message({message: msg,
                      type: 'success'})
                resolve({code, msg, data})
            })
            .catch((error) => {
                console.log(error.response)
                if (error.response.data) {
                    Message.error(error.response.data.msg)
                    reject(error.response.data)
                }
            })
    })
}
