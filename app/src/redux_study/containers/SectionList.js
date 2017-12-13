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

let Sources = ( props ) => {
    console.log(props)
    let input

    return (
        <div>
            <ul>
            {props.sources.map(source => 
                <Section key={source.id} name={source.name}/>
            )}
            </ul>
        </div>
    )
}
let SectionList = connect(
    mapStateToProps,
    mapDispatchToProps
)(Sources)

export default SectionList