import fetch from 'common/fetch'
let comment_uri = '/comment/'

//数据列表
export function list(params) {
  return fetch({
    url: comment_uri,
    method: 'GET',
    params
  })
}

export function get(id, params) {
  return fetch({
    url: comment_uri + id,
    method: 'GET',
    params
  })
}

export function del(id, data) {
  return fetch({
    url: comment_uri + id,
    method: 'DELETE',
    data
  })
}

export function save(id, data) {
    if (id) {
        return fetch({
            url: comment_uri + id,
            method: 'PUT',
            data
        })
    }
    return fetch({
        url: comment_uri,
        method: 'POST',
        data
    })
}

