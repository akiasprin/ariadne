import fetch from 'common/fetch'
let order_uri = '/order/'

//数据列表
export function list(params) {
  return fetch({
    url: order_uri,
    method: 'get',
    params
  })
}
//根据id查询数据
export function get(id, params) {
  console.log(id+params)
  return fetch({
    url: order_uri + id,
    method: 'get',
    params
  })
}

//根据id删除数据
export function del(id, data) {
  return fetch({
    url: order_uri + id,
    method: 'post',
    data
  })
}
//添加或修改数据
export function save(id, data) {
  return fetch({
    url: order_uri + (id ? id : ''),
    method: id ? 'put' : 'post',
    data
  })
}

