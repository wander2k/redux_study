let initstate = [{"id":1, "name":"love"},{"id":2, "name":"beauty"}]

const sources = (state = initstate, action) => {
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
  