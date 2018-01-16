import React from 'react'
import { connect } from 'react-redux'
import { Button,FormGroup,FormControl,ControlLabel } from 'react-bootstrap'
import { addFeed } from '../actions'
import SectionList from './SectionList'

let AddFeed = ({ sources, dispatch }) => {
    console.log(sources)
    let input
    let selectedSource

    return (
        <div>
            <form
                onSubmit={e => {
                    console.log("****Container:AddFeed:onSubmit triggered*****")
                    console.log(input.value)
                    console.log(selectedSource.value)
                    console.log(sources.find(element => element.id == selectedSource.value))
                    e.preventDefault()
                    if (!input.value.trim()) {
                        return
                    };
                    dispatch(addFeed(input.value, sources.find(element => element.id == selectedSource.value)))
                    input.value = ''
                }}
            >
                <FormGroup>
                    <ControlLabel>タイトル</ControlLabel>
                    <input
                        type="text"
                        ref={node => {
                            input = node
                        }}
                    />
                    <Button bsStyle="primary" type="submit">
                        Save
                    </Button>
                </FormGroup>
            </form>
            <div>
                <ul>                    
                    {sources.map(source => 
                        <li key={source.id}><input type="radio" name="selectedSection" 
                        value={source.id} 
                        ref={node => {
                            selectedSource = node 
                        }}/>{source.name}</li>
                    )}
                </ul>
            </div>
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