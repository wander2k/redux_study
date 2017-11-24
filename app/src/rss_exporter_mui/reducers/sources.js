const sources = (state = [], action) => {
    switch (action.type) {
      case 'ADD_SOURCE':
        return [
          ...state,
          {
            id: action.id,
            text: action.text,
            completed: false
          }
        ]
      default:
        return state
    }
  }
  
  export default sources
  