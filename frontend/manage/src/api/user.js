import fetch from 'common/fetch'
let user_uri = '/user/'

export function register(data) {
  return fetch({
    url: user_uri + 'register',
    method: 'post',
    data
  })
}

export function login(data) {
    return fetch({
        url: user_uri + 'login',
        method: 'post',
        data
    })
}

export function logout() {
  return fetch({
    url: user_uri + 'logout',
    method: 'post'
  })
}
