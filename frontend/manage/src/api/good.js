import fetch from 'common/fetch'
let good_uri = '/good/'

//数据列表
export function list(params) {
  return fetch({
    url: good_uri,
    method: 'GET',
    params
  })
}

export function get(id, params) {
  return fetch({
    url: good_uri + id,
    method: 'GET',
    params
  })
}

export function del(id, data) {
  return fetch({
    url: good_uri + id,
    method: 'DELETE',
    data
  })
}

export function save(id, data) {
    if (id) {
        return fetch({
            url: good_uri + id,
            method: 'PUT',
            data
        })
    }
    return fetch({
        url: good_uri,
        method: 'POST',
        data
    })
}

