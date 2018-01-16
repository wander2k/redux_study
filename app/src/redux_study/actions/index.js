let nextFeedId = 0;
export const addFeed = (text, source) => {
    return {
        type : 'ADD_FEED',
        id : nextFeedId++,
        text,
        source
    }
}