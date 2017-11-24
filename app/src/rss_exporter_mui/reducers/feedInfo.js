let initstate = { "title" : "new feed", b : "2"}

const feedInfo = (state = initstate, action) => {
    switch (action.type) {
      case 'SAVE_FEED':
        return state
      default:
        return state
    }
  }
  
  export default feedInfo
  