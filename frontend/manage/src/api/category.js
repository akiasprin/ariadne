import fetch from 'common/fetch'
import md5 from 'js-md5'
let category_uri = '/category/'

import axios from 'axios'
import {Message} from "element-ui";

export function list(params) {
  return fetch({
    url: category_uri,
    method: 'GET',
    params
  })
}

export function get(id, params) {
  return fetch({
    url: category_uri + id,
    method: 'GET',
    params
  })
}

export function del(id, data) {
  return fetch({
    url: category_uri + id,
    method: 'DELETE',
    data
  })
}

export function translate(query) {
  let appID = '6a7b53c63af42576'
  let appKey = 'HHCQB7qLKRHCs4Cg9o8ZLBUyu86IxdSX'
  let salt = 1
  let sign = md5(appID + query + salt + 'HHCQB7qLKRHCs4Cg9o8ZLBUyu86IxdSX')
  let params = {
    q: query,
    from: 'zh_CHS',
    to: 'EN',
    sign: sign,
    salt: salt,
    appKey: appID
  }
  return axios({
    url: 'http://localhost/apis/api',
    method: 'GET',
    timeout: 1500,
    params
  })
}

export function save(id, data) {
  if (id) {
    return fetch({
        url: category_uri + id,
        method: 'PUT',
        data
    })
  }
  return fetch({
      url: category_uri,
      method: 'POST',
    data
  })
}

