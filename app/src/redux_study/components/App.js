import React from 'react'
import AddFeed from '../containers/AddFeed'
import SectionList from '../containers/SectionList'
import FeedList from './FeedList'

const App = () => (
  <div>
    <AddFeed />
    <SectionList />
    <hr/>
    <div><b>The feed list:</b></div>
    <FeedList />
  </div>
)

export default App
