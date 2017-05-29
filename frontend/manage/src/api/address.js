import fetch from 'common/fetch'
let address_uri = '/address/'

import axios from 'axios'
import {Message} from "element-ui";

export function list(params) {
  return fetch({
    url: address_uri,
    method: 'GET',
    params
  })
}

export function get(id, params) {
  return fetch({
    url: address_uri + id,
    method: 'GET',
    params
  })
}

export function del(id, data) {
  return fetch({
    url: address_uri + id,
    method: 'DELETE',
    data
  })
}

export function save(id, data) {
  if (id) {
    return fetch({
        url: address_uri + id,
        method: 'PUT',
        data
    })
  }
  return fetch({
      url: address_uri,
      method: 'POST',
    data
  })
}

