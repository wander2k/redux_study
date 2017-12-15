import React from 'react'
import PropTyps from 'prop-types'
import { connect } from 'react-redux'


let Section = ({source}) => {
    console.log(source)
    return (
        <li key={source.id}><input type="radio" name="selectedSection" value={source.id}/>{source.name}</li>
    )
}

// const mapStateToProps = (state, name, ownProps) => {
//     console.log(state)
//     return {
//         id : state.id,
//         name : state.name
//     }
//  }

//  Section = connect(
//     mapStateToProps,
//     null     
//  )(Section)

export default Section