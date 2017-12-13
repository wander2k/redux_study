import React from 'react'
import {connect} from 'react-redux'
import PropTypes from 'prop-types'
import FeedLine from './FeedLine'


const mapStateToProps = state => {
    return {
        feeds : state.feeds
    }
}

const mapDispatchToProps = dispatch => {
    return {

    }
}

let Feeds = props => (
    <ul>
        {props.feeds.map(feed => 
            <FeedLine key={feed.id} {...feed} />
        )}
        </ul>
)

let FeedList = connect(
    mapStateToProps,
    mapDispatchToProps
)(Feeds)


export default FeedList 
