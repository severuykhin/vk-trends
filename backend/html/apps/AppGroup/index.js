import React from 'react'
import ReactDOM from 'react-dom'
import App from './App'

const initial_data = window.__INITIAL_DATA__ ? window.__INITIAL_DATA__ : {};

ReactDOM.render(<App data={initial_data}/>, document.getElementById('root'))