let nextFeedId = 0;
export const addFeed = text => {
    return {
        type : 'ADD_FEED',
        id : nextFeedId++,
        text
    }
}