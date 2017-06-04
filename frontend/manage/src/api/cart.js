import fetch from 'common/fetch'
let cart_uri = '/cart/'

import axios from 'axios'
import {Message} from "element-ui";

export function list(params) {
  return fetch({
    url: cart_uri,
    method: 'GET',
    params
  })
}


export function save(data) {
  return fetch({
      url: cart_uri,
      method: 'POST',
    data
  })
}

