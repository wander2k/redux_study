import { combineReducers } from 'redux'
import feedInfo from './feedInfo'
import sources from './sources'
//import todos from './todos'
//import visibilityFilter from './visibilityFilter'

const rssExporterMuiApp = combineReducers({
  feedInfo,
  sources
})

export default rssExporterMuiApp
