import React from 'react'
import { connect } from 'react-redux'
import { Button,FormGroup,FormControl,ControlLabel } from 'react-bootstrap'
import Section from '../components/Section'
//import { addTodo } from '../actions'

const mapStateToProps = (state, ownProps) => {
    return {
        sources : state.sources
    }
 }

 const mapDispatchToProps = dispatch => {
     return {

     }
 }

let SectionList = ( props ) => {
    console.log(props)
    let input

    return (
        <div>
            <ul>
            {props.sources.map(source => 
                <Section key={source.id} source={source}/>
            )}
            </ul>
        </div>
    )
}
SectionList = connect(
    mapStateToProps,
    mapDispatchToProps
)(SectionList)

export default SectionList