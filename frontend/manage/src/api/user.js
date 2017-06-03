import fetch from 'common/fetch'
let user_uri = '/user/'

export function get(id, params) {
  return fetch({
    url: user_uri + id,
    method: 'GET',
    params
  })
}

export function save(id, data) {
  return fetch({
    url: user_uri + id,
    method: 'POST',
    data
  })
}

export function sendcode(data) {
  return fetch({
    url: user_uri + 'sendcode',
    method: 'POST',
    data
  })
}

export function register(data) {
  return fetch({
    url: user_uri + 'register',
    method: 'POST',
    data
  })
}

export function login(data) {
    return fetch({
        url: user_uri + 'login',
        method: 'POST',
        data
    })
}

export function logout() {
  return fetch({
    url: user_uri + 'logout',
    method: 'POST'
  })
}
