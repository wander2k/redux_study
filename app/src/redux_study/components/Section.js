import React from 'react'
import PropTyps from 'prop-types'
import { connect } from 'react-redux'
import { Radio  } from 'react-bootstrap'


let Section = ({source}) => {
    console.log(source)
    return (
        <Radio inline name="selectedSection" value={source.id}>{source.name}</Radio>
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