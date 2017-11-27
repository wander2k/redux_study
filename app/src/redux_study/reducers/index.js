import { combineReducers } from 'redux'
import feeds from './feeds'
import sources from './sources'
//import todos from './todos'
//import visibilityFilter from './visibilityFilter'

const rssExporterMuiApp = combineReducers({
  feeds,
  sources
})

export default rssExporterMuiApp
