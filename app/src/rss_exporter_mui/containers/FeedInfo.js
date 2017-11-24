import React from 'react'
import { connect } from 'react-redux'
import { Button,FormGroup,FormControl,ControlLabel } from 'react-bootstrap'
//import { addTodo } from '../actions'

const mapStateToProps = (state, ownProps) => {
     return {
        feedInfo : state.feedInfo
     }
 }

 const mapDispatchToProps = dispatch => {
     return {

     }
 }

let FeedInfo = ({ dispatch }) => {
    let input

    return (
        <div>
            <form
                onSubmit={e => {
                    e.preventDefault()
                    if (!input.value.trim()) {
                        return
                    }
                    //dispatch(addTodo(input.value))
                    //input.value = ''
                }}
            >
                <FormGroup>
                    <ControlLabel>タイトル</ControlLabel>
                    <FormControl
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
        </div>
    )
}
FeedInfo = connect(
    mapStateToProps,
    mapDispatchToProps
)(FeedInfo)

export default FeedInfo