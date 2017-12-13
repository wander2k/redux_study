const feeds = (state = [], action) => {
  switch (action.type) {
    case 'ADD_FEED':
      console.log(action);
      return [
        ...state,
        {
          id: action.id,
          name: action.text
        }
      ]

    default:
      return state
  }
}

export default feeds