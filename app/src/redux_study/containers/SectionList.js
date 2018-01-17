import React from 'react'
import { connect } from 'react-redux'
import { InputGroup, Radio } from 'react-bootstrap'
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

 function validateRadio(value) {
    if(!value) {
      return 'You need to check this value'
    }
 }

let SectionList = ( props ) => {
    console.log(props)
    let input

    return (
        <InputGroup>
            {props.sources.map(source => 
                <Radio inline key={source.id} name="selectedSection" value={source.id} onChange={props.onChangeValue}>{source.name}</Radio>
            )}
        </InputGroup>
    )
}
SectionList = connect(
    mapStateToProps,
    mapDispatchToProps
)(SectionList)

export default SectionList