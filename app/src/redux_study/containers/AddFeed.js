import React from 'react'
import { connect } from 'react-redux'
import { Form, Button,FormGroup,FormControl,ControlLabel } from 'react-bootstrap'
import { addFeed } from '../actions'
import SectionList from './SectionList'

let AddFeed = ({ sources, dispatch }) => {
    console.log(sources)
    let input
    let selectedSource

    let handleChangeValue = e => { 
        console.log(e.target.value);
        selectedSource = sources.find(element => element.id == e.target.value);
        console.log(selectedSource);
    }

    return (
        <div>
            <Form inline
                onSubmit={e => {
                    e.preventDefault()
                    console.log("****Container:AddFeed:onSubmit triggered*****")
                    console.log(input.value)
                    console.log(selectedSource)
                    // console.log(sources.find(element => element.id == selectedSource.value))
                    if (!input.value.trim()) {
                        return
                    };
                    dispatch(addFeed(input.value, selectedSource.name))
                    input.value = ''
                }}
            >
                <FormGroup>
                    <ControlLabel>タイトル</ControlLabel>
                    <FormControl
                        type="text"
                        inputRef ={node => {
                            input = node
                        }}
                    />
                    <Button bsStyle="primary" type="submit">
                        Save
                    </Button>
                </FormGroup>
                <FormGroup>
                    <SectionList onChangeValue={handleChangeValue}/>
                </FormGroup>
            </Form>
        </div>
    )
}
const mapStateToProps = (state, ownProps) => {
    return {
        sources : state.sources
    }
 }

 const mapDispatchToProps = dispatch => {
    return {
    }
}

AddFeed = connect(
    mapStateToProps,
    null
)(AddFeed)

export default AddFeed