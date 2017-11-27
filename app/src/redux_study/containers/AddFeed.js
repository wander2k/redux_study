import React from 'react'
import { connect } from 'react-redux'
import { Button,FormGroup,FormControl,ControlLabel } from 'react-bootstrap'
import { addFeed } from '../actions'

let AddFeed = ({ dispatch }) => {
    let input

    return (
        <div>
            <form
                onSubmit={e => {
                    console.log(input.value)
                    e.preventDefault()
                    if (!input.value.trim()) {
                        return
                    };
                    dispatch(addFeed(input.value))
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
        </div>
    )
}
AddFeed = connect(
)(AddFeed)

export default AddFeed